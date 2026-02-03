<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Start the exam for a selected specialization.
     */
    public function start(Request $request)
    {
        $request->validate([
            'role' => 'required|string|in:frontend,backend,solutions_architect',
        ]);

        $user = $request->user();

        // Ensure user is authenticated via Discord (has discord_id)
        if (! $user->discord_id) {
            return response()->json(['error' => 'User not connected to Discord'], 400);
        }

        // Ensure user has not already passed or pending
        if ($user->exam_status === 'passed') {
            return response()->json(['error' => 'You have already passed the exam.'], 400);
        }
        
        if ($user->exam_status === 'pending') {
             // Optional: allow restarting if pending? The requirements say "Ensure the user has not already completed an exam".
             // "completed" usually implies passed. "pending" means in progress. 
             // If they click again, maybe they want to retry triggering the bot?
             // But let's restart it to be safe or just proceed. 
             // Requirement: "Ensure the user has not already completed an exam". 
             // "passed" = completed. failed = not completed. pending = in progress (not completed).
             // I'll allow retrying if failed or pending (in case bot failed first time), but block if passed.
        }

        try {
            $botUrl = config('services.discord_bot.url'); // or env directly, better config
            $apiKey = config('services.discord_bot.key');

            if (! $botUrl || ! $apiKey) {
                // Log invalid config
                \Illuminate\Support\Facades\Log::error('Discord Bot API configuration missing');
                return response()->json(['error' => 'Server configuration error'], 500);
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($botUrl, [
                'discord_id' => $user->discord_id,
                'action' => 'start_exam',
                'role' => $request->role,
            ]);

            if (! $response->successful()) {
                \Illuminate\Support\Facades\Log::error('Discord Bot API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json(['error' => 'Failed to reach Discord Bot'], 502);
            }

            // Update user status
            $user->update([
                'specialization_role' => $request->role,
                'exam_status' => 'pending',
            ]);

            return response()->json(['message' => 'Exam started successfully', 'status' => 'pending']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exam start exception: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
