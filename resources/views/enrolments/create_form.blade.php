@extends('layouts.master')

@section('title')
    Create Enrolment
@endsection

@section('description')
    Select a student to enrol into the selected course.
@endsection

@section('content')
    <h2>{{ $course->course_name }}, {{ $course->course_code }}</h2>

    <div class='table-card'>
        <h4>Enrol a student</h4>
        <p>Select a student and workshop, then click "Enrol student".</p>
        <hr>

        <form action="{{ url('enrolments') }}" method="post">
            @csrf

            <div class='input-wrapper'>
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <ul>
                    @foreach ($errors->get('course_id') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class='input-wrapper'>
                <label for="workshop" class="form-label">Select workshop</label>
                <select name="workshop" id="workshop" class="form-select">
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
            </div>

            <div class='input-wrapper'>
                <label for="student" class="form-label">Select student</label>
                <select name="student" id="student" class="form-select">
                    @forelse ($students as $student)
                        <option value="{{ $student->id }}" {{ old('student') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}, {{ $student->s_number }}
                        </option>
                    @empty
                        <option value="">No students available.</option>
                    @endforelse
                </select>
                <ul>
                    @foreach ($errors->get('student') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <button type="submit" class="btn btn-dark">Enrol student</button>
        </form>
    </div>
@endsection
