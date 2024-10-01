@extends('layouts.master')

@section('title')
    Create new enrolment
@endsection

@section('description')
    Select a student to enrol into the selected course.
@endsection

@section('content')
    <h5>Course:</h5>
    <ul>
        <li>{{ $course->course_name }}</li>
        <li>{{ $course->course_code }}</li>
    </ul>


    <form action="{{ url('enrolments') }}" method="post">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">
        <ul>
            @foreach ($errors->get('course_id') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>

        <label for="workshop">Select workshop</label>
        <select name="workshop" id="workshop">
            @foreach ($workshops as $workshop)
                <option value="{{ $workshop->id }}" {{ old('workshop') == $workshop->id ? 'selected' : '' }}>
                    {{ $workshop->location }}, {{ $workshop->day }}
                </option>
            @endforeach
        </select>
        <ul>
            @foreach ($errors->get('workshop') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>


        <label for="student">Select student</label>
        <select name="student" id="student">
            @forelse ($students as $student)
                <option value="{{ $student->id }}" {{ old('student') == $student->id ? 'selected' : '' }}>
                    {{ $student->name }}, {{ $student->s_number }}
                </option>
            @empty
                <option value="">No students available.</option>
            @endforelse
        </select>

        @foreach ($errors->get('student') as $error)
            <li style="color: #f46000">
                <em>{{ $error }}</em>
            </li>
        @endforeach

        <button type="submit">Enrol student</button>
    </form>
@endsection
