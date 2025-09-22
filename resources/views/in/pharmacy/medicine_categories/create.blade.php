@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Medicine Category</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul>
                    @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form action="{{ route('medicine-categories.store') }}" method="POST">
        @csrf

        @if(auth()->user()->hasRole('super_admin'))
            <div class="mb-3">
                <label for="pharmacy_id" class="form-label">Pharmacy</label>
                <select name="pharmacy_id" class="form-select" required>
                    <option value="">-- Select Pharmacy --</option>
                    @foreach(\App\Models\Pharmacy::all() as $pharmacy)
                        <option value="{{ $pharmacy->id }}" {{ old('pharmacy_id') == $pharmacy->id ? 'selected' : '' }}>
                            {{ $pharmacy->name }}
                        </option>
                    @endforeach
                </select>
                @error('pharmacy_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="category_name" class="form-control" value="{{ old('category_name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('medicine-categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
