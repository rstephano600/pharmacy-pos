@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Medicine Category</h2>

    <form action="{{ route('medicine-categories.update', $medicineCategory->id) }}" method="POST">
        @csrf
        @method('PUT')

        @if(auth()->user()->hasRole('super_admin'))
            <div class="mb-3">
                <label for="pharmacy_id" class="form-label">Pharmacy</label>
                <select name="pharmacy_id" class="form-select" required>
                    @foreach(\App\Models\Pharmacy::all() as $pharmacy)
                        <option value="{{ $pharmacy->id }}" {{ $medicineCategory->pharmacy_id == $pharmacy->id ? 'selected' : '' }}>
                            {{ $pharmacy->name }}
                        </option>
                    @endforeach
                </select>
                @error('pharmacy_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $medicineCategory->name) }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea name="description" class="form-control">{{ old('description', $medicineCategory->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('medicine-categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
