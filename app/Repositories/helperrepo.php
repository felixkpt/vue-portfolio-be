<?php

use App\Models\User;

function autoForm()
{
}

function currentUser()
{
    $user = request()->user();

    if (!$user) {
        // Fetch the associated token Model
        $token = App\Models\Sanctum\PersonalAccessToken::findToken(request()->bearerToken() ?? request()->token);
        
        // Get the assigned user
        $user = $token ? User::find($token->tokenable)->first() : null;
    }

    return $user;
}
