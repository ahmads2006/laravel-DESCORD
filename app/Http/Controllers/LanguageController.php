<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::where('is_active', true)->get();
        return response()->json($languages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
        ]);

        $user = auth()->user();
        $user->update(['language_id' => $request->language_id]);

        return response()->json(['message' => 'Language updated successfully', 'user' => $user]);
    }
}
