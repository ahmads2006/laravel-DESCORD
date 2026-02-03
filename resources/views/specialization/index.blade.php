@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center text-white">Select Your Specialization</h1>
    
    <div class="max-w-md mx-auto bg-gray-800 rounded-lg shadow-lg p-6">
        <form action="{{ route('specialization.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-4">
                @foreach($specializations as $spec)
                    <div class="relative">
                        <input class="hidden peer" id="spec_{{ $spec->id }}" type="radio" name="specialization_id" value="{{ $spec->id }}">
                        <label class="flex flex-col p-4 border-2 border-gray-700 rounded-lg cursor-pointer hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:bg-gray-700 transition" for="spec_{{ $spec->id }}">
                            <span class="text-lg font-semibold text-white">{{ $spec->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    Start Quiz
                </button>
            </div>

            @error('specialization_id')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </form>
    </div>
</div>
@endsection
