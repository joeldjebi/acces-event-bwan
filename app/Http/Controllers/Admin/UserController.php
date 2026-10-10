<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Gestion des administrateurs. Règles :
 * - tout administrateur peut créer, modifier et supprimer les autres administrateurs ;
 * - le propriétaire (premier administrateur) ne peut jamais être supprimé, et lui seul modifie son compte ;
 * - un administrateur ne peut pas supprimer son propre compte.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->whereIn('role', ['admin', 'editor'])
                ->orderByDesc('is_owner')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "L'administrateur {$user->name} a été créé.");
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeEdit($request, $user);

        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeEdit($request, $user);

        $validated = $request->validate($this->rules($user));

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Mot de passe facultatif en modification : laissé vide, il reste inchangé.
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "L'administrateur {$user->name} a été mis à jour.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->isOwner()) {
            return back()->withErrors(['user' => 'Le compte propriétaire ne peut pas être supprimé.']);
        }

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "L'administrateur {$name} a été supprimé.");
    }

    /**
     * Seul le propriétaire peut modifier son propre compte.
     */
    private function authorizeEdit(Request $request, User $user): void
    {
        abort_if($user->isOwner() && ! $user->is($request->user()), 403, 'Seul le propriétaire peut modifier ce compte.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(12)->letters()->numbers()],
        ];
    }
}
