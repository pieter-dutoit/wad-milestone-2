<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $submission = Submission::find($id);
        $assessment_id = $submission->assessment->id;

        // Date diff: https://stackoverflow.com/questions/41443987/finding-days-between-two-dates-in-laravel
        $due_date = new DateTime($submission->assessment->due_date);
        $current_date = new DateTime();
        $isLate = $due_date < $current_date;

        // Hidden review count:
        $hiddenCount = Auth::user()->reviews
            ->where('complete',  true)
            ->where('submission.assessment_id', $submission->assessment_id)
            ->where('submission.id', '!=', $submission->id)
            ->count();

        // Find reviewees in the same workshop, excluding this user
        $workshopPeers = User::whereHas('submissions', function ($query) use ($assessment_id, $submission) {
            $query->where('assessment_id', $assessment_id)->where('id', '!=', $submission->id);
        })->get();

        return view('submissions.edit_form')
            ->with('submission', $submission)
            ->with('isLate', $isLate)
            ->with('hiddenCount', $hiddenCount)
            ->with('peers', $workshopPeers);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $allFields = $request->all();

        // Get id's of all reviews marked as unavailable by the reviewer.
        $unavailableReviews = [];
        foreach ($allFields as $field => $value) {
            if (str_starts_with($field, 'unavailable')) {
                $unavailableReviews[] = explode('_', $field)[1];
            }
        }

        // Stored data in format that can be saved if validation passes:
        $validationArr = [];
        $validatedReviewees = [];
        // Groups review data by reviewee id:
        $groupedReviews = [];

        // Creates validation function input and groups data by review number
        foreach ($allFields as $field => $value) {
            // Only validate review-related fields
            if (str_starts_with($field, 'review')) {
                $review_num = explode('_', $field)[1];
                $groupedReviews[$review_num]['complete'] = true;

                // Get review ID.
                if (str_ends_with($field, '_id')) {
                    $groupedReviews[$review_num]['review_id'] = $value;
                }

                if (in_array($review_num, $unavailableReviews)) {
                    // Review was marked as not having anyone available to review.
                    $groupedReviews[$review_num]['unavailable'] = true;
                    $groupedReviews[$review_num]['text'] = 'There were no reviewees available.';
                    continue;
                }

                // Selected student validation
                if (str_ends_with($field, 'student')) {
                    // Creates validation rules for the selected student
                    // Make sure the selected user exists and has not been selected twice.
                    $validationArr[$field] = [
                        'required',
                        'exists:users,id',
                        function ($attribute, $value, $fail) use ($validatedReviewees) {
                            // https://laravel.com/docs/11.x/validation#using-closures
                            if ($value > -1 && in_array($value, $validatedReviewees)) {
                                $fail("Cannot review the same student more than once.");
                            }
                        }
                    ];
                    $validatedReviewees[] = $value;
                    $groupedReviews[$review_num]['reviewee_id'] = $value;
                }

                // Review text validation
                if (str_ends_with($field, '_text')) {
                    // Creates validation rules for the review text.
                    $validationArr[$field] = [
                        'required',
                        'max:5000',
                        function ($attribute, $value, $fail) {
                            // https://laravel.com/docs/11.x/validation#using-closures
                            if (count(explode(' ', $value)) < 5) {
                                $fail("Write at least 5 words.");
                            }
                        },
                    ];
                    // Prepares the review for submission
                    $groupedReviews[$review_num]['text'] = $value;
                }
            }
        }


        // https://laravel.com/docs/11.x/validation#manually-creating-validators
        // https://laravel.com/docs/11.x/validation#performing-additional-validation
        $validator = Validator::make($request->all(), $validationArr);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($groupedReviews as $data) {
            $review = Review::find($data['review_id']);
            unset($data['review_id']);
            $review->update($data);
            $review->save();
        }

        $submission = Submission::find($id);
        $submission->date_submitted = new DateTime();
        $submission->save();

        return redirect("submissions/$submission->id");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
