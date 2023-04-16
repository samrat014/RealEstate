<?php

namespace App\Http\Controllers\BackendApi;

use App\Classes\JsonRequest;
use App\Classes\UploadFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use JsonRequest, UploadFile;

    public function __construct()
    {
        $this->middleware('permission:read', ['only' => ['index', 'show']]);
        $this->middleware('permission:create', ['only' => ['store']]);
        $this->middleware('permission:delete', ['only' => ['destroy']]);
        $this->middleware('permission:update', ['only' => ['update']]);
    }

    public function index(Request $request)
    {
        $totalTransaction = 0;
        $totalIncome = 0;
        $totalExpenses = 0;

        if ($request->has('reconsilation')) {
            $transactions = Transaction::with('client', 'land')
                ->reconsilation($request->only('type', 'year_month'))
                ->get();
            foreach ($transactions as $transaction) {
                $totalTransaction += (int) $transaction->income + (int) $transaction->expenses;
                $totalIncome += (int) $transaction->income;
                $totalExpenses += (int) $transaction->expenses;
            }
        } else {
            $transactions = Transaction::with('client', 'land')
                ->land($request->only('land_id'))
                ->client($request->only('client_id'))
                ->filter($request->only('nepali_date', 'type'))
                ->paginate(10)
                ->withQueryString();
            $totalTransaction = DB::table('transactions')->sum(DB::raw('income + expenses'));
            $totalIncome = DB::table('transactions')->sum(DB::raw('income'));
            $totalExpenses = DB::table('transactions')->sum(DB::raw('expenses'));
        }

        return $this->success([
            'status' => true,
            'data' => $transactions,
            'totalTransaction'=> $totalTransaction,
            'totalIncome' => $totalIncome,
            'totalExpenses'=> $totalExpenses,
            'message' => 'Successful',
        ], 'transactions');
    }

    public function store(TransactionRequest $request)
    {
        // $request->validated();
        $expenses = $request->has('expenses') ? $request->expenses : '0';
        $income = $request->has('income') ? $request->income : '0';
        // checking the commision are empty or not if yes fill with 0
        $commission_rate = $request->filled('commission_rate') ? $request->commission_rate : '0';
        $total_commission = $request->filled('total_commission') ? $request->total_commission : '0';
        $total_commision_after_rate = $request->filled('total_commision_after_rate') ? $request->total_commision_after_rate : '0';

        $filePath = $request->hasFile('photo') ? $this->uploadFile($request->file('photo'), 'transaction') : null;

        Transaction::create([
            'client_id' => $request->client_id,
            'land_id' => $request->land_id,
            'price_per_anna' => $request->price_per_anna,
            'nepali_date' => $request->nepali_date,
            'type' => $request->type,
            'income' => $income,
            'expenses' => $expenses,
            'total_paid_amount' => $request->total_paid_amount,
            'commission_rate' => $commission_rate,
            'total_commission' => $total_commission,
            'photo' => $filePath,
            'total_commision_after_rate' => $total_commision_after_rate,
            'descriptions' => $request->descriptions,
            'ischeque' => $request->ischeque,
            'cheque_no' => $request->cheque_no,
            'cheque_exchange_date' => $request->cheque_exchange_date,
        ]);

        return $this->created([
            'status' => true,
            'message' => 'Transaction Stored Sucessufully',
        ]);
    }

    public function update(TransactionRequest $request)
    {
        $request->validated();
        $transaction = Transaction::find($request->only('id'))->first();
        if (! $transaction) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }

        $expenses = $request->has('expense') ? $request->expenses : '0';
        $income = $request->has('income') ? $request->income : '0';
        // checking the commision are  empty or not if yes fill with 0
        $commission_rate = $request->filled('commission_rate') ? $request->commission_rate : '0';
        $total_commission = $request->filled('total_commission') ? $request->total_commission : '0';
        $total_commision_after_rate = $request->filled('total_commision_after_rate') ? $request->total_commision_after_rate : '0';

        if ($request->hasFile('photo')) {
            $filePath = $request->hasFile('photo') ? $this->uploadFile($request->file('photo'), 'transaction') : null;
        }
        $transaction->update([
            'client_id' => $request->client_id,
            'land_id' => $request->land_id,
            'price_per_anna' => $request->price_per_anna,
            'nepali_date' => $request->nepali_date,
            'type' => $request->type,
            'income' => $income,
            'expenses' => $expenses,
            'total_paid_amount' => $request->total_paid_amount,
            'commission_rate' => $commission_rate,
            'total_commission' => $total_commission,
            'photo' => $filePath ?? $transaction->photo,
            'total_commision_after_rate' => $total_commision_after_rate,
            'descriptions' => $request->descriptions,
            'ischeque' => $request->ischeque,
            'cheque_no' => $request->cheque_no,
            'cheque_exchange_date' => $request->cheque_exchange_date,
        ]);

        return $this->success([
            'status' => true,
            'message' => 'Transaction Successfully Updated',
        ], 'land');
    }

    public function show($id)
    {
        $transaction = Transaction::with('land', 'client')->where('id', '=', $id)->first();
        if (! $transaction) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }

        return $this->success([
            'status' => true,
            'data' => $transaction,
            'message' => 'Successfully shown',
        ], 'transaction');
    }

    public function delete($id)
    {
        $transaction = Transaction::where('id', $id)->first();
        if (! $transaction) {
            return $this->notFound([
                'status' => false,
                'message' => '404 not found',
            ]);
        }
        $transaction->delete();

        return $this->success([
            'status' => true,
            'message' => 'land data deleted',
        ]);
    }
}
