@extends('layouts.master')

@section('title')
    {{ $submission->assessment->title }}
@endsection

@section('description')
    Complete all peer reviews below and click the submit button.
@endsection

@section('content')
    <h2>Instructions:</h2>
    <p>{{ $submission->assessment->instruction }}</p>
    <hr>

    <p>
        Due date: {{ $submission->assessment->due_date }}
        @if (!$isLate)
            <strong>Late submission</strong>
        @endif
    </p>

    <hr>

    <h3>Reviews to complete</h3>

    <p>You need to complete all <strong>{{ $submission->reviews->count() }}</strong> peer reviews below.</p>

    <form action="{{ url('submissions', [$submission->id]) }}" method="post">
        @csrf
        @method('PUT')

        @foreach ($submission->reviews as $index => $review)
            <hr>
            <input type="hidden" name="review_{{ $index + 1 }}_id" value="{{ $review->id }}">
            <div>
                <div class="absent-student-{{ $submission->assessment->type->type }}">
                    <input type="checkbox" id="absent_{{ $index + 1 }}" name="absent_{{ $index + 1 }}"
                        {{ old('absent_' . $index + 1) == 'on' ? 'checked' : '' }} />
                    <label for="absent_{{ $index + 1 }}">My assigned student is unavailable/not present</label>
                    @foreach ($errors->get('absent_' . ($index + 1)) as $error)
                        <li style="color: #f46000">
                            <em>{{ $error }}</em>
                        </li>
                    @endforeach


                    <div class="no-student-{{ $submission->assessment->type->type }}">
                        <span class="choose-other-student">Please select another student to review</span>
                        <input type="checkbox" id="unavailable_{{ $index + 1 }}" name="unavailable_{{ $index + 1 }}"
                            {{ old('unavailable_' . $index + 1) == 'on' ? 'checked' : '' }} />
                        <label for="unavailable_{{ $index + 1 }}">No student available to review</label>

                        @foreach ($errors->get('unavailable_' . ($index + 1)) as $error)
                            <li style="color: #f46000">
                                <em>{{ $error }}</em>
                            </li>
                        @endforeach



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

        <button type="submit">Submit peer reviews</button>
    </form>

    <br>
    <hr>

    <h4>Reviews received from your peers</h4>
    @if ($hiddenCount > 0)
        <p>You have received <em>{{ $hiddenCount }}</em> reviews! Reveal them by submitting your own reviews.</p>
    @else
        <p>You have not received any reviews yet. Check back later.</p>
    @endif
    {{-- {{ dd($errors) }} --}}
@endsection
