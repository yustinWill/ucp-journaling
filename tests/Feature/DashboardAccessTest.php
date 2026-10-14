<?php

namespace Tests\Feature;

use App\Domains\User\Actions\AuthenticateUserAction;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\EnsureDashboardAccess;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->default('ACTIVE');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'owner', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'admin', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'staff', 'guard_name' => 'web'],
        ]);

        Route::middleware(['web', 'auth', EnsureDashboardAccess::class])
            ->get('/dashboard-access-test', fn () => response('allowed'));
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    #[DataProvider('allowedDashboardRoleProvider')]
    public function test_owner_and_admin_roles_can_access_the_dashboard(int $roleId): void
    {
        $user = $this->createUser($roleId);

        $this->actingAs($user)
            ->get('/dashboard-access-test')
            ->assertOk()
            ->assertSeeText('allowed');
    }

    public function test_other_roles_cannot_access_the_dashboard(): void
    {
        $user = $this->createUser(3);

        $this->actingAs($user)
            ->get('/dashboard-access-test')
            ->assertForbidden();
    }

    #[DataProvider('allowedDashboardRoleProvider')]
    public function test_owner_and_admin_are_redirected_to_the_dashboard_after_login(int $roleId): void
    {
        $response = $this->loginResponseFor($this->createUser($roleId));

        $this->assertSame(route('dashboard.index'), $response->getTargetUrl());
    }

    public function test_other_roles_are_redirected_to_home_after_login(): void
    {
        $response = $this->loginResponseFor($this->createUser(3));

        $this->assertSame(route('home.view'), $response->getTargetUrl());
    }

    /**
     * @return array<string, array{int}>
     */
    public static function allowedDashboardRoleProvider(): array
    {
        return [
            'owner' => [1],
            'admin' => [2],
        ];
    }

    private function createUser(int $roleId): User
    {
        $user = User::query()->create([
            'name' => "Role {$roleId}",
            'email' => "role{$roleId}@example.com",
            'password' => 'password',
            'status' => 'ACTIVE',
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        return $user;
    }

    private function loginResponseFor(User $user): RedirectResponse
    {
        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];
        $request = Request::create('/login', 'POST', $credentials);
        $request->setUserResolver(fn () => $user);

        $authenticateUserAction = $this->mock(AuthenticateUserAction::class);
        $authenticateUserAction->shouldReceive('login')
            ->once()
            ->with($credentials)
            ->andReturnTrue();

        return app(LoginController::class)->authenticate($request, $authenticateUserAction);
    }
}
