@extends('layouts.app')

@section('title', 'Medicines Categories')
@section('header', 'Medicines categories')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('medicine-categories.create') }}" class="btn btn-primary">+ Add Category</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Description</th>
                @if(auth()->user()->hasRole('super_admin'))
                    <th>Pharmacy</th>
                @endif
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $index => $category)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->description }}</td>
                    @if(auth()->user()->hasRole('super_admin'))
                        <td>{{ $category->pharmacy->name ?? 'N/A' }}</td>
                    @endif
                    <td>{{ $category->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('medicine-categories.edit', $category->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('medicine-categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
</div>
@endsection
