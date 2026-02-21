# Database Side: Mapping Users to Roles

Como mapear usuários para funções diretamente no banco de dados.

## 📊 Database Structure

### Tabelas Principais

Spatie Permission usa estas tabelas:

```
roles                    - Armazena as funções
├── id (PK)
├── name (unique)        - Exemplo: 'admin', 'manager'
├── guard_name
└── created_at, updated_at

model_has_roles          - Mapeia usuários para funções (IMPORTANTE!)
├── role_id (FK → roles.id)
├── model_id (FK → users.id)
├── model_type          - Sempre 'App\Models\User'
└── PRIMARY KEY (role_id, model_type, model_id)
```

---

## 1️⃣ Direct SQL Insert

### Insert Role
```sql
INSERT INTO roles (name, guard_name, created_at, updated_at) 
VALUES 
('admin', 'web', NOW(), NOW()),
('manager', 'web', NOW(), NOW()),
('user', 'web', NOW(), NOW());
```

### Assign User to Role
```sql
-- Get IDs first
SELECT id FROM roles WHERE name = 'admin';        -- Should return 1
SELECT id FROM users WHERE email = 'admin@test.com'; -- Should return 5

-- Insert into model_has_roles
INSERT INTO model_has_roles (role_id, model_type, model_id) 
VALUES 
(1, 'App\Models\User', 5);  -- Assign 'admin' role to user ID 5
```

### Assign Multiple Roles to One User
```sql
-- User ID 5 gets both 'admin' and 'manager' roles
INSERT INTO model_has_roles (role_id, model_type, model_id) 
VALUES 
(1, 'App\Models\User', 5),  -- admin role
(2, 'App\Models\User', 5);  -- manager role
```

---

## 2️⃣ Raw SQL Queries (via Laravel)

```php
// In controller or seeder
use Illuminate\Support\Facades\DB;

// Insert role
DB::table('roles')->insert([
    'name' => 'supervisor',
    'guard_name' => 'web',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Get IDs
$roleId = DB::table('roles')->where('name', 'admin')->first()->id;
$userId = DB::table('users')->where('email', 'admin@test.com')->first()->id;

// Assign user to role
DB::table('model_has_roles')->insert([
    'role_id' => $roleId,
    'model_type' => 'App\Models\User',
    'model_id' => $userId,
]);
```

---

## 3️⃣ Bulk Assignment via SQL

### Assign Multiple Users to One Role
```sql
-- Assign 'user' role (id=3) to all users
INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT 3, 'App\Models\User', id FROM users;
```

### Assign Multiple Roles to Multiple Users
```sql
-- Create temporary mapping: user_id -> role_ids
-- Then insert for each combination

-- First, clear existing assignments (optional)
DELETE FROM model_has_roles WHERE model_type = 'App\Models\User';

-- Assign admin role to specific users
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT 1, 'App\Models\User', id FROM users WHERE id IN (1, 2, 3);

-- Assign manager role to other users
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT 2, 'App\Models\User', id FROM users WHERE id IN (4, 5, 6);

-- Assign user role to remaining users
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT 3, 'App\Models\User', id FROM users WHERE id > 6;
```

---

## 4️⃣ Update/Remove Assignments

### Remove User from Role
```sql
DELETE FROM model_has_roles 
WHERE role_id = 1 
  AND model_type = 'App\Models\User' 
  AND model_id = 5;
```

### Remove All Roles from User
```sql
DELETE FROM model_has_roles 
WHERE model_type = 'App\Models\User' 
AND model_id = 5;
```

### Replace All Roles (Sync)
```sql
-- User 5 currently has admin and manager, change to just editor

-- Step 1: Remove current roles
DELETE FROM model_has_roles 
WHERE model_type = 'App\Models\User' 
AND model_id = 5;

-- Step 2: Insert new role
INSERT INTO model_has_roles (role_id, model_type, model_id) 
SELECT id, 'App\Models\User', 5 
FROM roles 
WHERE name = 'editor';
```

---

## 5️⃣ Query Existing Assignments

### See All Users and Their Roles
```sql
SELECT 
    u.id,
    u.name,
    u.email,
    r.name as role_name
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\Models\User'
LEFT JOIN roles r ON mhr.role_id = r.id
ORDER BY u.id, r.name;
```

### See User's Roles
```sql
SELECT r.name 
FROM roles r
JOIN model_has_roles mhr ON r.id = mhr.role_id
WHERE mhr.model_type = 'App\Models\User' 
AND mhr.model_id = 5;  -- User ID 5
```

### See Users with Specific Role
```sql
SELECT u.* 
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'admin' 
AND mhr.model_type = 'App\Models\User';
```

### Count Users per Role
```sql
SELECT 
    r.name,
    COUNT(mhr.model_id) as user_count
FROM roles r
LEFT JOIN model_has_roles mhr ON r.id = mhr.role_id 
    AND mhr.model_type = 'App\Models\User'
GROUP BY r.id, r.name
ORDER BY user_count DESC;
```

---

## 6️⃣ SQL Script to Setup Everything

```sql
-- 1. Create roles
INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES 
('super_admin', 'web', NOW(), NOW()),
('admin', 'web', NOW(), NOW()),
('manager', 'web', NOW(), NOW()),
('user', 'web', NOW(), NOW());

-- 2. Assign roles to users
-- Assuming you have users with IDs: 1, 2, 3, 4, 5

-- User 1 = super_admin
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT id, 'App\Models\User', 1 FROM roles WHERE name = 'super_admin';

-- User 2 = admin
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT id, 'App\Models\User', 2 FROM roles WHERE name = 'admin';

-- User 3 = manager
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT id, 'App\Models\User', 3 FROM roles WHERE name = 'manager';

-- User 4 & 5 = user (basic)
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT id, 'App\Models\User', 4 FROM roles WHERE name = 'user'
UNION ALL
SELECT id, 'App\Models\User', 5 FROM roles WHERE name = 'user';

-- 3. Verify assignments
SELECT 
    u.id,
    u.name,
    u.email,
    GROUP_CONCAT(r.name) as roles
FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id 
    AND mhr.model_type = 'App\Models\User'
LEFT JOIN roles r ON mhr.role_id = r.id
GROUP BY u.id, u.name, u.email;
```

---

## 7️⃣ Using PhpMyAdmin/Workbench

### Step 1: Create Roles (roles table)

Go to **roles table** → Insert:

| id | name | guard_name | created_at | updated_at |
|---|---|---|---|---|
| 1 | super_admin | web | 2026-02-21... | 2026-02-21... |
| 2 | admin | web | 2026-02-21... | 2026-02-21... |
| 3 | manager | web | 2026-02-21... | 2026-02-21... |
| 4 | user | web | 2026-02-21... | 2026-02-21... |

### Step 2: Assign Roles (model_has_roles table)

Go to **model_has_roles table** → Insert:

| role_id | model_type | model_id |
|---------|-----------|----------|
| 1 | App\Models\User | 1 |
| 2 | App\Models\User | 2 |
| 3 | App\Models\User | 3 |
| 4 | App\Models\User | 4 |

---

## 8️⃣ Via Laravel Seeder (Recommended)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class InstallRolesToUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create roles
        $roles = [
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'manager', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Assign roles to users
        $assignments = [
            1 => 'super_admin',  // User 1 = super_admin
            2 => 'admin',        // User 2 = admin
            3 => 'manager',      // User 3 = manager
            4 => 'user',         // User 4 = user
            5 => 'user',         // User 5 = user
        ];

        foreach ($assignments as $userId => $roleName) {
            $user = User::find($userId);
            if ($user) {
                $user->assignRole($roleName);
            }
        }
    }
}
```

Run:
```bash
php artisan db:seed --class=InstallRolesToUsersSeeder
```

---

## 9️⃣ Import from CSV/Excel

If you have a CSV with user-role mapping:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportUserRoles extends Command
{
    protected $signature = 'import:user-roles {file}';
    protected $description = 'Import user roles from CSV';

    public function handle()
    {
        $csv = Reader::createFromPath($this->argument('file'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $user = User::where('email', $record['email'])->first();
            
            if ($user) {
                // Replace all roles
                $user->syncRoles(explode(',', $record['roles']));
                $this->info("Updated {$user->email}");
            }
        }
    }
}
```

CSV Format:
```
email,roles
admin@test.com,super_admin
manager@test.com,manager
user@test.com,user
```

Run:
```bash
php artisan import:user-roles storage/imports/users-roles.csv
```

---

## 🔟 Verify Assignments

### Check in Database
```bash
mysql -u root -p your_database

SELECT * FROM roles;
SELECT * FROM model_has_roles;

-- Join to see mapping
SELECT u.name, r.name as role FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
LEFT JOIN roles r ON mhr.role_id = r.id;
```

### Check in Laravel
```bash
php artisan tinker

> Auth::loginUsingId(1);
> Auth::user()->getRoleNames();
=> Illuminate\Support\Collection {#1234
     all: ["super_admin"],
   }
```

---

## ⚠️ Important Notes

### Column Names
- `model_type` should be exactly: `App\Models\User` (with namespace)
- NOT: `users` or just `User`

### Primary Key
- `model_has_roles` has composite primary key: `(role_id, model_type, model_id)`
- You cannot have duplicate assignments

### Foreign Keys
- `role_id` must exist in `roles.id`
- `model_id` must exist in `users.id`
- Enable foreign key checks to prevent errors

### Guard Name
- Usually `web` for web application
- Make sure it matches when creating roles

---

## 📈 Summary Table

| Task | SQL | Laravel |
|------|-----|---------|
| Create Role | `INSERT INTO roles...` | `Role::create([...])` |
| Assign Role | `INSERT INTO model_has_roles...` | `$user->assignRole(...)` |
| Check Role | `SELECT...WHERE role_id=1` | `$user->hasRole('admin')` |
| Remove Role | `DELETE FROM model_has_roles...` | `$user->removeRole(...)` |
| Sync Roles | `DELETE then INSERT` | `$user->syncRoles(...)` |
| List All | `SELECT...FROM roles...` | `Role::all()` |

---

## 🎯 Quick Database Setup

**Fastest way to setup:**

1. Create roles:
```sql
INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES 
('super_admin', 'web', NOW(), NOW()),
('admin', 'web', NOW(), NOW()),
('manager', 'web', NOW(), NOW()),
('user', 'web', NOW(), NOW());
```

2. Assign to users (modify IDs):
```sql
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES 
(1, 'App\Models\User', 1),  -- User 1 = super_admin
(2, 'App\Models\User', 2),  -- User 2 = admin
(3, 'App\Models\User', 3),  -- User 3 = manager
(4, 'App\Models\User', 4);  -- User 4 = user
```

3. Verify:
```sql
SELECT u.name, r.name FROM users u
LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
LEFT JOIN roles r ON mhr.role_id = r.id;
```

Done! ✅
