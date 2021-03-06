<?php

namespace LaravelEnso\Core\app\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use LaravelEnso\Core\app\Notifications\ResetPasswordNotification;

trait HasPassword
{
    public function currentPasswordIs(string $password)
    {
        return Hash::check($password, $this->password);
    }

    public function passwordExpired()
    {
        $lifetime = (int) config('enso.auth.password.lifetime');

        return $lifetime > 0 && ($this->password_updated_at === null
            || now()->diffInDays($this->password_updated_at) > $lifetime);
    }

    public function needsPasswordChange()
    {
        return (int) config('enso.auth.password.lifetime') > 0
            && $this->password_updated_at !== null
            && $this->passwordExpiresIn() <= 3;
    }

    public function passwordExpiresIn()
    {
        return $this->password_updated_at
            ->addDays((int) config('enso.auth.password.lifetime'))
            ->diffInDays(Carbon::now());
    }

    public function sendResetPasswordEmail()
    {
        $this->sendPasswordResetNotification(
            app('auth.password.broker')
                ->createToken($this)
        );
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(
            (new ResetPasswordNotification($token))
                ->locale($this->lang())
                ->onQueue('notifications')
        );
    }
}
