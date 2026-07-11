@extends('layouts.app')

@section('title', 'My Timetable')

@section('styles')
<style>
    .conflict-banner {
        background: linear-gradient(135deg, rgba(255,82,82,0.08), rgba(255,152,0,0.08));
        border-radius: 14px;
        border-left: 5px solid #ff5252;
        padding: 16px 20px;
    }
    .class-card-conflict {
        border-left: 4px solid #ff5252 !important;
        background: rgba(255, 82, 82, 0.04) !important;
        animation: conflictPulse 2s ease-in-out infinite;
    }
    @keyframes conflictPulse {
        0%, 100% { box-shadow: 0 2px 8px rgba(255, 82, 82, 0.1); }
        50% { box-shadow: 0 4px 16px rgba(255, 82, 82, 0.25); }
    }
    .conflict-detail-item {
        padding: 8px 14px;
        border-radius: 8px;
        border-left: 3px solid #ff5252;
        background: rgba(255, 82, 82, 0.03);
        margin-bottom: 6px;
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Timetable</h2>
</div>

{{-- Conflict Warning Banner --}}
@if(count($conflicts) > 0)
<div class="conflict-banner mb-4">
    <div class="d-flex align-items-start gap-3">
        <i class="fas fa-exclamation-triangle text-danger fa-lg mt-1"></i>
        <div>
            <h6 class="fw-bold text-danger mb-1">⚠️ Schedule Conflict Detected</h6>
            <p class="text-muted small mb-2">You have <strong>{{ count($conflicts) }}</strong> overlapping class{{ count($conflicts) > 1 ? 'es' : '' }} in your timetable. Please contact your department for clarification.</p>
            <div style="max-height: 150px; overflow-y: auto;">
                @foreach($conflicts as $conflict)
                <div class="conflict-detail-item">
                    <i class="fas fa-times-circle text-danger me-1"></i>
                    <strong>{{ $conflict['day'] }}:</strong>
                    <span class="badge bg-dark">{{ $conflict['slot_a']->subject->name ?? 'N/A' }}</span>
                    ({{ date('h:i A', strtotime($conflict['slot_a']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_a']->end_time)) }})
                    <span class="text-muted mx-1">conflicts with</span>
                    <span class="badge bg-dark">{{ $conflict['slot_b']->subject->name ?? 'N/A' }}</span>
                    ({{ date('h:i A', strtotime($conflict['slot_b']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_b']->end_time)) }})
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Weekly Schedule</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="15%">Day</th>
                        <th>Classes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($days as $day)
                        @php
                            $day_classes = $timetable->filter(function($t) use ($day) { return $t->day_of_week == $day; });
                        @endphp
                        <tr>
                            <td class="align-middle fw-bold">{{ $day }}</td>
                            <td class="text-start">
                                @if ($day_classes->isEmpty())
                                    <span class="text-muted fst-italic">No classes scheduled</span>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($day_classes as $cls)
                                            @php $isConflict = $conflictIds->contains($cls->id); @endphp
                                            <div class="card border-left-info shadow-sm p-2 mb-2 w-100 {{ $isConflict ? 'class-card-conflict' : '' }}" style="max-width: 300px; {{ !$isConflict ? 'border-left: 4px solid #36b9cc;' : '' }}">
                                                @if($isConflict)
                                                    <div class="mb-1">
                                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Overlap</span>
                                                    </div>
                                                @endif
                                                <strong class="text-info">{{ $cls->subject->name }}</strong>
                                                <div class="text-muted small">
                                                    <i class="far fa-clock"></i> {{ date('h:i A', strtotime($cls->start_time)) }} - {{ date('h:i A', strtotime($cls->end_time)) }}
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-chalkboard-teacher"></i> {{ $cls->faculty->first_name }} {{ $cls->faculty->last_name }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
