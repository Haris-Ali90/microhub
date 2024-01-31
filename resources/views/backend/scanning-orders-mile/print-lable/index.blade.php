<!DOCTYPE html>
<html>
<head>
    <title>Scanning Order Label</title>
    <style>

        @page {
            size: auto;   /* auto is the initial value */
            margin: 0;  /* this affects the margin in the printer settings */
        }
        table{
            position: fixed;
            width: 700px;
            min-height: 460px;
            border: 1px solid #ccc;
            padding: 10px;
            transform: translate(0px, 50%);
            left: 0;
            right: 0;
            margin: 0 auto;
        }
        tbody tr td:first-child {
            border-bottom: 2px solid #333;
            border-right: 2px solid #333;
            width: 34%;
        }
        tbody tr td:nth-child(2) {
            border-bottom: 2px solid #333;
            padding-left: 20px;
        }
        h1{
            background-color: #333;
            margin: 0;
            padding: 0px 30px;
            text-align: center;
            font-size: 85px;
            color: #fff;
        }
        span, p{
            font-size: 20px;
            text-transform: uppercase;
            color: #777;
            width: 250px !important;
            float: left;
            line-height: 25px;
            font-weight: 200;
            font-family: sans-serif;
        }
    </style>
</head>
<body  onload="window.print()">
<table style="width: 700px; min-height: 460px; margin: 0 auto; border: 1px solid #ccc; padding: 10px;" id="label-print">
    <tbody>
        <tr>
          <td scope="col"><img src="{!! app_asset('backend/css/assets/images/logo.png') !!}"></td>
          <td scope="col">
            <strong style="font-size: 20px;float: left;width: 20%; height: 100%;">From:</strong>
			 <p style="margin-top: 5px;font-size: 20px;">{{isset($ownHubDetail->title) ? $ownHubDetail->title : ''}}</p>
              <br>
            <span style="font-size: 20px;  margin: 0px 0px 0px 82px;">{{isset($ownHubDetail->address) ? $ownHubDetail->address : ''}}</span>
          </td>
        </tr>
        <tr>
          <td data-label="Account" style="height: 300px;">
            <strong style="font-size: 20px;">To:</strong>
            <p style="margin-top: 5px;font-size: 22px;">{{isset($otherHubDetail->title) ? $otherHubDetail->title : ''}}</p>
              <br>
              <br>
            <p style="margin: 0;font-size: 22px;">{{isset($otherHubDetail->address) ? $otherHubDetail->address : ''}}</p>
            <br>
            {{--<strong style="font-size: 20px;font-weight: 800;letter-spacing: 3px;">08:00-21:00</strong>
            <br>--}}
            <strong style="font-size: 20px;font-weight: 800;letter-spacing: 3px;">{{$otherHubDetail->created_at->format('Y-m-d')}}</strong>
            <br>
            <h1 style="background-color: #333;margin: 0;text-align: center;color: #fff;" >{{isset($otherHubDetail->postal__code) ? substr($otherHubDetail->postal__code ,0 ,3) : ''}}</h1>
        </td>

          <td data-label="Due Date">
              <span style="display: flex; justify-content: center; width: 100% !important;">
                   <div class="qr-code-wrap">
                      <img src="<?php echo $bundleID; ?>">
                  </div>
              {{--{{ $bundleID }}--}}
              </span>
            <small style="font-size: 16px;float: left;width: 100%;margin-top: 15px;font-weight: bold;">ORDER TRACKING NUMBER</small>
            <strong style="font-size: 35px; float: left;">{{$id}}</strong>
          </td>
        </tr>


        {{--<tr>
          <td scope="row" data-label="Account">
            <strong style="float: left;width: 100%;font-size: 20px; font-weight: 800;">Delivery Instructions</strong>
            <p style="float: left;width: 100%;font-size: 18px; font-weight: 400;">87 Boulevard Brunswick Toronto H9B 2J5</p>
          </td>
          <td data-label="Due Date">
            <p>VENDOR REF AA 1 1SSSS-3XX barcode</p>
            <img src="{!! app_asset('backend/css/assets/images/bar_code.png') !!}">
          </td>
        </tr>
        <tr>--}}
          <td colspan="2" style="width: 100%; border: none;font-size: 15px;" data-label="Account">For any questions or information about this package please call 1-647-931-6176 OR email support@joeyco.com</td>
        </tr>
    </tbody>
</table>
</body>
@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
@endsection


    <script type="text/javascript">
       /* function myFunction() {
            //var targetFile= window.open('D:\\xampp-7-2-33\htdocs\micro-hub\resources\views\backend\scanning-orders-mile');
            var prtContent = document.getElementById('label-print');
            console.log(prtContent);
            prtContent.print();
        }*/
       
    </script>

</html>



