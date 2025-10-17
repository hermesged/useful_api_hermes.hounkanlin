<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortenRequest;
use App\Models\ShortLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortLinkController extends Controller
{
    public function shorten(ShortenRequest $request): JsonResponse
    {
        $user = $request->user();
        $code = $request->custom_code ?? Str::random(6);
        while (ShortLink::where('code', $code)->exists()) {
            $code = Str::random(6);
        }
        $link = ShortLink::create([
            'user_id' => $user->id,
            'original_url' => $request->original_url,
            'code' => $code,
            'clicks' => 0,
        ]);
        return response()->json([
            'id' => $link->id,
            'user_id' => $link->user_id,
            'original_url' => $link->original_url,
            'code' => $link->code,
            'clicks' => $link->clicks,
            'created_at' => $link->created_at->toIso8601String(),
        ], 201);
    }

    public function redirect($code)
    {
        $link = ShortLink::where('code', $code)->first();
        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }
        $link->increment('clicks');
        return redirect()->away($link->original_url, 302);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $links = ShortLink::where('user_id', $user->id)
            ->select('id', 'original_url', 'code', 'clicks', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($links, 200);
    }

    public function destroy($id, Request $request): JsonResponse
    {
        $user = $request->user();
        $link = ShortLink::where('id', $id)->where('user_id', $user->id)->first();
        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }
        $link->delete();
        return response()->json(['message' => 'Link deleted successfully'], 200);
    }
}


