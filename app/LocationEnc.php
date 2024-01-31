<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

//use Illuminate\Database\Eloquent\SoftDeletes;

class LocationEnc extends Model
{

    private $key;
    private $cipher;

    public function __construct()
    {
        $this->key = 'c9e92bb1ffd642abc4ceef9f4c6b1b3aaae8f5291e4ac127d58f4ae29272d79d903dfdb7c7eb6e487b979001c1658bb0a3e5c09a94d6ae90f7242c1a4cac60663f9cbc36ba4fe4b33e735fb6a23184d32be5cfd9aa5744f68af48cbbce805328bab49c99b708e44598a4efe765d75d7e48370ad1cb8f916e239cbb8ddfdfe3fe';
        $this->cipher = 'f13c9f69097a462be81995330c7c68f754f0c6026720c16ad2c1f5f316452ee000ce71d64ed065145afdd99b43c0d632b1703fc6a6754284f5d19b82dc3697d664dc9f66147f374d46c94cf23a78f14f0c6823d1cbaa19c157b4cb81e106b79b11593dcddf675951bc07f54528fc8c03cf66e9c437595d1cac658a737ab1183f';
    }



    // use SoftDeletes; //add this line
    protected $table = 'locations_enc';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];


    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = DB::raw("AES_ENCRYPT('".$value."', '".$this->key."', '".$this->cipher."')");
    }
    public function setPostalCodeAttribute($value)
    {
        $this->attributes['postal_code'] = DB::raw("AES_ENCRYPT('".$value."', '".$this->key."', '".$this->cipher."')");
    }
    public function setBuzzerAttribute($value)
    {
        $this->attributes['buzzer'] = DB::raw("AES_ENCRYPT('".$value."', '".$this->key."', '".$this->cipher."')");
    }
    public function setSuiteAttribute($value)
    {
        $this->attributes['suite'] = DB::raw("AES_ENCRYPT('".$value."', '".$this->key."', '".$this->cipher."')");
    }
    public function setLatitudeAttribute($value)
    {
        $this->attributes['latitude'] = DB::raw("AES_ENCRYPT('".$value."', '".$this->key."', '".$this->cipher."')");
    }
    public function setLongitudeAttribute($value)
    {
        $this->attributes['longitude'] = DB::raw("AES_ENCRYPT('".$value."', '".$this->key."', '".$this->cipher."')");
    }

    public function setDecryptAddressAttribute($value, $locationId)
    {
        $query = DB::select("SELECT address, AES_DECRYPT(address,'$this->key','$this->cipher') AS decrypt_address  FROM locations_enc WHERE id = $locationId");
        return $query[0]->decrypt_address;
    }





}
