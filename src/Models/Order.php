<?php
namespace Arbory\Merchant\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @package Arbory\Merchant\Models
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $table = 'merchant_orders';

    const STATUS_CART       = 1;
    const STATUS_PENDING    = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_COMPLETE   = 4;
    const STATUS_CANCELED   = 5;

    public function getAmountDecimal(){
        // round and float
        $float = round(($this->payment_amount/100), 2, PHP_ROUND_HALF_EVEN);
        return number_format($float, 2, '.', '');
    }

    public function getCurrency(){
        return $this->currency_code;
    }
}