<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
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
        $admin = Admin::with('roles')->paginate(15);

        return $this->success([
            'status' => true,
            'data' => $admin,
            'message' => 'Successful',
        ], 'user');
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $pwd = $request->get('password');
            $user = Admin::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($pwd),
            ]);
            $roleId = $request->get('role_id');
            $role = Role::where('id', $roleId)->first();
            $user->assignRole($role);

            DB::commit();

            return $this->created([
                'status' => true,
                'message' => 'user created sucessufully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'Server Error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $user = Admin::where('id', $id)->first();
        if (! $user) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }

        return $this->success([
            'status' => true,
            'data' => $user,
            'message' => 'Successfully shown',
        ], 'user');
    }

     public function destroy($id)
     {
         $user = Admin::where('id', $id)->first();

         if (! $user) {
             return $this->notFound([
                 'status' => false,
                 'message' => '404 not found',
             ]);
         }
         $user->delete();

         return $this->success([
             'status' => true,
             'message' => 'user deleted',
         ]);
     }

     public function update(UserUpdateRequest $request)
     {
         $user = Admin::findorfail($request->id);
         if (! $user) {
             return $this->notFound([
                 'status' => false,
                 'message'=> '404 not found',
             ]);
         }

         $user->update([
             'name' => $request->name,
             'email' => $request->email,
         ]);
         $roleId = $request->get('role_id');
         $role = Role::where('id', $roleId)->first();
         $user->syncRoles($role);

         return $this->success([
             'status' => true,
             'message'=> 'updated',
         ], 'user');
     }
}
