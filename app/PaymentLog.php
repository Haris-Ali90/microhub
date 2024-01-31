<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{

    protected $table = 'payment_logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'hub_id', 'process_id', 'charge_id', 'amount','plan', 'status','currency', 'description', 'card_no', 'exp_month', 'exp_year', 'cvc'
    ];



}
