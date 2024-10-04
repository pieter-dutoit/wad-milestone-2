@extends('layouts.master')

@section('title')
    Peer Review Assessment Details
@endsection

@section('description')
    This page displays the selected Peer Review Assessment's information.
@endsection

@section('content')
    <h2>{{ $assessment->title }}</h2>
    <h3>{{ $course->course_name }}, {{ $course->course_code }}</h3>

    <div class='table-card'>
        <div class='row-between'>
            <h4>Assessment details</h4>
            <a class="btn btn-dark" href="{{ url("assessments/$assessment->id/edit") }}">Edit assessment</a>
        </div>
        <hr>
        <table class='table'>
            <tbody>
                <tr>
                    <th scope="col">Instruction</th>
                    <td>{{ $assessment->instruction }}</td>
                <tr>
                    <th scope="col">Type</th>
                    <td>{{ ['teacher_assign' => 'Teacher assign', 'student_select' => 'Student select'][$assessment->type->type] }}
                    </td>
                </tr>
                <tr>
                    <th scope="col">Max Score</th>
                    <td>{{ $assessment->max_score }}</td>
                </tr>
                <tr>
                    <th scope="col">Number of reviews per student</th>
                    <td>{{ $assessment->num_reviews }}</td>
                </tr>
                <tr>
                    <th scope="col">Number of submissions</th>
                    <td>{{ $submittedReviewCount }}/{{ $assessment->submissions->count() }}</td>
                </tr>
                </tr>
            </tbody>
        </table>
    </div>


    {{-- View list of students assignments/submissions --}}
    <div class='table-card'>
        <h4>Students / Reviewers</h4>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Workshop</th>
                    <th scope="col">Reviews Submitted</th>
                    <th scope="col">Reviews Received</th>
                    <th scope="col">Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $index => $submission)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>
                            <a href="{{ url('submissions/' . $submission->id . '/edit') }}">
                                {{ $submission->student->name }}
                            </a>
                        </td>

                        <td>
                            <em style="white-space: nowrap; font-size: 13px;">
                                {{ $submission->student->enrolments->where('course_id', $submission->assessment->course_id)->first()->workshop->location }}
                                @isset($submission->group_num)
                                    | Group {{ $submission->group_num }}
                                @endisset
                            </em>
                        </td>
                        <td>
                            {{ $submission->reviews->where('complete', true)->count() }}
                        </td>
                        <td>
                            {{ $submission->student->reviews->where('submission.assessment.id', $assessment->id)->where('complete', true)->count() }}
                        </td>
                        <td>
                            {{ $submission->score ?? '-' }} /
                            {{ $assessment->max_score }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan='5'>No students to show</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <hr>

        <span class="pages">
            Page {{ $page }} of {{ $numPages }}
        </span>

        <nav>
            <ul class="pagination">
                <li>Previous</li>
                <li class="page-item">
                    <a class="page-link" href="{{ url("/assessments/$assessment->id?page=$prevPage") }}"
                        aria-label="Previous">
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
    </div>
@endsection
