<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use JsonRequest;

    public function __construct()
    {
        $this->middleware('permission:read', ['only' => ['index', 'show']]);
        $this->middleware('permission:create', ['only' => ['store']]);
        $this->middleware('permission:delete', ['only' => ['destroy']]);
        $this->middleware('permission:update', ['only' => ['update']]);
    }

    public function index()
    {
        $roles = Role::all()->map(fn ($role) => [
            'id' => $role->id,
            'name' => $this->replaceString($role->name),
        ]);

        return $this->success([
            'status' => true,
            'data' => $roles,
            'message' => 'Successful',
        ], 'role');
    }

    public function replaceString($name): string
    {
        return ucwords(str_replace('-', ' ', $name));
    }
}
