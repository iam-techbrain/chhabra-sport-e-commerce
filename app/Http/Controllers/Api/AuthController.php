<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user and save into MySQL users table.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Enforce role to be customer by default, preventing privilege escalation
        $role = 'customer';

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ]
        ], 201);
    }

    /**
     * Login user checking MySQL users table.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email/Phone and Password are required.'
            ], 422);
        }

        $loginInput = trim($request->email);

        // Find user by email or phone
        $user = User::where('email', strtolower($loginInput))
            ->orWhere('phone', $loginInput)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully!',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials. Please check your email/phone and password.'
        ], 401);
    }

    /**
     * Get list of all users for admin dashboard.
     */
    public function index()
    {
        $users = User::orderBy('id', 'asc')->get()->map(function($user) {
            $ordersCount = \App\Models\Order::where('user_id', $user->id)
                ->orWhere('customer_email', strtolower($user->email))
                ->orWhere(function($q) use ($user) {
                    if (!empty($user->phone)) {
                        $q->where('customer_phone', $user->phone);
                    }
                })
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?: 'N/A',
                'role' => ucfirst($user->role ?: 'customer'),
                'orders_count' => $ordersCount,
                'status' => 'active',
                'registered_at' => $user->created_at ? $user->created_at->format('d M Y') : 'N/A'
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
