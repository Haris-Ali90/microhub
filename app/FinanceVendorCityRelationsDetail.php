<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceVendorCityRelationsDetail extends Model
{

    //use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'finance_vendor_city_relations_detail';


    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     *ORM Functions
     *
     **/

    /**
     * Get FinanceVendorCityRelations data.
     */
    public function FinanceVendorCityRelations()
    {
        return $this->belongsTo(FinanceVendorCityRelations::class,'vendor_city_realtions_id', 'id');
    }


    /**
     * Get FinanceVendorCityRelations data.
     */
    public function Verndor()
    {
        return $this->belongsTo(Vendor::class,'vendors_id', 'id');
    }

}
