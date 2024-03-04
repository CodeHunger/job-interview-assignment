<?php

namespace Tests\Feature;

use App\Events\LowProductStockEvent;
use App\Mail\LowProductStock;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LowStockEmailTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_send_low_stock_email() {
        Mail::fake();

        event(new LowProductStockEvent([1,2,3]));

        Mail::assertSent(LowProductStock::class, function (LowProductStock $mail) {
            return str($mail->render())->squish()->toString() === '<h1>De volgende producten zijn bijna op:</h1> <ul> <b>Vivian Bogan</b> - 1000 / 1000 <b>Rafael Bailey</b> - 1000 / 1000 <b>Dr. Louisa Senger DVM</b> - 1000 / 1000 </ul>';
        });
    }
}
