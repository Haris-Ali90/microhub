<?php
\Laravel\Section::start('content');
use \JoeyCo\Tools\StatusMap;
use \Laravel\Input;

?>
<style>
    input[type="text"] {
  width: 20%;
  height: 29px;
  border: 1px solid #aaa;
  border-radius: 4px;
  margin: 0px 5px;
  outline: none;
  padding: 8px;
  box-sizing: border-box;
  transition: 0.3s;
}

input[type="text"]:focus {
  border-color: dodgerBlue;
  box-shadow: 0 0 8px 0 dodgerBlue;
}

.inputWithIcon input[type="text"] {
  padding-left: 40px;
}

.inputWithIcon {
  position: relative;
}

.inputWithIcon i {
  position: absolute;
  left: 0;
  top: 8px;
  padding: 9px 8px;
  color: #aaa;
  transition: 0.3s;
}

.inputWithIcon input[type="text"]:focus + i {
  color: dodgerBlue;
}

.inputWithIcon.inputIconBg i {
  background-color: #aaa;
  color: #fff;
  padding: 9px 4px;
  border-radius: 4px 0 0 4px;
}

.inputWithIcon.inputIconBg input[type="text"]:focus + i {
  color: #fff;
  background-color: dodgerBlue;
}

table#main  { 
	width: 100%; 
	border-collapse: collapse; 
	margin:50px auto;
	}

/* Zebra striping */
tr:nth-of-type(odd) { 
	background: #eee; 
	}

th#main { 
	background: #3e3e3e; 
	color: white; 
	font-weight: bold;
	}

th { 
	background: #bad709; 
	color: black; 
	font-weight: bold;
	}

td, th { 
	padding: 10px; 
	border: 1px solid #ccc; 
	text-align: left; 
	font-size: 16px;
	}
  select#status_id {
    width: 100% !important;
    padding: 4px 0;
}
select#status_id:focus {
    border: none;
    color: #000;
}
select#status_id:focus {
    outline: none;
}
.center {
  text-align: center;
 
}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
<script>
    
   
</script>
<?php 
$d1 = "20".date('y-m-d');
?>
<section class="route-sec">
<h2 style="width:100%; background-color:#3e3e3e; color:white; text-align:center; font-size:20px; font-weight:bold; 
            margin-bottom:25px; padding:10px 0px">Amazon Orders Date Update</h2>
      <div class='center'>
        <input type="date" id='dateid' name="date" placeholder="date" min=<?php echo $d1 ?> value= "<?php echo $d1 ?>"required style="height: 40px;width: 215px;"  >
        <button style="width:100px"  id="search" type="submit" class="change button green-gradient">Update</button>
          </div>
  

   
  

</section>
 <script>

 </script>
<script>
$(function() {
$(".change").click(function(){
  var date1=document.getElementById("dateid").value;
    var date = new Date(date1);
    var dateto=new Date(date.setDate(date.getDate() - 1)).toISOString().slice(0, 10);
    
    var x = confirm("Are you sure you want to update order of this date "+date1+" to "+ dateto);
  if(x)
      {
    $.ajax({
        type: "POST",
        url: 'amazon/order/date/change',
        data:{date : date1},
    
        success: function(data){
    
          location.reload();  

    }
});
}

//return false;
});
});

</script>
<?php
\Laravel\Section::stop();
echo \Laravel\View::make('layouts.main')->with(get_defined_vars())->render();