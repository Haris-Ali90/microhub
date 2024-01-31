<?php
\Laravel\Asset::script('jquery-ui.js', '/js/jquery/jquery-ui-1.10.3.custom.min.js', array('jquery'));
\Laravel\Asset::style('jquery-ui.css', '/css/jqueryui/joeyco/jquery-ui-1.10.3.custom.min.css', array('fonts', 'reset', 'common', 'style'));
\Laravel\Asset::style('jquery-ui-1.10.3.custom.min.css', '/css/jqueryui/joeyco/jquery-ui-1.10.3.custom.min.css');
\Laravel\Asset::style('jquery-ui-timepicker.css', '/css/jqueryui/joeyco/jquery-ui-timepicker.css', array('jquery-ui-1.10.3.custom.min.css'));
\Laravel\Asset::script('jquery-ui-1.10.3.custom.min.js', '/js/jquery/jquery-ui-1.10.3.custom.min.js', array('jquery'));
\Laravel\Asset::script('jquery-ui-timepicker.js', '/js/jquery/jquery-ui-timepicker.js', array('jquery', 'jquery-ui-1.10.3.custom.min.js'));
\Laravel\Section::start('content');
use \JoeyCo\Tools\StatusMap;
?>
<<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>

<style>
.modal {
    top: 0 !important;
    left: 0;
    margin: 0px;
}
.modal.fade{
    opacity: 1
}
/* .modal a.close-modal{
    top: -5px;
    right: -5px
} */
.form-control {
    display: block;
    width: 100%;
    height: 40px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.form-control1 {
    
    width: 80%;
    height: 30px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.form-control:focus {
    border-color: #66afe9;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
}
.form-group {
    margin-bottom: 15px;
}
.orders-table tbody td {
    padding: 12px;
}
.data-table th, .data-table td {
    padding: 15px 5px;
    }
    .modal-header{
        font-size:16px;
    }
    .modal-body h4 {
    background: #f6762c;
    padding: 8px 10px;
    margin-bottom: 10px;
    font-weight: bold;
    color: #fff;
}
#tracking_id_chosen {
    width: 280px !important;
    margin-right: 5px !important;
    
}
#sprint_id_chosen {
    width: 280px !important;
    margin-right: 5px !important;
    float: left;
}
select {
    margin: 0px 5px 0 0 !important;
}
.tracking_id input {
    padding: 18px !important;
}
.sprint_id input {
    padding: 18px !important;
}

</style>
<section class="content">
    <div>
        
    
    <h2 style="width:100%; background-color:#3e3e3e; color:white; text-align:center; 
        font-size:20px; font-weight:bold; margin-bottom:25px; padding:10px 0px">Status Update</h2>
        <div class="form-group">
   <form id="filter"  style="padding: 10px;" action="" method="get">
                <?php 
                if(!isset($_REQUEST['date']) || empty($_REQUEST['date'])){
                    $date = date('Y-m-d');
                }
                else {
                    $date = $_REQUEST['date'];
                }
                ?>    
                <input id="date" name="date" type="date" placeholder="Select Date" style="padding:10px" value="<?php echo $date  ?>">
                
                <button style="width:100px;height: 45px;" id="search" type="submit" class="button green-gradient">Filter</button>
                
            </form>


<form id="filter" name='typeCV' style="padding: 10px;" action="dispatch/hub/routific/updatestatus" method="post">
<input type="hidden" name="date" id="date" value='<?php echo $date; ?>' class="form-control"  />
                <div class="selectId">
               
                  <select name='type' id='type' style="width:25%; float:left;"  class="form-control"  onchange="myFunction()"> 

                  <option value='0'>Select type</option>  
                   <option value='1'>Order ID </option>                               
                   <option value='2'>Tracking ID</option>  
                 </select>
                 </div> 
            
              <div class="sprint_id" >
                <select   id="sprint_id" name="sprint_id[]" style="width:45%;  float:left;" multiple class="form-control chosen-select" required > 
                <!-- <option value="">Please Select Sprint ID</option> -->
                <?php
                $sprint=JoeyCo\Laravel\Sprint::
                //join('sprint__tasks','sprint__tasks.sprint_id','=','sprint__sprints.id')->
                where_null('sprint__sprints.deleted_at')
                ->where('sprint__sprints.created_at','like',$date."%")
                ->get(['sprint__sprints.id']);

                foreach($sprint as $oc){ ?>
                    <option value="<?php echo $oc->attributes['id'] ?>">
                    CR-<?php echo $oc->attributes['id'] ?></option>
                <?php }
                ?>
                </select>
        </div>
        
        <div class="tracking_id" style="float: left;" >
            <select multiple id="tracking_id" name="tracking_id[]" style="width:45%;  float:left;" multiple class="form-control chosen-select" required >
              <!-- <option value="">Please Select Tracking ID</option> -->
			  <?php 
              $tracking=JoeyCo\Laravel\MerchantIds::join('sprint__tasks','merchantids.task_id','=','sprint__tasks.id')
              ->where_null('merchantids.deleted_at')->where_not_null('merchantids.tracking_id')
              // ->where(\DB::raw("from_unixtime(due_time-14400)"),'like',$date."%")
              ->where('merchantids.created_at','like',$date."%")
              ->get(['merchantids.tracking_id','merchantids.id']);
			 
			  
              foreach($tracking as $v){
				 
			  
				 ?>
                  <option value="<?php echo $v->attributes['id'] ?>"><?php echo $v->attributes['tracking_id']?></option>
             <?php }
              ?>
            </select>
        </div>
      
        <div class="status" >
            <select  id="status_id" name="status_id" style="width:45%;  float:left;" class='form-control' required > 
              <option value="">Please Select Status</option>
              <?php
             // $s=array( 15,135,140,137,136,124,112,108,104,121,133,103,106,107,109,110,132,131);
             // $s=array(101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133);
              $s=array(15,17,18,24,28,32,36,61,67,68,102,111,113,114,116,117,118,135,140,137,136,124,112,108,104,121,133,103,106,107,109,110,132,131,141,142,143,144);
            sort($s);
			

              foreach($s as $oc){ ?>
                  <option value="<?php echo $oc ?>">
				  <?php echo StatusMap::getDescription($oc) ?></option>
             <?php }
              ?>
            </select>
        </div>
                    </div>
                    <br><br>
                  <button style="height:40px; margin-left: 10px;" id="search" type="submit" class="button green-gradient">Update Status</button>
                  
              </form>
          </div>
          </div>
          
</section>
<?php //dd($stat);?>
<script>
src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
$(document).ready(function() {
   
       
        $(".tracking_id").hide();
        $(".sprint_id").hide();
        $('#tracking_id').prop('required',false);
         $('#tracking_id').prop('required',false);
     

       
    });

function myFunction() {
 
 if (document.typeCV.type.value == "2")  {
    $(".tracking_id").show();
   //$('.sprint_id').removeAttr('required');​​​​​
    $('#tracking_id').prop('required',true);
    $(".sprint_id").hide();
    $('#sprint_id').prop('required',false);
 }
 else if(document.typeCV.type.value == "1"){
    $(".sprint_id").show();
    $('#tracking_id').prop('required',false);
    $(".tracking_id").hide();
     $('#sprint_id').prop('required',true);
 }
 else{
    $(".tracking_id").hide();
        $(".sprint_id").hide();
 }
}
$(".chosen-select").chosen({
  no_results_text: "Oops, nothing found!"
})

</script>



<?php
\Laravel\Section::stop();
echo \Laravel\View::make('layouts.main')->with(get_defined_vars())->render();
