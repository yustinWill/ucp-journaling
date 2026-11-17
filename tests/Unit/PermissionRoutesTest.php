<?php

namespace Tests\Unit;

use Tests\TestCase;

class PermissionRoutesTest extends TestCase
{
    public function test_write_routes_require_their_permission(): void
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
            'sales_order.revenue_split.update' => 'revenue_split.edit',
            'sales_order.revenue_split.reset' => 'revenue_split.edit',
            'purchase_order.add' => 'purchase_order.create',
            'purchase_order.store' => 'purchase_order.create',
            'purchase_order.edit' => 'purchase_order.edit',
            'purchase_order.update' => 'purchase_order.edit',
            'purchase_delivery.add' => 'purchase_delivery.create',
            'purchase_delivery.store' => 'purchase_delivery.create',
            'purchase_delivery.edit' => 'purchase_delivery.edit',
            'purchase_delivery.update' => 'purchase_delivery.edit',
            'purchase_invoice.add' => 'purchase_invoice.create',
            'purchase_invoice.store' => 'purchase_invoice.create',
            'purchase_invoice.edit' => 'purchase_invoice.edit',
            'purchase_invoice.update' => 'purchase_invoice.edit',
            'return_management.add' => 'return_management.create',
            'return_management.store' => 'return_management.create',
        ];

        foreach ($routes as $name => $permission) {
            $route = app('router')->getRoutes()->getByName("main_transaction.$name");

            $this->assertNotNull($route, $name);
            $this->assertContains("can:$permission", $route->gatherMiddleware(), $name);
        }
    }
}
