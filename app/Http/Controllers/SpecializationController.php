<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::where('is_active', true)->get();
        return view('specialization.index', compact('specializations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
        ]);

        // Proceed to quiz start
        return redirect()->route('quiz.start', ['specialization' => $request->specialization_id]);
    }
}
