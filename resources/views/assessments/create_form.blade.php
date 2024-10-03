@extends('layouts.master')

@section('title')
    Create Peer Review Assessment
@endsection

@section('description')
    Create a new peer review assessment for all students enrolled in this course.
@endsection

@section('content')
    <h2>{{ $course->course_name }}, {{ $course->course_code }}</h2>

    <div class='table-card'>
        <h4>Create a new assessment</h4>
        <p>All students will be required to complete this assessment.</p>
        <hr>

        <form action="{{ url('assessments') }}" method="post">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <div class="input-wrapper">
                <label class="form-label" for="title">Title</label>
                <input class="form-control" type="text" name="title" id="title" value="{{ old('title') }}">
                <ul>
                    @foreach ($errors->get('title') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="input-wrapper">
                <label class="form-label" for="instruction">Instruction</label>
                <textarea rows="7" cols="30" class="form-control" name="instruction" id="instruction">{{ old('instruction') }}</textarea>
                <ul>
                    @foreach ($errors->get('instruction') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="input-wrapper">
                <label class="form-label" for="due_date">Due Date</label>
                <input class="form-control" type="datetime-local" name="due_date" id="due_date"
                    value="{{ old('due_date') }}">
                <ul>
                    @foreach ($errors->get('due_date') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="input-wrapper">
                <label class="form-label" for="num_reviews">Number of Reviews (per learner)</label>
                <input class="form-control" type="number" name="num_reviews" id="num_reviews"
                    value="{{ old('num_reviews') }}">
                <ul>
                    @foreach ($errors->get('num_reviews') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="input-wrapper">
                <label class="form-label" for="max_score">Max Score <em>(Between 1 and 100)</em></label>
                <input class="form-control" type="number" name="max_score" id="max_score" value="{{ old('max_score') }}">
                <ul>
                    @foreach ($errors->get('max_score') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </div>

            <fieldset class="input-wrapper">
                <label class="form-label">Select the Review Type:</label>

                @foreach ($reviewTypes as $index => $reviewType)
                    <div>
                        <input class="form-check-input" type="radio" name="type_id" id="{{ $reviewType->id }}"
                            value="{{ $reviewType->id }}"
                            {{ (old('type_id') == $reviewType->id ? 'checked' : $index == 0) ? 'checked' : '' }}>

                        <label class="form-check-label"
                            for="{{ $reviewType->id }}">{{ ['student_select' => 'Student select', 'teacher_assign' => 'Teacher assign'][$reviewType->type] }}</label>
                    </div>

                    <p>
                        {{ [
                            'teacher_assign' =>
                                'Students will be automatically grouped and assigned reviewees. A group contains a random selection of students from the same workshop. Groups size is based on the "Number of reviews" value, and may exceed that number to ensure all students are assiged to a group.',
                            'student_select' => 'Students will be required to select reviewees from their workshop.',
                        ][$reviewType->type] }}
                    </p>
                @endforeach

                <ul>
                    @foreach ($errors->get('reviewType') as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach
                </ul>
            </fieldset>

            <button class="btn btn-dark" type="submit">Create assessment</button>
        </form>
    </div>
@endsection
