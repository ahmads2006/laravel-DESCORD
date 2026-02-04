<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Services\Discord\DiscordBotService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function show(Test $test, DiscordBotService $discord)
    {
        if ($test->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $roleAssigned = false;
        if ($test->passed && $test->specialization->discord_role_id) {
             $discord->assignRole(
                $test->user->discord_id,
                $test->specialization->discord_role_id
            );
            $roleAssigned = true;
        }

        return response()->json([
            'test' => $test,
            'role_assigned' => $roleAssigned
        ]);
    }
    
    public function history()
    {
        $tests = auth()->user()->tests()->with('specialization')->latest()->get();
        return response()->json($tests);
    }
}
