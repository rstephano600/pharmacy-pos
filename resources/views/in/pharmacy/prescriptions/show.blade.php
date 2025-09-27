@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Prescription #{{ $prescription->id }}</h1>

    <div class="card mb-3">
        <div class="card-header">General Information</div>
        <div class="card-body">
            <p><strong>Customer:</strong> {{ $prescription->customer->name ?? '-' }}</p>
            <p><strong>Doctor:</strong> {{ $prescription->doctor_name }}</p>
            <p><strong>Diagnosis:</strong> {{ $prescription->diagnosis }}</p>
            <p><strong>Notes:</strong> {{ $prescription->notes ?? '-' }}</p>
            <p><strong>Status:</strong>
                <span class="badge bg-{{ $prescription->status == 'pending' ? 'warning' : 'success' }}">
                    {{ ucfirst($prescription->status) }}
                </span>
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Prescribed Medicines</div>
        <div class="card-body">
            @if($prescription->items->count())
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescription->items as $item)
                            <tr>
                                <td>{{ $item->medicine->name ?? '-' }}</td>
                                <td>{{ $item->dosage }}</td>
                                <td>{{ $item->frequency }}</td>
                                <td>{{ $item->duration }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status == 'pending' ? 'warning' : 'success' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No medicines assigned yet.</p>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">⬅ Back</a>
        <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-primary">✏ Edit</a>
    </div>
</div>
@endsection
