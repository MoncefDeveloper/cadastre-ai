App\Models\User::with('roles')->get(['id', 'name', 'email'])->each(fn($u) => dump($u->id . ' | ' . $u->name . ' | Role: ' . $u->roles->pluck('name')->join(', ')));
