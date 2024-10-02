@extends('layouts.master')

@section('title')
    View Peer Review Assessment
    <h4>{{ $course->course_name }}, {{ $course->course_code }}</h4>
@endsection

@section('description')
    This page displays the selected Peer Review Assessment's information.
@endsection

@section('content')
    <h3>Assessment details:</h3>
    <ul>
        <li>Title: {{ $assessment->title }}</li>
        <li>Instruction: {{ $assessment->instruction }}</li>
        <li>Type: {{ $assessment->type->type }}</li>
        <li>Max score: {{ $assessment->max_score }}</li>
        <li>Number of reviews per student: {{ $assessment->num_reviews }}</li>
        <li>Number of submissions: {{ $submittedReviewCount }}/{{ $assessment->submissions->count() }}</li>
    </ul>

    @if ($submittedReviewCount > 0)
        <p>This assessment is locked for editing, because {{ $submittedReviewCount }} student(s) have submitted their
            reviews.</p>
    @else
        <a href="{{ url("assessments/$assessment->id/edit") }}">Edit this assessment</a>
    @endif

    <hr>

    <h4>Enrolled students:</h4>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">S Number</th>
                <th scope="col">Name</th>
                <th scope="col">Num Reviews Submitted</th>
                <th scope="col">Num Reviews Received</th>
                <th scope="col">Score</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    <th scope="row">{{ $student->s_number }}</th>
                    <td>
                        {{ $student->name }}
                    </td>
                    <td>
                        {{ $student->submissions->where('assessment_id', $assessment->id)->first()->reviews->where('complete', true)->count() }}
                    </td>
                    <td>
                        {{ $student->reviews->where('complete', true)->where('submission.assessment_id', $assessment->id)->where('submission.assessment.id', $assessment->id)->count() }}
                    </td>
                    <td>
                        {{ $student->submissions->where('assessment_id', $assessment->id)->first()->score ?? '-' }} /
                        {{ $assessment->max_score }}
                    </td>
                    <td>
                        <a
                            href="{{ url('submissions/' . $student->submissions->where('assessment_id', $assessment->id)->first()->id . '/edit') }}">
                            View details
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan='5'>No students to show</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li>Previous</li>
            <li class="page-item">
                <a class="page-link" href="{{ url("/assessments/$assessment->id?page=$prevPage") }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ url("/assessments/$assessment->id?page=$nextPage") }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li>Next</li>
        </ul>
    </nav>
@endsection
