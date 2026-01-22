<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    public function reset(User $user, array $input): void
    {
        $request = new RegisterRequest();

        Validator::make($input, [
            'password' => $request->rules()['password'],
        ], $request->messages())->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}