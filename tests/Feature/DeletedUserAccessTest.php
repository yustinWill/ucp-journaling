<?php

namespace Tests\Feature;

use App\Domains\User\Actions\AuthenticateUserAction;
use App\Domains\User\Services\UserService;
use App\Exceptions\AppException;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeletedUserAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('status')->nullable();
            $table->rememberToken();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_soft_delete_keeps_the_user_row_and_sets_inactive_status(): void
    {
        $user = $this->createUser();

        app(UserService::class)->softDelete($user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
            'status' => 'INACTIVE',
        ]);
        $this->assertNotNull(DB::table('users')->where('id', $user->id)->value('deleted_at'));
        $this->assertNull(User::query()->find($user->id));
        $this->assertNotNull(User::withTrashed()->find($user->id));
    }

    public function test_deleted_user_cannot_authenticate(): void
    {
        $user = $this->createUser();
        app(UserService::class)->softDelete($user->id);

        $this->expectException(AppException::class);

        app(AuthenticateUserAction::class)->login([
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = $this->createUser(['status' => 'INACTIVE']);

        $this->expectException(AppException::class);

        app(AuthenticateUserAction::class)->login([
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => null,
            'password' => 'password',
            'status' => 'ACTIVE',
        ], $attributes));
    }
}
