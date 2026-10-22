<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Employee::latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(10)->withQueryString();
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        $shopId = current_shop_id();

        // Create user with employee role
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['username'] . '@example.com', // Auto-generate email from username
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'role' => 'employee',
            'shop_id' => $shopId,
        ]);

        // Create employee linked to user
        $validated['user_id'] = $user->id;
        $validated['shop_id'] = $shopId;
        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user');
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . ($employee->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
        ]);

        // Update or create user
        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['username'] . '@example.com', // Auto-generate email from username
        ];
        
        if (!empty($validated['password'])) {
            $userData['password'] = bcrypt($validated['password']);
        }
        
        if ($employee->user) {
            $employee->user->update($userData);
        } else {
            $userData['role'] = 'employee';
            $user = \App\Models\User::create($userData);
            $employee->update(['user_id' => $user->id]);
        }

        // Update employee
        $employee->update([
            'name' => $validated['name'],
            'designation' => $validated['designation'],
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        
        $employee->delete();

        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Employee',
            'description' => "Deleted Employee: {$name}"
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
