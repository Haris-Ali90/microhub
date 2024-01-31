<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
     /**
     * Table name.
     *
     * @var array
     */

    public $table = 'document_types';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];
  

}
