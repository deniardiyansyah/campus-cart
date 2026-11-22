<?php

class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user']['id'] : null;
    }

    public static function hasRole(array $roles): bool
    {
        return self::check() && in_array($_SESSION['user']['role'], $roles, true);
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'              => (int) $user['id'],
            'name'            => $user['name'],
            'email'           => $user['email'],
            'role'            => $user['role'],
            'organization_id' => $user['organization_id'] !== null ? (int) $user['organization_id'] : null,
        ];

        Database::update('users', ['last_login_at' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
    }

    public static function logout(): void
    {
        unset($_SESSION['user'], $_SESSION['_csrf']);
        session_regenerate_id(true);
    }

    // Tujuan setelah login sesuai role
    public static function homePath(): string
    {
        switch (self::user()['role'] ?? '') {
            case 'seller':
            case 'organization':
            case 'vendor':
                return '/seller';
            case 'moderator':
            case 'admin':
                return '/moderator';
            default:
                return '/';
        }
    }
}
