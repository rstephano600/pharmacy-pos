@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Edit Pharmacy</h2>

    <form action="{{ route('superadmin.pharmacies.update', $pharmacy) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        @include('in.superadmin.pharmacies.form', ['pharmacy' => $pharmacy])

        <button type="submit" class="btn btn-warning">Update</button>
        <a href="{{ route('superadmin.pharmacies.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
