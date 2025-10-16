<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        return response()->json(['user_id' => $wallet->user_id, 'balance' => (float)$wallet->balance], 200);
    }

    public function topup(Request $request): JsonResponse
    {
        $validated = $request->validate(['amount' => 'required|numeric|min:0.01|max:10000']);
        $user = $request->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        DB::transaction(function () use ($wallet, $validated, &$transaction) {
            $wallet->balance += $validated['amount'];
            $wallet->save();
            $transaction = Transaction::create([
                'sender_id' => null,
                'receiver_id' => $wallet->user_id,
                'amount' => $validated['amount'],
                'status' => 'success',
            ]);
        });
        return response()->json([
            'user_id' => $wallet->user_id,
            'balance' => (float)$wallet->balance,
            'topup_amount' => (float)$validated['amount'],
            'created_at' => now()->toIso8601String(),
        ], 201);
    }

    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);
        $sender = $request->user();
        if ($validated['receiver_id'] === $sender->id) {
            return response()->json(['message' => 'Cannot transfer to yourself'], 400);
        }
        $receiver = User::find($validated['receiver_id']);
        $senderWallet = Wallet::firstOrCreate(['user_id' => $sender->id], ['balance' => 0]);
        $receiverWallet = Wallet::firstOrCreate(['user_id' => $receiver->id], ['balance' => 0]);
        if ($senderWallet->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }
        DB::transaction(function () use ($senderWallet, $receiverWallet, $validated, $sender, $receiver, &$transaction) {
            $senderWallet->balance -= $validated['amount'];
            $receiverWallet->balance += $validated['amount'];
            $senderWallet->save();
            $receiverWallet->save();
            $transaction = Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $validated['amount'],
                'status' => 'success',
            ]);
        });
         return response()->json([
            'transaction_id' => $transaction->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => (float)$validated['amount'],
            'status' => 'success',
            'created_at' => $transaction->created_at->toIso8601String(),
        ], 201);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();
        $transactions = Transaction::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'sender_id', 'receiver_id', 'amount', 'status', 'created_at']);
        return response()->json($transactions, 200);
    }
}