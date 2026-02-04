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
            return response()->json(['error' => 'You have already completed this test.'], 403);
        }

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

        return response()->json([
            'test' => $test,
            'questions' => $questions,
            'specialization' => $specialization
        ]);
    }

    public function submit(Test $test, Request $request)
    {
        if ($test->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->testService->submitTest($test, $request->input('answers', []));

        return response()->json([
            'success' => true, 
            'test_id' => $test->id,
            'message' => 'Test submitted successfully'
        ]);
    }

    public function autosave(Test $test, Request $request)
    {
        if ($test->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->testService->saveProgress($test, $request->input('answers', []));

        return response()->json(['success' => true]);
    }
}
