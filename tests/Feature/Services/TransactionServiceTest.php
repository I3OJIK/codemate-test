<?php

namespace Tests\Feature\Services;

use App\DTOs\Requests\InternalTransactionDto;
use App\DTOs\Requests\TransferDto;
use App\Enum\TransactionStatus;
use App\Exceptions\InsufficientFundsException;
use App\Models\Balance;
use App\Models\User;
use App\Services\BalanceService;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private BalanceService $balanceService;
    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->balanceService = new BalanceService();
        $this->transactionService = new TransactionService($this->balanceService);
    }
    
    #[Test]
    public function it_performs_deposit_and_creates_transaction(): void
    {
        $user = User::factory()->create();

        $dto = new InternalTransactionDto(
            userId: $user->id,
            amount: 1000,
            comment: 'Test deposit'
        );
        
        $this->transactionService->deposit($dto);

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'amount' => 1000,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'status' => TransactionStatus::DEPOSIT,
            'amount' => 1000,
        ]);
    }
    
   

    #[Test]
    public function it_performs_withdrawal_and_creates_transaction(): void
    {
        $user = User::factory()->create();
        Balance::create([
            'user_id' => $user->id,
            'amount' => 1000.55,
        ]);

        $dto = new InternalTransactionDto(
            userId: $user->id,
            amount: 1000,
            comment: 'Test withdraw'
        );
        
        $this->transactionService->withdraw($dto);

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'amount' => 0.55,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'status' => TransactionStatus::WITHDRAW,
            'amount' => 1000,
        ]);
    }

    #[Test]
    public function it_performs_transfer_and_creates_transactions(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        Balance::insert([
            [
                'user_id' => $fromUser->id,
                'amount' => 1000.55,
            ],
            [
                'user_id' => $toUser->id,
                'amount' => 0,
            ],
        ]);

        $dto = new TransferDto(
            fromUserId: $fromUser->id,
            toUserId: $toUser->id,
            amount: 1000,
            comment: 'Test transfer'
        );
        
        $this->transactionService->transfer($dto);

        // проверка баланса
        $this->assertEquals(0.55, Balance::where('user_id', $fromUser->id)->first()->amount);
        $this->assertEquals(1000, Balance::where('user_id', $toUser->id)->first()->amount);

        //транзакция отправки средств от отправителя
        $this->assertDatabaseHas('transactions', [
            'user_id' => $fromUser->id,
            'status' => TransactionStatus::TRANSFER_OUT,
            'related_user_id' => $toUser->id,
            'amount' => 1000,
        ]);

        //транзакция отправки средств получателю
        $this->assertDatabaseHas('transactions', [
            'user_id' => $toUser->id,
            'status' => TransactionStatus::TRANSFER_IN,
            'related_user_id' => $fromUser->id,
            'amount' => 1000,
        ]);
    }

    #[Test]
    public function it_throws_exception_when_transfer_from_nonexistent_balance(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        Balance::create([
            'user_id' => $fromUser->id,
            'amount' => 1000.55,
        ]);

        $dto = new TransferDto(
            fromUserId: $fromUser->id,
            toUserId: $toUser->id,
            amount: 1000,
            comment: 'Test transfer'
        );
        
        $this->transactionService->transfer($dto);
    }

    #[Test]
    public function it_throws_exception_when_transfer_with_insufficient_funds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        Balance::insert([
            [
                'user_id' => $fromUser->id,
                'amount' => 1000.55,
            ],
            [
                'user_id' => $toUser->id,
                'amount' => 0,
            ],
        ]);

        $dto = new TransferDto(
            fromUserId: $fromUser->id,
            toUserId: $toUser->id,
            amount: 1300,
            comment: 'Test transfer'
        );
        
        $this->transactionService->transfer($dto);
    }
   
}



