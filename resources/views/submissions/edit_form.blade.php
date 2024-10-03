@extends('layouts.master')

@section('title')
    @if ($isTeacher)
        View Submission
    @else
        Write Reviews
    @endif
@endsection

@section('description')
    @if ($isTeacher)
        See all reviews submitted and received by the selected student.
    @else
        Complete all peer reviews below and click the submit button.
    @endif
@endsection

@section('content')
    <h2>
        {{ $submission->assessment->title }}
    </h2>

    @if ($isTeacher)
        <h3>
            Student: {{ $submission->student->name }}, {{ $submission->student->s_number }}
        </h3>
    @endif

    {{-- Display submission details --}}
    <ul class='list-group'>
        @if ($submission->date_submitted == null)
            @if ($isLate)
                <li class='list-group-item'>No submission!</li>
            @else
                <li class='list-group-item'>Not yet submitted.</li>
            @endif
        @else
            <li class='list-group-item'>
                Submitted on: <strong>{{ $submission->date_submitted }}</strong>
            </li>

            @if ($submission->score == null)
                <li class='list-group-item'>Score: <strong>Not yet marked.</strong></li>
            @else
                <li class='list-group-item'>Score:
                    <strong>{{ $submission->score }}/{{ $submission->assessment->max_score }}</strong>
                </li>
            @endif
        @endif
    </ul>


    {{-- Allow teacher to mark student, if not yet marked --}}
    @if ($isTeacher && $submission->score == null)
        <div class="table-card">
            <h4>Score this student</h4>

            <hr>

            @if ($isLate || $submission->date_submitted != null)
                <form action="{{ url('submissions', [$submission->id]) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="input-wrapper">
                        <label class="form-label" for="score">Score out of
                            {{ $submission->assessment->max_score }}:</label>
                        <input class="form-control" type="number" name="score" id="score" value={{ old('score') }}>
                        @foreach ($errors->get('score') as $error)
                            <li style="color: #f46000">
                                <em>{{ $error }}</em>
                            </li>
                        @endforeach
                    </div>

                    <button class="btn btn-dark" type="submit">Submit</button>
                </form>
            @else
                <p>Marking will be available after the student submits, or after the due date.</p>
            @endif
        </div>
    @endif



    <div class='table-card'>
        <h4>Assessment details</h4>
        <hr>

        <table class='table'>
            <tbody>
                <tr>
                    <th scope="col">Instruction</th>
                    <td>{{ $submission->assessment->instruction }}</td>
                <tr>
                    <th scope="col">Type</th>
                    <td>{{ $submission->assessment->type->type }}</td>
                </tr>
                <tr>
                    <th scope="col">Highest possible score</th>
                    <td>{{ $submission->assessment->max_score }}</td>
                </tr>
                <tr>
                    <th scope="col">Due date</th>
                    <td>{{ $submission->assessment->due_date }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($isTeacher)
        <div class="table-card">
            <h4>Reviews Submitted</h4>
            <hr>
            <ul>
                @forelse ($submission->reviews->where('complete', true) as $index => $review)
                    <li>{{ $review->text }}</li>
                @empty
                    <li>This student has not submitted any reviews.</li>
                @endforelse
            </ul>

        </div>
        {{-- Submitted reviews only, for teacher to see --}}
    @endif

    @if (!$isTeacher)
        <div class="table-card">
            <h4>Reviews to complete</h4>

            <p>You need to complete all <strong>{{ $submission->reviews->count() }}</strong> peer reviews below.</p>

            <hr>

            {{-- Form for student to complete the reviews --}}
            <form action="{{ url('submissions', [$submission->id]) }}" method="post">
                @csrf
                @method('PUT')

                @foreach ($submission->reviews as $index => $review)
                    <div class="review-group">
                        <h5>Review #{{ $index + 1 }}</h5>
                        <input type="hidden" name="review_{{ $index + 1 }}_id" value="{{ $review->id }}">
                        <div>
                            {{-- Checkbox for is an assigned student is absent --}}
                            <div class="review-parent absent-student-{{ $submission->assessment->type->type }}">
                                <input class="form-check-input me-1" type="checkbox" id="absent_{{ $index + 1 }}"
                                    name="absent_{{ $index + 1 }}"
                                    {{ old('absent_' . $index + 1) == 'on' ? 'checked' : '' }} />

                                <label for="absent_{{ $index + 1 }}">
                                    The assigned student is unavailable/not present
                                </label>

                                {{-- Error message --}}
                                @foreach ($errors->get('absent_' . ($index + 1)) as $error)
                                    <li style="color: #f46000">
                                        <em>{{ $error }}</em>
                                    </li>
                                @endforeach

                                {{-- Checkbox for id there is no student to review --}}
                                <div class="no-student no-student-{{ $submission->assessment->type->type }}">

                                    <span class="choose-other-student">Please select another student to review.</span>

                                    <input class="form-check-input me-1" type="checkbox"
                                        id="unavailable_{{ $index + 1 }}" name="unavailable_{{ $index + 1 }}"
                                        {{ old('unavailable_' . $index + 1) == 'on' ? 'checked' : '' }} />

                                    <label class="list-group-item" for="unavailable_{{ $index + 1 }}">
                                        No student available to review
                                    </label>

                                    {{-- Error message --}}
                                    @foreach ($errors->get('unavailable_' . ($index + 1)) as $error)
                                        <li style="color: #f46000">
                                            <em>{{ $error }}</em>
                                        </li>
                                    @endforeach

                                    {{-- Select input for students --}}
                                    <div class='review-inputs'>
                                        <div class="input-wrapper">
                                            <label class="form-label student-select-label" for="student">
                                                {{ $submission->assessment->type->type == 'student_select' ? 'Select' : 'Assigned' }}
                                                student
                                            </label>

                                            <select class="form-select" name="review_{{ $index + 1 }}_student"
                                                id="student">
                                                <option value="-1" selected>Select a student to review</option>
                                                @forelse ($peers as $peer)
                                                    <option value="{{ $peer->id }}"
                                                        {{ old('review_' . ($index + 1) . '_student') == $peer->id || (isset($review->reviewee) && $review->reviewee->id == $peer->id) ? 'selected' : '' }}>
                                                        {{ $peer->name }}
                                                    </option>
                                                @empty
                                                    <option value="">No students available.</option>
                                                @endforelse
                                            </select>
                                            @foreach ($errors->get('review_' . ($index + 1) . '_student') as $error)
                                                <li style="color: #f46000">
                                                    <em>{{ $error }}</em>
                                                </li>
                                            @endforeach
                                        </div>

                                        {{-- Text area input for review --}}
                                        <div class="input-wrapper">
                                            <label class="form-label" for="review_{{ $index + 1 }}_text">Peer Review
                                                {{ $index + 1 }}</em></label>
                                            <textarea class="form-control" name="review_{{ $index + 1 }}_text" id="review_{{ $index + 1 }}_text"
                                                placeholder="Write a review...">{{ old('review_' . ($index + 1) . '_text') }}</textarea>
                                            @foreach ($errors->get('review_' . ($index + 1) . '_text') as $error)
                                                <li style="color: #f46000">
                                                    <em>{{ $error }}</em>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{-- Submit button --}}
                <button class="btn btn-dark" type="submit">Submit peer reviews</button>
            </form>

            <br>
            <hr>
        </div>
    @endif

    {{-- Show reviews received by the student --}}
    <div class="table-card">
        <h4>Reviews received</h4>

        <hr>

        @if (count($reviewsReceived) > 0)
            @if ($isTeacher)
                {{-- Teacher can see all reviews --}}
                @forelse ($reviewsReceived as $review)
                    <li>{{ $review->text }}</li>
                @empty
                    <li>This student has not submitted any reviews.</li>
                @endforelse
            @else
                {{-- Student needs to submit own reviews before they can the what they received --}}
                <p>
                    You have received
                    <strong>{{ count($reviewsReceived) }}</strong>
                    review(s)! Reveal them by submitting your own reviews.
                </p>
            @endif
        @else
            <p>No reviews have been received.</p>
        @endif
    </div>


@endsection
