@extends('layouts.app')

@section('title', 'My Timetable')

@section('styles')
<style>
    .premium-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 150, 136, 0.15);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    .timetable-day-header {
        font-weight: 700;
        color: #00796b;
        background-color: rgba(0, 150, 136, 0.05) !important;
        vertical-align: middle;
        font-size: 1.1rem;
        border-right: 2px solid rgba(0, 150, 136, 0.1) !important;
    }
    .class-item-card {
        background: #ffffff;
        border-left: 5px solid #009688;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        padding: 15px;
        margin-bottom: 12px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        width: 100%;
        max-width: 320px;
    }
    .class-item-card:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 150, 136, 0.15);
        border-left-color: #00796b;
    }
    .class-item-card.conflict-card {
        border-left: 5px solid #ff5252 !important;
        background: rgba(255, 82, 82, 0.03);
        animation: conflictGlow 2s ease-in-out infinite;
    }
    .class-item-card.conflict-card:hover {
        box-shadow: 0 8px 25px rgba(255, 82, 82, 0.2);
    }
    @keyframes conflictGlow {
        0%, 100% { box-shadow: 0 4px 15px rgba(255, 82, 82, 0.08); }
        50% { box-shadow: 0 6px 20px rgba(255, 82, 82, 0.2); }
    }
    .text-teal {
        color: #009688 !important;
    }
    .badge-course {
        background-color: rgba(0, 150, 136, 0.1);
        color: #00796b;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid rgba(0, 150, 136, 0.2);
    }
    .badge-semester {
        background-color: rgba(255, 152, 0, 0.1);
        color: #fb8c00;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid rgba(255, 152, 0, 0.2);
    }
    .icon-wrapper {
        min-width: 20px;
        display: inline-block;
        text-align: center;
        margin-right: 5px;
    }
    .conflict-banner {
        background: linear-gradient(135deg, rgba(255,82,82,0.08), rgba(255,152,0,0.08));
        border-radius: 14px;
        border-left: 5px solid #ff5252;
        padding: 16px 20px;
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
    <div>
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-alt text-teal me-2"></i>My Timetable</h2>
        <p class="text-muted mb-0">View your weekly teaching schedule, courses, and assigned rooms.</p>
    </div>
</div>

{{-- Conflict Warning Banner --}}
@if(count($conflicts) > 0)
<div class="conflict-banner mb-4">
    <div class="d-flex align-items-start gap-3">
        <i class="fas fa-exclamation-triangle text-danger fa-lg mt-1"></i>
        <div>
            <h6 class="fw-bold text-danger mb-1">⚠️ Schedule Conflict Detected — You Are Assigned to Multiple Classes at the Same Time</h6>
            <p class="text-muted small mb-2"><strong>{{ count($conflicts) }}</strong> overlapping assignment{{ count($conflicts) > 1 ? 's' : '' }} found. Please contact the admin to resolve.</p>
            <div style="max-height: 150px; overflow-y: auto;">
                @foreach($conflicts as $conflict)
                <div class="conflict-detail-item">
                    <i class="fas fa-times-circle text-danger me-1"></i>
                    <strong>{{ $conflict['day'] }}:</strong>
                    <span class="badge bg-dark">{{ $conflict['slot_a']->subject->name ?? 'N/A' }}</span>
                    ({{ date('h:i A', strtotime($conflict['slot_a']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_a']->end_time)) }})
                    @if($conflict['slot_a']->course)
                        <small class="text-muted">{{ $conflict['slot_a']->course->name }} Sem {{ $conflict['slot_a']->semester }}</small>
                    @endif
                    <span class="text-muted mx-1">↔</span>
                    <span class="badge bg-dark">{{ $conflict['slot_b']->subject->name ?? 'N/A' }}</span>
                    ({{ date('h:i A', strtotime($conflict['slot_b']->start_time)) }}-{{ date('h:i A', strtotime($conflict['slot_b']->end_time)) }})
                    @if($conflict['slot_b']->course)
                        <small class="text-muted">{{ $conflict['slot_b']->course->name }} Sem {{ $conflict['slot_b']->semester }}</small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="card premium-card border-0 mb-4">
    <div class="card-header py-3 bg-transparent border-bottom">
        <h5 class="m-0 fw-bold text-teal"><i class="fas fa-chalkboard-teacher me-1"></i> Weekly Teaching Schedule</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center" style="border-collapse: separate; border-spacing: 0; border-radius: 12px; overflow: hidden; border: 1px solid #dee2e6;">
                <thead>
                    <tr style="background-color: #f1f5f9;">
                        <th width="15%" class="py-3 fw-bold text-teal" style="font-size: 0.95rem;">Day</th>
                        <th class="py-3 fw-bold text-teal text-start ps-4" style="font-size: 0.95rem;">Assigned Classes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($days as $day)
                        @php
                            $day_classes = $timetable->filter(function($t) use ($day) { return $t->day_of_week == $day; });
                        @endphp
                        <tr>
                            <td class="timetable-day-header">{{ $day }}</td>
                            <td class="text-start ps-4 py-3 bg-light-subtle">
                                @if ($day_classes->isEmpty())
                                    <span class="text-muted fst-italic fs-7"><i class="fas fa-calendar-minus me-1"></i>No classes scheduled for today</span>
                                @else
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach ($day_classes as $cls)
                                            @php $isConflict = $conflictIds->contains($cls->id); @endphp
                                            <div class="class-item-card {{ $isConflict ? 'conflict-card' : '' }}">
                                                @if($isConflict)
                                                    <div class="mb-2">
                                                        <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Scheduling Overlap</span>
                                                    </div>
                                                @endif
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge-course">{{ $cls->course->name ?? 'N/A' }}</span>
                                                    <span class="badge-semester">Sem {{ $cls->semester }}</span>
                                                </div>
                                                <h6 class="fw-bold mb-2 text-dark">{{ $cls->subject->name }}</h6>
                                                <div class="text-muted small mb-1">
                                                    <span class="icon-wrapper"><i class="far fa-clock text-teal"></i></span> 
                                                    {{ date('h:i A', strtotime($cls->start_time)) }} - {{ date('h:i A', strtotime($cls->end_time)) }}
                                                </div>
                                                <div class="text-muted small mb-1">
                                                    <span class="icon-wrapper"><i class="fas fa-barcode text-teal"></i></span> 
                                                    Code: {{ $cls->subject->code }}
                                                </div>
                                                @if(!empty($cls->room))
                                                    <div class="text-muted small">
                                                        <span class="icon-wrapper"><i class="fas fa-map-marker-alt text-teal"></i></span> 
                                                        Room: <b>{{ $cls->room }}</b>
                                                    </div>
                                                @else
                                                    <div class="text-muted small">
                                                        <span class="icon-wrapper"><i class="fas fa-map-marker-alt text-muted"></i></span> 
                                                        Room: <i>Unassigned</i>
                                                    </div>
                                                @endif
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
