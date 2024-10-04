@extends('layouts.master')

@section('title')
    Course Details
@endsection

@section('description')
    This page lists all course details, including teaching staff and assessments.
@endsection

@section('content')
    <h2>{{ $course->course_name }}, {{ $course->course_code }}</h2>

    {{-- Teachers table --}}
    <div class='table-card'>
        <h4>Teaching Staff</h4>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Workshop(s)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $index => $teacher)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $teacher->name }}</h5>

                        <td>
                            @foreach ($teacher->enrolments->where('course_id', $course->id) as $enrolment)
                                {{ $enrolment->workshop->location }}, {{ $enrolment->workshop->day }}{{ '; ' }}
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">There are no teachers assigned to this course.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Enrolments --}}
    @if ($isTeacher)
        <div class='table-card'>
            <div class='row-between'>
                <h4>Student Enrolments</h4>
                <a class="btn btn-dark" href="{{ url("enrolments/create?course=$course->id") }}">Enrol a Student</a>
            </div>

            <p>There are <strong>{{ $course->users->where('role.role', 'student')->count() }} </strong>student(s) enrolled
                in this course.
            </p>

        </div>
    @endif



    {{-- Teachers: all assignments for this course --}}
    @if ($isTeacher)
        <div class='table-card'>
            <div class='row-between'>
                <h4>Peer Review Assessments</h4>
                <a class="btn btn-dark" href="{{ url("assessments/create?course=$course->id") }}">Create assessment</a>
            </div>

            <p>
                Create a new assessment, or select an assessment to view student submissions
            </p>

            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $index => $assessment)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>
                                <a href="{{ url('assessments', [$assessment->id]) }}">{{ $assessment->title }}</a>
                            </td>
                            <td>{{ $assessment->due_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan='3'>There are no assessments for this course.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif


    {{-- Students submissions for this course --}}
    @if (!$isTeacher)
        <div class='table-card'>
            <div class='row-between'>
                <h4>Peer Review Assessments</h4>
            </div>
            <p>
                Complete each assignment before the displayed due date.
            </p>

            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Group</th>
                        <th scope="col">Due Date</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                        <tr>
                            <td>
                                {{ $assessment->assessment->title }}
                            </td>
                            <td>
                                @if ($assessment->group_num)
                                    {{ $assessment->group_num }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>{{ $assessment->assessment->due_date }}</td>
                            <td>

                                @if ($assessment->date_submitted != null)
                                    <em>Submitted</em>
                                @else
                                @endif

                            </td>
                            <td>
                                @if ($assessment->date_submitted != null)
                                    <a href="{{ url('submissions', [$assessment->id]) }}">
                                        @if ($isTeacher)
                                            View & Mark
                                        @else
                                            View submission
                                        @endif
                                    </a>
                                @else
                                    <a href="{{ url('submissions', [$assessment->id]) }}/edit">
                                        @if ($isTeacher)
                                            View & Mark
                                        @else
                                            View assessment
                                        @endif
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan='4'>There are no assessments for this course.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

@endsection
