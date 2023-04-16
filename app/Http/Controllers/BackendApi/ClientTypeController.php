<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientTypeRequest;
use App\Models\ClientType;
use Illuminate\Support\Facades\Auth;

class ClientTypeController extends Controller
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
        $clientTypes = ClientType::paginate(15);

        return $this->success([
            'status' => true,
            'data' => $clientTypes,
            'message' => 'Successful',
        ], 'clientTypes');
    }

    public function store(ClientTypeRequest $request)
    {
        $request->validated();
        ClientType::create([
            'type' => $request->type,
        ]);

        return $this->created([
            'status' => true,
            'message' => 'client-type created sucessufully',
        ]);
    }

    public function show($id)
    {
        $client = ClientType::where('id', $id)->first();
        if (! $client) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }

        return $this->success([
            'status' => true,
            'data' => $client,
            'message' => 'Successfully shown',
        ], 'clientTypes');
    }

    public function update(ClientTypeRequest $request)
    {
        // dd($request->user()->permissions);
        $request->validated();
        $client = ClientType::find($request->only('id'));
        if ($client->isEmpty()) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }
        ClientType::where('id', $request->id)->update([
            'type' => $request->type,
        ]);

        return $this->success([
            'status' => true,
            'message' => 'Successfully Update',
        ], 'clientTypes');
    }

    public function destroy($id)
    {
        $client = ClientType::where('id', $id)->first();
        if (! $client) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }
        $client->delete();

        return $this->success([
            'status' => true,
            'message' => 'Client type deleted',
        ]);
    }
}
