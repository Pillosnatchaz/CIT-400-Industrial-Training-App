<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\LogsActivity;

class UsersController extends Controller
{
    use LogsActivity;
    
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

        $user = User::create($validatedData);

        $this->logActivity('User', $user->id, 'created', ['data' => $validatedData]);

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

        $originalAttributes = $user->getOriginal();

        $validatedData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'phone' => 'nullable|max:255|numeric',
            'role' => 'required|in:admin,member',
        ]);

        $user->update($validatedData);

        $this->logActivity('User', $user->id, 'updated', [
            'old_attributes' => $originalAttributes,
            'new_attributes' => $user->getChanges() // This gives you only the attributes that changed, with their new values
        ]);

        return redirect()->route('users.index');
    }

    public function destroy (User $user)
    {
        $deletedUserData = $user->toArray();

        $user->delete();

        $this->logActivity('User', $user->id, 'deleted', ['data' => $deletedUserData]);

        // return redirect()->route('users.index');
        return response()->json(['message' => 'User deleted successfully']); // Return a JSON response

    }
}
