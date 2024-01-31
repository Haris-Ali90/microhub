
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet"/>
<link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>

<?php
$user = Auth::user();
if($user->email!="admin@gmail.com")
{

$data = explode(',', $user['rights']);
$dataPermission = explode(',', $user['permissions']);
}

else{
    $data = [];
    $dataPermission=[];
}

 ?>
@extends( 'backend.layouts.app' )

@section('title', 'Enable For  Routing')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <!-- <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet"> -->
    <!-- DataTables Responsive CSS -->
    <!-- <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet"> -->
    <!-- Image Viewer CSS -->
    <!-- <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet"> -->
    
@endsection
<style>
.pac-container.pac-logo {
    z-index: 9999;
}
/* .it .btn-orange
{
  background-color: blue;
  border-color: #777!important;
  color: #777;
  text-align: left;
  width:100%;
}
.it input.form-control
{
  
  border:none;
  margin-bottom:0px;
  border-radius: 0px;
  border-bottom: 1px solid #ddd;
  box-shadow: none;
}
.it .form-control:focus
{
  border-color: #ff4d0d;
  box-shadow: none;
  outline: none;
}
.fileUpload {
    position: relative;
    overflow: hidden;
}
.fileUpload input.upload {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
} */

.loader {
    position: relative;
    right: 0%;
    top: 0%;
    justify-content: center;
    text-align: center;
    /* text-align: center; */
    border: 18px solid #e36d28;
    border-radius: 50%;
    border-top: 16px solid #34495e;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 4s linear infinite;
    animation: spin 2s linear infinite;
    margin: 0 auto;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
td.ab .view {
    display: none !important;
}
.loader-iner-warp {
    position: relative;
    width: 100%;
    text-align: center;
    top: 40%;
}
@media all and (max-width: 600px){
  select {
     width: 100%; max-width: 100%;
  }
}
button.btn.btn {
    color: #333;
    background-color: #c6dd38;
    border-color: #c6dd38;
}
textarea {
    min-width: 300px ! important;
    max-width: 300px ! important;
   
    min-height: 30px ! important;
}
</style>
@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>
    <link href="{{ backend_asset('js/jquery-ui.css') }}" rel="stylesheet"> -->
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>-->
    <script src="https://unpkg.com/bootprompt@6.0.2/bootprompt.js"></script>
    

@endsection


@section('content')

<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
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
           
           
        <div class="">
            <div class="page-title">
                <div class="title_left amazon-text">
                   
                </div>
            </div>

            <div class="clearfix"></div>
            <!--Count Div Row Open-->
            <div class="row">

            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row"><!---row-->
                
                        <div class="col-md-12 col-sm-12 col-xs-12">  <!---col-md-12 col-sm-12 col-xs-12-->
                           
                               <div class="x_panel"> <!---x_panel-->
                        
                        <div class="x_title">   <!---x_title-->
                           
                        <h1 style="position: relative;padding-right: 160px;"><b>Enable For  Routing </b> <button  style='background-color: #c6dd38; float:right;position: absolute;right: 0px;' data-id={{$id}} class='btn btn enableRouteOrder'>Enable Route</button></h1>
                        
                            <div class="clearfix">  <!---clearfix-->
                                
                            </div> <!---clearfix end-->
                            
                        </div>
                        <div class="loader-background" style="
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 9999;
    width: 100%;
    height: 100%;
    background-color: #000000ba; display: none "
    >
    <div class="loader-iner-warp">
                        <div class="loader" style="display: none" ></div>
            </div>
                        </div>
                         <!---x_title end-->

                       
                        

                        <!-- table -->
                        <div  id='table-div'>
                        <div class="x_title">   <!---x_title-->
                           
                           <h2><br> <b>Enable Route Order Detail </b></h2>

                          <div class="clearfix">  <!---clearfix-->
                              
                          </div> <!---clearfix end-->
                      
              </div>
                  
                   <!---x_title end-->
                       <!---x_content end-->
                       <div class="x_content">
                           <!---x_content start-->
                                   @include( 'backend.layouts.notification_message' )
                                                   <div class="table-responsive">
                                       <!---table-responsive start-->
                                       <table class="table table-striped table-bordered"  id="datatable" border="2">
                                       <thead>
                                       <tr>
                                       <th> <input class='check' type='checkbox' name='check' value="0" id="checkAll"> </th> 
                                                           <th>Tracking Id </th> 
                                                           <th> Address </th>
                                                           <th> Postal code </th>
                                                          
                                       </tr>
                                       </thead>
                                       <tbody>
                                       <?php 
    
    foreach($orders as $order) {
       
           echo "<tr>";
           echo "<td><input class='check' id='check' type='checkbox' name='check' value='".$order->tracking_id."'></td>";
           echo "<td>".$order->tracking_id."</td>";
           echo "<td>".$order->address."</td>";
           echo "<td>".$order->postal_code."</td>";
       
   
          
           echo "</tr>";
           
                
    }
             
      ?>
             </tbody>
                                       </table>      
                                       </div>
                           <!---table-responsive end-->
                                       
                       </div>
                       <!---x_content end-->
                       </div>
                        <!-- table end -->
                         
                    <!---clearfix end-->
                </div>
                <!---x_panel end-->
                        </div>
                        <!---col-md-12 col-sm-12 col-xs-12 end-->

            </div>
         <!---row-- end>
    </div>
    <!-- /#page-wrapper -->
    
  
  
                            @include( 'backend.layouts.notification_message' )
   @endsection


@section('inlineJS')


<script>
//enableRouteOrder
 // Joey Count for Route edit button function

 $(document).on('click','#checkAll',function(){
            //  aler('s');
            $('input:checkbox').not(this).prop('checked', this.checked);
    });
    
 $(document).on('click','.enableRouteOrder',function(){

    var ids = [];
    $.each($("input[name='check']:checked"), function(){
        ids.push($(this).val());
        });
        if(ids==0)
        {
            bootprompt.alert('Please  select the  order to Enable for Routing');
        }
        else
        {
        element = $(this);

        $(".loader").show();
                $(".loader-background").show();
        var del_id = element.attr("data-id");
        element.prop('disabled', true);
        $.ajax({
              type: "post",
              url: "{{ URL::to('backend/enable/order/forroute')}}",
              data:{ids:ids,file_id:del_id},
                           beforeSend: function (request) {
                                   return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                                       },
              success: function (data) {
                if(data.status_code==200)
                {
                    $(".loader").hide();
                $(".loader-background").hide();
                bootprompt.alert({
                                    message: "Order Enable for Routing Successfully",
                                    callback: (result) => {  location.reload();/* result is a boolean; true = OK, false = Cancel*/ }
                                    });
                    
                }
                else
                {
                    $(".loader").hide();
                $(".loader-background").hide();
                    bootprompt.alert(data['error']);
                }
             
               

              },
              error:function (error) {
                $(".loader").hide();
                $(".loader-background").hide();
                                bootprompt.alert("some error");
         
              }
          });
        }

    });




</script>
<script type="text/javascript">
      
        $(document).ready(function () {

            $('#datatable').DataTable({
              "lengthMenu": [ 50,100, 250, 500, 750, 1000]
            });

            $(".group1").colorbox({height:"50%",width:"50%"});

            $(document).on('click', '.status_change', function(e){
                var Uid = $(this).data('id');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to change user status??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    btns: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {

                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo URL::to('/'); ?>/api/changeUserStatus/"+Uid,
                                    data: {},
                                    success: function(data)
                                    {
                                        if(data== '0' || data== 0 )
                                        {
                                            var DataToset = '<btn type="btn" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="'+Uid+'" data-target=".bs-example-modal-sm">Blocked</btn>';
                                            $('#CurerntStatusDiv'+Uid).html(DataToset);
                                        }
                                        else
                                        {
                                            var DataToset = '<btn type="btn" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="'+Uid+'" data-target=".bs-example-modal-sm">Active</btn>'
                                            $('#CurerntStatusDiv'+Uid).html(DataToset);
                                        }
                                    }
                                });

                            }
                        },
                        cancel: function () {
                            //$.alert('you clicked on <strong>cancel</strong>');
                        }
                    }
                });
            });

            $(document).on('click', '.form-delete', function(e){

                var $form = $(this);
                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to delete user ??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    btns: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {
                                $form.submit();
                            }
                        },
                        cancel: function () {
                            //$.alert('you clicked on <strong>cancel</strong>');
                        }
                    }
                });
            });

        });

    </script>
    <script>
        $(document).ready(function() {
            $('#birthday').daterangepicker({
                singleDatePicker: true,
                calender_style: "picker_4"
            }, function(start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
    </script>

@endsection

