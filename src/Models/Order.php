<?php
namespace Arbory\Payments\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @package Arbory\Payments\Models
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
        return round(($this->payment_amount/100), 2, PHP_ROUND_HALF_EVEN);
    }

    public function getCurrency(){
        return $this->currency_code;
    }
}