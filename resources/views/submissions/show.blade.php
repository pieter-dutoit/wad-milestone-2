@extends('layouts.master')

@section('title')
    Peer Review Submission
@endsection

@section('description')
    This page displays the peer reviews that you have submitted and received.
@endsection

@section('content')
    <h2>
        {{ $submission->assessment->title }}
    </h2>

    <h3>
        {{ $submission->assessment->course->course_name }}, {{ $submission->assessment->course->course_code }}
    </h3>

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
                <li class="review-summary-text">{{ $review->text }}</li>
            @empty
                <li>This student has not submitted any reviews.</li>
            @endforelse
        </ul>
    </div>


    <div class="table-card">
        <h4>Reviews received ({{ count($reviewsReceived) }}/{{ $submission->reviews->count() }})</h4>
        <hr>
        @if (count($reviewsReceived) > 0)
            <ul class="review-summary">
                @forelse ($reviewsReceived as $review)
                    <li><strong>Reviewer name:</strong> {{ $review->submission->student->name }} </li>
                    <li><strong>Student number:</strong> {{ $review->submission->student->s_number }} </li>
                    <li><strong>Review text:</strong></li>
                    <li>{{ $review->text }}</li>
                    <li class="review-summary-text">

                    </li>
                    @if ($review->reported)
                        <em style="color: #ff6000">This review has been reported.</em>
                    @else
                        <b>Didn't present your work to this student? Report their review as fake:</b>
                        <form action="{{ url('reviews', [$review->id]) }}" method="post">
                            @csrf
                            @method('put')
                            <button class="btn btn-danger" type="submit">
                                Report
                            </button>
                        </form>
                    @endif
                @empty
                    <li>This student has not received any reviews.</li>
                @endforelse
            </ul>
        @else
            <p>No reviews have been received.</p>
        @endif
    </div>
@endsection
