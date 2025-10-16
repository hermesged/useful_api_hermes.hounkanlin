<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Module;

class CheckModuleActive
{
    public function handle(Request $request, Closure $next)
    {
        $routeParams = $request->route() ? $request->route()->parameters() : [];
        $moduleId = null;
        if (isset($routeParams['module_id'])) {
            $moduleId = $routeParams['module_id'];
        } elseif (isset($routeParams['module'])) {
            $moduleId = $routeParams['module'];
        } elseif (isset($routeParams['id'])) {
            $moduleId = $routeParams['id'];
        } elseif ($request->has('module_id')) {
            $moduleId = $request->get('module_id');
        }
        if (!$moduleId) {
            return response()->json(['error' => 'Module inactive. Please activate this module to use it.'], 403);
        }
        $module = Module::find($moduleId);
        if (!$module) {
            return response()->json(['error' => 'Module not found'], 404);
        }
        $user = $request->user();
        $record = $user->modules()->where('modules.id', $module->id)->first();
        if (!$record || !$record->pivot->active) {
            return response()->json(['error' => 'Module inactive. Please activate this module to use it.'], 403);
        }
        return $next($request);
    }
}