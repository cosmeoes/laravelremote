<?php

namespace Tests\Unit;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_sticky()
    {
        $sticky = Order::make(['sticky' => true]);
        $nonSticky = Order::make(['sticky' => false]);

        $this->assertTrue($sticky->isSticky());
        $this->assertFalse($nonSticky->isSticky());
    }

    public function test_has_color_hightlight()
    {
        $color = Order::make(['color' => "#FFFFFF"]);
        $nonColor = Order::make(['color' => null]);

        $this->assertTrue($color->hasColorHighlight());
        $this->assertFalse($nonColor->hasColorHighlight());
    }

    public function test_has_company_logo()
    {
        $withLogo = Order::make(['logo_path' => "some/logo/path.jpg"]);
        $withoutLogo = Order::make(['logo_path' => null]);

        $this->assertTrue($withLogo->hasCompanyLogo());
        $this->assertFalse($withoutLogo->hasCompanyLogo());
    }

    public function test_has_singed_edit_url()
    {
        $order = Order::factory()->create();

        $url = $order->editURL();

        $this->assertEquals(URL::signedRoute('order.edit', ['id' => $order->id]), $url);
    }
}
