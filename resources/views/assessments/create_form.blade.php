@extends('layouts.master')

@section('title')
    Create Peer Review Assessment
@endsection

@section('description')
    Create a new peer review assessment for all students enrolled in this course.
@endsection

@section('content')
    <h5>Course:</h5>
    <ul>
        <li>{{ $course->course_name }}</li>
        <li>{{ $course->course_code }}</li>
    </ul>


    <form action="{{ url('assessments') }}" method="post">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}">
        <ul>
            @foreach ($errors->get('title') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>

        <label for="instruction">Instruction</label>
        <input type="textarea" name="instruction" id="instruction" value="{{ old('instruction') }}">
        <ul>
            @foreach ($errors->get('instruction') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>

        <label for="due_date">Due date</label>
        <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}">
        <ul>
            @foreach ($errors->get('due_date') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>

        <label for="num_reviews">Number of Reviews (per learner)</label>
        <input type="number" name="num_reviews" id="num_reviews" value="{{ old('num_reviews') }}">
        <ul>
            @foreach ($errors->get('num_reviews') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>

        <label for="max_score">Max score <em>(Between 1 and 100)</em></label>
        <input type="number" name="max_score" id="max_score" value="{{ old('max_score') }}">
        <ul>
            @foreach ($errors->get('max_score') as $error)
                <li style="color: #f46000">
                    <em>{{ $error }}</em>
                </li>
            @endforeach
        </ul>

        <fieldset>
            <legend>Select the Review Type:</legend>

            @foreach ($reviewTypes as $index => $reviewType)
                <div>
                    <input type="radio" name="type_id" id="{{ $reviewType->id }}" value="{{ $reviewType->id }}"
                        {{ (old('reviewType') == $reviewType->id ? 'checked' : $index == 0) ? 'checked' : '' }}>
                    <label for="{{ $reviewType->id }}">{{ $reviewType->type }}</label>
                </div>
            @endforeach

            <ul>
                @foreach ($errors->get('reviewType') as $error)
                    <li style="color: #f46000">
                        <em>{{ $error }}</em>
                    </li>
                @endforeach
            </ul>
        </fieldset>

        <button type="submit">Create assessment</button>
    </form>
@endsection
