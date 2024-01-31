<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 *
 * @author Abdul Basit Khan <basit.khan6454@gmail.com>
 * @date   01/04/2022
 */
class SystemParameter extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'system_parameters';

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
    protected $casts = [

    ];

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = trim(strtolower($value));
    }


    /*public function getKeyValue($key)
    {

    }*/

    public static function  getKeyValue($keys)
    {
        if( gettype($keys) == 'string')
        {
            return SystemParameter::where('key',$keys)->first();
        }
        elseif( gettype($keys) == 'array' )
        {
            return SystemParameter::whereIn('key',$keys)->get()->pluck([],'key'); //->pluck('value','key')->toAraay();
        }

    }

}

