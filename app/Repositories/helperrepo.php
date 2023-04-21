<?php

function autoForm()
{
}

function currentUser()
{
    $user = request()->user();

    if (!$user) {
        // Fetch the associated token Model
        $token = \Laravel\Sanctum\PersonalAccessToken::findToken(request()->bearerToken() ?? request()->token);
        // Get the assigned user
        $user = $token->tokenable ?? null;
    }

    return $user;
}
