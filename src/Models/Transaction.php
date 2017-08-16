<?php
namespace Arbory\Payments\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    const STATUS_CREATED     = 1;
    const STATUS_INITIALIZED = 2;
    const STATUS_ACCEPTED    = 3;
    const STATUS_PROCESSED   = 4;
    const STATUS_ERROR       = 5;
    const STATUS_REVERSED    = 6;

    protected $table = 'merchant_transactions';

    protected $fillable = ['object_class', 'object_id', 'status', 'gateway', 'options', 'amount', 'token_id', 'description', 'language_code', 'currency_code'];
}