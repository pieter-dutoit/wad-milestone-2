@extends('layouts.master')

@section('title')
    {{ Auth::user()->role->role == 'teacher' ? 'Courses you teach' : 'Enrolments' }}
@endsection

@section('description')
    This page lists all courses that you
    {{ Auth::user()->role->role == 'teacher' ? 'teach' : 'are enrolled in' }}.
    Select a course to view its details.
@endsection

@section('content')
    <ul>
        @forelse ($enrolments as $enrolment)
            <li>
                <a href="{{ url('courses', [$enrolment->course->id]) }}">
                    {{ $enrolment->course->course_name }}
                    {{ $enrolment->course->course_code }}
                </a>

            </li>
        @empty
            <li>You are not enrolled in any courses.</li>
        @endforelse
    </ul>
@endsection
