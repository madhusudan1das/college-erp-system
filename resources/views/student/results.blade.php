@extends('layouts.app')

@section('title', 'My Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Results</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Academic Performance</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Exam Type</th>
                        <th>Marks Obtained</th>
                        <th>Max Marks</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $row)
                        @php
                            $percentage = ($row->max_marks > 0) ? ($row->marks_obtained / $row->max_marks) * 100 : 0; 
                            
                            $badge_class = 'bg-info text-dark';
                            $display_type = $row->exam_type;
                            if ($row->exam_type == 'online_exam') {
                                $badge_class = 'bg-primary text-white';
                                $display_type = 'Online Exam';
                            }
                        @endphp
                        <tr>
                            <td>{{ $row->subject->code }}</td>
                            <td>{{ $row->subject->name }}</td>
                            <td><span class="badge {{ $badge_class }} text-uppercase">{{ $display_type }}</span></td>
                            <td><strong>{{ $row->marks_obtained }}</strong></td>
                            <td>{{ $row->max_marks }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $percentage >= 40 ? 'bg-success' : 'bg-danger' }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%;" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                         {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
