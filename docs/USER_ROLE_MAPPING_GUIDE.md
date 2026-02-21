# 🗺️ WHERE TO MAP USER ROLES - Complete Guide

## 📍 Mapping Locations in Your Application

Your application stores roles in **TWO places**:

| System | Location | Purpose |
|--------|----------|---------|
| **Legacy** | `users.role_id` | Old system - stores role reference |
| **Spatie** | `model_has_roles` table | New system - Spatie permission mapping |

---

## 🎯 WHERE TO ADD ROLE ASSIGNMENT

### 1️⃣ In CreateUserAction (Create New User)

**File**: `app/Domains/User/Actions/CreateUserAction.php`

**Current State**:
```php
public function execute(array $data)
{
    return DB::transaction(function () use ($data) {
        $user_id = app(UserService::class)->create($data);

        app(UserCompanyService::class)->create([...]);
        app(UserAuditLogService::class)->create([...]);
    });
}
```

**ADD THIS**: Assign Spatie role after user creation

```php
public function execute(array $data)
{
    return DB::transaction(function () use ($data) {
        $user_id = app(UserService::class)->create($data);

        // 🆕 ASSIGN SPATIE ROLE
        $user = \App\Models\User::find($user_id);
        if (isset($data['role'])) {
            $role = \Spatie\Permission\Models\Role::find($data['role']);
            if ($role) {
                $user->assignRole($role->name);
            }
        }

        app(UserCompanyService::class)->create([...]);
        app(UserAuditLogService::class)->create([...]);
    });
}
```

---

### 2️⃣ In UpdateUserAction (Update Existing User)

**File**: `app/Domains/User/Actions/UpdateUserAction.php`

**Pattern**:
```php
public function execute(int $user_id, array $data)
{
    return DB::transaction(function () use ($user_id, $data) {
        app(UserService::class)->update($user_id, $data);

        // 🆕 SYNC SPATIE ROLE
        $user = \App\Models\User::find($user_id);
        if (isset($data['role'])) {
            $role = \Spatie\Permission\Models\Role::find($data['role']);
            if ($role) {
                $user->syncRoles([$role->name]);
            }
        }

        app(UserAuditLogService::class)->create([...]);
    });
}
```

---

### ✅ COMPLETE IMPLEMENTATION

#### 1. Update CreateUserAction
```php
<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserAuditLogService;
use App\Domains\User\Services\UserService;
use App\Domains\UserCompany\Services\UserCompanyService;
use App\Enums\ActionTypes;
use App\Enums\GeneralStatuses;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CreateUserAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create user and store user_id
            $user_id = app(UserService::class)->create($data);

            // Assign Spatie role to user
            $user = User::find($user_id);
            if (isset($data['role']) && $data['role']) {
                $role = Role::find($data['role']);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }

            // Create user company relationship
            app(UserCompanyService::class)->create([
                'user_id' => $user_id,
                'company_code' => session('user_company_code'),
                'status' => GeneralStatuses::ACTIVE,
                'default_company' => true,
            ]);

            // Create audit log
            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan User',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];

            app(UserAuditLogService::class)->create($log_data, $user_id);

            return $user_id;
        });
    }
}
```

#### 2. Update UpdateUserAction
```php
<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserAuditLogService;
use App\Domains\User\Services\UserService;
use App\Enums\ActionTypes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdateUserAction
{
    public function execute(int $user_id, array $data)
    {
        return DB::transaction(function () use ($user_id, $data) {
            // Get old data for audit log
            $oldUser = User::find($user_id);
            $oldData = $oldUser->toArray();

            // Update user data
            app(UserService::class)->update($user_id, $data);

            // Sync Spatie role
            $user = User::find($user_id);
            if (isset($data['role']) && $data['role']) {
                $role = Role::find($data['role']);
                if ($role) {
                    // syncRoles replaces existing roles
                    $user->syncRoles([$role->name]);
                }
            }

            // Create audit log
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Pembaruan User',
                'metadata' => json_encode([
                    'before' => $oldData,
                    'after' => $data,
                ]),
            ];

            app(UserAuditLogService::class)->create($log_data, $user_id);

            return $user_id;
        });
    }
}
```

---

## 📊 DATABASE MAPPING

### Table Structure for Role Assignment

**Table**: `model_has_roles`

| Column | Type | Purpose |
|--------|------|---------|
| `role_id` | integer | Foreign key to roles table |
| `model_type` | string | Model class (e.g., App\Models\User) |
| `model_id` | integer | User ID |

**Example Row**:
```
role_id: 2
model_type: App\Models\User
model_id: 1
```

This maps:
- User ID 1
- To Role ID 2 (e.g., "admin")

---

## 🔄 RELATED LOCATIONS

### Where Roles Are Defined
**File**: `database/migrations/2026_02_08_004339_generate_permissions_from_ui_menu.php`

```php
$roles = [
    'super_admin' => [...],
    'admin' => [...],
    'manager' => [...],
    'staff' => [...],
    'viewer' => [...],
];
```

### Where Roles Are Displayed
**File**: `app/Http/Controllers/MasterData/UserController.php`

```php
public function add()
{
    $roles = app(RoleService::class)->findAll();
    // ... pass to view
}

public function index()
{
    $roles = app(RoleService::class)->findAll();
    // ... pass to view
}
```

### Where User-Role Form Submission Happens
**File**: `app/Http/Controllers/MasterData/UserController.php`

```php
public function save(Request $request)
{
    $validated = $request->validate([
        'role' => 'required|exists:roles,id',
        // ... other fields
    ]);

    app(CreateUserAction::class)->execute($validated);
    // ↑ This is where you pass the role_id to be assigned
}
```

---

## 🛠️ IMPLEMENTATION STEPS

### Step 1: Check UpdateUserAction Exists
```bash
ls app/Domains/User/Actions/UpdateUserAction.php
```

If it doesn't exist, create it.

### Step 2: Update Both Actions
Add the Spatie role assignment code to:
- `CreateUserAction.php` (add after user creation)
- `UpdateUserAction.php` (add after user update)

### Step 3: Import Required Classes
```php
use App\Models\User;
use Spatie\Permission\Models\Role;
```

### Step 4: Test Role Assignment
```bash
# In Tinker, create a user and check role assignment
php artisan tinker

# Check user role
> $user = User::find(1);
> $user->roles;
> $user->hasRole('admin');

# Check in database
> DB::table('model_has_roles')->where('model_id', 1)->get();
```

---

## 📝 USER CREATION FORM

**File**: `resources/views/pages/master_data/user/add.blade.php`

Make sure the form includes:

```blade
<form action="{{ route('master_data.user.save') }}" method="POST">
    @csrf

    <input type="text" name="name" required>
    <input type="email" name="email" required>
    
    <!-- ROLE SELECTION -->
    <select name="role" required>
        <option value="">-- Select Role --</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
    </select>

    <input type="password" name="password" required>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Create User</button>
</form>
```

---

## ✨ VISUAL FLOW

```
User Submits Form
        ↓
UserController::save()
        ↓
Validate (including role)
        ↓
CreateUserAction::execute()
        ↓
UserService::create()  (stores in users table)
        ↓
🆕 User::assignRole()  (stores in model_has_roles table)
        ↓
UserCompanyService::create()
        ↓
UserAuditLogService::create()
        ↓
✓ User created with role assigned
```

---

## 🔍 VERIFY ROLE ASSIGNMENT

```php
// In Tinker
php artisan tinker

// Check that user has role
> $user = User::find(1);
> $user->hasRole('admin')
true

// Check all permissions from role
> $user->getAllPermissions()
Collection with 73 permissions

// Check user's roles
> $user->getRoleNames()
Collection ['admin']

// Check in model_has_roles table
> DB::table('model_has_roles')->where('model_id', 1)->get()
Collection with role assignment
```

---

## 🚨 IMPORTANT NOTES

### Current vs New System
- **role_id column**: Used for backward compatibility
- **model_has_roles table**: Used for Spatie permissions

### Why Both?
- `role_id` in users table stores the selected role (for display)
- `model_has_roles` table actually maps the Spatie role

### Best Practice
- Always assign roles via Spatie `assignRole()` or `syncRoles()`
- Keep role_id in users table for reference
- This allows gradual migration if needed

---

## 📚 Related Documentation

See these files for more context:
- `PERMISSION_QUICK_REFERENCE.md` - Role definitions
- `docs/SPATIE_PERMISSION_GUIDE.md` - How to use permissions
- `app/Helpers/PermissionHelper.php` - Helper methods
- `app/Http/Controllers/PermissionExampleController.php` - Examples

---

## 🎯 SUMMARY

| Item | Location |
|------|----------|
| **Where to assign roles** | `CreateUserAction.php` and `UpdateUserAction.php` |
| **How to assign** | `$user->assignRole($role->name)` |
| **How to update** | `$user->syncRoles([$role->name])` |
| **Table storing mapping** | `model_has_roles` |
| **Form location** | `resources/views/pages/master_data/user/add.blade.php` |
| **Controller handling form** | `app/Http/Controllers/MasterData/UserController.php` |
| **Role definitions** | Seeded via migration during `php artisan migrate` |

**Ready to implement?** Update your CreateUserAction and UpdateUserAction files with the code above! 🚀
