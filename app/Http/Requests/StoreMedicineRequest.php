<?php

namespace App\Http\Requests;

use App\Models\Medicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pharmacy_id' => ['required', 'exists:pharmacies,id'],
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'strength' => ['nullable', 'string', 'max:255'],
            'form' => ['required', 'string', Rule::in(array_keys(Medicine::FORMS))],
            'prescription_type' => ['required', 'string', Rule::in(array_keys(Medicine::PRESCRIPTION_TYPES))],
            'storage_type' => ['required', 'string', Rule::in(array_keys(Medicine::STORAGE_TYPES))],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:medicines,barcode'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pharmacy_id.required' => 'Please select a pharmacy.',
            'pharmacy_id.exists' => 'The selected pharmacy is invalid.',
            'name.required' => 'Medicine name is required.',
            'category_id.required' => 'Please select a medicine category.',
            'category_id.exists' => 'The selected category is invalid.',
            'form.required' => 'Please select a medicine form.',
            'form.in' => 'The selected form is invalid.',
            'prescription_type.required' => 'Please select a prescription type.',
            'prescription_type.in' => 'The selected prescription type is invalid.',
            'storage_type.required' => 'Please select a storage type.',
            'storage_type.in' => 'The selected storage type is invalid.',
            'barcode.unique' => 'This barcode is already in use.',
            'unit_price.required' => 'Unit price is required.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'unit_price.min' => 'Unit price cannot be negative.',
            'reorder_level.required' => 'Reorder level is required.',
            'reorder_level.integer' => 'Reorder level must be a whole number.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'pharmacy_id' => 'pharmacy',
            'category_id' => 'category',
            'unit_price' => 'unit price',
            'reorder_level' => 'reorder level',
            'is_active' => 'active status',
        ];
    }
}

class UpdateMedicineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $medicineId = $this->route('medicine')?->id ?? $this->route('id');
        
        return [
            'pharmacy_id' => ['required', 'exists:pharmacies,id'],
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'strength' => ['nullable', 'string', 'max:255'],
            'form' => ['required', 'string', Rule::in(array_keys(Medicine::FORMS))],
            'prescription_type' => ['required', 'string', Rule::in(array_keys(Medicine::PRESCRIPTION_TYPES))],
            'storage_type' => ['required', 'string', Rule::in(array_keys(Medicine::STORAGE_TYPES))],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('medicines', 'barcode')->ignore($medicineId)],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pharmacy_id.required' => 'Please select a pharmacy.',
            'pharmacy_id.exists' => 'The selected pharmacy is invalid.',
            'name.required' => 'Medicine name is required.',
            'category_id.required' => 'Please select a medicine category.',
            'category_id.exists' => 'The selected category is invalid.',
            'form.required' => 'Please select a medicine form.',
            'form.in' => 'The selected form is invalid.',
            'prescription_type.required' => 'Please select a prescription type.',
            'prescription_type.in' => 'The selected prescription type is invalid.',
            'storage_type.required' => 'Please select a storage type.',
            'storage_type.in' => 'The selected storage type is invalid.',
            'barcode.unique' => 'This barcode is already in use.',
            'unit_price.required' => 'Unit price is required.',
            'unit_price.numeric' => 'Unit price must be a valid number.',
            'unit_price.min' => 'Unit price cannot be negative.',
            'reorder_level.required' => 'Reorder level is required.',
            'reorder_level.integer' => 'Reorder level must be a whole number.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'pharmacy_id' => 'pharmacy',
            'category_id' => 'category',
            'unit_price' => 'unit price',
            'reorder_level' => 'reorder level',
            'is_active' => 'active status',
        ];
    }
}