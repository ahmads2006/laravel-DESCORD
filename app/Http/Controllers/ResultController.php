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
            abort(403);
        }

        // If passed and not yet assigned role (maybe checks if role assigned?)
        // Ideally we should assign role immediately upon passing in Service or Event, 
        // but Controller is fine for now as per plan.
        
        $roleAssigned = false;
        if ($test->passed && $test->specialization->discord_role_id) {
             $discord->assignRole(
                $test->user->discord_id,
                $test->specialization->discord_role_id
            );
            $roleAssigned = true;
        }

        return view('results.show', compact('test', 'roleAssigned'));
    }
    
    public function history()
    {
        $tests = auth()->user()->tests()->with('specialization')->latest()->get();
        return view('results.history', compact('tests'));
    }
}
