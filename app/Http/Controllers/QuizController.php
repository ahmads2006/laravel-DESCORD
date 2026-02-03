<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Test;
use App\Services\Quiz\TestService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function start(Specialization $specialization, Request $request)
    {
        $user = $request->user();

        if ($this->testService->hasCompletedTest($user, $specialization)) {
            // Already took this test
            return redirect()->route('results.history')->with('error', 'You have already completed this test.');
        }

        // Create a new test or continue existing incomplete one?
        // Requirement: "No The page can be reloaded to bypass the timer."
        // So we should check for an existing INCOMPLETE test.
        $existingTest = $user->tests()
            ->where('specialization_id', $specialization->id)
            ->where('completed', false)
            ->latest()
            ->first();

        if ($existingTest) {
            $test = $existingTest;
        } else {
            $test = $this->testService->startTest($user, $specialization);
        }

        $questions = $this->testService->getQuestionsForTest($test);

        return view('quiz.take', compact('test', 'questions', 'specialization'));
    }

    public function submit(Test $test, Request $request)
    {
        // Security check: Ensure test belongs to user
        if ($test->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->testService->submitTest($test, $request->input('answers', []));

        return redirect()->route('results.show', $test->id);
    }

    public function autosave(Test $test, Request $request)
    {
        if ($test->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->testService->saveProgress($test, $request->input('answers', []));

        return response()->json(['success' => true]);
    }
}
