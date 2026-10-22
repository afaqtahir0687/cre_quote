<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of all users across the platform.
     */
    public function index(Request $request)
    {
        $query = User::with('shop');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();
        $shops = Shop::orderBy('name')->get();

        return view('super_admin.users.index', compact('users', 'shops'));
    }

    /**
     * Show form to create a user.
     */
    public function create()
    {
        $shops = Shop::active()->orderBy('name')->get();
        return view('super_admin.users.create', compact('shops'));
    }

    /**
     * Store newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,admin,employee',
            'shop_id' => 'nullable|required_unless:role,super_admin|exists:shops,id',
            'designation' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'shop_id' => $request->role === 'super_admin' ? $request->shop_id : $request->shop_id,
        ]);

        if (in_array($request->role, ['admin', 'employee']) && $user->shop_id) {
            Employee::create([
                'shop_id' => $user->shop_id,
                'user_id' => $user->id,
                'name' => $user->name,
                'designation' => $request->designation ?: ($request->role === 'admin' ? 'Shop Admin' : 'Staff'),
            ]);
        }

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show form to edit user.
     */
    public function edit(User $user)
    {
        $shops = Shop::orderBy('name')->get();
        $user->load(['shop', 'employee']);
        return view('super_admin.users.edit', compact('user', 'shops'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:super_admin,admin,employee',
            'shop_id' => 'nullable|required_unless:role,super_admin|exists:shops,id',
            'designation' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'shop_id' => $request->shop_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update or create linked employee
        if ($user->employee) {
            $user->employee->update([
                'name' => $user->name,
                'shop_id' => $user->shop_id,
                'designation' => $request->designation ?: $user->employee->designation,
            ]);
        } elseif (in_array($user->role, ['admin', 'employee']) && $user->shop_id) {
            Employee::create([
                'shop_id' => $user->shop_id,
                'user_id' => $user->id,
                'name' => $user->name,
                'designation' => $request->designation ?: ($user->role === 'admin' ? 'Shop Admin' : 'Staff'),
            ]);
        }

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
