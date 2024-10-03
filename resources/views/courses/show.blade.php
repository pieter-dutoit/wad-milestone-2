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
                        <td>{{ $teacher['teacher']->name }}</h5>

                        <td>
                            @foreach ($teacher['workshops'] as $workshop)
                                {{ $workshop->location }}, {{ $workshop->day }},
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



    {{-- Assessments table --}}
    <div class='table-card'>
        <div class='row-between'>
            <h4>Peer Review Assessments</h4>
            @if ($isTeacher)
                <a class="btn btn-dark" href="{{ url("assessments/create?course=$course->id") }}">Create assessment</a>
            @endif
        </div>

        <p>
            @if ($isTeacher)
                Select a assessment to view student submissions, or create a new assessment.
            @else
                Complete each assignment before the displayed due date.
            @endif
        </p>

        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Due Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($course->assessments as $index => $assessment)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>
                            @if ($isTeacher)
                                <a href="{{ url('assessments', [$assessment->id]) }}">{{ $assessment->title }}</a>
                            @else
                                <a
                                    href="{{ url('submissions', [$assessment->submissions->where('student_id', Auth::user()->id)->first()->id]) }}/edit">{{ $assessment->title }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $assessment->due_date }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan='4'>There are no assessments for this course.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
