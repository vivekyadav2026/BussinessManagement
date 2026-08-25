<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user.roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->latest()->paginate(15);
        return view('organization.employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Role::where('organization_id', auth()->user()->organization_id)
                     ->where('name', '!=', 'Organization Admin')
                     ->get();
                     
        $locations = \App\Models\Location::where('organization_id', auth()->user()->organization_id)->get();
                     
        return view('organization.employees.create', compact('roles', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'employee_code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'locations' => 'nullable|array',
            
            // Login account fields
            'create_account' => 'nullable|boolean',
            'password' => ['nullable', 'required_if:create_account,1', Password::defaults()],
            'role' => 'nullable|required_if:create_account,1|exists:roles,name',
        ]);

        if ($request->create_account) {
            $request->validate(['email' => 'required|email|unique:users,email']);
        }

        DB::transaction(function () use ($request) {
            $userId = null;
            
            if ($request->create_account) {
                $user = User::create([
                    'organization_id' => auth()->user()->organization_id,
                    'name' => trim($request->first_name . ' ' . $request->last_name),
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->assignRole($request->role);
                
                if ($request->has('locations')) {
                    $user->locations()->sync($request->locations);
                }
                $userId = $user->id;
            }

            Employee::create([
                'organization_id' => auth()->user()->organization_id,
                'user_id' => $userId,
                'location_id' => $request->locations[0] ?? null,
                'employee_code' => $request->employee_code,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'designation' => $request->designation,
                'joining_date' => $request->joining_date,
                'address' => $request->address,
                'status' => 'active',
            ]);
        });

        return redirect()->route('organization.employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('organization.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $roles = Role::where('organization_id', auth()->user()->organization_id)
                     ->where('name', '!=', 'Organization Admin')
                     ->get();
                     
        $locations = \App\Models\Location::where('organization_id', auth()->user()->organization_id)->get();
        $userLocations = $employee->user ? $employee->user->locations->pluck('id')->toArray() : [$employee->location_id];
                     
        return view('organization.employees.edit', compact('employee', 'roles', 'locations', 'userLocations'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'employee_code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'locations' => 'nullable|array',
            
            // Login account fields
            'role' => 'nullable|exists:roles,name',
            'reset_password' => 'nullable|boolean',
            'password' => ['nullable', 'required_if:reset_password,1', Password::defaults()],
        ]);

        if ($employee->user_id && $request->email !== $employee->user->email) {
            $request->validate(['email' => 'required|email|unique:users,email,'.$employee->user_id]);
        }

        DB::transaction(function () use ($request, $employee) {
            $employee->update([
                'employee_code' => $request->employee_code,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'designation' => $request->designation,
                'joining_date' => $request->joining_date,
                'address' => $request->address,
                'location_id' => $request->locations[0] ?? null,
            ]);

            if ($employee->user) {
                $userData = [
                    'name' => trim($request->first_name . ' ' . $request->last_name),
                    'email' => $request->email,
                ];
                
                if ($request->reset_password && $request->password) {
                    $userData['password'] = Hash::make($request->password);
                }
                
                $employee->user->update($userData);
                
                if ($request->role) {
                    $employee->user->roles()->detach();
                    $employee->user->assignRole($request->role);
                }
                
                $employee->user->locations()->sync($request->locations ?? []);
            }
        });

        return redirect()->route('organization.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function toggleStatus(Employee $employee)
    {
        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
        $employee->update(['status' => $newStatus]);
        
        return back()->with('success', 'Employee status updated.');
    }
}
