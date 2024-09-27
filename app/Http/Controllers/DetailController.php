<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;


use Illuminate\Http\Request;

class DetailController extends Controller
{
    protected $users = []; // Array to store user details

    public function index(Request $request)
    {
        $roles = Role::all();
        $users = User::with('role')->get();
        
        // You can also pass a flag to check if there are no users
        $noUsers = $users->isEmpty(); // Check if the collection is empty

        return view('users.userview', compact('users', 'noUsers', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'description' => 'nullable|string',
            'role_id' => 'required|integer',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = new User($request->only(['name', 'email', 'phone', 'description', 'role_id']));

        // Handle file upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }  else {
            $user->profile_image = 'images/default-profile.png';
        }

        $user->save();

        return response()->json(['success' => true]);
    }

    public function fetchUsers()
    {
        $users = User::with('role')->get();
        return $users;
        
    }

}
