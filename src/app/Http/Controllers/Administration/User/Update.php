<?php

namespace LaravelEnso\Core\app\Http\Controllers\Administration\User;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use LaravelEnso\Core\app\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LaravelEnso\Core\app\Http\Requests\ValidateUserRequest;

class Update extends Controller
{
    use AuthorizesRequests;

    public function __invoke(ValidateUserRequest $request, User $user)
    {
        $this->authorize('handle', $user);

        if ($request->filled('password')) {
            $this->authorize('change-password', $user);
            $user->password = bcrypt($request->get('password'));
        }

        $user->fill($request->validated());

        if ($user->isDirty('group_id')) {
            $this->authorize('change-group', $user);
        }

        if ($user->isDirty('role_id')) {
            $this->authorize('change-role', $user);
        }

        $user->save();

        if (collect($user->getChanges())->keys()->contains('password')) {
            Event::dispatch(new PasswordReset($user));
        }

        return ['message' => __('The user was successfully updated')];
    }
}
