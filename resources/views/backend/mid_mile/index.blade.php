<?php
use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;
use App\Slots;
use App\Sprint;
?>
@extends( 'backend.layouts.app' )
@section('title', 'Hub And Stores')
@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
          rel="stylesheet"/>
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>
    <link href="{{ backend_asset('libraries/first-mile-hub/index.css') }}" rel="stylesheet">
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
        <div class="alert-message"></div>
        <div class="custom_us">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-green">
                    <button style="color:#f5f5f5" ; type="button" class="close" data-dismiss="alert"><strong><b><i
                                        class="fa fa-close"></i></b></strong></button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-red">
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
                        <h3>Mid Mile Hubs List<small></small></h3>
                    </div>
                </div>

                <div class="clearfix"></div>
{{--            <div class="clearfix"></div>--}}
            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panell">
                        <div class="x_title">
                            <?php
                                if (!isset($_REQUEST['date'])) {
                                    $date = date('Y-m-d');
                                } else {
                                    $date = $_REQUEST['date'];
                                }
                            ?>
                            <form method="get" action="" class="col-md-6">
                                <div class="col-md-6">
                                <label>Search By Date</label>
                                <input type="date" name="date" id="date" required="" placeholder="Search"
                                       value="<?php echo $date ?>">
                                </div>
                                <div class="col-md-4">
                                <button class="btn btn-primary" type="submit" style="margin-top: -3%,4%">
                                    Go</a> </button>
                                </div>
                            </form>
                                <div class="col-md-6 btn_col">
                                    <button type='button' class='counts btn btn orange-gradient actBtn'
                                            data-id='{{$id}}'>Count<i class='fa fa-eye'></i>
                                    </button>

                                    <button type='button' class='route btn btn black-gradient actBtn'
                                            data-id='{{$id}}'>Submit For Route <i class='fa fa-eye'></i>
                                    </button>

                                    <a href='{{ url('mid/mile/slots/list/hub_id/'. $id) }}'
                                       class='btn btn orange-gradient actBtn'>View Slots <i
                                                class='fa fa-eye'></i></a>
                                </div>


                        {{--                            <a href="{{ route('create.hub.stores') }}">--}}
                        {{--                                <button style="margin-left:10px" class="btn green-gradient"><i class="fa fa-plus"></i>--}}
                        {{--                                    Create Zone--}}
                        {{--                                </button>--}}
                        {{--                            </a>--}}
                        <!-- <button type='button' style="margin-left:10px" class="routeCount btn btn green-gradient actBtn" data-toggle="modal" > <i class="fa fa-eye"></i>Get Total Orders Count</button> -->
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th style="width: 11%;">Hub Id</th>
                                        <th style="width: 11%;">Hub Title</th>
                                        <th style="width: 11%;">Hub Address</th>
                                        <th style="width: 11%;">Hub Order Count</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $hub)
                                            <tr>
                                                <td>{{ $hub->id }}</td>
                                                <td>{{ $hub->title }}</td>
                                                <td>{{ $hub->address }}</td>
                                                <td>{{ $hub->sprint->count() }}</td>
                                            </tr>
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
    {{--  Loader  --}}
    <div id="wait" style="display:none;position:fixed;top:50%;left:50%;padding:2px;"><img
                src="{{app_asset('images/loading.gif')}} " width="104" height="64"/><br></div>

    {{--  Submit For Route For Routific VRP  --}}
    <div id="ex10" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> Submit For Route</h4>
                </div>
                <form action='../mid/mile/job/store' method='post' class='submitForRoute'>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="hub-store" name="hub-store" value="">
                    <input type="hidden" id="create_date" name="create_date" value=<?php echo date("Y-m-d") ?> >
                    <div class="form-group">
                        <p><b>Are you sure you want to Submit For Route ?</b></p>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green-gradient btn-xs">Yes</button>
                        <button type="button" class="btn red-gradient btn-xs" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{--  Get Count Of Hub Stores Orders  --}}
    <div id="ex20" class="modal" style="display: none;margin-top: 245px;">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
{{--                    <a class="btn black-gradient" data-dismiss="modal" aria-hidden="true">Close</a>--}}
                    <a class="close" data-dismiss="modal" >&times;</a>
                    <h4 style="text-align: -webkit-center;font-size: x-large;" id="count_detail" class="modal-title">
                        Count Details</h4>
                </div>
{{--                <div style="padding-left: 208px;" class="form-group">--}}
{{--                    <label style=" font-size: 14px; width: 40%; color: black">Title :</label>--}}
{{--                    <p style="    font-size: 14px;     margin-left: 113px;color: black" id="name"></p>--}}
{{--                </div>--}}

                <div style="padding-left: 208px;" class="form-group">
                    <label style=" font-size: 14px; width: 40%; color: black">Order Count:</label>
                    <p style="    font-size: 14px;     margin-left: 113px;color: black" id="order"></p>
                </div>
                <div style="padding-left: 208px;" class="form-group">
                    <label style="    font-size: 14px;width: 40%;color: black">Total joeys count</label>
                    <p style="    font-size: 14px;    margin-left: 113px;color: black" id="joeys_count"></p>
                </div>
                <div style="padding-left: 208px;" class="form-group">
                    <label style="    font-size: 14px;width: 40%;color: black">Slot details</label>
                    <p style="    font-size: 14px;color: black" id="slots_detail"></p>
                </div>
                <div style="padding-left: 208px;" class="form-group">
                    <a type='button' id="aaa" style="margin-top:10px;width: 168px;"
                       class='route btn btn black-gradient actBtn' data-id='17'>Submit For Route <i class='fa fa-eye'></i></a>
                </div>
            </div>
        </div>
    </div>

{{--    <script type="text/javascript">--}}
{{--        function closeModal() {--}}
{{--            alert('asd');--}}
{{--            $(this).modal("hide");--}}
{{--        }--}}
{{--    </script>--}}

    <script type="text/javascript">
        {{-- Show and hide loader gif script --}}
        $(document).ready(function () {
            $(document).ajaxStart(function () {
                $("#wait").css("display", "block");
            });
            $(document).ajaxComplete(function () {
                $("#wait").css("display", "none");
            });
            $("button").click(function () {
                $("#txt").load("demo_ajax_load.asp");
            });
        });

        // Vendor ids dropdown in hub stores listing
        $(document).ready(function () {
            $(".accordion").click(function () {
                //toggleactiveclass
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $(this).addClass('active');
                }
                //addcssClass
                if ($(this).hasClass('active')) {
                    $(this).parent().find(".panell").css({
                        "maxHeight": "20px",
                    });
                } else {
                    $(this).parent().find(".panell").css({
                        "maxHeight": "0px",
                    });
                }
            });

            var i = 0;
            $(document).ready(function () {
                i++;
                var scntDiv = $('#add_words');
                $('<div class="line"><label>Postal Code </label><input type="text" value="" name="postal[]" maxlength="3 " style="padding-left: 15px;" placeholder="Postal code" pattern="[A-Za-z]{1}[0-9]{1}[A-Za-z]{1}" required  /> <button class="remScnt btn red-gradient">x</button></div>').appendTo(scntDiv);
            });

            var scntDiv = $('#add_words');
            var wordscount = 1;
            // var i = $('.line').size() + 1;
            $('#add').click(function () {
                // alert()
                var inputFields = parseInt($('#inputs').val());
                for (var n = i; n < inputFields; ++n) {
                    wordscount++;
                    $('<div class="line"><label>Postal Code </label><input type="text" value="" name="postal[]" maxlength="3" placeholder="Postal code" pattern="[A-Za-z]{1}[0-9]{1}[A-Za-z]{1}" required  /> <button class="remScnt btn red-gradient">x</button></div>').appendTo(scntDiv);
                    i++;
                }
                return false;
            });
            //    Remove button
            $('#add_words').on('click', '.remScnt', function () {
                if (i > 1) {
                    $(this).parent().remove();
                    i--;
                }
                return false;
            });
        });


        // close modal function on click 2022-05-19


        // detailsFunc
        $(document).ready(function () {
            $(".details").click(function () {
                var a;
//    var element = $(this);
                var del_id = element.attr("data-id");

                // console.log(del_id);
                $.ajax({
                    type: "GET",
                    url: '../../zonestesting/' + del_id + '/detail',

                    success: function (data) {
                        a = JSON.parse(data);
                        console.log(a);

                        console.log(a['data']['hub_id']);


                        $('#zone_id').html('' + a['data']['id']);
                        $('#hub_id_d').html('' + a['data']['hub_id']);
                        // console.log($("#hub_id"))
                        $('#title_d').html('' + a['data']['title']);
                        $('#address_d').html('' + a['data']['address']);


                        arrNew_d = [];
                        var post = '';
                        $.each(a['postalcodedata'], function (i, val) {
                            //  arrNew_d.push(val['postal_code'])
                            if (post == "") {
                                post = val['postal_code'];
                            } else {
                                post = post + ',' + val['postal_code'];
                            }
                            $('#postal_code_d').html('' + post);
                        })


                        $('#ex3').modal();


                    }

                });
            });
        });
        //routeCount function routeCount
        $(document).ready(function () {
            $(".routeCount").click(function () {
                var a;

                var element = $(this);
                var date = $('#date').val()

                // var del_id = element.attr("data-id");

                var id = $('#hub_id').val();
                $.ajax({
                    type: "GET",
                    url: '../../zonestesting/count/' + id + '/' + date,


                    beforeSend: function () {
                        // Show image container
                        $("#wait").show();
                    },
                    success: function (data) {
                        a = JSON.parse(data);
                        console.log(a);

                        $('#total_orders').html('' + a.orders);
                        $('#not_in_route').html('' + a.d_orders);


                        $('#ex21').modal();


                    },
                    complete: function (data) {
                        // Hide image container
                        $("#wait").hide();
                    }
                });
            });
        });

        //order count function
        $(document).ready(function () {
            $(".counts").click(function () {
                var a;
                var element = $(this);
                var date = $('#date').val()
                var hub_id = element.attr("data-id");

                $.ajax({
                    type: "GET",
                    url: './mile/order/count/hub_id/' + hub_id + '/date/' + date,
                    success: function (data) {
                        $(".loader").hide();
                        $(".loader-background").hide();
                        $("#wait").css("display", "none");
                        $('#ex20').modal();
                        a = JSON.parse(data);
                        console.log(a);
                        // $('#count_detail').html('' + a.title);
                        // $('#name').html('' + a.title);
                        $('#d').html('' + a.id);
                        $('#order').html('' + a.orders);
                        $('#d-orders').html('' + a.not_in_route);
                        $('#joeys_count').html('' + a.joeys_count);
                        var x = '';
                        for (var i = 0; i < a.slots_detail.length; i++) {
                            x = x + a['slots_detail'][i].name + ":" + "" + a['slots_detail'][i].joey_count + ' ';
                        }
                        $('#slots_detail').html(x);
                        $('#ex20').find('#aaa').attr('data-id', hub_id);
                        // $('#ex20').modal();

                    }
                });
            });
        });


        // updateFunc
        $(document).ready(function () {
            $(".update").click(function () {
                var a;
                var element = $(this);
                var del_id = element.attr("data-id");
                // console.log(del_id);
                $.ajax({
                    type: "GET",
                    url: '../../zonestesting/' + del_id + '/update',
                    success: function (data) {
                        a = JSON.parse(data);
                        console.log(a);
                        $('#id_time').val('' + a['data']['id']);
                        $('#title_edit').val('' + a['data']['title']);
                        $('#address_edit').val('' + a['data']['address']);
                        $('.testing').val('' + a['data']['zone_type']);


                        arrNew = [];
                        $.each(a['postalcodedata'], function (i, val) {
                            arrNew.push(val['postal_code'])
                        })

                        var addInputs = $('.addInputs');
                        var inputcount = arrNew.length;

                        var i = 0;
                        $(addInputs).empty();
                        for (var n = i; n < inputcount; ++n) {
                            $('<div class="lineEdit"><input type="text" value=' + arrNew[i] + ' name="postal_code_edit[]" maxlength="3" placeholder="Postal code" pattern="[A-Za-z]{1}[0-9]{1}[A-Za-z]{1}" class="form-control" required /><button class="remScntedit btn red-gradient" >x</button></div>').appendTo(addInputs);
                            i++;
                        }


                        $("#addmore").click(function () {

                            $('<div class="lineEdit"><input type="text" value="" name="postal_code_edit[]" maxlength="3"  placeholder="Postal code" pattern="[A-Za-z]{1}[0-9]{1}[A-Za-z]{1}" class="form-control" required><button class="remScntedit btn red-gradient" >x</button></div>').appendTo(addInputs);
                            $('#addInputs').append(lineEdit.clone());
                        });


                        $('.addInputs').on('click', '.remScntedit', function () {
                            var addInputs = $('.remScntedit');

                            if (addInputs.length > 1) {

                                $(this).parent().remove();
                                i--;
                            }
                            return false;
                        });


                        $('#ex2').modal();
                    }
                });
            });
        });

        //DeleteFunc
        $(function () {
            $(".delete").click(function () {

                var element = $(this);
                var del_id = element.attr("data-id");
                $('#delete_id').val('' + del_id);
                $('#ex4').modal();
            });
        });

        // clearOrder
        $(document).on('click', '.totalOrderCount', function () {
            element = $(this);

            var hub_id = element.attr("data-id");
            let date = document.getElementsByName('date')[0].value;
            $(".loader").show();
            $(".loader-background").show();
            $.ajax({
                type: "post",
                url: "{{ URL::to('backend/total/order/notinroute')}}",
                data: {id: hub_id, date: date},
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                success: function (data) {
                    $(".loader").hide();
                    $(".loader-background").hide();
                    if (data.status_code == 200) {


                        $('#totalordercount').modal();
                        $('#totalordercount #total_orders_counts').text(data.total_count);
                        $('#totalordercount #not_in_routes_counts').text(data.not_in_route_counts);
                    }


                },
                error: function (error) {
                    $(".loader").hide();
                    $(".loader-background").hide();

                    bootprompt.alert('some error');
                }
            });


        });


    </script>

    <script type="text/javascript">
        // Datatable
        $(document).ready(function () {

            $('#datatable').DataTable({

                "lengthMenu": [25, 50, 100, 250, 500, 750, 1000]


            });

            $(".group1").colorbox({height: "50%", width: "50%"});

            $(document).on('click', '.status_change', function (e) {
                var Uid = $(this).data('id');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to change user status??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {

                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo URL::to('/'); ?>/api/changeUserStatus/" + Uid,
                                    data: {},
                                    success: function (data) {
                                        if (data == '0' || data == 0) {
                                            var DataToset = '<button type="btn" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="' + Uid + '" data-target=".bs-example-modal-sm">Blocked</button>';
                                            $('#CurerntStatusDiv' + Uid).html(DataToset);
                                        } else {
                                            var DataToset = '<button type="btn" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="' + Uid + '" data-target=".bs-example-modal-sm">Active</button>'
                                            $('#CurerntStatusDiv' + Uid).html(DataToset);
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

            $(document).on('click', '.form-delete', function (e) {

                var $form = $(this);
                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to delete user ??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
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

        //show Route submit conformation alert
        $(function () {
            $(".route").click(function () {
                var element = $(this);
                var del_id = element.attr("data-id");
                console.log(del_id);
                $('#hub-store').val('' + del_id);
                $('#ex10').modal();
                $('#ex20').modal('hide');
            });
        })

        //Submit Route Request for First mile 2022-03-21
        $('.submitForRoute').submit(function (event) {
            event.preventDefault();
            // get the form data
            var data = new FormData();
            data.append('hub_id', $('input[name=hub-store]').val());
            data.append('create_date', $('#date').val());

            // var selectedDate = $('#date').val();
            $('#ex10').modal('toggle');

            // process the form
            $(".loader").show();
            $(".loader-background").show();
            // console.log('after');
            //run ajax with data
            $.ajax({
                url: $('.submitForRoute').attr('action'),
                type: 'POST',
                contentType: false,
                processData: false,
                data: data,
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                //show success response
                success: function (data) {
                    // $('#ex20').toggle();
                    // console.log('success');
                    $(".loader").hide();
                    $(".loader-background").hide();
                    $('#ex20').modal('hide');
                    if (data.status_code == 200) {

                        $('.alert-message').html('<div class="alert alert-success alert-green"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.success + '</strong>');
                    } else {
                        $('.alert-message').html('<div class="alert alert-danger alert-red"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.error + '</strong>');
                    }
                },
                failure: function (result) {
                    // $('#ex20').modal('hide');
                    $(".loader").hide();
                    $(".loader-background").hide();
                    bootprompt.alert(result);
                }
            });
            event.preventDefault();
        });
    </script>
@endsection