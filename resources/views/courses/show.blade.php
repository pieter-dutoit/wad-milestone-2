@extends('layouts.master')

@section('title')
    Course Details
@endsection

@section('description')
    This page lists all course details including the teaching staff, and assessments.
@endsection

@section('content')

    @if (Auth::user()->role->role == 'teacher')
        <a href="{{ url("enrolments/create?course=$course->id") }}">Enrol a Student</a>
        <a href="{{ url("assessments/create?course=$course->id") }}">Create Peer Review Assessment</a>
    @endif

    <ul>
        <li>{{ $course->course_name }}</li>
        <li>{{ $course->course_code }}</li>
    </ul>

    <h4>Teachers:</h4>

    <ul>
        @forelse ($teachers as $teacher)
            <li>
                <h5>{{ $teacher['teacher']->name }}</h5>
                <ul>
                    @foreach ($teacher['workshops'] as $workshop)
                        <li>
                            {{ $workshop->location }}, {{ $workshop->day }}
                        </li>
                    @endforeach
                </ul>
            </li>
        @empty
            <li>There are no teachers members assigned to this course.</li>
        @endforelse
    </ul>


    <h4>Peer Review Assessments:</h4>

    <ul>
        @forelse ($course->assessments as $assessment)
            <li>
                <a href="{{ url('assessments', [$assessment->id]) }}">{{ $assessment->title }}</a>
                <p>Due date: {{ $assessment->due_date }}</p>
            </li>
        @empty
            <li>There are no assessments for this course.</li>
        @endforelse
    </ul>
@endsection
