@extends('layouts.master')

@section('title')
    @if ($isTeacher)
        View Submission
    @else
        {{ $submission->assessment->title }}
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
        @if ($isTeacher)
            Student: {{ $submission->student->name }}, {{ $submission->student->s_number }}
        @else
            Instructions:
        @endif
    </h2>

    {{-- Display submission details --}}
    <ul>
        <li>Highest possible mark: <strong>{{ $submission->assessment->max_score }}</strong></li>
        <li> Due date: {{ $submission->assessment->due_date }}</li>
        @if ($submission->date_submitted == null)
            @if ($isLate)
                <li>No submission!</li>
            @else
                <li>Not yet submitted.</li>
            @endif
        @else
            <li>
                Submitted on: {{ $submission->date_submitted }}
            </li>

            @if ($submission->score == null)
                <li>Score:</em>: <strong>Not yet marked.</strong></li>
            @else
                <li>Score: {{ $submission->score }}/{{ $submission->assessment->max_score }}</li>
            @endif
        @endif
    </ul>

    <hr>

    {{-- Allow teacher to mark student, if not yet marked --}}
    @if ($submission->score == null)
        <h5>Score this student</h5>

        @if ($isTeacher && ($isLate || $submission->date_submitted != null))
            <form action="{{ url('submissions', [$submission->id]) }}" method="post">
                @csrf
                @method('PUT')
                <label for="score">Score out of {{ $submission->assessment->max_score }}:</label>
                <input type="number" name="score" id="score" value={{ old('score') }}>
                <button type="submit">Submit</button>


                @foreach ($errors->get('score') as $error)
                    <li style="color: #f46000">
                        <em>{{ $error }}</em>
                    </li>
                @endforeach
            </form>
        @else
            <p>Marking will be available after the student submits.</p>
        @endif
    @endif

    <hr>





    @if ($isTeacher)
        {{-- Submitted reviews only, for teacher to see --}}
        <h4>Reviews Submitted:</h4>
        <ul>
            @forelse ($submission->reviews->where('complete', true) as $index => $review)
                <li>{{ $review->text }}</li>
            @empty
                <li>This student has not submitted any reviews.</li>
            @endforelse
        </ul>
    @endif

    @if (!$isTeacher)
        <p>{{ $submission->assessment->instruction }}</p>
        <hr>

        <h3>Reviews to complete</h3>

        <p>You need to complete all <strong>{{ $submission->reviews->count() }}</strong> peer reviews below.</p>

        {{-- Form for student to complete the reviews --}}
        <form action="{{ url('submissions', [$submission->id]) }}" method="post">
            @csrf
            @method('PUT')

            @foreach ($submission->reviews as $index => $review)
                <hr>
                <input type="hidden" name="review_{{ $index + 1 }}_id" value="{{ $review->id }}">
                <div>
                    {{-- Checkbox for is an assigned student is absent --}}
                    <div class="absent-student-{{ $submission->assessment->type->type }}">
                        <input type="checkbox" id="absent_{{ $index + 1 }}" name="absent_{{ $index + 1 }}"
                            {{ old('absent_' . $index + 1) == 'on' ? 'checked' : '' }} />
                        <label for="absent_{{ $index + 1 }}">My assigned student is unavailable/not present</label>
                        @foreach ($errors->get('absent_' . ($index + 1)) as $error)
                            <li style="color: #f46000">
                                <em>{{ $error }}</em>
                            </li>
                        @endforeach


                        {{-- Checkbox for id there is no student to review --}}
                        <div class="no-student-{{ $submission->assessment->type->type }}">
                            <span class="choose-other-student">Please select another student to review</span>
                            <input type="checkbox" id="unavailable_{{ $index + 1 }}"
                                name="unavailable_{{ $index + 1 }}"
                                {{ old('unavailable_' . $index + 1) == 'on' ? 'checked' : '' }} />
                            <label for="unavailable_{{ $index + 1 }}">No student available to review</label>

                            @foreach ($errors->get('unavailable_' . ($index + 1)) as $error)
                                <li style="color: #f46000">
                                    <em>{{ $error }}</em>
                                </li>
                            @endforeach

                            {{-- Select input for students --}}
                            <div class='review-inputs'>
                                <label for="student">Select student</label>
                                <select name="review_{{ $index + 1 }}_student" id="student">
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

                                {{-- Text area input for review --}}
                                <label for="review_{{ $index + 1 }}_text">Peer Review {{ $index + 1 }}</em></label>
                                <textarea name="review_{{ $index + 1 }}_text" id="review_{{ $index + 1 }}_text" placeholder="Write a review...">{{ old('review_' . ($index + 1) . '_text') }}</textarea>
                                @foreach ($errors->get('review_' . ($index + 1) . '_text') as $error)
                                    <li style="color: #f46000">
                                        <em>{{ $error }}</em>
                                    </li>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
            {{-- Submit button --}}
            <button type="submit">Submit peer reviews</button>
        </form>

        <br>
        <hr>
    @endif

    {{-- Show reviews received by the student --}}
    <h4>Reviews received</h4>
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
            <p>You have received
                <strong>{{ count($reviewsReceived) }}</strong>
                reviews! Reveal them by submitting your own reviews.
            </p>
        @endif
    @else
        <p>No reviews have been received.</p>
    @endif
@endsection
