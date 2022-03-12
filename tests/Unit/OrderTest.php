<?php

namespace Tests\Unit;

use App\Models\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
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
}
