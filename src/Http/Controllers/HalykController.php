<?php

namespace Studioone\Halyk\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HalykController extends Controller
{

    public function index(Request $request)
    {
        dd('Hello from Halyk!');
    }

    public function redirect()
    {
        dd(1);
    }
}
