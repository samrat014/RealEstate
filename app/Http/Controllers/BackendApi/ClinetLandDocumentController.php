<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Classes\UploadFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientLandRequest;
use App\Http\Requests\ClientLandUpdateRequest;
use App\Models\ClinetLandDocument;
use App\Models\Land;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinetLandDocumentController extends Controller
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
    public function index(Request $request)
    {
        $lands = Land::with('landdocument')
            ->filter($request->only('location', 'kitta'))
            ->paginate(15);

        return $this->success([
            'status' => true,
            'data' => $lands,
            'message' => 'Successful',
        ], 'lands');
    }

    public function all()
    {
        $lands = Land::select('id', 'kitta', 'location', 'area')->get();

        return $this->success([
            'status' => true,
            'data' => $lands,
            'message' => 'Successful',
        ], 'lands');
    }

    public function store(ClientLandRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validated(); //* validation valied
            $land_id = Land::insertGetId([
                'client_id' => $request->client_id,
                'location' => $request->location,
                'kitta' => $request->kitta,
                'area' => $request->area,
                'price_per_area' => $request->price_per_area,
            ]);
            // uploading the  documents
            if ($request->hasFile('document')) {
                $documents = $request->file('document');
                foreach ($documents as $doc) {
                    $doc_Path = $this->UploadFile($doc, 'lands');
                    ClinetLandDocument::create([
                        'land_id'     => $land_id,
                        'document'     =>  $doc_Path,
                    ]);
                }
            }

            DB::commit();

            return $this->created([
                'status' => true,
                'message' => 'land created sucessufully',
            ]);
        } catch (\Exception $exc) {
            DB::rollBack();

            return response()->json([
                'Server Error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $clientlanddocument = Land::with('landdocument')
            ->where('id', $id)
            ->first();
        if (! $clientlanddocument) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }

        return $this->success([
            'status' => true,
            'data' => $clientlanddocument,
            'message' => 'Successfully shown',
        ], 'land');
    }

    public function update(ClientLandUpdateRequest $request)
    {
        $request->validated();
        $clientlanddocument = Land::find($request->only('id'))->first();
        if (! $clientlanddocument) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }
        $docment = ClinetLandDocument::where('land_id', $request->only('id'))->first();
        $clientlanddocument->update([
            'client_id' => $request->client_id,
            'location' => $request->location,
            'kitta' => $request->kitta,
            'area' => $request->area,
            'price_per_area' => $request->price_per_area,
        ]);

        // updating the  documents
        if ($request->hasFile('document')) {
            $documents = $request->file('document');
            foreach ($documents as $doc) {
                $doc_Path = $this->UploadFile($doc, 'lands');
                $docment->update([
                    'document' => $doc_Path,
                ]);
            }
        }

        return $this->success([
            'status' => true,
            'message' => 'Successfully Update',
        ], 'land');
    }

    public function destroy($id)
    {
        $clientlanddocument = Land::where('id', $id)->first();
        if (! $clientlanddocument) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }
        $clientlanddocument->delete();

        return $this->success([
            'status' => true,
            'message' => 'land data deleted',
        ]);
    }
}
