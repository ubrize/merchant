<?php

namespace Arbory\Merchant\Seeds;

use App\Order\OrderStatusType;
use Arbory\Merchant\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model = new Order();
        \DB::table($model->getTable())->insert([
            'id' => 1,
            'status' => OrderStatusType::STATUS_CART,
            'total' => 100,
            'payment_currency' => 'EUR',
            'language' => 'lv'
        ]);
    }
}
