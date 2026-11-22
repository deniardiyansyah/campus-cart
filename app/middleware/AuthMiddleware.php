<?php

class AuthMiddleware
{
    public static function handle(): void
    {
        if (Auth::check()) {
            return;
        }

        if (is_ajax()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['message' => 'Silakan masuk terlebih dahulu.']);
            exit;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            $_SESSION['intended'] = '/' . current_path();
        }
        Flash::set('warning', 'Silakan masuk terlebih dahulu.');
        redirect('login');
    }
}
