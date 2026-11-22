<?php

class RoleMiddleware
{
    public static function handle(array $roles): void
    {
        AuthMiddleware::handle();

        if (!Auth::hasRole($roles)) {
            abort(403);
        }
    }
}
