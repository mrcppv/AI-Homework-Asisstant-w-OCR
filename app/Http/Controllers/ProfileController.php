<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\DailyQuestionCount;
use Carbon\Carbon;

class ProfileController extends Controller
{

    public function Index(User $user): Response|RedirectResponse
    {
        if (Auth::id() !== $user->id && !Auth::user()->owner) {
            return redirect()->back()->with('error', 'You can only view your own profile.');

        }

        $limit = $user->getQuestionLimit();
        $remainingQuestions = $limit - $user->getDailyQuestionCount();

        return Inertia::render('Profile/Account', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'owner' => $user->owner,
                'photo' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
                'deleted_at' => $user->deleted_at,
                'subscription_type' => $user->subscription_type,
                'daily_question_limit' => $limit,
                'questions_left' => $remainingQuestions,
            ],
        ]);
    }





    public function update(User $user): RedirectResponse
    {
        if (Auth::id() !== $user->id && !Auth::user()->owner) {
            return redirect()->route('accountIndex', Auth::id())->with('error', 'You can only update your own profile.');
        }

        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
            'photo' => ['nullable', 'image'],
        ]);

        $user->update(Request::only('first_name', 'last_name', 'email', 'owner'));

        if (Request::file('photo')) {
            $user->update(['photo_path' => Request::file('photo')->store('users')]);
        }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::back()->with('success', 'User updated.');
    }

    public function upgradeSubscription(User $user): RedirectResponse
    {
        if (Auth::id() !== $user->id && !Auth::user()->owner) {
            return redirect()->route('accountIndex', Auth::id())->with('error', 'You can only update your own subscription.');
        }

        Request::validate([
            'subscription_type' => ['required', 'integer', 'min:1', 'max:3'],
        ]);

        $user->update(['subscription_type' => Request::input('subscription_type')]);

        return Redirect::back()->with('success', 'Subscription updated.');
    }

    public function destroy(User $user): RedirectResponse
    {

        $user->delete();

        return Redirect::back()->with('success', 'Account deleted.');
    }

    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        return Redirect::back()->with('success', 'User restored.');
    }


}
