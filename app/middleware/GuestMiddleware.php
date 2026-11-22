<?php

class GuestMiddleware
{
    public static function handle(): void
    {
        if (Auth::check()) {
            redirect(Auth::homePath());
        }
    }
}
