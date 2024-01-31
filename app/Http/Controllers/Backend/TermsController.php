<?php

namespace App\Http\Controllers\Backend;

use App\Agreements;
use App\AgreementsUser;
use App\HubProcess;
use Illuminate\Http\Request;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TermsController extends Controller
{

    function getIndex(){

        return view('backend/terms');
    }

}
