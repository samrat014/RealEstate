<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Classes\UploadFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class ClinetController extends Controller
{
    use JsonRequest, UploadFile;

    public function __construct()
    {
        $this->middleware('permission:read', ['only' => ['index', 'show']]);
        $this->middleware('permission:create', ['only' => ['store']]);
        $this->middleware('permission:delete', ['only' => ['destroy']]);
        $this->middleware('permission:update', ['only' => ['update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::with('clientType', 'clientDocument')->paginate(15);

        return $this->success([
            'status' => true,
            'data' => $clients,
            'message' => 'Successful',
        ], 'clients');
    }

    public function all()
    {
        $clients = Client::select('id', 'name')->get();

        return $this->success([
            'status' => true,
            'data' => $clients,
            'message' => 'Successful',
        ], 'clients');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * //     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated(); //* validation valied
            $clients_id = Client::insertGetId([
                'client_types_id' => $request->client_types_id,
                'name' => $request->name,
                'phone_no' => $request->phone_no,
                'phone_no_1' => $request->phone_no_1,
                'citizenship_no' => $request->citizenship_no,
                'passport_no' => $request->passport_no,
                'license_no' => $request->license_no,
                'permanent_address' => $request->permanent_address,
                'temporary_address' => $request->temporary_address,
            ]);
            //! uploading image
            $citizenshipPath = $this->UploadFile($request->file('citizenship'), 'citizenship');
            $photoPath = $this->UploadFile($request->file('photo'), 'photo');
            $panPath = $request->hasFile('pan') ? $this->uploadFile($request->file('pan'), 'pan') : null;
            $passportPath = $request->hasFile('passport') ? $this->UploadFile($request->file('passport'), 'passport') : null;

            ClientDocument::create([
                'client_id' => $clients_id,
                'citizenship' => $citizenshipPath,
                'pan' => $panPath,
                'passport' => $passportPath,
                'photo' => $photoPath,
            ]);
            DB::commit();

            return $this->created([
                'status' => true,
                'message' => 'client created sucessufully',
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json([
                'Server Error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::with('clientType', 'clientDocument')->where('id', '=', $id)->first();
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
        ], 'client');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * //     * @return \Illuminate\Http\Response
     */
    public function update(ClientUpdateRequest $request)
    {
        $request->validated();
        DB::beginTransaction();
        try {
            $client = Client::find($request->only('id'))->first();
            if (! $client) {
                return $this->notFound([
                    'status' => false,
                    'message' => '404 not found',
                ]);
            }
            $clientDocument = ClientDocument::where('client_id', $request->only('id'))->first();

            $client->update([
                'client_types_id' => $request->client_types_id,
                'name' => $request->name,
                'phone_no' => $request->phone_no,
                'phone_no_1' => $request->phone_no_1,
                'citizenship_no' => $request->citizenship_no,
                'passport_no' => $request->passport_no,
                'license_no' => $request->license_no,
                'permanent_address' => $request->permanent_address,
                'temporary_address' => $request->temporary_address,
            ]);

            if ($request->hasFile('citizenship')) {
                $request->validate([
                    'citizenship' => 'mimes:pdf,png,jpeg,jpg|max:5048',
                ]);
                $citizenshipPath = $this->UploadFile($request->file('citizenship'), 'citizenship');
            }
            if ($request->hasFile('photo')) {
                $request->validate([
                    'photo' => 'required|mimes:pdf,png,jpeg,jpg|max:5048',
                ]);
                $photoPath = $this->UploadFile($request->file('photo'), 'photo');
            }
            if ($request->hasFile('pan')) {
                $panPath = $request->hasFile('pan') ? $this->uploadFile($request->file('pan'), 'pan') : null;
            }
            if ($request->hasFile('passport')) {
                $passportPath = $request->hasFile('passport') ? $this->UploadFile($request->file('passport'), 'passport') : null;
            }

            $clientDocument->update([
                'citizenship' => $citizenshipPath ?? $clientDocument->citizenship,
                'pan' => $panPath ?? $clientDocument->pan,
                'passport' => $passportPath ?? $clientDocument->passport,
                'photo' => $photoPath ?? $clientDocument->photo,
            ]);

            DB::Commit();

            return $this->success([
                'status' => true,
                'message' => 'Successfully Update',
            ], 'client');
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'Server Error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::where('id', $id)->first();
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
