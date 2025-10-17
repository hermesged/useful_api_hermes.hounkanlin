<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json($users);
    }


    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }


    public function store(Request $request)
    {
        if (!auth()->user()->is_admin) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8'
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }



    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $authUser = auth()->user();

        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8'
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->fill($request->except(['password']));
        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => $user
        ]);
    }



    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();
        
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }


}

