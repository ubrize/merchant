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
        'total_vat',
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

    public function add(Orderable $item)
    {
        $params = [
            'order_id' => $this->id,
            'object_options' => $item->getOptions(),
            'object_class' => $item->getClass(),
            'object_id' => $item->getId(),
            'price' => $item->getPrice(),
            'vat' => $item->getVatRate(),
            'quantity' => $item->getQuantity(),
            'total' => intval($item->getTotal()),
            'total_vat' => intval($item->getTotalVat()),
            'summary' => $item->getSummary()
        ];

        $orderLine = $this->orderLines()->create($params);

        $this->total += $orderLine->total;
        $this->total_vat += $orderLine->total_vat;
        $this->save();

        return $orderLine;
    }

    public function empty(){
        $this->orderLines()->each(function($line){
            /** @var OrderLine $line */
            $line->delete();
        });

        $this->total = 0;
        $this->total_vat = 0;
        $this->save();
    }

    public static function getExistingInCart($sessionId)
    {
        $cartOrder = Order::where('session_id', $sessionId)->where('status', self::STATUS_CART)->first();
       return $cartOrder;
    }
}