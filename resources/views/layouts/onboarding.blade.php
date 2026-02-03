<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Discord Onboarding') — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            *{margin:0;padding:0;box-sizing:border-box}body{font-family:'Instrument Sans',system-ui,sans-serif;background:#0f0f10;color:#f0f0f0;min-height:100vh;line-height:1.5}.container{max-width:420px;margin:0 auto;padding:2rem}.card{background:#1a1a1c;border:1px solid #2a2a2e;border-radius:12px;padding:2rem;margin-bottom:1.5rem}.btn{display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.75rem 1.5rem;border-radius:8px;font-weight:600;text-decoration:none;cursor:pointer;border:none;font-size:1rem;transition:all .2s}.btn-primary{background:#5865f2;color:white}.btn-primary:hover{background:#4752c4}.btn-secondary{background:#2a2a2e;color:#f0f0f0;border:1px solid #3a3a3e}.btn-secondary:hover{background:#3a3a3e}.avatar{width:64px;height:64px;border-radius:50%;object-fit:cover}.text-muted{color:#8a8a8d}.text-success{color:#57f287}.text-error{color:#ed4245}.step{display:flex;gap:0.5rem;margin-bottom:1rem;font-size:0.875rem}.step-done{color:#57f287}.step-active{color:#5865f2;font-weight:600}input[type=radio]{display:none}.role-option{display:flex;align-items:center;gap:1rem;padding:1rem;border:2px solid #2a2a2e;border-radius:8px;cursor:pointer;margin-bottom:0.75rem;transition:all .2s}.role-option:hover{border-color:#3a3a3e}.role-option.selected{border-color:#5865f2;background:rgba(88,101,242,.1)}.lang-option{display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.25rem;margin:0.25rem;border:2px solid #2a2a2e;border-radius:8px;cursor:pointer;transition:all .2s}.lang-option:hover,.lang-option.selected{border-color:#5865f2}
        </style>
    @endif
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-md">
            @if (session('success'))
                <div class="mb-4 p-4 rounded-lg bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500/50 text-red-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

        @auth
        <form method="POST" action="{{ route('auth.logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="text-sm text-zinc-500 hover:text-zinc-400 transition-colors">
                Log out
            </button>
        </form>
        @endauth
    </div>
</body>
</html>
