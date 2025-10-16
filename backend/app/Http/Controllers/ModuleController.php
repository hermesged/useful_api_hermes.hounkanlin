<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function index(): JsonResponse
    {
        $modules = Module::select('id', 'name', 'description')->orderBy('id')->get();
        return response()->json($modules, 200);
    }

    public function activate($id, Request $request): JsonResponse
    {
        $module = Module::find($id);
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }
        $user = $request->user();
        DB::table('user_modules')->updateOrInsert(
            ['user_id' => $user->id, 'module_id' => $module->id],
            ['active' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        return response()->json(['message' => 'Module activated'], 200);
    }

    public function deactivate($id, Request $request): JsonResponse
    {
        $module = Module::find($id);
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }
        $user = $request->user();
        DB::table('user_modules')->updateOrInsert(
            ['user_id' => $user->id, 'module_id' => $module->id],
            ['active' => false, 'updated_at' => now(), 'created_at' => now()]
        );
        return response()->json(['message' => 'Module deactivated'], 200);
    }
}
