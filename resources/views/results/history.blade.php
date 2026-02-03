@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center text-white">Your Quiz History</h1>
    
    <div class="max-w-2xl mx-auto bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        @if($tests->isEmpty())
            <p class="p-8 text-center text-gray-400">You haven't taken any quizzes yet.</p>
        @else
            <table class="min-w-full leading-normal text-left">
                <tbody>
                    @foreach($tests as $test)
                        <tr class="border-b border-gray-700 hover:bg-gray-750">
                            <td class="px-5 py-5 text-sm">
                                <p class="text-white font-semibold">{{ $test->specialization->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $test->started_at->format('M d, Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-5 text-sm">
                                @if($test->passed)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Passed
                                    </span>
                                @elseif(!$test->completed)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Incomplete
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-5 text-sm text-right">
                                <a href="{{ route('results.show', $test->id) }}" class="text-blue-400 hover:text-blue-300">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
     <div class="text-center mt-6">
        <a href="{{ route('specialization.select') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition">
            Take New Quiz
        </a>
    </div>
</div>
@endsection
