<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


class UsersConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('role', 'like', '%' . $request->search . '%');
        }

        // Order by creation date in descending order
        $Users = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Configration/Users/Index', [
            'Users' => $Users,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        $Roles=Role::all();
        return Inertia::render('Configration/Users/Create', [
            'roles' => $Roles,
        ]);

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Fix the method name and ensure password_confirmation field is validated
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'nullable', // 'password_confirmation' is automatically checked by 'confirmed'
            'role' => 'nullable',
        ]);
    
        // dd($validated);
        // Create a new user with the validated data, hashing the password
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']), // Hash the password before storing
            'role' => $validated['role'],
        ]);
    
        // Redirect to the users index page
        return Redirect::route('setting.user.index')->with('success', 'User created successfully!');
    }
    

    /**
     * Display the specified resource.
     */
    public function edit(string $id)
    {
        $User=User::find($id);
        $Roles=Role::all();
        return Inertia::render('Configration/Users/Update', [
            'user' => $User,
            'roles' => $Roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'confirm_password' => 'nullable', // 'password_confirmation' is automatically checked by 'confirmed'
            'role' => 'nullable',
        ]);
        $User=User::find($id);
        $User->name=$validated['name'];
        $User->email=$validated['email'];
        $User->password=bcrypt($validated['password']);
        $User->role=$validated['role'];
        $User->update();


    
        // Redirect to the users index page
        return Redirect::route('setting.user.index')->with('success', 'User created successfully!');
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $User=User::find($id);
        $User->delete();
        return redirect()->back();
    }
}
