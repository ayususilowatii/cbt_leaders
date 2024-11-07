<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use App\Models\CourseQuestion;
use App\Models\CourseAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Redirect;

class StudentAnswerController extends Controller
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
    public function store(Request $request, Course $course, $question)
{
    // Retrieve the question details
    $question_details = CourseQuestion::where('id', $question)->first();

    // Validate the request
    $validated = $request->validate([
        'answer_id' => 'required|exists:course_answers,id', // Corrected exists rule
    ]);

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Retrieve the selected answer from the CourseAnswer model
        $selectedAnswer = CourseAnswer::find($validated['answer_id']); // Corrected to CourseAnswer

        // Ensure the selected answer is valid for the given question
        if ($selectedAnswer->course_question_id != $question) {
            throw ValidationException::withMessages([
                'system_error' => ['Jawaban tidak tersedia pada pertanyaan ini.'],
            ]);
        }

        // Check if the user has already answered this question
        $existingAnswer = StudentAnswer::where('user_id', Auth::id())
                                        ->where('course_question_id', $question)
                                        ->first();

        if ($existingAnswer) {
            throw ValidationException::withMessages([
                'system_error' => ['Kamu telah menjawab pertanyaan ini sebelumnya.'],
            ]);
        }

        // Save the answer
        $answerValue = $selectedAnswer->is_correct ? 'correct' : 'wrong';
        StudentAnswer::create([
            'user_id' => Auth::id(),
            'course_question_id' => $question,
            'answer' => $answerValue,
        ]);

        // Commit the transaction if everything is successful
        DB::commit();

        // Retrieve the next question
        $nextQuestion = CourseQuestion::where('course_id', $course->id)
                                      ->where('id', '>', $question)
                                      ->orderBy('id', 'asc')
                                      ->first();

        // Redirect to the next question or finish page
        if ($nextQuestion) {
            return redirect()->route('dashboard.learning.course', [
                'course' => $course->id,
                'question' => $nextQuestion->id,
            ]);
        } else {
            return redirect()->route('dashboard.learning.finished.course', $course->id);
        }

    } catch (\Exception $e) {
        // Rollback the transaction if something goes wrong
        DB::rollBack();

        // Handle exception and rethrow with messages
        throw ValidationException::withMessages([
            'system_error' => ['System error: ' . $e->getMessage()],
        ]);
    }
}



    /**
     * Display the specified resource.
     */
    public function show(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAnswer $studentAnswer)
    {
        //
    }
}
