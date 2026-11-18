<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%$v%")->orWhere('email', 'like', "%$v%")))
            ->when($request->role, fn ($q, $v) => $q->where('role', $v))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User(['active' => true, 'role' => 'agent'])]);
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);
        $user = User::create($data);
        Activity::log('utilisateur_cree', $user, "Création du compte {$user->email} ({$user->role})");

        return redirect()->route('users.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->valider($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($user->id === $request->user()->id && ($data['role'] !== 'super_admin' || ! $data['active'])) {
            return back()->with('error', 'Vous ne pouvez pas retirer vos propres droits ni désactiver votre compte.');
        }

        $user->update($data);
        Activity::log('utilisateur_modifie', $user, "Modification du compte {$user->email}");

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['active' => ! $user->active]);
        Activity::log($user->active ? 'utilisateur_active' : 'utilisateur_desactive', $user, $user->email);

        return back()->with('success', $user->active ? 'Compte activé.' : 'Compte désactivé.');
    }

    private function valider(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(array_keys(config('intendance.roles')))],
            'password' => [$user ? 'nullable' : 'required', Password::min(8)->letters()->numbers()],
        ]);
        $data['active'] = $request->boolean('active');

        return $data;
    }
}
