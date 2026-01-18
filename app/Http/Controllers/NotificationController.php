<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->whereNull('read_at')
            ->latest()
            ->get();

        return view('notifications.index', compact('notifications'));
    }
}
