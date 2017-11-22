<?php

namespace Arbory\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Arbory\Merchant\Utils\Orderable;
use Arbory\Merchant\Models\OrderLine;

/**
 * Class Order
 *
 * @package Arbory\Merchant\Models
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $table = 'merchant_orders';

    const STATUS_CART = 1;
    const STATUS_PENDING = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_COMPLETE = 4;
    const STATUS_CANCELED = 5;

    protected $fillable = [
        'status',
        'total',
        'payment_currency',
        'status_updated_at',
        'payment_started_at',
        'language',
        'session_id',
        'owner_type',
        'owner_id',
    ];

    public function owner()
    {
        return $this->morphTo('owner');
    }

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'order_id', 'id');
    }

    public function getCurrency()
    {
        return $this->currency_code;
    }
}