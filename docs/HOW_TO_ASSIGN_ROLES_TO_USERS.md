# How to Map Users to Roles (Spatie Permission)

## 1️⃣ Assign Role to User

### Method 1: Using assignRole()
```php
// Single role
$user->assignRole('admin');

// Multiple roles
$user->assignRole(['admin', 'manager']);
$user->assignRole('admin', 'manager');
```

### Method 2: Using syncRoles() (Replace all roles)
```php
// Replace all existing roles with new ones
$user->syncRoles(['admin', 'manager']);

// If user had 'editor' role, it will be removed
```

### Method 3: Direct Eloquent via SQL
```php
// Using sync with associative array (if you want to track assignments)
$user->syncRoles(['admin' => [], 'manager' => []]);
```

---

## 2️⃣ Remove Role from User

### Remove Single Role
```php
$user->removeRole('admin');
```

### Remove All Roles
```php
$user->syncRoles([]); // or
$user->getRoleNames()->each(fn($role) => $user->removeRole($role));
```

---

## 3️⃣ Check User Roles

### Check if User Has Role
```php
$user->hasRole('admin');                    // true/false
$user->hasRole(['admin', 'manager']);       // Has any of these?
$user->hasAllRoles(['admin', 'manager']);   // Has all of these?
$user->hasAnyRole(['admin', 'manager']);    // Has any of these?
```

### Get User Roles
```php
// Get all role names
$roleNames = $user->getRoleNames();  // Collection of role names
// Result: ["admin", "manager"]

// Get role objects
$roles = $user->roles;  // Collection of Role models

// Access role properties
foreach ($user->roles as $role) {
    echo $role->name;
    echo $role->guard_name;
}
```

---

## 4️⃣ In Controller (Practical Examples)

### Store User with Role
```php
public function store(Request $request)
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);
    
    // Assign role
    if ($request->has('role')) {
        $user->assignRole($request->role);
    }
    
    return redirect()->route('users.show', $user);
}
```

### Update User Role
```php
public function update(Request $request, User $user)
{
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);
    
    // Update role - replace all existing roles
    if ($request->has('role')) {
        $user->syncRoles([$request->role]);
    }
    
    return redirect()->route('users.show', $user);
}
```

### Assign Multiple Roles
```php
public function assignRoles(Request $request, User $user)
{
    $roles = $request->input('roles', []);
    
    // Replace all roles with these new ones
    $user->syncRoles($roles);
    
    return redirect()->back()->with('success', 'Roles assigned');
}
```

---

## 5️⃣ In Tinker (Quick Assignment)

```bash
php artisan tinker

# Get user
> $user = App\Models\User::find(1);

# Assign single role
> $user->assignRole('admin');

# Assign multiple roles
> $user->assignRole(['admin', 'manager']);

# Check roles
> $user->getRoleNames();
=> Illuminate\Support\Collection {#1234
     all: [
       "admin",
       "manager",
     ],
   }

# Sync (replace all)
> $user->syncRoles(['editor']);

# Check again
> $user->getRoleNames();
=> Illuminate\Support\Collection {
     all: [
       "editor",
     ],
   }
```

---

## 6️⃣ In View (Form to Assign Role)

### Simple Dropdown
```blade
<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="">-- Select Role --</option>
            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                <option value="{{ $role->name }}" 
                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">Save</button>
</form>
```

### Multiple Checkboxes
```blade
<form action="{{ route('users.assignRoles', $user) }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label>Roles</label>
        <div>
            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                <div class="form-check">
                    <input type="checkbox" name="roles[]" 
                        value="{{ $role->name }}" 
                        class="form-check-input"
                        id="role-{{ $role->id }}"
                        {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    <label class="form-check-label" for="role-{{ $role->id }}">
                        {{ ucfirst($role->name) }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Assign Roles</button>
</form>
```

---

## 7️⃣ Database Behind The Scenes

When you do `$user->assignRole('admin')`, it creates a record in the `model_has_roles` table:

```
model_has_roles table:
┌────────┬──────────────┬──────────────┬───────────┐
│ role_id│ model_type   │ model_id     │ model_type│
├────────┼──────────────┼──────────────┼───────────┤
│ 1      │ App\Models\User │ 5        │ web       │
│ 2      │ App\Models\User │ 5        │ web       │
└────────┴──────────────┴──────────────┴───────────┘
```

Where:
- `role_id` = ID dari role di table `roles`
- `model_id` = ID dari user di table `users`
- `model_type` = Class name dari model (App\Models\User)

---

## 8️⃣ Seeding Roles to Users

### In Seeder
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class AssignRolesToUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles first
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        
        // Assign roles to users
        $superAdmin = User::where('email', 'admin@example.com')->first();
        if ($superAdmin) {
            $superAdmin->assignRole('super_admin');
        }
        
        $manager = User::where('email', 'manager@example.com')->first();
        if ($manager) {
            $manager->assignRole('manager');
        }
        
        $normalUser = User::where('email', 'user@example.com')->first();
        if ($normalUser) {
            $normalUser->assignRole('user');
        }
    }
}
```

### Run Seeder
```bash
php artisan db:seed --class=AssignRolesToUsersSeeder
```

---

## 9️⃣ Difference: assignRole vs syncRoles

| Method | Behavior | Use Case |
|--------|----------|----------|
| `assignRole()` | **Adds** new roles (keeps existing) | Add single/multiple roles |
| `syncRoles()` | **Replace** all roles with new ones | Update all roles at once |
| `removeRole()` | **Remove** specific role | Remove one role |

**Example:**

```php
// User has: ["editor"]

$user->assignRole('admin');
// Now has: ["editor", "admin"]  ← ADDS

$user->syncRoles(['admin']);
// Now has: ["admin"]  ← REPLACES

$user->removeRole('admin');
// Now has: []  ← REMOVES
```

---

## 🔟 Complete User Management Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Create form
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }
    
    // Store user with role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
        
        // Assign role
        $user->assignRole($validated['role']);
        
        return redirect()->route('users.show', $user)
            ->with('success', 'User created and role assigned');
    }
    
    // Edit form
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }
    
    // Update user and role
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        // Update role - replace all roles
        $user->syncRoles([$validated['role']]);
        
        return redirect()->route('users.show', $user)
            ->with('success', 'User updated');
    }
    
    // Show user
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
```

---

## ✅ Quick Summary

```php
// Assign role
$user->assignRole('admin');
$user->assignRole(['admin', 'manager']);

// Get roles
$user->getRoleNames();        // ["admin", "manager"]
$user->roles;                 // Role collection

// Check roles
$user->hasRole('admin');
$user->hasAllRoles(['admin', 'manager']);

// Update roles (replace all)
$user->syncRoles(['editor']);

// Remove role
$user->removeRole('admin');
```

---

## 🎯 For Your Application

To quickly assign roles to existing users:

```bash
php artisan tinker

# Get all users
> $users = App\Models\User::all();

# Assign admin role to first user
> $users->first()->assignRole('super_admin');

# Assign different roles
> App\Models\User::find(1)->assignRole('super_admin');
> App\Models\User::find(2)->assignRole('admin');
> App\Models\User::find(3)->assignRole('manager');
> App\Models\User::find(4)->assignRole('user');

# Verify
> App\Models\User::find(1)->getRoleNames();
```

That's it! 🎉
