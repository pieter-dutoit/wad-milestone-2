@extends('layouts.master')

@section('title')
    @if ($isTeacher)
        Student Submission
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

    <h3>
        {{ $submission->assessment->course->course_name }}, {{ $submission->assessment->course->course_code }}
    </h3>

    @if ($isTeacher)
        <h4>
            Student: {{ $submission->student->name }}, {{ $submission->student->s_number }}
        </h4>
    @endif

    @isset($submission->group_num)
        <h4>
            Group {{ $submission->group_num }}
        </h4>
    @endisset


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
                <strong>Completed</strong>
            </li>
            <li class='list-group-item'>
                Submitted on: <strong>{{ $submission->date_submitted }}</strong>
            </li>

            @if ($submission->score == null)
                <li class='list-group-item'><strong>Not yet marked</strong></li>
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


    {{-- Assessment details card --}}
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
                    <td>{{ ['teacher_assign' => 'Teacher assign', 'student_select' => 'Student select'][$submission->assessment->type->type] }}
                    </td>
                </tr>
                <tr>
                    <th scope="col">Highest possible score</th>
                    <td>{{ $submission->assessment->max_score }}</td>
                </tr>
                <tr>
                    <th scope="col">Minimum reviews required</th>
                    <td>{{ $submission->assessment->num_reviews }}</td>
                </tr>
                <tr>
                    <th scope="col">Due date</th>
                    <td>{{ $submission->assessment->due_date }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if (!$isTeacher)
        <div class="table-card">
            <h4>Reviews to complete</h4>
            <p>You need to complete all <strong>{{ $submission->reviews->count() }}</strong> peer reviews below.</p>
            <hr>
            {{-- Form for student to complete the reviews --}}
            <form action="{{ url('submissions', [$submission->id]) }}" method="post">
                @csrf
                @method('put')

                @foreach ($submission->reviews as $index => $review)
                    <div class="review-group">
                        <h5>Review #{{ $index + 1 }}</h5>
                        <input type="hidden" name="review_{{ $index + 1 }}_id" value="{{ $review->id }}">
                        <div>
                            {{-- Checkbox for if there is no student to review --}}
                            <div class="no-student">
                                <input class="form-check-input me-1 student_unavailable_check" type="checkbox"
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
                @endforeach
                {{-- Submit button --}}
                <button class="btn btn-dark" type="submit">Submit peer reviews</button>
            </form>

            <br>
            <hr>
        </div>
    @endif

    {{-- Summary of reviews, for teacher only --}}
    @if ($isTeacher)
        <div class="table-card">
            <h4>Reviews Submitted</h4>
            <hr>
            <ul class="review-summary">
                @forelse ($submission->reviews->where('complete', true) as $index => $review)
                    <li>
                        <h5>Review #{{ $index + 1 }}</h5>
                    </li>
                    <li><strong>Reviewee name:</strong> {{ $review->reviewee ? $review->reviewee->name : '' }} </li>
                    <li><strong>Student number:</strong> {{ $review->reviewee ? $review->reviewee->s_number : '' }} </li>
                    <li><strong>Review text:</strong></li>
                    <li>{{ $review->text }}</li>
                    @if ($review->reported)
                        <em style="color: #ff6000">This reviewee has reported this review as not being genuine.</em>
                    @endif
                    <li class="review-summary-text"></li>
                @empty
                    <li>This student has not submitted any reviews.</li>
                @endforelse
            </ul>
        </div>
    @endif

    {{-- Show reviews received by the student --}}
    <div class="table-card">
        <h4>Reviews received ({{ count($reviewsReceived) }}/{{ $submission->reviews->count() }})</h4>
        <hr>
        @if (count($reviewsReceived) > 0)
            @if ($isTeacher)
                {{-- Teacher can see all reviews --}}
                <ul class="review-summary">
                    @forelse ($reviewsReceived as $review)
                        <li><strong>Reviewer name:</strong> {{ $review->submission->student->name }} </li>
                        <li><strong>Student number:</strong> {{ $review->submission->student->s_number }} </li>
                        <li><strong>Review text:</strong></li>
                        <li>{{ $review->text }}</li>
                        @if ($review->reported)
                            <em style="color: #ff6000">This review has been reported as non-genuine by the reviewee.</em>
                        @endif
                        <li class="review-summary-text"></li>
                    @empty
                        <li>This student has not received any reviews.</li>
                    @endforelse
                </ul>
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
