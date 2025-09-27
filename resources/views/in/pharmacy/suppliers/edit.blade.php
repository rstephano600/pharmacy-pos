@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Supplier</h1>
    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf @method('PUT')
        @include('in.pharmacy.suppliers._form')
        <button class="btn btn-success">Update</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
