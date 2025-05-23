<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Forbidden');
        }

        return $dataTable->render('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store (Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'phone' => 'nullable|max:255|numeric',
            'role' => 'required|in:admin,member',
            'password' => 'required|max:255',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('users.index');
    }

    public function edit (User $user) 
    {
        return view('users.edit', compact('users'));
    }

    public function show (User $user)
    {
        return view('users.show', compact('users'));
    }

    public function update (Request $request, User $user)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'phone' => 'nullable|max:255|numeric',
            'role' => 'required|in:admin,member',
        ]);

        $user->update($validatedData);

        return redirect()->route('users.index');
    }

    public function destroy (User $user)
    {
        $user->delete();

        // return redirect()->route('users.index');
        return response()->json(['message' => 'User deleted successfully']); // Return a JSON response

    }
}
