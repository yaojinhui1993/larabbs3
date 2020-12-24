<?php

namespace App\Http\Controllers;

class PagesController extends Controller
{
    public function root()
    {
        return view('pages.root');
    }

    public function permissionDenied()
    {
        if (config('administrator.permission')()) {
            return redirect(config('administrator.uri'), 302);
        }

        return view('pages.permission_denied');
    }
}
