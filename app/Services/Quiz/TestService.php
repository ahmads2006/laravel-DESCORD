<?php

namespace App\Services\Quiz;

use App\Models\Action;
use App\Models\Answer;
use App\Models\Choice;
use App\Models\Question;
use App\Models\Specialization;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Collection;

class TestService
{
    public function hasCompletedTest(User $user, Specialization $spec): bool
    {
        // Check if user has a PASSED test for this specialization
        // If they failed, they might be allowed to retake depending on rules.
        // The requirements say: "Protection against retaking the test multiple times for the same specialty."
        // Usually implies once passed, or once attempted?
        // "Protects against manipulation... Protection against retaking... for same specialty"
        // Let's assume one attempt per specialization for now, or at least one COMPLETED attempt.
        
        return Test::where('user_id', $user->id)
            ->where('specialization_id', $spec->id)
            ->where('completed', true)
            ->exists();
    }

    public function startTest(User $user, Specialization $spec): Test
    {
        // Create a new test
        $test = Test::create([
            'user_id' => $user->id,
            'specialization_id' => $spec->id,
            'language_id' => $user->language_id,
            'started_at' => now(),
            'completed' => false,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Pick 3 random questions
        $questions = Question::where('specialization_id', $spec->id)
            ->where('language_id', $user->language_id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // Attach questions to test
        $test->questions()->attach($questions->pluck('id'));

        return $test->fresh();
    }

    public function getQuestionsForTest(Test $test): Collection
    {
        return $test->questions()
            ->with(['choices' => function ($query) {
                // Hide is_correct from frontend
                $query->select('id', 'question_id', 'choice_text');
            }])
            ->get();
    }

    public function saveProgress(Test $test, array $answers): void
    {
        if ($test->completed) {
            return;
        }

        foreach ($answers as $questionId => $choiceIdOrArray) {
            // Handle multiple choice (array) vs single choice (id)
            // Existing code assumed single choice ($choiceId), but user added multiple choice support in view?
            // The view shows "checkbox" if mulitple.
            // But DB schema `answers` has `choice_id` (FK to choices). So it's one row per choice selected.
            // If strictly single choice in DB design (answer belongs to ONE choice), we need multiple rows for multiple choice question.
            // But `Answer` model has `question_id` and `choice_id`.
            // If multiple choices for same question, we need multiple Answer records.

            // First, clear existing answers for this question to avoid duplicates/stale state
            Answer::where('test_id', $test->id)
                ->where('question_id', $questionId)
                ->delete();

            $choiceIds = is_array($choiceIdOrArray) ? $choiceIdOrArray : [$choiceIdOrArray];

            foreach ($choiceIds as $cId) {
                if (!$cId) continue;
                
                $choice = Choice::find($cId);
                // Verify choice belongs to question?
                if (!$choice || $choice->question_id != $questionId) continue;
                
                Answer::create([
                    'test_id' => $test->id,
                    'question_id' => $questionId,
                    'choice_id' => $cId,
                    'is_correct' => $choice->is_correct, // Storing it now for easier grading later
                ]);
            }
        }
    }

    public function submitTest(Test $test, array $answers): void
    {
        if ($test->completed) {
            abort(403, 'Test already submitted.');
        }

        // Save final state of answers
        $this->saveProgress($test, $answers);

        // Calculate score
        $correctCount = 0;
        
        // Reload questions to ensure we have all of them
        // Note: we need to check ALL questions for the test, not just the ones answered.
        $test->load('questions');
        
        // Get all answers for this test
        $allAnswers = Answer::where('test_id', $test->id)->get()->groupBy('question_id');

        foreach ($test->questions as $question) {
            $questionAnswers = $allAnswers->get($question->id);
            
            // Logic for correctness:
            // Single choice: Is the selected choice correct?
            // Any correct choice selected? 
            // The Migration `choices` has `is_correct`. `answers` has `is_correct` copied.
            
            // If standard single choice:
            // If any of the user's answers for this question is correct (and only one allowed/selected), count it?
            
            // NOTE: The previous logic was:
            // $choice = Choice::where('id', $choiceId)->... first();
            // $isCorrect = $choice?->is_correct;
            
            // If we assume single choice for now (as per original spec 1 correct answer), 
            // then we just check if the stored answer is correct.
            
            if ($questionAnswers && $questionAnswers->isNotEmpty()) {
                 // Check if any selected answer is correct? Or ALL?
                 // For single choice, there is only one.
                 // If User selected the correct one, `is_correct` is true.
                 foreach ($questionAnswers as $ans) {
                     if ($ans->is_correct) {
                         $correctCount++;
                         break; // Count once per question
                     }
                 }
            }
        }

        $total = $test->questions->count();
        $incorrectCount = $total - $correctCount;
        $passed = $this->passed($correctCount, $total);

        $test->update([
            'correct_count' => $correctCount,
            'incorrect_count' => $incorrectCount,
            'ended_at' => now(),
            'duration_seconds' => now()->diffInSeconds($test->started_at),
            'completed' => true,
            'passed' => $passed,
        ]);
    }

    protected function passed(int $correct, int $total): bool
    {
        // Requirements say validation is adjustable.
        $threshold = 60; // Example: 60%
        if ($total === 0) return false;
        
        return (($correct / $total) * 100) >= $threshold;
    }
}
