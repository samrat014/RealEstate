<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use JsonRequest;

    public function index(Request $request)
    {
        $user = $request->user();
        $roles = $user->load('roles.permissions');

        return ProfileResource::make($roles);
    }
}
