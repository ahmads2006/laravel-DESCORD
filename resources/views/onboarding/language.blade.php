@extends('layouts.onboarding')

@section('title', 'Choose Language')

@section('content')
<div class="flex items-center gap-4 mb-8">
    @if($user->discord_avatar_url)
        <img src="{{ $user->discord_avatar_url }}" alt="{{ $user->discord_username }}" class="w-14 h-14 rounded-full border-2 border-zinc-700">
    @else
        <div class="w-14 h-14 rounded-full bg-zinc-700 flex items-center justify-center text-xl font-bold">
            {{ strtoupper(substr($user->discord_username ?? $user->name, 0, 1)) }}
        </div>
    @endif
    <div>
        <h1 class="text-xl font-semibold text-zinc-100">Hello, {{ $user->discord_username ?? $user->name }}</h1>
        <p class="text-sm text-zinc-500">Step 1 of 2 — Choose your language</p>
    </div>
</div>

<div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-8 shadow-xl">
    <form method="POST" action="{{ route('onboarding.language.save') }}">
        @csrf
        <label class="block text-sm font-medium text-zinc-400 mb-4">Select your preferred language</label>
        <div class="flex flex-wrap gap-3">
            <label class="cursor-pointer">
                <input type="radio" name="language" value="en" {{ ($user->language ?? 'en') === 'en' ? 'checked' : '' }} class="sr-only peer">
                <span class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border-2 border-zinc-700 hover:border-zinc-600 peer-checked:border-[#5865F2] peer-checked:bg-[#5865F2]/10 transition-colors">
                    🇬🇧 English
                </span>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="language" value="ar" {{ ($user->language ?? '') === 'ar' ? 'checked' : '' }} class="sr-only peer">
                <span class="inline-flex items-center gap-2 px-5 py-3 rounded-lg border-2 border-zinc-700 hover:border-zinc-600 peer-checked:border-[#5865F2] peer-checked:bg-[#5865F2]/10 transition-colors">
                    🇸🇦 العربية
                </span>
            </label>
        </div>

        <button type="submit" class="mt-6 w-full py-4 px-6 rounded-lg bg-[#5865F2] hover:bg-[#4752C4] text-white font-semibold transition-colors">
            Continue
        </button>
    </form>
</div>

<div class="flex justify-center gap-4 mt-4 text-xs text-zinc-500">
    <span class="text-[#5865F2] font-medium">● Language</span>
    <span>○ Role</span>
</div>
@endsection
