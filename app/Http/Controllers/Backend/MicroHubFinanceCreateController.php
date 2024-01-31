<?php

namespace App\Http\Controllers\Backend;

use App\AmazonEnteries;
use App\Post;
use Illuminate\Http\Request;
use App\Sprint;
use App\Http\Requests;
use App\Http\Controllers\Backend\BackendController;

use App\User;
use App\Teachers;
use App\Institute;
use App\Amazon;
use App\Amazon_count;
use App\Ctc;
use App\Ctc_count;
use App\CoursesRequest;
use date;
use DB;
use whereBetween;
use Carbon\Carbon;
use PDFlib;

class MicroHubFinanceCreateController extends BackendController
{

    
    /**
     * Get Montreal ,Ottawa ,Ctc dashboard count and graph
     */
    public function getIndex(Request $request)
    {

        

    }

public function add(){
    return backend_view('finance_create');
}

}
