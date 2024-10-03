@extends('layouts.master')

@section('title')
    {{ $isTeacher ? 'Your Courses' : 'Course Enrolments' }}
@endsection

@section('description')
    This page lists all courses that you
    {{ $isTeacher ? 'teach' : 'are enrolled in' }}.
    Select a course to view its details.
@endsection

@section('content')
    <ul class="list-horisontal">
        @forelse ($enrolments as $enrolment)
            <li>
                <div class="card m-3" style="width: 18rem;">
                    <img src="{{ asset('images/course.svg') }}" class="card-img-top"
                        alt="{{ $enrolment->course->course_code }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $enrolment->course->course_name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $enrolment->course->course_code }}</h6>
                        <a href="{{ url('courses', [$enrolment->course->id]) }}" class="btn btn-dark">View details</a>
                    </div>
                </div>
            </li>
        @empty
            <li>You are not enrolled in any courses.</li>
        @endforelse
    </ul>
@endsection
