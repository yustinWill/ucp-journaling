<?php

namespace Tests\Unit;

use Tests\TestCase;

class SalesPermissionRoutesTest extends TestCase
{
    public function test_sales_write_routes_require_their_permission(): void
    {
        $routes = [
            'sales_order.add' => 'sales_order.create',
            'sales_order.store' => 'sales_order.create',
            'sales_order.edit' => 'sales_order.edit',
            'sales_order.update' => 'sales_order.edit',
            'sales_order.cancel' => 'sales_order.edit',
            'sales_delivery.add' => 'sales_delivery.create',
            'sales_delivery.create' => 'sales_delivery.create',
            'sales_delivery.edit' => 'sales_delivery.edit',
            'sales_delivery.update' => 'sales_delivery.edit',
            'sales_delivery.accept' => 'sales_delivery.edit',
            'sales_delivery.confirm' => 'sales_delivery.edit',
            'sales_invoice.add' => 'sales_invoice.create',
            'sales_invoice.store' => 'sales_invoice.create',
            'sales_invoice.edit' => 'sales_invoice.edit',
            'sales_invoice.update' => 'sales_invoice.edit',
            'sales_invoice.payment_recipient' => 'sales_invoice.edit',
            'sales_invoice.payment_save' => 'sales_invoice.edit',
        ];

        foreach ($routes as $name => $permission) {
            $route = app('router')->getRoutes()->getByName("main_transaction.$name");

            $this->assertNotNull($route, $name);
            $this->assertContains("can:$permission", $route->gatherMiddleware(), $name);
        }
    }
}
