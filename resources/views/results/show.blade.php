@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-gray-800 rounded-lg shadow-lg p-8 text-center">
        @if($test->passed)
            <div class="text-green-500 mb-4">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Congratulations!</h2>
            <p class="text-gray-300 mb-6">You passed the <span class="text-indigo-400 font-semibold">{{ $test->specialization->name }}</span> quiz.</p>
            
            <div class="bg-gray-700 rounded p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">Score</span>
                    <span class="text-white font-bold text-xl">{{ $test->correct_count }} / {{ $test->questions()->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Time Taken</span>
                    <span class="text-white font-bold">{{ gmdate("i:s", $test->duration_seconds) }}</span>
                </div>
            </div>

            @if($roleAssigned)
                <div class="bg-green-900/50 border border-green-500/50 rounded p-4 text-green-200 text-sm mb-6">
                    <p>The <strong>{{ $test->specialization->name }}</strong> role has been assigned to you in Discord!</p>
                </div>
            @endif

        @else
            <div class="text-red-500 mb-4">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Test Failed</h2>
            <p class="text-gray-300 mb-6">Unfortunately, you did not achieve the required score.</p>
            
             <div class="bg-gray-700 rounded p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">Score</span>
                    <span class="text-white font-bold text-xl">{{ $test->correct_count }} / {{ $test->questions()->count() }}</span>
                </div>
                <div class="text-xs text-gray-500 mt-2">
                    Don't worry, you can try other specializations or retry later.
                </div>
            </div>
        @endif

        <a href="{{ route('specialization.select') }}" class="inline-block bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded transition">
            Choose Another Specialization
        </a>
    </div>
</div>
@endsection
