<?php

namespace Tests\Feature;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConcurrentTransferTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_concurrent_transfer(): void
    {
        $this->seed();
    

        // dd(User::all());
        Concurrency::run([
            fn() => Http::post('http://nginx/api/deposit', [
                'user_id' => 1,
                'amount' => 4000,
                'comment' => 'transfer 1',
            ]),
            fn() => Http::post('http://nginx/api/deposit', [
                'user_id' => 3,
                'amount' => 4000,
                'comment' => 'transfer 1',
            ]),
            fn() => Http::post('http://nginx/api/transfer', [
                'from_user_id' => 1,
                'to_user_id' => 2,
                'amount' => 2000,
                'comment' => 'transfer 1',
            ]),

            fn() => Http::post('http://nginx/api/transfer', [
                    'from_user_id' => 3,
                    'to_user_id' => 2,
                    'amount' => 1000,
                    'comment' => 'perevod 1'
            ]),
            
        ]);

        $balanceTo = Balance::where('user_id', 2)->first()->amount;
        $this->assertEquals(3000, $balanceTo); // 2000 + 1000
    }
}
