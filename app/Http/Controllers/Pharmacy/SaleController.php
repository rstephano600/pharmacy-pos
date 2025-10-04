<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Pharmacy;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\PharmacyStock;
use App\Models\StockMovement;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Display sales list with enhanced filtering
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'seller', 'pharmacy', 'prescription']);

        $user = Auth::user();
        $pharmacyId = session('active_pharmacy_id');

        // Apply pharmacy filter for non-super admins
        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            $query->where('pharmacy_id', $pharmacyId);
        }

        // Enhanced search filters
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', "%{$request->invoice_number}%");
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        if ($request->filled('sold_by')) {
            $query->where('sold_by', $request->sold_by);
        }

        // Get summary statistics
        $stats = $this->getSalesStatistics($query->clone(), $request);

        $sales = $query->latest()->paginate(15)->withQueryString();

        // Get filter options
        $customers = Customer::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->orderBy('name')
            ->get();

        $sellers = \App\Models\User::whereHas('sales', function ($q) use ($user, $pharmacyId) {
            if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
                $q->where('pharmacy_id', $pharmacyId);
            }
        })->get();

        return view('in.pharmacy.sales.index', compact('sales', 'stats', 'customers', 'sellers'));
    }

    /**
     * Show create form with enhanced medicine selection
     */
    public function create()
    {
        $user = Auth::user();
        $pharmacyId = session('active_pharmacy_id');

        $pharmacies = $user->role === \App\Models\User::ROLE_SUPER_ADMIN
            ? Pharmacy::all()
            : [];

        $customers = Customer::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->orderBy('name')
            ->get();

        // Get medicines with available stock only
        $medicines = Medicine::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->whereHas('pharmacyStocks', fn($q) => $q->where('available_quantity', '>', 0))
            ->with(['pharmacyStocks' => fn($q) => $q->where('available_quantity', '>', 0)])
            ->orderBy('name')
            ->get();

        // Get available batches with stock
        $batches = MedicineBatch::query()
        ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
        ->where('quantity_available', '>', 0)
        ->where(function ($q) {
        $q->whereNull('expiry_date')
          ->orWhere('expiry_date', '>', now());
        })
        ->with('medicine')
        ->orderBy('expiry_date')
        ->get();

        // Get pending prescriptions
        $prescriptions = Prescription::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->where('status', 'pending')
            ->with('customer')
            ->latest()
            ->get();

            $activePharmacy = null;

        if ($pharmacyId) {
            $activePharmacy = Pharmacy::find($pharmacyId);
        }
        return view('in.pharmacy.sales.create', compact('pharmacies', 'customers', 'batches', 'medicines', 'prescriptions', 'activePharmacy'));
    }

    /**
     * Store sale with enhanced validation and stock management
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pharmacyId = $user->role !== \App\Models\User::ROLE_SUPER_ADMIN ? session('active_pharmacy_id') : null;

        $validated = $request->validate([
            'pharmacy_id' => $user->role === \App\Models\User::ROLE_SUPER_ADMIN
                ? 'required|exists:pharmacies,id'
                : 'nullable',
            'customer_id' => 'nullable|exists:customers,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'sale_date' => 'required|date|before_or_equal:today',
            'sale_time' => 'nullable|date_format:H:i',
            'payment_method' => 'required|in:cash,insurance,mpesa,card,bank_transfer',
            'payment_reference' => 'nullable|string|max:100',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.medicine_batch_id' => 'required|exists:medicine_batches,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.dosage_instructions' => 'nullable|string|max:255',
        ]);

        // Set pharmacy ID for non-super admins
        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            $validated['pharmacy_id'] = $pharmacyId;
        }

        // Additional validation
        $this->validateSaleItems($validated['items'], $validated['pharmacy_id']);

        return DB::transaction(function () use ($validated, $request, $user) {
            // Generate unique invoice number
            $invoiceNumber = $this->generateInvoiceNumber($validated['pharmacy_id']);
            
            // Calculate totals
            $totals = $this->calculateSaleTotals($validated['items'], $validated);

            // Create sale record
            $sale = Sale::create([
                'pharmacy_id' => $validated['pharmacy_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'prescription_id' => $validated['prescription_id'] ?? null,
                'sold_by' => $user->id,
                'invoice_number' => $invoiceNumber,
                'sale_date' => $validated['sale_date'],
                'sale_time' => $validated['sale_time'] ?? now()->format('H:i:s'),
                'subtotal' => $totals['subtotal'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => $totals['tax_amount'],
                'discount_rate' => $validated['discount_rate'] ?? 0,
                'discount_amount' => $totals['discount_amount'],
                'total_amount' => $totals['total_amount'],
                'status' => 'completed',
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'completed_at' => now(),
            ]);

            // Process sale items
            $this->processSaleItems($sale, $validated['items']);

            // Update prescription status if linked
            if ($validated['prescription_id']) {
                Prescription::find($validated['prescription_id'])->update(['status' => 'dispensed']);
            }

            return redirect()->route('sales.show', $sale)
                ->with('success', "Sale completed successfully. Invoice: {$invoiceNumber}");
        });
    }

    /**
     * Show sale details with enhanced information
     */
    public function show(Sale $sale)
    {
        $this->authorizeAccess($sale);

        $sale->load([
            'items.medicine',
            'items.medicineBatch',
            'customer',
            'seller',
            'pharmacy',
            'prescription.prescriptionItems'
        ]);

        // Get related stock movements
        $stockMovements = StockMovement::whereIn('reference_id', $sale->items->pluck('id'))
            ->where('reference_type', 'sale')
            ->with(['medicine', 'medicineBatch', 'creator'])
            ->latest()
            ->get();

        // Calculate profit margins
        $profitAnalysis = $this->calculateProfitAnalysis($sale);

        return view('in.pharmacy.sales.show', compact('sale', 'stockMovements', 'profitAnalysis'));
    }

    /**
     * Edit sale (limited to certain fields to maintain integrity)
     */
    public function edit(Sale $sale)
    {
        $this->authorizeAccess($sale);

        // Only allow editing of pending or specific completed sales
        if (!in_array($sale->status, ['pending', 'completed']) || $sale->created_at->diffInHours(now()) > 24) {
            return redirect()->back()->with('error', 'This sale cannot be edited anymore.');
        }

        $user = Auth::user();
        $pharmacyId = session('active_pharmacy_id');

        $customers = Customer::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->orderBy('name')
            ->get();

        $prescriptions = Prescription::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->whereIn('status', ['pending', 'dispensed'])
            ->with('customer')
            ->latest()
            ->get();

        return view('in.pharmacy.sales.edit', compact('sale', 'customers', 'prescriptions'));
    }

    /**
     * Update sale with limited fields
     */
    public function update(Request $request, Sale $sale)
    {
        $this->authorizeAccess($sale);

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'payment_method' => 'required|in:cash,insurance,mpesa,card,bank_transfer',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $sale->update($validated);

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale updated successfully.');
    }

    /**
     * Cancel/Refund sale
     */
    public function cancel(Sale $sale)
    {
        $this->authorizeAccess($sale);

        if ($sale->status === 'cancelled') {
            return redirect()->back()->with('error', 'Sale is already cancelled.');
        }

        return DB::transaction(function () use ($sale) {
            // Restore stock quantities
            foreach ($sale->items as $item) {
                $batch = $item->medicineBatch;
                $batch->increment('quantity_available', $item->quantity);

                // Update pharmacy stock
                $stock = PharmacyStock::where('pharmacy_id', $sale->pharmacy_id)
                    ->where('medicine_id', $item->medicine_id)
                    ->first();

                if ($stock) {
                    $stock->increment('available_quantity', $item->quantity);
                }

                // Record stock movement
                StockMovement::create([
                    'pharmacy_id' => $sale->pharmacy_id,
                    'medicine_id' => $item->medicine_id,
                    'medicine_batch_id' => $item->medicine_batch_id,
                    'movement_type' => 'return',
                    'quantity_change' => $item->quantity,
                    'quantity_before' => $batch->quantity_available - $item->quantity,
                    'quantity_after' => $batch->quantity_available,
                    'reference_type' => 'sale_cancellation',
                    'reference_id' => $sale->id,
                    'notes' => "Sale cancelled: {$sale->invoice_number}",
                    'created_by' => Auth::id()
                ]);
            }

            // Update sale status
            $sale->update([
                'status' => 'cancelled',
                'notes' => ($sale->notes ? $sale->notes . "\n" : '') . "Sale cancelled on " . now()->format('Y-m-d H:i:s')
            ]);

            // Update prescription status if linked
            if ($sale->prescription_id) {
                Prescription::find($sale->prescription_id)->update(['status' => 'pending']);
            }

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale cancelled and stock restored successfully.');
        });
    }

    /**
     * Delete sale (only for super admin or recent sales)
     */
    public function destroy(Sale $sale)
    {
        $this->authorizeAccess($sale);

        // Additional restrictions for deletion
        if (Auth::user()->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            if ($sale->created_at->diffInHours(now()) > 2) {
                return redirect()->back()->with('error', 'Cannot delete sales older than 2 hours.');
            }
        }

        if ($sale->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot delete completed sales. Use cancel instead.');
        }

        DB::transaction(function () use ($sale) {
            // Restore stock if needed
            if ($sale->status !== 'cancelled') {
                foreach ($sale->items as $item) {
                    $item->medicineBatch->increment('quantity_available', $item->quantity);
                    
                    $stock = PharmacyStock::where('pharmacy_id', $sale->pharmacy_id)
                        ->where('medicine_id', $item->medicine_id)
                        ->first();

                    if ($stock) {
                        $stock->increment('available_quantity', $item->quantity);
                    }
                }
            }

            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    /**
     * Get sales analytics/reports
     */
    public function analytics(Request $request)
    {
        $user = Auth::user();
        $pharmacyId = $user->role !== \App\Models\User::ROLE_SUPER_ADMIN ? session('active_pharmacy_id') : null;

        $query = Sale::query()
            ->when($pharmacyId, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->where('status', 'completed');

        // Date filters
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $query->whereBetween('sale_date', [$dateFrom, $dateTo]);

        $analytics = [
            'total_sales' => $query->sum('total_amount'),
            'total_transactions' => $query->count(),
            'average_sale' => $query->avg('total_amount'),
            'top_medicines' => $this->getTopSellingMedicines($query->clone()),
            'daily_sales' => $this->getDailySales($query->clone()),
            'payment_methods' => $this->getPaymentMethodBreakdown($query->clone()),
            'profit_analysis' => $this->getProfitAnalysis($query->clone()),
        ];

        return view('in.pharmacy.sales.analytics', compact('analytics', 'dateFrom', 'dateTo'));
    }

    // Protected helper methods

    protected function authorizeAccess(Sale $sale)
    {
        $user = Auth::user();

        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN &&
            $sale->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this sale.');
        }
    }

    protected function validateSaleItems(array $items, int $pharmacyId)
    {
        foreach ($items as $index => $item) {
            $batch = MedicineBatch::find($item['medicine_batch_id']);

            if (!$batch || $batch->pharmacy_id !== $pharmacyId) {
                throw ValidationException::withMessages([
                    "items.{$index}.medicine_batch_id" => 'Invalid medicine batch for this pharmacy.'
                ]);
            }

            if ($batch->quantity_available < $item['quantity']) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => "Insufficient stock. Available: {$batch->quantity_available}"
                ]);
            }

            if ($batch->is_expired || $batch->expiry_date <= now()) {
                throw ValidationException::withMessages([
                    "items.{$index}.medicine_batch_id" => 'Cannot sell expired medicine batch.'
                ]);
            }

            if ($item['medicine_id'] != $batch->medicine_id) {
                throw ValidationException::withMessages([
                    "items.{$index}.medicine_id" => 'Medicine does not match the selected batch.'
                ]);
            }
        }
    }

    protected function generateInvoiceNumber(int $pharmacyId): string
    {
        $pharmacy = Pharmacy::find($pharmacyId);
        $prefix = $pharmacy ? strtoupper(substr($pharmacy->name, 0, 3)) : 'PHR';
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(uniqid(), -4));

        return "{$prefix}-{$timestamp}-{$random}";
    }

    protected function calculateSaleTotals(array $items, array $validated): array
    {
        $subtotal = 0;
        $totalDiscount = 0;

        foreach ($items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $lineTotal;
            $totalDiscount += $item['discount_amount'] ?? 0;
        }

        $discountRate = $validated['discount_rate'] ?? 0;
        $additionalDiscount = ($subtotal * $discountRate) / 100;
        $totalDiscountAmount = $totalDiscount + $additionalDiscount;

        $afterDiscount = $subtotal - $totalDiscountAmount;
        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = ($afterDiscount * $taxRate) / 100;

        $totalAmount = $afterDiscount + $taxAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($totalDiscountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }

    protected function processSaleItems(Sale $sale, array $items)
    {
        foreach ($items as $item) {
            $batch = MedicineBatch::find($item['medicine_batch_id']);
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $discount = $item['discount_amount'] ?? 0;

            // Create sale item
            SaleItem::create([
                'sale_id' => $sale->id,
                'medicine_id' => $item['medicine_id'],
                'medicine_batch_id' => $item['medicine_batch_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'unit_cost' => $batch->unit_cost,
                'total_price' => $lineTotal - $discount,
                'discount_amount' => $discount,
                'dosage_instructions' => $item['dosage_instructions'] ?? null,
                'expiry_date_at_sale' => $batch->expiry_date,
            ]);

            // Update batch stock
            $batch->decrement('quantity_available', $item['quantity']);

            // Update pharmacy stock
            $stock = PharmacyStock::where('pharmacy_id', $sale->pharmacy_id)
                ->where('medicine_id', $item['medicine_id'])
                ->first();

            if ($stock) {
                $stock->decrement('available_quantity', $item['quantity']);
            }

            // Record stock movement
            StockMovement::create([
                'pharmacy_id' => $sale->pharmacy_id,
                'medicine_id' => $item['medicine_id'],
                'medicine_batch_id' => $item['medicine_batch_id'],
                'movement_type' => 'sale',
                'quantity_change' => -$item['quantity'],
                'quantity_before' => $batch->quantity_available + $item['quantity'],
                'quantity_after' => $batch->quantity_available,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'notes' => "Sold via invoice: {$sale->invoice_number}",
                'created_by' => Auth::id()
            ]);
        }
    }

    protected function getSalesStatistics($query, $request): array
    {
        $baseQuery = $query->where('status', 'completed');

        return [
            'total_sales' => $baseQuery->sum('total_amount'),
            'total_transactions' => $baseQuery->count(),
            'average_sale' => round($baseQuery->avg('total_amount'), 2),
            'today_sales' => $baseQuery->clone()->whereDate('sale_date', today())->sum('total_amount'),
            'this_month' => $baseQuery->clone()->whereMonth('sale_date', now()->month)->sum('total_amount'),
        ];
    }

    protected function calculateProfitAnalysis(Sale $sale): array
    {
        $totalCost = $sale->items->sum(fn($item) => $item->quantity * $item->unit_cost);
        $totalRevenue = $sale->total_amount;
        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        return [
            'total_cost' => round($totalCost, 2),
            'total_revenue' => round($totalRevenue, 2),
            'gross_profit' => round($grossProfit, 2),
            'profit_margin' => round($profitMargin, 2),
        ];
    }

    protected function getTopSellingMedicines($query): array
    {
        return $query->with('items.medicine')
            ->get()
            ->flatMap->items
            ->groupBy('medicine_id')
            ->map(function ($items, $medicineId) {
                return [
                    'medicine' => $items->first()->medicine,
                    'quantity_sold' => $items->sum('quantity'),
                    'total_revenue' => $items->sum('total_price'),
                ];
            })
            ->sortByDesc('quantity_sold')
            ->take(10)
            ->values()
            ->toArray();
    }

    protected function getDailySales($query): array
    {
        return $query->selectRaw('sale_date, SUM(total_amount) as daily_total, COUNT(*) as transaction_count')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get()
            ->toArray();
    }

    protected function getPaymentMethodBreakdown($query): array
    {
        return $query->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->toArray();
    }

    protected function getProfitAnalysis($query): array
    {
        $sales = $query->with('items')->get();
        
        $totalRevenue = $sales->sum('total_amount');
        $totalCost = $sales->sum(function ($sale) {
            return $sale->items->sum(fn($item) => $item->quantity * $item->unit_cost);
        });

        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_cost' => round($totalCost, 2),
            'gross_profit' => round($totalRevenue - $totalCost, 2),
            'profit_margin' => $totalRevenue > 0 ? round((($totalRevenue - $totalCost) / $totalRevenue) * 100, 2) : 0,
        ];
    }
}