<?php
namespace Arbory\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'merchant_transactions';

    const STATUS_CREATED     = 1;
    const STATUS_INITIALIZED = 2;
    const STATUS_ACCEPTED    = 3;
    const STATUS_PROCESSED   = 4;
    const STATUS_ERROR       = 5;
    const STATUS_REVERSED    = 6;
}