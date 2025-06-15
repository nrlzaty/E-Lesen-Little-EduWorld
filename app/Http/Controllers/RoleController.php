<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('code_name', 'like', '%' . $request->search . '%');
        }

        // Order by creation date in descending order
        $Roles = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Configration/Roles/Index', [
            'Roles' => $Roles,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        return Inertia::render('Configration/Roles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Fix the method name and ensure password_confirmation field is validated
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_name' => 'required',
            'is_active' => 'nullable',
        ]);

        // dd($validated);
        // Create a new user with the validated data, hashing the password
        Role::create([
            'name' => $validated['name'],
            'code_name' => $validated['code_name'],
            'is_active' => $validated['is_active'], // Hash the password before storing
        ]);

        // Redirect to the users index page
        return Redirect::route('setting.role.index')->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $Role = Role::find($id);
        return Inertia::render('Configration/Roles/Update', [
            'role' => $Role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_name' => 'required',
            'is_active' => 'nullable',
        ]);

        // dd($validated);
        // Create a new user with the validated data, hashing the password
        $Role = Role::find($id);
        $Role->name = $validated['name'];
        $Role->code_name = $validated['code_name'];
        $Role->is_active = $validated['is_active'];

        $Role->update();

        // Redirect to the users index page
        return Redirect::route('setting.role.index')->with('success', 'Role created successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Role = Role::find($id);
        $Role->delete();
        return redirect()->back();
    }
}
