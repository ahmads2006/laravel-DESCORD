<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::where('is_active', true)->get();
        return response()->json($specializations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialization_id' => 'required|exists:specializations,id',
        ]);

        // Just update prompt or ensure frontend knows what to do
        return response()->json([
            'message' => 'Specialization selected', 
            'specialization_id' => $request->specialization_id
        ]);
    }
}
