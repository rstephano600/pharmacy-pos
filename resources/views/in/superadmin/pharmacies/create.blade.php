@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Add Pharmacy</h2>

    <form action="{{ route('superadmin.pharmacies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('in.superadmin.pharmacies.form')

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('superadmin.pharmacies.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
