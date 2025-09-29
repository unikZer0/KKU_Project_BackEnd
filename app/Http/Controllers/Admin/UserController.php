<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'uid' => 'required|string|unique:users,uid|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'phonenumber' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,staff,borrower',
            ]);

            $validated['password'] = bcrypt($validated['password']);
            $validated['ip_address'] = $request->ip();
            $validated['verified'] = $request->boolean('verified', false);

            $user = User::create($validated);

            // Log user creation
            $this->logUser('create', $user, [
                'description' => "เพิ่มผู้ใช้ใหม่ '{$user->name}' ({$user->email}) เข้าสู่ระบบด้วยบทบาท '{$user->role}'",
                'severity' => 'info'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'uid' => 'required|string|max:255|unique:users,uid,' . $id,
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'phonenumber' => 'required|string|max:255',
                'role' => 'required|in:admin,staff,borrower',
                'verified' => 'boolean',
            ]);

            if ($request->filled('password')) {
                $validated['password'] = bcrypt($request->password);
            }

            $user->update($validated);

            // Log user update
            $this->logUser('update', $user, [
                'description' => "แก้ไขข้อมูลผู้ใช้ '{$user->name}' ({$user->email}) เรียบร้อย",
                'severity' => 'info'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting the last admin
            if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete the last admin user'
                ], 400);
            }

            // Log user deletion
            $this->logUser('delete', $user, [
                'description' => "ลบผู้ใช้ '{$user->name}' ({$user->email}) ออกจากระบบเรียบร้อย",
                'severity' => 'warning'
            ]);

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
