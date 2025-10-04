
{{-- resources/views/in/pharmacy/sales/analytics.blade.php --}}
@extends('layouts.app')

@section('title', 'Sales Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Analytics</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sales
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Date Filter --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Date Range Filter</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('sales.analytics') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>From Date</label>
                                        <input type="date" name="date_from" class="form-control" 
                                               value="{{ $dateFrom }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>To Date</label>
                                        <input type="date" name="date_to" class="form-control" 
                                               value="{{ $dateTo }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Summary Cards --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2>${{ number_format($analytics['total_sales'], 2) }}</h2>
                                    <p class="mb-0">Total Sales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2>{{ number_format($analytics['total_transactions']) }}</h2>
                                    <p class="mb-0">Total Transactions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h2>${{ number_format($analytics['average_sale'], 2) }}</h2>
                                    <p class="mb-0">Average Sale</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h2>${{ number_format($analytics['profit_analysis']['gross_profit'], 2) }}</h2>
                                    <p class="mb-0">Gross Profit</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Daily Sales Chart --}}
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Daily Sales Trend</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailySalesChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Methods --}}
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Payment Methods</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentMethodsChart"></canvas>
                                    <div class="mt-3">
                                        @foreach($analytics['payment_methods'] as $method)
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>{{ ucfirst($method['payment_method']) }}:</span>
                                                <span>
                                                    {{ $method['count'] }} transactions
                                                    <br><small class="text-muted">${{ number_format($method['total'], 2) }}</small>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        {{-- Top Selling Medicines --}}
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Top Selling Medicines</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Medicine</th>
                                                    <th>Quantity Sold</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($analytics['top_medicines'] as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <strong>{{ $item['medicine']['name'] }}</strong>
                                                            <br><small class="text-muted">{{ $item['medicine']['generic_name'] ?? 'N/A' }}</small>
                                                        </td>
                                                        <td>{{ number_format($item['quantity_sold']) }}</td>
                                                        <td>${{ number_format($item['total_revenue'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Profit Analysis --}}
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Profit Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total Revenue:</strong></td>
                                            <td class="text-right">${{ number_format($analytics['profit_analysis']['total_revenue'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Cost:</strong></td>
                                            <td class="text-right">${{ number_format($analytics['profit_analysis']['total_cost'], 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Gross Profit:</strong></td>
                                            <td class="text-right text-success">
                                                <strong>${{ number_format($analytics['profit_analysis']['gross_profit'], 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Profit Margin:</strong></td>
                                            <td class="text-right text-success">
                                                <strong>{{ number_format($analytics['profit_analysis']['profit_margin'], 2) }}%</strong>
                                            </td>
                                        </tr>
                                    </table>

                                    {{-- Profit Trend Indicator --}}
                                    <div class="progress mt-3">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ min($analytics['profit_analysis']['profit_margin'], 100) }}%" 
                                             aria-valuenow="{{ $analytics['profit_analysis']['profit_margin'] }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($analytics['profit_analysis']['profit_margin'], 1) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Profit Margin Indicator</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Sales Chart
const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
const dailySalesData = @json($analytics['daily_sales']);

new Chart(dailySalesCtx, {
    type: 'line',
    data: {
        labels: dailySalesData.map(item => item.sale_date),
        datasets: [{
            label: 'Daily Sales ($)',
            data: dailySalesData.map(item => item.daily_total),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }, {
            label: 'Transaction Count',
            data: dailySalesData.map(item => item.transaction_count),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Sales Amount ($)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Number of Transactions'
                },
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Payment Methods Pie Chart
const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
const paymentMethodsData = @json($analytics['payment_methods']);

new Chart(paymentMethodsCtx, {
    type: 'doughnut',
    data: {
        labels: paymentMethodsData.map(item => item.payment_method.charAt(0).toUpperCase() + item.payment_method.slice(1)),
        datasets: [{
            data: paymentMethodsData.map(item => item.total),
            backgroundColor: [
                '#FF6384',
                '#36A2EB', 
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
@endsection
