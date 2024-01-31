<?php 
use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;


if(!isset($_REQUEST['date']) || empty($_REQUEST['date'])){
    $date = "20".date('y-m-d');
}
else {
    $date = $_REQUEST['date'];
}

?>
@extends( 'backend.layouts.app' )

@section('title', 'Jobs')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
<style>
.green-gradient, .green-gradient:hover {
    color: #fff;
    background: #bad709;
    background: -moz-linear-gradient(top, #bad709 0%, #afca09 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bad709), color-stop(100%,#afca09));
    background: -webkit-linear-gradient(top, #bad709 0%,#afca09 100%);
    background: linear-gradient(to bottom, #bad709 0%,#afca09 100%);
}
.black-gradient,
.black-gradient:hover {
    color: #fff;
    background: #535353;
    background: -moz-linear-gradient(top,  #535353 0%, #353535 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#535353), color-stop(100%,#353535));
    background: -webkit-linear-gradient(top,  #535353 0%,#353535 100%);
    background: linear-gradient(to bottom,  #535353 0%,#353535 100%);
}

.red-gradient,
.red-gradient:hover {
    color: #fff;
    background: #da4927;
    background: -moz-linear-gradient(top,  #da4927 0%, #c94323 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#da4927), color-stop(100%,#c94323));
    background: -webkit-linear-gradient(top,  #da4927 0%,#c94323 100%);
    background: linear-gradient(to bottom,  #da4927 0%,#c94323 100%);
}

.orange-gradient,
.orange-gradient:hover {
    color: #fff;
    background: #f6762c;
    background: -moz-linear-gradient(top,  #f6762c 0%, #d66626 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f6762c), color-stop(100%,#d66626));
    background: -webkit-linear-gradient(top,  #f6762c 0%,#d66626 100%);
    background: linear-gradient(to bottom,  #f6762c     0%,#d66626 100%);
}

.btn{
    font-size : 12px;
}
.modal-dialog {
    width: 94%;
}

.modal-header .close 
{
    opacity: 1;
    margin: 5px 0;
    padding: 0;
}
.modal-footer .close 
{
    opacity: 1;
    margin: 5px 0;
    padding: 0;
}
.modal-header h4 {
    color: #000;
}
.modal-footer {
    padding: 0 10px;
    text-align: right;
    border-top: 1px solid #e5e5e5;
}
.modal-header {
    padding: 5px 15px;
    border-bottom: 1px solid #e5e5e5;
    background: #c6dd38;
}
/*button.button.orange-gradient {
    border: none;
    line-height: 12px;
    display: inline-block;
    margin: 0;
    border-radius: 4px;
    padding: 8px 20px;
    color: #fff;
    background: #e46d24;
}*/
.form-group {
    width: 100%;
    margin: 10px 0;
    padding: 0 15px;
}
.form-group input, .form-group select {
    width: 80% !important;
    height: 30px;
}
.form-group label {
    width: 25%;
    float: left;
    clear: both;
}
input#date {
    height: 30px;
    width: 194px;
}



.form-group label {
    width: 50px;
}
div#route .form-group {
    width: 25%;
    float: left;
}

div#route {
    position: absolute;
    z-index: 9999;
    top: 83px;
    width: 97%;
}

.
div {
    display: block;
}
















/* start */
.iycaQH {
    position: absolute;
    background-color: white;
    border-radius: 0.286em;
    box-shadow: rgba(86, 102, 108, 0.24) 0px 1px 5px 0px;
    overflow: hidden;
    margin: 1.429em 0px 0px;
    z-index: 9999;
    width: 30%;
    top: 70px;
    left: 26px;
}
.cBZXtz {
    display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    -webkit-box-align: center;
    align-items: center;
}
.bdDqgn {
    padding: 0.6em 1em;
    background-color: white;
    border-bottom-left-radius: 0.286em;
    border-bottom-right-radius: 0.286em;
    max-height: 28.571em;
    overflow: scroll;
}
.cBZXtz {
    display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    -webkit-box-align: center;
    align-items: center;
}
.kikQSm {
    display: inline-block;
    max-width: 100%;
    font-size: 0.857em;
    font-family: Lato;
    font-weight: 700;
    color: rgb(86, 102, 108);
    margin-bottom: 0.429em;
}
.gdoBAT {
    font-size: 12px;
    margin: 0px 0px 5px 10px;
    color: rgb(86, 102, 108);
}


    </style>
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>
    <link href="{{ backend_asset('js/jquery-ui.css') }}" rel="stylesheet"> -->

@endsection

@section('inlineJS')
    
@endsection

@section('content')

<div class="right_col" role="main">
        <div class="">
                    @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>    
                <strong>{{ $message }}</strong>
            </div>
            @endif
            
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>    
                <strong>{{ $message }}</strong>
            </div>
            @endif
            
            @if ($message = Session::get('warning'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>    
                <strong>{{ $message }}</strong>
            </div>
            @endif
            
            @if ($message = Session::get('info'))
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>    
                <strong>{{ $message }}</strong>
            </div>
            @endif
            
            @if ($errors->any())
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>    
                Please check the form below for errors
            </div>
            @endif
            <div class="page-title">
                <div class="title_left amazon-text">
                    <h3>Routific Jobs<small></small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
           
            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                        <form id="filter"  style="padding: 10px;" action="" method="get">  
            <input id="date" name="date" style="width:35%" type="date" placeholder="date "   value='{{$date}}' class="form-control1">
                <button  id="search" type="submit" class="btn green-gradient">Filter</button>
                
            </form>
         
           
                            <div class="clearfix"></div>
                        </div>


                        <div class="x_content">

                         @include( 'backend.layouts.notification_message' )

	                <div class="table-responsive">
	                    <table id="datatable" class="table table-striped table-bordered">
	                      <thead stylesheet="color:black;">
	                      		<tr>
                                    <th>Id</th>
                                    <th>Job Id</th>
                                    <th>Status</th>
                                    <th>Action</th>
	                      		</tr>
	                      </thead>
                          <tbody>
                                {{--*/ $i = 1 /*--}}
                                @foreach( $datas as $record )
                                    <tr class="">
                                        <td>{{ $i }}</td>
                                        <td>{{ $record->job_id }}</td>
                                        <td>{{ ucfirst($record->status) }}</td>
                                        <td>
                                        @if($record->status!='finished')
                                        <button type='button'  class='create btn  green-gradient actBtn ' data-id="{{$record->job_id}}" >Craete Route <i class='fa fa-eye'></i></button>
                                        
                                        @endif
                                        <button type='button'  class='delete btn  red-gradient actBtn ' data-id="{{$record->id}}" >Delete <i class='fa fa-trash'></i></button>
                                            
                                        </td>
                                    </tr>
                                    {{--*/ $i++ /*--}}
                                @endforeach
                                </tbody>
	  					</table>
	                </div>


                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
  
    <!-- /#page-wrapper -->

  
    <!-- CreateSLotsModal -->
    

    <!-- UpdateSLotModal -->
                 <!-- DeleteSlotsModal -->
   <div id="ex4" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div id="delteform">
            </div>
        </div>
    </div>





    <div id="ex6" class="modal" style="display: none">
        <div class='modal-dialog'>
          <div id='main_form'>
        </div>
        </div>
    </div>





<script type="text/javascript">

$(function() {
$(".create").click(function(){
var element = $(this);
var del_id = element.attr("data-id");
document.getElementById('main_form').innerHTML ="<div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Create Route</h4></div><form action='../../../create/"+del_id+"/route' method='get'><div class='form-group'><p><b>Are you sure you want to pull these routes?</b></p></div><div class='form-group'><button type='submit' class='btn green-gradient btn-xs' >Yes</button><button type='button' class='btn red-gradient btn-xs' data-dismiss='modal' >No</button></div>  </form></div>";

$('#ex6').modal();
});
});



$(function() {
$(".delete").click(function(){
var element = $(this);
var del_id = element.attr("data-id");
document.getElementById('delteform').innerHTML ="<div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Delete Route</h4></div><form action='../../../routific/job/delete' method='post'><input type='hidden' name='_token' value='{{ csrf_token() }}'><input type='hidden' name='delete_id' value="+del_id+"><div class='form-group'><p><b>Are you sure you want to delete this?</b></p></div><div class='form-group'> <button type='submit' class='btn green-gradient btn-xs' >Yes</button><button type='button' class='btn red-gradient btn-xs' data-dismiss='modal' >No</button></div></form></div>";

$('#ex4').modal();
});
});












    </script>
  

@endsection