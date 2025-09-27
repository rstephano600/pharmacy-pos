@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Supplier</h1>
    <form action="{{ route('suppliers.store') }}" method="POST">
        @include('in.pharmacy.suppliers._form')
        <button class="btn btn-success">Save</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
