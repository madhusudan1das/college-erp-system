@extends('layouts.app')

@section('title', 'My Timetable')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Timetable</h2>
</div>

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
                                            <div class="card border-left-info shadow-sm p-2 mb-2 w-100" style="max-width: 300px; border-left: 4px solid #36b9cc;">
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
