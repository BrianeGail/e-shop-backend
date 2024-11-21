<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Ensure unique email
            'password' => 'required|string|min:8', // Password must be at least 8 characters
        ]);

        // Check if the email already exists
        $existingUser = User::where('email', $validatedData['email'])->first();
        if ($existingUser) {
            return response()->json(['error' => 'Email already in use'], 400); // Send error if email exists
        }

        // Create a new user instance
        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']); // Hash the password
        $user->save(); // Save the user to the database

      
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('YourAppName')->plainTextToken, // Issue a token if using Sanctum
        ], 201);
    }

    public function login(Request $request)
    {
     
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
      
        $user = User::where('email', $request->email)->first();
    
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is not matched',
            ], 401);
        }
    
     
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
    
}
