<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceVendorCityRelations extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */
    protected $table = 'finance_vendor_city_relations';

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
    public function FinanceVendorCityRelationsDetail()
    {
        return $this->hasMany(FinanceVendorCityRelationsDetail::class,'vendor_city_realtions_id', 'id');
    }

    /**
     * Get Vendors data.
     */
    public function Vendors()
    {   return $this->belongsToMany(Vendor::class, 'finance_vendor_city_relations_detail', 'vendor_city_realtions_id', 'vendors_id');

    }

}
