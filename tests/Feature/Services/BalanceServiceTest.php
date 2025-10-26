<?php

namespace Tests\Feature\Services;

use App\Exceptions\InsufficientFundsException;
use App\Models\Balance;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private BalanceService $balanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->balanceService = new BalanceService();
    }
    
    #[Test]
    public function it_creates_and_locks_balance_when_not_exists(): void
    {
        
        $user = User::factory()->create();
        //ызываем метод для несуществующего пользователя
        $balance = $this->balanceService->getOrCreateLockedBalance($user->id);

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'amount' => 0,
        ]);

        $this->assertEquals(0, $balance->amount);
    }

    #[Test]
    public function it_locks_existing_balance(): void
    {
        
        $user = User::factory()->create();
        Balance::create([
            'user_id' => $user->id,
            'amount' => 1000,
        ]);

        $balance = $this->balanceService->lockBalance($user->id);

        $this->assertEquals($balance->id, $user->id);
    }

    #[Test]
    public function it_increments_balance(): void
    {
        $user = User::factory()->create();
        $balance = Balance::create([
            'user_id' => $user->id,
            'amount' => 1000,
        ]);
       
        $this->balanceService->updateBalance($balance, 1000.99, true);

        $this->assertEquals(2000.99, $balance->refresh()->amount);
    }

    #[Test]
    public function it_decrements_balance(): void
    {
        $user = User::factory()->create();
        $balance = Balance::create([
            'user_id' => $user->id,
            'amount' => 1000,
        ]);
       
        $this->balanceService->updateBalance($balance, 1000, false);

        $this->assertEquals(0, $balance->refresh()->amount);
    }

    #[Test]
    public function it_get_balance(): void
    {
        $user = User::factory()->create();
        Balance::create([
            'user_id' => $user->id,
            'amount' => 1000,
        ]);
       
        $amount = $this->balanceService->getBalance($user->id);

        $this->assertEquals(1000, $amount);
    }

    #[Test]
    public function it_throws_exception_when_not_exist_balance(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->balanceService->getBalance(999);
    }

    #[Test]
    public function it_throws_exception_when_insufficient_funds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $user = User::factory()->create();
        $balance = Balance::create([
            'user_id' => $user->id,
            'amount' => 1000,
        ]);
       
        $this->balanceService->updateBalance($balance, 1001, false);
    }
}



