<!DOCTYPE html>
<html>
<head>
    <title>Order Label</title>
    <style>
        table{
            /*position: fixed;*/
            width: 700px;
            min-height: 460px;
            border: 1px solid #ccc;
            padding: 10px;
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
        .bar-code {
            height: 61px;
        }

        .sizing {
            min-height: unset;
            display: grid;
            align-items: flex-start;
            justify-content: center;
            float: left;
            width: 100%;
            margin: 300px 0;
        }
        /*For A4 and A5*/
        @if(in_array('a4_size',$printSize))

        .table-br{
            min-height: 83px !important;
        }
        @media print{
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
        /*For Letter*/
        @elseif(in_array('letter_size',$printSize))

        .table-br {
            min-height: 50px !important;
        }
        @media print {
            @page {
                size: Letter portrait;
                margin: 0;
            }
        }
        /*For Legal*/
        @elseif(in_array('legal_size',$printSize))
        .table-br{
            min-height: 194px !important;
        }
        @media print {
            @page {
                size: Legal portrait;
                margin: 0;
            }
        }
        /*For Executive*/
        @elseif(in_array('executive_size',$printSize))
        .table-br{
            min-height: 53px !important;
        }
        @media print {
            @page {
                /*size: 5.8in 8.4in;*/
                size: 177.8mm 266.7mm;
                margin: 0;
            }

        }
        /*Default A4 and A5*/
        @elseif(empty($printSize))
        .table-br{
            min-height: 740px !important;
        }
        @media print{
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
        /*For A4 and A5*/
        @elseif(in_array('a5_size',$printSize))
        .table-br{
            min-height: 23px !important;
        }
        @media print {
            @page {
                /*size: 5.8in 8.4in;*/
                size: 190.8mm 265.7mm;
                margin: 0;
            }

        }
        @endif



    </style>
</head>
<body onload="window.print()">

<div class="print-label-div">
    @foreach($printLabelData as $labelData)
        <div class='sizing'>
        <table id="label-print">

            <tbody>
            <tr>
                <td scope="col"><img src="{!! app_asset('backend/css/assets/images/logo.png') !!}"></td>
                <td scope="col">
                    <strong style="font-size: 20px;float: left;width: 20%; height: 100%;">From:</strong>
                    <p style="margin-top: 5px;font-size: 20px;">{{isset($labelData->vendor_name) ? $labelData->vendor_name : ''}}</p>
                    <br>
                    <span style="font-size: 20px; margin: 0px 0px 0px 82px;">{{isset($labelData->vendor_address) ? $labelData->vendor_address : ''}}</span>
                </td>
            </tr>
            <tr>
                <td data-label="Account" style="height: 300px;">
                    <strong style="font-size: 20px;">To:</strong>
                    <p style="margin-top: 5px;font-size: 22px;">{{isset($labelData->sprint_name) ? $labelData->sprint_name : ''}}</p>
                    <br>
                    <br>
                    <p style="margin: 0;font-size: 22px;">{{isset($labelData->sprint_address) ? $labelData->sprint_address : ''}}</p>
                    <br>

                    <strong style="font-size: 20px;font-weight: 800;letter-spacing: 3px;">{{isset($labelData->created_at) ? $labelData->created_at : ''}}</strong>
                    <br>
                    <h1 style="background-color: #333;margin: 0;text-align: center;color: #fff;" >{{isset($labelData->sprint_postal_code) ? substr($labelData->sprint_postal_code ,0 ,3) : ''}}</h1>
                </td>

                <td data-label="Due Date">
              <span style="display: flex; justify-content: center; width: 100% !important;">
                   <div class="">
                       <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{ $labelData->tracking_id }}" alt='QR code' height='150' width='150' />
                       @php
                           $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                       @endphp
                       <img class="bar-code" src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($labelData->tracking_id, $generatorPNG::TYPE_CODE_128)) }}"width="400" height="60">
                  </div>
                  {{--{{ $bundleID }}--}}
                  {{-- @php
                       $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                   @endphp
                   {!! $generator->getBarcode($labelData->id, $generator::TYPE_CODE_128) !!}--}}
              </span>
                    <small style="font-size: 16px;float: left;width: 100%;margin-top: 15px;font-weight: bold;">ORDER TRACKING NUMBER</small>
                    <strong style="font-size: 22px; float: left;">{{isset($labelData->tracking_id) ? $labelData->tracking_id : ''}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 100%; border: none;font-size: 15px;" data-label="Account">For any questions or information about this package please call 1-647-931-6176 OR email support@joeyco.com</td>
            </tr>
            </tbody>
        </table>
        </div>

        <div class="">

        </div>
    @endforeach
</div>
</body>
@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
@endsection


<script type="text/javascript">


</script>

</html>



