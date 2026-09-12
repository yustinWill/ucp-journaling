<?php

namespace Tests\Unit;

use App\Http\Controllers\MainTransaction\SalesOrderController;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class SalesOrderRevertControllerTest extends TestCase
{
    public function test_revert_page_keeps_view_and_execute_permissions_separate(): void
    {
        $user = Mockery::mock();
        $user->shouldReceive('can')
            ->once()
            ->with('sales_order.delete')
            ->andReturnFalse();

        $request = Request::create('/main_transaction/sales_order/revert');
        $request->setUserResolver(fn () => $user);

        $view = app(SalesOrderController::class)->revertIndex($request);

        $this->assertSame('pages.main_transaction.sales_order.revert', $view->name());
        $this->assertFalse($view->getData()['canExecuteRevert']);
    }

    public function test_destroy_rejects_user_without_sales_order_delete_permission(): void
    {
        $user = Mockery::mock();
        $user->shouldReceive('can')
            ->once()
            ->with('sales_order.delete')
            ->andReturnFalse();

        $request = Request::create('/main_transaction/sales_order/delete/10', 'DELETE');
        $request->setUserResolver(fn () => $user);

        $response = app(SalesOrderController::class)->destroy($request, 10);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
    }

    public function test_revert_log_page_uses_its_own_view_permission(): void
    {
        $view = app(SalesOrderController::class)->revertLogIndex();
        $route = app('router')->getRoutes()->getByName('main_transaction.sales_order_revert_log.index');
        $dataRoute = app('router')->getRoutes()->getByName('main_transaction.sales_order_revert_log.index_data_table');

        $this->assertSame('pages.main_transaction.sales_order.revert_log', $view->name());
        $this->assertContains('can:sales_order_revert_log.view', $route->gatherMiddleware());
        $this->assertContains('can:sales_order_revert_log.view', $dataRoute->gatherMiddleware());
    }
}
