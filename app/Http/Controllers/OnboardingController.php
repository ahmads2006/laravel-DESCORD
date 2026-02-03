<?php

namespace App\Http\Controllers;

use App\Services\DiscordRoleAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function __construct(
        protected DiscordRoleAssignmentService $roleService
    ) {}

    /**
     * Show the login page.
     */
    public function login(): View
    {
        return view('onboarding.login');
    }

    /**
     * Show the language selection page.
     */
    public function language(): View
    {
        $this->ensureAuthenticated();

        return view('onboarding.language', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Save language selection.
     */
    public function saveLanguage(Request $request): RedirectResponse
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'language' => ['required', Rule::in(['en', 'ar'])],
        ]);

        Auth::user()->update(['language' => $validated['language']]);

        return redirect()->route('onboarding.role');
    }

    /**
     * Show the role selection page.
     */
    public function role(): View
    {
        $this->ensureAuthenticated();

        return view('onboarding.role', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Save role selection and assign role in Discord.
     */
    public function saveRole(Request $request): RedirectResponse
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'specialization' => ['required', Rule::in(['frontend', 'backend', 'solutions_architect'])],
        ]);

        $user = Auth::user();

        if (! $user->discord_id) {
            return redirect()->route('onboarding.role')
                ->with('error', 'Discord account not linked. Please log in again.');
        }

        $accessToken = $request->session()->get('discord_access_token');
        $result = $this->roleService->assignRole(
            $user->discord_id,
            $validated['specialization'],
            $accessToken
        );

        if (! $result['success']) {
            return redirect()->route('onboarding.role')
                ->with('error', $result['message']);
        }

        $user->update(['specialization_role' => $validated['specialization']]);

        // Clear the access token from session after use
        $request->session()->forget('discord_access_token');

        return redirect()->route('onboarding.success')
            ->with('success', $result['message']);
    }

    /**
     * Show the success page.
     */
    public function success(): View
    {
        $this->ensureAuthenticated();

        return view('onboarding.success', [
            'user' => Auth::user(),
        ]);
    }

    protected function ensureAuthenticated(): void
    {
        if (! Auth::check()) {
            abort(redirect()->route('onboarding.login'));
        }
    }
}
