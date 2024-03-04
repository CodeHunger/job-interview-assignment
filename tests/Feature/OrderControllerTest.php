<?php

namespace Tests\Feature;

use App\Events\LowProductStockEvent;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_get_order_index(): void {
        $expectedResult = '[{"products":[{"id":1,"properties":null,"productType":"solarPanel","amount":5},{"id":3,"properties":null,"productType":"solarPanel","amount":5},{"id":25,"properties":null,"productType":"inverter","amount":1}],"id":1,"created":"2024-03-04 09:58:53"}]';
        $response = $this->getJson('/api/orders?userId=1');
        $response->assertStatus(200);
        $this->assertEquals($expectedResult, $response->getContent());
    }

    public function test_it_can_get_index_error(): void {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(403);
    }

    public function test_it_can_store_order(): void {
        $response = $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 1,
                    "amount" => 1
                ],
                [
                    "id" => 3,
                    "amount" => 2
                ]
            ],
        ]);

        $response->assertStatus(200);
        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('orderId', $result);

        $orderId = $result['orderId'];

        $order = Order::find($orderId);
        $products = $order->products()->withPivot('amount')->get();
        $this->assertCount(2, $products);
        $this->assertEquals(1, $products[0]->id);
        $this->assertEquals(1, $products[0]->pivot->amount);
        $this->assertEquals(3, $products[1]->id);
        $this->assertEquals(2, $products[1]->pivot->amount);
    }

    public function test_it_can_update_stock(): void {
        $response = $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 2,
                    "amount" => 900
                ],
                [
                    "id" => 4,
                    "amount" => 120
                ]
            ],
        ]);


        $product1 = Product::find(2);
        $product2 = Product::find(4);

        $this->assertEquals(100, $product1->stock);
        $this->assertEquals(880, $product2->stock);
    }

    public function test_it_can_boardcast_events(): void {
        Event::fake();

        $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 1,
                    "amount" => 100
                ],
            ],
        ]);

        Event::assertNotDispatched(LowProductStockEvent::class);

        $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 1,
                    "amount" => 800
                ],
            ],
        ]);

        Event::assertDispatched(LowProductStockEvent::class, 1);

        $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 1,
                    "amount" => 10
                ],
            ],
        ]);

        Event::assertDispatched(LowProductStockEvent::class, 1);
    }

    public function test_it_can_get_stock_exceptions(): void {
        // user id missing
        $this->postJson('/api/orders', [
            "products" => [],
        ])->assertStatus(403);

        // product id missing
        $this->postJson('/api/orders', [
            "userId" => 1,
        ])->assertStatus(403);

        // unknown user
        $this->postJson('/api/orders', [
            "userId" => 99999999999,
            "products" => [],
        ])->assertStatus(403);

        // unknown product
        $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 9999999999999999999,
                    "amount" => 9
                ],
            ],
        ])->assertStatus(403);

        // max order size exceeded
        $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 1,
                    "amount" => 1000001
                ],
            ],
        ])->assertStatus(403);

        // product out of stock
        $this->postJson('/api/orders', [
            "userId" => 1,
            "products" => [
                [
                    "id" => 1,
                    "amount" => 10000
                ],
            ],
        ])->assertStatus(403);
    }
}
