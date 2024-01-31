<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiLogs extends Model{

    public $connection = 'mysql2';
    public $table = 'dashboard_logs';

    const REQUEST_LOGIN = 1;
    const RESPONSE_LOGOUT= 2;
    
}
