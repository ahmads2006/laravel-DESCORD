@extends('layouts.onboarding')

@section('title', 'Choose Role')

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
        <h1 class="text-xl font-semibold text-zinc-100">Almost there, {{ $user->discord_username ?? $user->name }}</h1>
        <p class="text-sm text-zinc-500">Step 2 of 2 — Choose your specialization</p>
    </div>
</div>

<div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-8 shadow-xl">
    <form method="POST" action="{{ route('onboarding.role.save') }}" id="role-form">
        @csrf
        <label class="block text-sm font-medium text-zinc-400 mb-4">Select your specialization (one role only)</label>

        <label class="block cursor-pointer mb-3">
            <input type="radio" name="specialization" value="frontend" {{ $user->specialization_role === 'frontend' ? 'checked' : '' }} class="sr-only peer">
            <div class="flex items-center gap-4 p-4 rounded-lg border-2 border-zinc-700 hover:border-zinc-600 peer-checked:border-[#5865F2] peer-checked:bg-[#5865F2]/10 transition-colors">
                <span class="text-2xl">🎨</span>
                <div>
                    <span class="font-medium text-zinc-100">Frontend Developer</span>
                    <p class="text-sm text-zinc-500">Build user interfaces and client-side applications</p>
                </div>
            </div>
        </label>

        <label class="block cursor-pointer mb-3">
            <input type="radio" name="specialization" value="backend" {{ $user->specialization_role === 'backend' ? 'checked' : '' }} class="sr-only peer">
            <div class="flex items-center gap-4 p-4 rounded-lg border-2 border-zinc-700 hover:border-zinc-600 peer-checked:border-[#5865F2] peer-checked:bg-[#5865F2]/10 transition-colors">
                <span class="text-2xl">🛠</span>
                <div>
                    <span class="font-medium text-zinc-100">Backend Developer</span>
                    <p class="text-sm text-zinc-500">Develop APIs, databases, and server-side logic</p>
                </div>
            </div>
        </label>

        <label class="block cursor-pointer mb-4">
            <input type="radio" name="specialization" value="solutions_architect" {{ $user->specialization_role === 'solutions_architect' ? 'checked' : '' }} class="sr-only peer">
            <div class="flex items-center gap-4 p-4 rounded-lg border-2 border-zinc-700 hover:border-zinc-600 peer-checked:border-[#5865F2] peer-checked:bg-[#5865F2]/10 transition-colors">
                <span class="text-2xl">🏗</span>
                <div>
                    <span class="font-medium text-zinc-100">Solutions Architect</span>
                    <p class="text-sm text-zinc-500">Design systems and technical solutions</p>
                </div>
            </div>
        </label>

        <p class="text-xs text-zinc-500 mb-4">
            Your selected role will be assigned to you in the Discord server.
        </p>

        <button type="submit" class="w-full py-4 px-6 rounded-lg bg-[#5865F2] hover:bg-[#4752C4] text-white font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="submit-btn">
            Complete Onboarding
        </button>
    </form>
</div>

<div class="flex justify-center gap-4 mt-4 text-xs text-zinc-500">
    <span>● Language</span>
    <span class="text-[#5865F2] font-medium">● Role</span>
</div>

<script>
    document.getElementById('role-form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.textContent = 'Assigning role...';
    });
</script>
@endsection
