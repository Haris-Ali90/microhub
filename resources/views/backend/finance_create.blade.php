<?php
$user = Auth::user();
if ($user->email != "admin@gmail.com") {

    $data = explode(',', $user['rights']);
    $permissions = explode(',', $user['permissions']);
} else {
    $data = [];
    $permissions = [];
}
?>

@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')

@section('CSSLibraries')
    <style>
        .dashboard-statistics-box {
            min-height: 400px;
            margin: 15px 0px;
            position: relative;
            box-sizing: border-box;
        }

        .dashboard-statistics-box.dashboard-statistics-tbl-show td {
            padding-top: 52px;
            padding-bottom: 52px;
        }
    </style>
@endsection
@section('JSLibraries')
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ backend_asset('nprogress/nprogress.js') }}"></script>
    <script src="{{ backend_asset('libraries/gauge.js/dist/gauge.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/skycons/skycons.js') }}"></script>
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>

@endsection


@section('content')
    <!--right_col open-->
    <div class="page-content-wrapper cutomMainbox_us" style="min-height:1100px !important">
                <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
                <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Delete Record</h4>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this record?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn default" data-dismiss="modal">No</button>
                                <button type="button" class="btn blue" id="deleteButton" data-dismiss="modal">Yes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
                <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
                    <!-- BEGIN PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
            <h3 class="page-title">Joey Plan Create <small></small></h3>
        <ol class="breadcrumb">

                            <li class="breadcrumb-item"><a href="https://finance.joeyco.com/dashboard">Dashboard</a></li>


                            <li class="breadcrumb-item"><a href="https://finance.joeyco.com/joey-plan">Joey Plan List</a></li>


                            <li class="breadcrumb-item active">Add</li>

            </ol>


        <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->

    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">

        <div class="session-wrapper ">


    </div>        <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet box blue">

                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-plus"></i> Joey Plan Create
                    </div>
                </div>

                <div class="portlet-body">

                    <h4>&nbsp;</h4>

                    <form method="POST" action="https://finance.joeyco.com/joey-plan" class="form-horizontal" role="form" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="kNJd2mlCxEyrVF0BF0L5GM9411RrQqFeyYeIszm1">                        <input type="hidden" name="_method" value="POST">
                        <!--from-input-wraper-open-->
                        <div class="row from-input-wraper">

                            <div class="col-md-12">
                                <p class="section-heading">Plan Main Details Section</p>
                            </div>

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Plan Name *</label>

                                    <input type="text" name="plane_name" value="" class="form-control" required="">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Internal Name *</label>
                                    <input type="text" name="internal_name" value="" class="form-control" required="">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Category Vehicle Using Type *</label>
                                    <select name="vehicle_using_type" value="" class="form-control vehicle_using_type" required="">
                                        <option value="">Please Select an option</option>
                                        <option value="0">Personal Vehicle</option>
                                        <option value="1">JoeyCo Vehicle</option>
                                    </select>
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Scheduled Commission </label>
                                    <input type="number" min="0" step="0.01" name="scheduled_commission" value="0" class="form-control">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Unscheduled commission </label>
                                    <input type="number" min="0" step="0.01" name="unscheduled_commission" value="0" class="form-control">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Hourly Rate </label>
                                    <input type="number" min="0" step="0.01" name="hourly_rate_main" value="0" class="form-control">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Can View All Orders </label>
                                    <select name="view_all_orders" value="0" class="form-control view_all_orders">
                                    <option value="0" selected="">No</option>
                                    <option value="1">Yes</option>
                                    </select>
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Has Minimum Hourly Rate </label>
                                    <select name="has_minimum_hourly" value="0" class="form-control has_minimum_hourly">
                                    <option value="0" selected="">No</option>
                                    <option value="1">Yes</option>
                                    </select>
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Minimum Hourly Income </label>
                                    <input type="number" min="0" step="0.01" name="minimum_hourly_income" value="0" class="form-control">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Cash Limit </label>
                                    <input type="number" min="0" step="0.01" name="cash_limit" value="0" class="form-control">
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col">
                                <div class="form-group">
                                    <label class="control-label">Plan Type*</label>
                                    <select name="plane_type" value="" class="form-control plane-type" required="">
                                    <option value="">Please Select an option</option>
                                    <option date-sub-section-names="Sub contractor (Regular)|Sub contractor custom routing|Sub contractor big box" value="sub_contractor|sub_contractor_custom_routing|sub_contractor_big_box">Sub-Contractor (Joey)</option>

                                    <option date-sub-section-names="Sub hourly area (Regular)|Downtown hourly area (Regular)|Sub area custom routing|Downtown area custom routing|Sub area big box|Downtown area big box" value="sub_hourly|downtown_hourly|sub_hourly_custom_routing|downtown_hourly_custom_routing|sub_hourly_big_box|downtown_hourly_big_box">By Area (by zone hourly)</option>
                                    <option date-sub-section-names="Sub area (Regular)|Downtown area (Regular)|Sub area custom routing|Downtown area custom routing|Sub area big box|Downtown area big box" value="sub_per_drop|downtown_per_drop|sub_per_drop_custom_routing|downtown_per_drop_custom_routing|sub_per_drop_big_box|downtown_per_drop_big_box">By Area (by zone per drop)</option>
                                    <option date-sub-section-names="Broker (Regular)|Broker custom routing|Broker big box" value="brooker|brooker_custom_routing|brooker_big_box">Broker (joey contractor)</option>
                                    <!--<option value="low|high">low|high</option>-->
                                    <option date-sub-section-names="Bracket pricing (Regular)" value="bracket_pricing|hourly|custom_route|big_box">Bracket pricing hourly (tier pricing)</option>
                                    <option date-sub-section-names="Bracket pricing (Regular)" value="bracket_pricing|per_drop|custom_route|big_box">Bracket pricing per drop (tier pricing)</option>
                                    <option date-sub-section-names="Group zone" value="group_zone_pricing_per_drop|custom_route|big_box">Group zone pricing per drop</option>
                                    </select>
                                                                    </div>
                                <!--from-input-col-close-->
                            </div>
                            <!--from-input-wraper-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col select-hub hide">
                                <div class="form-group">
                                    <label class="control-label">Select Hub *</label>
                                    <select name="hub" value="" class="form-control hub-selection ">
                                    <option value="">Select an option</option>
                                    <option value="17">Toronto</option>
                                    <option value="19">Ottawa</option>
                                    <option value="16">Montreal</option>
                                    </select>
                                                                    </div>
                            </div>
                            <!--from-input-col-close-->

                            <!--from-input-col-open-->
                            <div class="col-md-4 col-sm-6 from-input-col select-hub hide">
                                <div class="form-group">
                                    <label class="control-label">Plan Base Amount</label>
                                    <input type="number" min="0" step="0.01" name="plan_base_amount" value="2.95" class="form-control baseamount" required="">
                                </div>
                            </div>
                            <!--from-input-col-close-->


                            <!--include-group-zone-section-->
                            <!--from-block-pricing-open-->
<div class="col-sm-12 no-padding from-input-wraper calculate-btn-bind-with-inputs form-section-group-by-zone select-hub hide">
    <div class="col-sm-12 col-md-12">
        <p class="section-heading">Create Group Of Zones</p>
    </div>

    <!--group-zone-form-main-wrap-open-->
    <div class="row group-zone-form-main-wrap row-no-gutters">



            <input type="hidden" value="" class="group_zone_id" name="group_zone_id">
            <input type="hidden" value="" class="group_zone_hub_name" name="group_zone_hub_name">
            <input type="hidden" value="" class="group_zone_hub_id" name="group_zone_hub_id">

            <!--from-input-col-open-->
            <div class="col-md-4 col-sm-6 from-input-col ">
                <div class="form-group">
                    <label class="control-label">Group Name *</label>
                    <input type="text" name="group_name" class="form-control group_name">
                </div>
            </div>
            <!--from-input-col-close-->
            <!--from-input-col-open-->
            <div class="col-md-5 col-sm-6 from-input-col ">
                <div class="form-group">
                    <label class="control-label">Zone List *</label>
                    <select class="form-control group_zones select2-hidden-accessible" name="group_zones[]" multiple="" data-select2-id="select2-data-1-jepn" tabindex="-1" aria-hidden="true">
                    </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="select2-data-2-1pd0" style="width: 100px;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered" id="select2-group_zones-4k-container"></ul><span class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" autocomplete="off" aria-describedby="select2-group_zones-4k-container" placeholder="Select an option" style="width: 100%;"></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>

                </div>
            </div>
            <!--from-input-col-close-->

            <!--from-input-col-open-->
            <div class="col-md-3 col-sm-12 mt-27">
                <div class="form-group">
                    <button type="button" class="btn orange group-zone-save-btn"> Save </button>
                    <button type="button" class="btn orange group-zone-reset-btn"> Reset form </button>
                </div>
            </div>
            <!--from-input-col-close-->



    </div>
    <!--group-zone-form-main-wrap-close-->

    <!--group-zone-lis-open-->
    <div class="row group-zone-list row-no-gutters">
        <div class="col-md-12 col-sm-12">
            <table class="table table-striped table-bordered table-hover group-by-zone-tbl">
                <thead>
                    <tr><th class="group-by-zone-name">Group Name</th>
                    <th class="group-by-zones">Group Zones</th>
                    <th class="group-by-zones-action">Action</th>
                </tr></thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!--group-zone-lis-close-->



</div>
<!--from-block-pricing-close-->
                            <!--include-bracket-pricing-section-->
                            <!--from-input-wraper-bracket-pricing-open-->
<div class="col-sm-12 no-padding from-input-wraper calculate-btn-bind-with-inputs form-section-3 select-hub hide">

    <div class="col-sm-12 col-md-12">
        <p class="section-heading">Plan Sub Details Section</p>
    </div>

    <div class="col-md-12 bracket-calculation-btn-div-sticky">
        <a class="btn green bracket-calculate-btn"> Calculate </a>
        <a class="btn green add-bracket-input-box"> Add Bracket </a>
    </div>

    <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap bracket-pricing-detail-form-wrap hide">

        <!--bracket-pricing-input-box-open-->
        <div class="col-sm-12 bracket-pricing-input-box ">

            <!--bracket-pricing-input-box-inner-open-->
            <div class="col-sm-12 bracket-pricing-input-box-inner bracket-pricing-input-can-add bracket-pricing-input-box-org">

                <div class="col-sm-12 col-md-12">
                    <p class="detail-form-wrap-h4">Bracket pricing box (Regular)</p>
                </div>

                <input type="hidden" name="bracket_section_type[]" class="bracket_section_type" value="">
                <input type="hidden" name="bracket_section_sort_order[]" class="bracket_section_sort_order" value="">

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col  hide">
                    <div class="form-group">
                        <label class="control-label">Total Hours *</label>
                        <input type="text" name="total_hours_bracket[]" class="duration form-control timepicker bracket-total-hours" data-input-type="date">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Min Drops *</label>
                        <input type="number" min="0" name="min_drops[]" class="form-control bracket-min-drops" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Max Drops *</label>
                        <input type="number" min="0" name="max_drops[]" class="form-control bracket-max-drops" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Hourly Rate *</label>
                        <input type="number" min="0" step="0.001" name="hourly_rate_bracket[]" class="form-control bracket-hourly-rate" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Per Drop Amount *</label>
                        <input type="number" min="0" step="0.001" name="amount_bracket[]" class="form-control bracket-amount" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Truck Charges</label>
                        <input type="number" min="0" step="0.001" name="gas_truck_amount_bracket[]" class="form-control bracket-gas-truck-amount" value="0" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Tax %</label>
                        <input type="number" min="0" step="0.001" value="0" name="tax_bracket[]" class="tax form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges </label>
                        <input type="number" min="0" name="sub_joey_charges_bracket[]" class="form-control bracket-sub-joey-charges readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges min / max</label>
                        <input type="text" name="sub_joey_charges_min_max_bracket[]" class="form-control bracket-sub-joey-min-mix-charges readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges </label>
                        <input type="number" min="0" name="tax_charges_bracket[]" class="form-control bracket-joey-tax-charges readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges min / max</label>
                        <input type="text" name="tax_charges_min_max_bracket[]" class="form-control bracket-joey-tax-min-max-charges readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey charges </label>
                        <input type="number" min="0" name="total_cost_bracket[]" class="form-control bracket-total-cost readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey Charges min /  max </label>
                        <input type="text" name="total_cost_min_max_bracket[]" class="form-control bracket-total-cost-min-max readonly-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue min / max</label>
                        <input type="text" name="rev_min_max_bracket[]" class="bracket-rev-min-max form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue</label>
                        <input type="number" min="0" step="0.001" name="bracket_rev[]" class="bracket-rev form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="company_tax_charges_bracket[]" class="company_tax_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges min / max</label>
                        <input type="text" name="company_tax_charges_min_max_bracket[]" value="0 / 0" class="company-tax-charges-min-max form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="company_total_charges_bracket[]" class="company_total_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges min / max</label>
                        <input type="text" name="company_total_charges_min_max_bracket[]" value="0 / 0" class="company-total-charges-min-max form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins </label>
                        <input type="number" min="0" step="0.001" name="bracket_margins[]" class="bracket-margins form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins min / max </label>
                        <input type="text" min="0" name="margins_min_max_bracket[]" step="0.001" class="bracket-margins-min-max form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->



            </div>
            <!--bracket-pricing-input-box-inner-close-->

            <!--bracket-pricing-input-box-inner-open-->
            <div class="col-sm-12 bracket-pricing-input-box-inner bracket-pricing-custom-routing bracket-pricing-static-box hide">

                <div class="col-sm-12 col-md-12">
                    <p class="detail-form-wrap-h4">Custom Routing Section</p>
                </div>

                <input type="hidden" name="bracket_custom_route_section_type[]" class="bracket_section_type bracket_custom_route_section_type" value="">
                <input type="hidden" name="bracket_custom_route_section_sort_order[]" class="bracket_section_sort_order bracket_custom_route_section_sort_order" value="">

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col  hide">
                    <div class="form-group">
                        <label class="control-label">Total Hours *</label>
                        <input type="text" name="total_hours_bracket_custom_route[]" class="duration form-control timepicker bracket-total-hours-custom-route" data-input-type="date">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Min Drops *</label>
                        <input type="number" min="0" name="min_drops_custom_route[]" class="form-control bracket-min-drops-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Max Drops *</label>
                        <input type="number" min="0" name="max_drops_custom_route[]" class="form-control bracket-max-drops-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Hourly Rate *</label>
                        <input type="number" min="0" step="0.001" name="hourly_rate_bracket_custom_route[]" class="form-control bracket-hourly-rate-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Per Drop Amount *</label>
                        <input type="number" min="0" step="0.001" name="amount_bracket_custom_route[]" class="form-control bracket-amount-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Truck Charges</label>
                        <input type="number" min="0" step="0.001" name="gas_truck_amount_bracket_custom_route[]" class="form-control bracket-gas-truck-amount-custom-route" value="0" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Tax %</label>
                        <input type="number" min="0" step="0.001" value="0" name="tax_custom_route[]" class="tax-custom-route form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges </label>
                        <input type="number" min="0" name="sub_joey_charges_custom_route[]" class="form-control bracket-sub-joey-charges-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges min / max</label>
                        <input type="text" name="sub_joey_charges_min_max_custom_route[]" class="form-control bracket-sub-joey-charges-min-max-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges </label>
                        <input type="number" min="0" name="tax_charges_custom_route[]" class="form-control bracket-joey-tax-charges-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges min / max </label>
                        <input type="text" name="tax_charges_min_max_custom_route[]" class="form-control bracket-joey-tax-min-max-charges-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net joey charges </label>
                        <input type="number" min="0" name="total_cost_bracket_custom_route[]" class="form-control bracket-total-cost-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey Charges min /  max </label>
                        <input type="text" name="total_cost_min_max_custom_route[]" class="form-control bracket-total-cost-min-max-custom-route readonly-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue min / max</label>
                        <input type="text" name="rev_min_max_custom_route[]" class="bracket-rev-min-max-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue</label>
                        <input type="number" min="0" step="0.001" name="bracket_rev_custom_route[]" class="bracket-rev-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="company_tax_charges_custom_route[]" class="company-tax-charges-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges min / max</label>
                        <input type="text" name="company_tax_charges_min_max_custom_route[]" class="company-tax-charges-min-max-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="company_total_charges_custom_route[]" class="company-total-charges-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges min / max</label>
                        <input type="text" name="company_total_charges_min_max_custom_route[]" class="company-total-charges-min-max-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins </label>
                        <input type="number" min="0" step="0.001" name="bracket_margins_custom_route[]" class="bracket-margins-custom-route  form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins min / max </label>
                        <input type="text" name="margins_min_max_custom_route[]" min="0" step="0.001" class="bracket-margins-min-max-custom-route form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->



            </div>
            <!--bracket-pricing-input-box-inner-close-->


            <!--bracket-pricing-input-box-inner-open-->
            <div class="col-sm-12 bracket-pricing-input-box-inner bracket-pricing-big-box bracket-pricing-static-box hide">

                <div class="col-sm-12 col-md-12">
                    <p class="detail-form-wrap-h4">Big Box Section</p>
                </div>
                <input type="hidden" name="bracket_custom_route_section_type[]" class="bracket_section_type bracket_big_box_section_type" value="">
                <input type="hidden" name="bracket_custom_route_section_sort_order[]" class="bracket_section_sort_order bracket_big_box_section_sort_order" value="">

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col  hide">
                    <div class="form-group">
                        <label class="control-label">Total Hours *</label>
                        <input type="text" name="total_hours_bracket_custom_route[]" class="duration form-control timepicker bracket-total-hours-custom-route" data-input-type="date">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Min Drops *</label>
                        <input type="number" min="0" name="min_drops_custom_route[]" class="form-control bracket-min-drops-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Max Drops *</label>
                        <input type="number" min="0" name="max_drops_custom_route[]" class="form-control bracket-max-drops-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Hourly Rate *</label>
                        <input type="number" min="0" step="0.001" name="hourly_rate_bracket_custom_route[]" class="form-control bracket-hourly-rate-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Per Drop Amount *</label>
                        <input type="number" min="0" step="0.001" name="amount_bracket_custom_route[]" class="form-control bracket-amount-custom-route" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Truck Charges</label>
                        <input type="number" min="0" step="0.001" name="gas_truck_amount_bracket_custom_route[]" class="form-control bracket-gas-truck-amount-custom-route" value="0" data-input-type="number">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Tax %</label>
                        <input type="number" min="0" step="0.001" value="0" name="tax_custom_route[]" class="tax-custom-route form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges </label>
                        <input type="number" min="0" name="sub_joey_charges_custom_route[]" class="form-control bracket-sub-joey-charges-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges min / max</label>
                        <input type="text" name="sub_joey_charges_min_max_custom_route[]" class="form-control bracket-sub-joey-charges-min-max-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges </label>
                        <input type="number" min="0" name="tax_charges_custom_route[]" class="form-control bracket-joey-tax-charges-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges min / max </label>
                        <input type="text" name="tax_charges_min_max_custom_route[]" class="form-control bracket-joey-tax-min-max-charges-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net joey charges </label>
                        <input type="number" min="0" name="total_cost_bracket_custom_route[]" class="form-control bracket-total-cost-custom-route readonly-input" data-input-type="number" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey Charges min /  max </label>
                        <input type="text" name="total_cost_min_max_custom_route[]" class="form-control bracket-total-cost-min-max-custom-route readonly-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue min / max</label>
                        <input type="text" name="rev_min_max_custom_route[]" class="bracket-rev-min-max-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue</label>
                        <input type="number" min="0" step="0.001" name="bracket_rev_custom_route[]" class="bracket-rev-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="company_tax_charges_custom_route[]" class="company-tax-charges-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges min / max</label>
                        <input type="text" name="company_tax_charges_min_max_custom_route[]" class="company-tax-charges-min-max-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="company_total_charges_custom_route[]" class="company-total-charges-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges min / max</label>
                        <input type="text" name="company_total_charges_min_max_custom_route[]" class="company-total-charges-min-max-custom-route form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins </label>
                        <input type="number" min="0" step="0.001" name="bracket_margins_custom_route[]" class="bracket-margins-custom-route  form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins min / max </label>
                        <input type="text" name="margins_min_max_custom_route[]" min="0" step="0.001" class="bracket-margins-min-max-custom-route form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->



            </div>
            <!--bracket-pricing-input-box-inner-close-->

            <!--from-input-col-open-->
            <div class="col-sm-12 text-right ">
                <div class="form-group">
                    <button type="submit" class="btn orange" id="save"> Save </button>
                    <a href="https://finance.joeyco.com/joey-plan" class="btn orange" id="cancel"> Cancel </a>
                </div>
            </div>
            <!--from-input-col-close-->
        </div>
        <!--bracket-pricing-input-box-close-->

    </div>
    <!--detail-form-one-wrap-close-->


</div>
<!--from-input-wraper-bracket-pricing-close-->

                            <!--include-bracket-pricing-section-->
                            <!--from-block-pricing-open-->
<div class="col-sm-12 no-padding from-input-wraper calculate-btn-bind-with-inputs form-section-2 select-hub hide">
    <div class="col-sm-12 col-md-12">
        <p class="section-heading">Plan Sub Details Section</p>
    </div>

    <div class="col-md-12 calculation-btn-div-sticky">
        <a class="btn green calculate-btn"> Calculate </a>
    </div>

        <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap detail-form-wrap-1">

        <div class="col-sm-12 col-sm-12">
            <h4 class="detail-form-wrap-h4 detail-form-wrap-h4-1">Sub area (Regular)</h4>
        </div>

        <input type="hidden" name="block_section_type[]" class="block_section_type" value="sub_per_drop">
        <input type="hidden" name="sorting_order[]" class="sorting_order" value="1">

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Drops *</label>
                <input type="number" min="0" name="drops[]" class="form-control drops drops-1" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Daily Drops *</label>
                <input type="number" min="0" name="daily_drops[]" class="daily_drops form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Weekly Drops</label>
                <input type="number" min="0" name="weekly_drops[]" class="weekly_drops form-control" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration *</label>
                <input type="text" name="duration[]" class="duration form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration Per Drop *</label>
                <input type="text" name="duration_per_drop[]" class="duration_per_drop form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Pickup Buffer *</label>
                <input type="text" name="buffer_time[]" class="buffer_time form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Hourly Rate *</label>
                <input type="number" min="0" step="0.001" name="hourly_rate[]" class="hourly_rate form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Total Hours </label>
                <input type="text" name="total_hours[]" class="total_hours form-control timepicker" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Revenue</label>
                <input type="number" min="0" step="0.001" name="rev[]" class="rev form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Tax %</label>
                <input type="number" min="0" step="0.001" value="0" name="tax[]" class="tax form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_tax_charges[]" class="company_tax_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Total Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_total_charges[]" class="company_total_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Charges *</label>
                <input type="number" min="0" step="0.001" name="amount[]" class="amount form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Sub Joey Charges </label>
                <input type="number" min="0" step="0.001" name="sub_joey_charges[]" class="sub_joey_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="tax_charges[]" class="tax_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Net Joey Charges </label>
                <input type="number" min="0" step="0.001" name="total_cost[]" class="total_cost form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Truck Charges</label>
                <input type="number" min="0" step="0.001" name="gas_truck_amount[]" class="gas_truck_amount form-control" value="0" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Margins </label>
                <input type="number" min="0" step="0.001" name="margins[]" class="margins form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

    </div>
    <!--detail-form-one-wrap-close-->
        <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap detail-form-wrap-2">

        <div class="col-sm-12 col-sm-12">
            <h4 class="detail-form-wrap-h4 detail-form-wrap-h4-2">Downtown area (Regular)</h4>
        </div>

        <input type="hidden" name="block_section_type[]" class="block_section_type" value="downtown_per_drop">
        <input type="hidden" name="sorting_order[]" class="sorting_order" value="2">

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Drops *</label>
                <input type="number" min="0" name="drops[]" class="form-control drops drops-2" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Daily Drops *</label>
                <input type="number" min="0" name="daily_drops[]" class="daily_drops form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Weekly Drops</label>
                <input type="number" min="0" name="weekly_drops[]" class="weekly_drops form-control" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration *</label>
                <input type="text" name="duration[]" class="duration form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration Per Drop *</label>
                <input type="text" name="duration_per_drop[]" class="duration_per_drop form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Pickup Buffer *</label>
                <input type="text" name="buffer_time[]" class="buffer_time form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Hourly Rate *</label>
                <input type="number" min="0" step="0.001" name="hourly_rate[]" class="hourly_rate form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Total Hours </label>
                <input type="text" name="total_hours[]" class="total_hours form-control timepicker" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Revenue</label>
                <input type="number" min="0" step="0.001" name="rev[]" class="rev form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Tax %</label>
                <input type="number" min="0" step="0.001" value="0" name="tax[]" class="tax form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_tax_charges[]" class="company_tax_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Total Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_total_charges[]" class="company_total_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Charges *</label>
                <input type="number" min="0" step="0.001" name="amount[]" class="amount form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Sub Joey Charges </label>
                <input type="number" min="0" step="0.001" name="sub_joey_charges[]" class="sub_joey_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="tax_charges[]" class="tax_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Net Joey Charges </label>
                <input type="number" min="0" step="0.001" name="total_cost[]" class="total_cost form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Truck Charges</label>
                <input type="number" min="0" step="0.001" name="gas_truck_amount[]" class="gas_truck_amount form-control" value="0" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Margins </label>
                <input type="number" min="0" step="0.001" name="margins[]" class="margins form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

    </div>
    <!--detail-form-one-wrap-close-->
        <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap detail-form-wrap-3">

        <div class="col-sm-12 col-sm-12">
            <h4 class="detail-form-wrap-h4 detail-form-wrap-h4-3">Sub area custom routing</h4>
        </div>

        <input type="hidden" name="block_section_type[]" class="block_section_type" value="sub_per_drop_custom_routing">
        <input type="hidden" name="sorting_order[]" class="sorting_order" value="3">

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Drops *</label>
                <input type="number" min="0" name="drops[]" class="form-control drops drops-3" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Daily Drops *</label>
                <input type="number" min="0" name="daily_drops[]" class="daily_drops form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Weekly Drops</label>
                <input type="number" min="0" name="weekly_drops[]" class="weekly_drops form-control" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration *</label>
                <input type="text" name="duration[]" class="duration form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration Per Drop *</label>
                <input type="text" name="duration_per_drop[]" class="duration_per_drop form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Pickup Buffer *</label>
                <input type="text" name="buffer_time[]" class="buffer_time form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Hourly Rate *</label>
                <input type="number" min="0" step="0.001" name="hourly_rate[]" class="hourly_rate form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Total Hours </label>
                <input type="text" name="total_hours[]" class="total_hours form-control timepicker" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Revenue</label>
                <input type="number" min="0" step="0.001" name="rev[]" class="rev form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Tax %</label>
                <input type="number" min="0" step="0.001" value="0" name="tax[]" class="tax form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_tax_charges[]" class="company_tax_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Total Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_total_charges[]" class="company_total_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Charges *</label>
                <input type="number" min="0" step="0.001" name="amount[]" class="amount form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Sub Joey Charges </label>
                <input type="number" min="0" step="0.001" name="sub_joey_charges[]" class="sub_joey_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="tax_charges[]" class="tax_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Net Joey Charges </label>
                <input type="number" min="0" step="0.001" name="total_cost[]" class="total_cost form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Truck Charges</label>
                <input type="number" min="0" step="0.001" name="gas_truck_amount[]" class="gas_truck_amount form-control" value="0" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Margins </label>
                <input type="number" min="0" step="0.001" name="margins[]" class="margins form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

    </div>
    <!--detail-form-one-wrap-close-->
        <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap detail-form-wrap-4">

        <div class="col-sm-12 col-sm-12">
            <h4 class="detail-form-wrap-h4 detail-form-wrap-h4-4">Downtown area custom routing</h4>
        </div>

        <input type="hidden" name="block_section_type[]" class="block_section_type" value="downtown_per_drop_custom_routing">
        <input type="hidden" name="sorting_order[]" class="sorting_order" value="4">

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Drops *</label>
                <input type="number" min="0" name="drops[]" class="form-control drops drops-4" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Daily Drops *</label>
                <input type="number" min="0" name="daily_drops[]" class="daily_drops form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Weekly Drops</label>
                <input type="number" min="0" name="weekly_drops[]" class="weekly_drops form-control" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration *</label>
                <input type="text" name="duration[]" class="duration form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration Per Drop *</label>
                <input type="text" name="duration_per_drop[]" class="duration_per_drop form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Pickup Buffer *</label>
                <input type="text" name="buffer_time[]" class="buffer_time form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Hourly Rate *</label>
                <input type="number" min="0" step="0.001" name="hourly_rate[]" class="hourly_rate form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Total Hours </label>
                <input type="text" name="total_hours[]" class="total_hours form-control timepicker" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Revenue</label>
                <input type="number" min="0" step="0.001" name="rev[]" class="rev form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Tax %</label>
                <input type="number" min="0" step="0.001" value="0" name="tax[]" class="tax form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_tax_charges[]" class="company_tax_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Total Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_total_charges[]" class="company_total_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Charges *</label>
                <input type="number" min="0" step="0.001" name="amount[]" class="amount form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Sub Joey Charges </label>
                <input type="number" min="0" step="0.001" name="sub_joey_charges[]" class="sub_joey_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="tax_charges[]" class="tax_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Net Joey Charges </label>
                <input type="number" min="0" step="0.001" name="total_cost[]" class="total_cost form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Truck Charges</label>
                <input type="number" min="0" step="0.001" name="gas_truck_amount[]" class="gas_truck_amount form-control" value="0" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Margins </label>
                <input type="number" min="0" step="0.001" name="margins[]" class="margins form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

    </div>
    <!--detail-form-one-wrap-close-->
        <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap detail-form-wrap-5">

        <div class="col-sm-12 col-sm-12">
            <h4 class="detail-form-wrap-h4 detail-form-wrap-h4-5">Sub area big box</h4>
        </div>

        <input type="hidden" name="block_section_type[]" class="block_section_type" value="sub_per_drop_big_box">
        <input type="hidden" name="sorting_order[]" class="sorting_order" value="5">

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Drops *</label>
                <input type="number" min="0" name="drops[]" class="form-control drops drops-5" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Daily Drops *</label>
                <input type="number" min="0" name="daily_drops[]" class="daily_drops form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Weekly Drops</label>
                <input type="number" min="0" name="weekly_drops[]" class="weekly_drops form-control" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration *</label>
                <input type="text" name="duration[]" class="duration form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration Per Drop *</label>
                <input type="text" name="duration_per_drop[]" class="duration_per_drop form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Pickup Buffer *</label>
                <input type="text" name="buffer_time[]" class="buffer_time form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Hourly Rate *</label>
                <input type="number" min="0" step="0.001" name="hourly_rate[]" class="hourly_rate form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Total Hours </label>
                <input type="text" name="total_hours[]" class="total_hours form-control timepicker" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Revenue</label>
                <input type="number" min="0" step="0.001" name="rev[]" class="rev form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Tax %</label>
                <input type="number" min="0" step="0.001" value="0" name="tax[]" class="tax form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_tax_charges[]" class="company_tax_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Total Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_total_charges[]" class="company_total_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Charges *</label>
                <input type="number" min="0" step="0.001" name="amount[]" class="amount form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Sub Joey Charges </label>
                <input type="number" min="0" step="0.001" name="sub_joey_charges[]" class="sub_joey_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="tax_charges[]" class="tax_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Net Joey Charges </label>
                <input type="number" min="0" step="0.001" name="total_cost[]" class="total_cost form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Truck Charges</label>
                <input type="number" min="0" step="0.001" name="gas_truck_amount[]" class="gas_truck_amount form-control" value="0" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Margins </label>
                <input type="number" min="0" step="0.001" name="margins[]" class="margins form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

    </div>
    <!--detail-form-one-wrap-close-->
        <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap detail-form-wrap-6">

        <div class="col-sm-12 col-sm-12">
            <h4 class="detail-form-wrap-h4 detail-form-wrap-h4-6">Downtown area big box</h4>
        </div>

        <input type="hidden" name="block_section_type[]" class="block_section_type" value="downtown_per_drop_big_box">
        <input type="hidden" name="sorting_order[]" class="sorting_order" value="6">

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Drops *</label>
                <input type="number" min="0" name="drops[]" class="form-control drops drops-6" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Daily Drops *</label>
                <input type="number" min="0" name="daily_drops[]" class="daily_drops form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Weekly Drops</label>
                <input type="number" min="0" name="weekly_drops[]" class="weekly_drops form-control" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration *</label>
                <input type="text" name="duration[]" class="duration form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Duration Per Drop *</label>
                <input type="text" name="duration_per_drop[]" class="duration_per_drop form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Pickup Buffer *</label>
                <input type="text" name="buffer_time[]" class="buffer_time form-control timepicker">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Hourly Rate *</label>
                <input type="number" min="0" step="0.001" name="hourly_rate[]" class="hourly_rate form-control">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col hide">
            <div class="form-group">
                <label class="control-label">Total Hours </label>
                <input type="text" name="total_hours[]" class="total_hours form-control timepicker" readonly="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Revenue</label>
                <input type="number" min="0" step="0.001" name="rev[]" class="rev form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Tax %</label>
                <input type="number" min="0" step="0.001" value="0" name="tax[]" class="tax form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_tax_charges[]" class="company_tax_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Company Total Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="company_total_charges[]" class="company_total_charges form-control" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Charges *</label>
                <input type="number" min="0" step="0.001" name="amount[]" class="amount form-control" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Sub Joey Charges </label>
                <input type="number" min="0" step="0.001" name="sub_joey_charges[]" class="sub_joey_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->


        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Joey Tax Charges</label>
                <input type="number" min="0" step="0.001" value="0" name="tax_charges[]" class="tax_charges form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Net Joey Charges </label>
                <input type="number" min="0" step="0.001" name="total_cost[]" class="total_cost form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Truck Charges</label>
                <input type="number" min="0" step="0.001" name="gas_truck_amount[]" class="gas_truck_amount form-control" value="0" required="">
            </div>
        </div>
        <!--from-input-col-close-->

        <!--from-input-col-open-->
        <div class="col-md-4 col-sm-6 from-input-col">
            <div class="form-group">
                <label class="control-label">Margins </label>
                <input type="number" min="0" step="0.001" name="margins[]" class="margins form-control readonly-input calculation-input" readonly="" required="">
            </div>
        </div>
        <!--from-input-col-close-->

    </div>
    <!--detail-form-one-wrap-close-->

    <!--from-input-col-open-->
    <div class="col-sm-12 text-right ">
        <div class="form-group">
            <button type="submit" class="btn orange" id="save"> Save </button>
            <a href="https://finance.joeyco.com/joey-plan" class="btn orange" id="cancel"> Cancel </a>
        </div>
    </div>
    <!--from-input-col-close-->

</div>
<!--from-block-pricing-close-->
                            <!--include-dynamic-block-section-->
                            <!--from-input-wraper-dynamic-section-open-->
<div class="col-sm-12 no-padding from-input-wraper calculate-btn-bind-with-inputs form-section-4  select-hub hide">

    <div class="col-sm-12 col-md-12">
        <p class="section-heading">Plan Sub Details Section</p>
    </div>

    <div class="col-md-12 bracket-calculation-btn-div-sticky">
        <a class="btn green dynamic-section-calculate-btn"> Calculate </a>
        <a class="btn green add-dynamic-section-input-box"> Add Section </a>
    </div>

    <!--detail-form-one-wrap-open-->
    <div class="row detail-form-wrap dynamic-section-detail-form-wrap hide">

        <!--dynamic-section-input-box-open-->
        <div class="col-sm-12 dynamic-section-input-box ">

            <!--dynamic-section-input-box-inner-open-->
            <div class="col-sm-12 dynamic-section-input-box-inner dynamic-section-input-can-add dynamic-section-input-box-org">

                <div class="col-sm-12 col-md-12">
                    <p class="detail-form-wrap-h4">Normal Pricing box</p>
                </div>


                <input type="hidden" name="dynamic_section_type[]" data-prefix="dynamic_section" class="dynamic_section_type" value="dynamic_section">
                <input type="hidden" name="dynamic_section_sort_order[]" class="dynamic_section_sort_order" value="3">

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Zone Group *</label>
                        <select class="form-control dynamic_section_group_zones " name="dynamic_section_group_zones[]">
                            <option value="">Select an option</option>
                        </select>

                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Drops *</label>
                        <input type="number" min="0" name="dynamic_section_drops[]" class="form-control  dynamic_section_drops">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Daily Drops *</label>
                        <input type="number" min="0" name="dynamic_section_daily_drops[]" class="dynamic_section_daily_drops form-control ">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Weekly Drops</label>
                        <input type="number" min="0" name="dynamic_section_weekly_drops[]" class="dynamic_section_weekly_drops form-control " readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Duration *</label>
                        <input type="text" name="dynamic_section_duration[]" class="dynamic_section_duration form-control timepicker ">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Duration Per Drop *</label>
                        <input type="text" name="dynamic_section_duration_per_drop[]" class="dynamic_section_duration_per_drop form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Pickup Buffer *</label>
                        <input type="text" name="dynamic_section_buffer_time[]" class="dynamic_section_buffer_time form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Hourly Rate *</label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_hourly_rate[]" class="dynamic_section_hourly_rate form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Total Hours </label>
                        <input type="text" name="dynamic_section_total_hours[]" class="dynamic_section_total_hours form-control timepicker" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue</label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_rev[]" class="dynamic_section_rev form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Tax %</label>
                        <input type="number" min="0" step="0.001" value="0" name="dynamic_section_tax[]" class="dynamic_section_tax form-control">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="dynamic_section_company_tax_charges[]" class="dynamic_section_company_tax_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="dynamic_section_company_total_charges[]" class="dynamic_section_company_total_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Charges *</label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_amount[]" class="dynamic_section_amount form-control">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges </label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_sub_joey_charges[]" class="dynamic_section_sub_joey_charges form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="dynamic_section_tax_charges[]" class="dynamic_section_tax_charges form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey Charges </label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_total_cost[]" class="dynamic_section_total_cost form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Truck Charges</label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_gas_truck_amount[]" class="dynamic_section_gas_truck_amount form-control" value="0">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins </label>
                        <input type="number" min="0" step="0.001" name="dynamic_section_margins[]" class="dynamic_section_margins form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>



            </div>
            <!--dynamic-section-input-box-inner-close-->

            <!--dynamic-section-input-box-inner-open-->
            <div class="col-sm-12 dynamic-section-input-box-inner dynamic-section-custom-route dynamic-section-static-box hide">

                <div class="col-sm-12 col-md-12">
                    <p class="detail-form-wrap-h4">Custom Routing Pricing box</p>
                </div>


                <input type="hidden" name="dynamic_section_type[]" data-prefix="custom_route_dynamic_section" class="dynamic_section_type" value="custom_route_dynamic_section">
                <input type="hidden" name="dynamic_section_sort_order[]" class="dynamic_section_sort_order" value="2">

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Drops *</label>
                        <input type="number" min="0" name="custom_route_dynamic_section_drops[]" value="1" class="form-control custom_route_dynamic_section_drops" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Daily Drops *</label>
                        <input type="number" min="0" name="custom_route_dynamic_section_daily_drops[]" class="custom_route_dynamic_section_daily_drops form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Weekly Drops</label>
                        <input type="number" min="0" name="custom_route_dynamic_section_weekly_drops[]" class="custom_route_dynamic_section_weekly_drops form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Duration *</label>
                        <input type="text" name="custom_route_dynamic_section_duration[]" class="custom_route_dynamic_section_duration form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Duration Per Drop *</label>
                        <input type="text" name="custom_route_dynamic_section_duration_per_drop[]" class="custom_route_dynamic_section_duration_per_drop form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Pickup Buffer *</label>
                        <input type="text" name="custom_route_dynamic_section_buffer_time[]" class="custom_route_dynamic_section_buffer_time form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Hourly Rate *</label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_hourly_rate[]" class="custom_route_dynamic_section_hourly_rate form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Total Hours </label>
                        <input type="text" name="custom_route_dynamic_section_total_hours[]" class="custom_route_dynamic_section_total_hours form-control timepicker" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue</label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_rev[]" class="custom_route_dynamic_section_rev form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Tax %</label>
                        <input type="number" min="0" step="0.001" value="0" name="custom_route_dynamic_section_tax[]" class="custom_route_dynamic_section_tax form-control">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="custom_route_dynamic_section_company_tax_charges[]" class="custom_route_dynamic_section_company_tax_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="custom_route_dynamic_section_company_total_charges[]" class="custom_route_dynamic_section_company_total_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Charges *</label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_amount[]" class="custom_route_dynamic_section_amount form-control">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges </label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_sub_joey_charges[]" class="custom_route_dynamic_section_sub_joey_charges form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="custom_route_dynamic_section_tax_charges[]" class="custom_route_dynamic_section_tax_charges form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey Charges </label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_total_cost[]" class="custom_route_dynamic_section_total_cost form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Truck Charges</label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_gas_truck_amount[]" class="custom_route_dynamic_section_gas_truck_amount form-control" value="0">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins </label>
                        <input type="number" min="0" step="0.001" name="custom_route_dynamic_section_margins[]" class="custom_route_dynamic_section_margins form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>

            </div>
            <!--dynamic-section-input-box-inner-close-->


            <!--dynamic-section-input-box-inner-open-->
            <div class="col-sm-12 dynamic-section-input-box-inner dynamic-section-big-box dynamic-section-static-box hide">

                <div class="col-sm-12 col-md-12">
                    <p class="detail-form-wrap-h4">Big Box Pricing box</p>
                </div>


                <input type="hidden" name="dynamic_section_type[]" data-prefix="big_box_dynamic_section" class="dynamic_section_type" value="big_box_dynamic_section">
                <input type="hidden" name="dynamic_section_sort_order[]" class="dynamic_section_sort_order" value="1">

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Drops *</label>
                        <input type="number" min="0" name="big_box_dynamic_section_drops[]" value="1" class="form-control big_box_dynamic_section_drops" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Daily Drops *</label>
                        <input type="number" min="0" name="big_box_dynamic_section_daily_drops[]" class="big_box_dynamic_section_daily_drops form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Weekly Drops</label>
                        <input type="number" min="0" name="big_box_dynamic_section_weekly_drops[]" class="big_box_dynamic_section_weekly_drops form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Duration *</label>
                        <input type="text" name="big_box_dynamic_section_duration[]" class="big_box_dynamic_section_duration form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Duration Per Drop *</label>
                        <input type="text" name="big_box_dynamic_section_duration_per_drop[]" class="big_box_dynamic_section_duration_per_drop form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Pickup Buffer *</label>
                        <input type="text" name="big_box_dynamic_section_buffer_time[]" class="big_box_dynamic_section_buffer_time form-control timepicker">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Hourly Rate *</label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_hourly_rate[]" class="big_box_dynamic_section_hourly_rate form-control">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Total Hours </label>
                        <input type="text" name="big_box_dynamic_section_total_hours[]" class="big_box_dynamic_section_total_hours form-control timepicker" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Revenue</label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_rev[]" class="big_box_dynamic_section_rev form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Tax %</label>
                        <input type="number" min="0" step="0.001" value="0" name="big_box_dynamic_section_tax[]" class="big_box_dynamic_section_tax form-control">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="big_box_dynamic_section_company_tax_charges[]" class="big_box_dynamic_section_company_tax_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Company Total Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="big_box_dynamic_section_company_total_charges[]" class="big_box_dynamic_section_company_total_charges form-control" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Charges *</label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_amount[]" class="big_box_dynamic_section_amount form-control">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Sub Joey Charges </label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_sub_joey_charges[]" class="big_box_dynamic_section_sub_joey_charges form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->


                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Joey Tax Charges</label>
                        <input type="number" min="0" step="0.001" value="0" name="big_box_dynamic_section_tax_charges[]" class="big_box_dynamic_section_tax_charges form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Net Joey Charges </label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_total_cost[]" class="big_box_dynamic_section_total_cost form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Truck Charges</label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_gas_truck_amount[]" class="big_box_dynamic_section_gas_truck_amount form-control" value="0">
                    </div>
                </div>
                <!--from-input-col-close-->

                <!--from-input-col-open-->
                <div class="col-md-4 col-sm-6 from-input-col hide">
                    <div class="form-group">
                        <label class="control-label">Margins </label>
                        <input type="number" min="0" step="0.001" name="big_box_dynamic_section_margins[]" class="big_box_dynamic_section_margins form-control readonly-input calculation-input" readonly="">
                    </div>
                </div>
                <!--from-input-col-close-->

            </div>
            <!--dynamic-section-input-box-inner-close-->

            <!--from-input-col-open-->
            <div class="col-sm-12 text-right ">
                <div class="form-group">
                    <button type="submit" class="btn orange" id="save"> Save </button>
                    <a href="https://finance.joeyco.com/joey-plan" class="btn orange" id="cancel"> Cancel </a>
                </div>
            </div>
            <!--from-input-col-close-->
        </div>
        <!--dynamic-section-input-box-close-->

    </div>
    <!--detail-form-one-wrap-close-->


</div>
<!--from-input-wraper-dynamic-section-close-->
                        </div></form>
                        <!--from-input-wraper-close-->

                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
            </div>
    <!-- /footer content -->
    <!-- /#page-wrapper -->
    <script src="https://finance.joeyco.com/assets/admin/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="https://finance.joeyco.com/assets/admin/scripts/custom/admin.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        var base_url = 'https://finance.joeyco.com/admin';
        var app_url = 'https://finance.joeyco.com';
        var CSRF_TOKEN = '7yM8QWydq2AGfYxj3Ar48SfyMQdzETmgzJqEze6L';
    </script>

    <!-- END CORE PLUGINS -->
    <script src="https://finance.joeyco.com/assets/admin/scripts/custom/joeyco-script.js"></script>
    <script src="https://finance.joeyco.com/assets/admin/scripts/custom/custom-js-helpers.js"></script>
        <script type="text/javascript" src="https://finance.joeyco.com/assets/admin/plugins/ckeditor/ckeditor.js"></script>
    <script src="https://finance.joeyco.com/assets/admin/scripts/core/app.js"></script>
    <!--<script src="https://finance.joeyco.com/assets/admin/scripts/custom/jquery.datetimepicker.full.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/locale/nl.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <script>

        let is_calculate = false;
        function calcualtion_validation_hendler($check) {
            is_calculate = $check;
        }

        // select2 init
//        $('.joeys-list').select2({
//            minimumInputLength: 2,
//            placeholder: "Search a joey to assign",
//            allowClear: true,
//            matcher: matchStart,
//            sorter: function(data) {
//                console.log(data)
//                return data.sort(function(a, b) {
//                    return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
//                });
//            }
//        });

        // validation for calculation  on form submi
        $('form').submit(function(){
            // validation on
            if(is_calculate == false )
            {
                // show error  alert and breack the loop
                ShowSessionAlert('danger','Please calculate first before saving plan ',true);
                return false
            }
        });

        // making is_calculate false if the user change the value of any input
        $(document).on('change','.calculate-btn-bind-with-inputs input',function(){
            calcualtion_validation_hendler(false);
        });


        $('.calculate-btn').click(function () {
            calcualtion_validation_hendler(true);
            calculation();
        });

        function calculation()
        {

            // base variable
            let base_amount = $('.baseamount').val();

            $('.baseamount').removeClass('error-input');

            // checking the plan base amount is not null
            if( 0 > base_amount ||  base_amount == '')
            {
                alert('Please set base amount for calculations');
                $('.baseamount').addClass('error-input');
                return ;
            }



            let selected_plan = $('.plane-type').val();
            let vehicle_using_type = $('.vehicle_using_type').val();

            // validation
            if(selected_plan == ''){ alert('Please select plan type'); return;}
            else if(vehicle_using_type == ''){ alert('Category Vehicle Using Type'); return;}

            /*geting of selection types*/
            let selected_plan_types = selected_plan.split("|");

            /*showing form and its input*/
            // input varables
            let from_el = '';
            let drop_val = 0;
            let amount_val = 0;
            let gas_val = 0;
            let tax = 0;
            let duration_Per_drop = 0;
            let buffer_time = 0;
            let daily_drops = 0;
            let duration = 0;
            let hourly_rate = 0;


            // output variable
            var rev_val = 0;
            var calculated_tax_val = 0;
            var company_tax_val = 0;
            var company_rev_with_tax = 0;
            var total_cost = 0;
            var margins = 0;
            var sub_joey_charges_val = 0;
            var duration_Per_drop_in_seconds = 0;
            var buffer_time_in_seconds = 0;
            var total_hours = '00:00:00';
            var total_hours_in_seconds = 0;
            var weekly_drops = 0;
            var duration_in_seconds = 0;

            let counter = 1;
            // looping the calculation data according to forms selected
            selected_plan_types.forEach((value, index) => {

                // catching from eleemt
                from_el = $('.detail-form-wrap-'+counter);

                // getting values from current form input
                drop_val = from_el.find('.drops').val() > 0 ? parseFloat(from_el.find('.drops').val()): 0 ;
                amount_val = from_el.find('.amount').val() > 0 ? parseFloat(from_el.find('.amount').val()) : 0 ;
                gas_val = from_el.find('.gas_truck_amount').val() > 0 ? parseFloat(from_el.find('.gas_truck_amount').val()) : 0 ;
                tax = from_el.find('.tax').val() > 0 ? parseFloat(from_el.find('.tax').val()) : 0 ;
                duration_Per_drop = from_el.find('.duration_per_drop').val();
                buffer_time = from_el.find('.buffer_time').val();
                daily_drops = from_el.find('.daily_drops').val();
                duration = from_el.find('.duration').val();
                hourly_rate = from_el.find('.hourly_rate').val();



                /**
                 *  calculation blocks
                 */

                // calculation for sub_contractor and joey vehicle
                if(selected_plan =='sub_contractor|sub_contractor_custom_routing|sub_contractor_big_box' && vehicle_using_type > 0 )
                {

                    // validation section
                    if(amount_val == '' || amount_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    // calculating company tax amount if tax value us enter
                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;


                    // doing calculation

                    sub_joey_charges_val = drop_val*amount_val;
                    calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                    //total_cost =((amount_val+gas_val)*drop_val)+calculated_tax_val;
                    total_cost = calculated_tax_val + sub_joey_charges_val;
                    margins = (1-(sub_joey_charges_val / rev_val))*100;


                    // setting up values
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));

                }
                else if(selected_plan =='sub_contractor|sub_contractor_custom_routing|sub_contractor_big_box' && vehicle_using_type == 0) // calculation for sub_contractor and own vehicle
                {

                    // validation section
                    if(amount_val == '' || amount_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    // calculating company tax amount if tax value us enter
                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;

                    // doing calculation
                    sub_joey_charges_val = drop_val*amount_val;
                    calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                    //total_cost = calculated_tax_val ;
                    total_cost = calculated_tax_val + sub_joey_charges_val;
                    margins = (1-(sub_joey_charges_val / rev_val ) ) * 100;

                    // setting up values
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));
                    //from_el.find('.gas_truck_amount').val(0);
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));

                }
                else if(selected_plan =='sub_hourly|downtown_hourly|sub_hourly_custom_routing|downtown_hourly_custom_routing|sub_hourly_big_box|downtown_hourly_big_box' && vehicle_using_type > 0 ) // calculation for sub|downtown and joey vehicle
                {

                    // checking calculation type
                    if(value == 'sub_hourly' || value == 'downtown_hourly' ) //block for per drop calculation
                    {

                        // validation section
                        if(duration_Per_drop == '' ||  buffer_time == '' || hourly_rate < 0 || hourly_rate == '')
                        {
                            // show error  alert and breack the loop
                            ShowSessionAlert('danger','please enter the correct "Duration Per Drop , Pickup Buffer , Hourly Rate" value and hit calculate then save',true);
                            calcualtion_validation_hendler(false);
                            return false;
                        }

                        /*calculating total hours */
                        duration_Per_drop_in_seconds = convert_time_string_seconds(duration_Per_drop);
                        buffer_time_in_seconds = convert_time_string_seconds(buffer_time);
                        total_hours_in_seconds = (drop_val * duration_Per_drop_in_seconds) + buffer_time_in_seconds;
                        total_hours = convert_seconds_time_strings(total_hours_in_seconds);

                        /*calculation sub joey charges by hourly*/
                        sub_joey_charges_val = (((total_hours_in_seconds / 60) / 60)* hourly_rate);
                    }
                    else
                    {

                        // removing total hours
                        total_hours = null;

                        // validation section
                        if(amount_val == '' || amount_val < 0)
                        {
                            // show error  alert and breack the loop
                            ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                            calcualtion_validation_hendler(false);
                            return false;
                        }

                        // by per drop calculation
                        sub_joey_charges_val = drop_val*amount_val;
                    }



                    /*calculating tax */
                    calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                    calculated_tax_val = (calculated_tax_val > 0 ) ? calculated_tax_val : 0 ;

                    /*calculating total cost */
                    total_cost = calculated_tax_val + sub_joey_charges_val;

                    // calculating payout per drop
                    // calculating company tax amount if tax value us enter
                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;


                    // calculating margins
                    margins = (1-(sub_joey_charges_val / rev_val) ) * 100;

                    // setting up values
                    from_el.find('.total_hours').val(total_hours);
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));

                }
                else if(selected_plan =='sub_hourly|downtown_hourly|sub_hourly_custom_routing|downtown_hourly_custom_routing|sub_hourly_big_box|downtown_hourly_big_box' && vehicle_using_type == 0 ) // calculation for sub per drop | downtown per drop and joey vehicle
                {


                    // checking calculation type
                    if(value == 'sub_hourly' || value == 'downtown_hourly' ) //block for per drop calculation
                    {

                        // validation section
                        if(duration_Per_drop == '' ||  buffer_time == '' || hourly_rate < 0 || hourly_rate == '')
                        {
                            // show error  alert and breack the loop
                            ShowSessionAlert('danger','please enter the correct "Duration Per Drop , Pickup Buffer , Hourly Rate" value and hit calculate then save',true);
                            calcualtion_validation_hendler(false);
                            return false;
                        }

                        /*calculating total hours */
                        duration_Per_drop_in_seconds = convert_time_string_seconds(duration_Per_drop);
                        buffer_time_in_seconds = convert_time_string_seconds(buffer_time);
                        total_hours_in_seconds = (drop_val * duration_Per_drop_in_seconds) + buffer_time_in_seconds;
                        total_hours = convert_seconds_time_strings(total_hours_in_seconds);

                        /*calculation sub joey charges*/
                        sub_joey_charges_val = (((total_hours_in_seconds / 60) / 60)* hourly_rate) ;
                    }
                    else
                    {

                        // removing total hours
                        total_hours = null;

                        // validation section
                        if(amount_val == '' || amount_val < 0)
                        {
                            // show error  alert and breack the loop
                            ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                            calcualtion_validation_hendler(false);
                            return false;
                        }

                        // by per drop calculation
                        sub_joey_charges_val = drop_val*amount_val;
                    }
                    //-----for sub calculation------//
                    gas_val = 0;

                    /*calculating tax */
                    calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                    calculated_tax_val = (calculated_tax_val > 0 ) ? calculated_tax_val : 0 ;

                    /*calculating total cost */
                    total_cost = calculated_tax_val + sub_joey_charges_val;

                    /*calculating total cost */
                    //total_cost_1 = ((((total_hours_in_seconds /60) / 60)* hourly_rate) + gas_val) + calculated_tax_val;

                    // calculating company tax amount if tax value us enter
                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;

                    // calculating margins
                    margins = (1-( sub_joey_charges_val / rev_val ) ) * 100;

                    // setting up values
                    from_el.find('.total_hours').val(total_hours);
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));

                }
                else if(selected_plan =='sub_per_drop|downtown_per_drop|sub_per_drop_custom_routing|downtown_per_drop_custom_routing|sub_per_drop_big_box|downtown_per_drop_big_box' && vehicle_using_type == 0 ) // calculation for sub|downtown and joey vehicle
                {

                    // validation section
                    if(amount_val == '' || amount_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    //-----for sub per drop calculation------//

                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;
                    sub_joey_charges_val = drop_val * amount_val;
                    calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                    total_cost = calculated_tax_val + sub_joey_charges_val;
                    margins = (1-(sub_joey_charges_val / rev_val ) ) * 100;

                    // setting up values
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));

                }
                else if(selected_plan =='sub_per_drop|downtown_per_drop|sub_per_drop_custom_routing|downtown_per_drop_custom_routing|sub_per_drop_big_box|downtown_per_drop_big_box' && vehicle_using_type > 0 ) // calculation for sub per drop | downtown per drop and joey vehicle
                {
                    // validation section
                    if(amount_val == '' || amount_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    //-----for sub per calculation------//

                    // doing calculation
                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;
                    sub_joey_charges_val = drop_val * amount_val;
                    calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                    total_cost = calculated_tax_val + sub_joey_charges_val;
                    margins = (1-( sub_joey_charges_val / rev_val) ) * 100;

                    // setting up values
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));

                }
                else if(selected_plan =='brooker|brooker_custom_routing|brooker_big_box' ) // calculation for brooker
                {

                    // validation section
                    if(amount_val == '' || amount_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    // doing calculation
                    sub_joey_charges_val = drop_val * amount_val;
                    calculated_tax_val = (tax / 100) * sub_joey_charges_val;
                    rev_val = drop_val * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;
                    total_cost = calculated_tax_val + sub_joey_charges_val;
                    margins = (1 - (sub_joey_charges_val / rev_val)) * 100;

                    // setting up values
                    from_el.find('.sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    from_el.find('.tax_charges').val(calculated_tax_val.toFixed(3));
                    from_el.find('.rev').val(rev_val.toFixed(3));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.total_cost').val(total_cost.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(3));

                }
                else if(selected_plan =='low|high' &&  vehicle_using_type > 0) // calculation for low|high and joey vehicle
                {
                    //-----for low calculation------//

                    //---- calculating  weekly drops ----//
                    weekly_drops = daily_drops * 7 ;

                    //---- Total Hours ----/
                    duration_in_seconds =  convert_time_string_seconds(duration);
                    total_hours = (duration_in_seconds / 60 ) / 60;

                    //---- total cost ----/
                    total_cost = (total_hours * hourly_rate) + gas_val;

                    // calculating payout per drop
                    rev_val = daily_drops * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;



                    // calculating margins
                    margins = (1-(total_cost / rev_val) ) * 100;

                    // setting up values
                    from_el.find('.weekly_drops').val(weekly_drops);
                    from_el.find('.total_hours').val(duration);
                    from_el.find('.total_cost').val(total_cost.toFixed(2));
                    from_el.find('.rev').val(rev_val.toFixed(2));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(2));
                }
                else if(selected_plan =='low|high' &&  vehicle_using_type == 0) // calculation for low|high and joey vehicle
                {
                    gas_val = 0;
                    //-----for low calculation------//

                    //---- calculating  weekly drops ----//
                    weekly_drops = daily_drops * 7 ;
                    //---- Total Hours ----/
                    duration_in_seconds =  convert_time_string_seconds(duration);
                    total_hours = (duration_in_seconds / 60 ) / 60;

                    //---- total cost ----/
                    total_cost = (total_hours * hourly_rate) + gas_val;

                    // calculating payout per drop
                    rev_val = daily_drops * base_amount;
                    company_tax_val = (tax / 100) * rev_val;
                    company_rev_with_tax = company_tax_val + rev_val;


                    // calculating margins
                    margins = (1-(total_cost / rev_val ) ) * 100;


                    // setting up values
                    from_el.find('.weekly_drops').val(weekly_drops);
                    from_el.find('.total_hours').val(duration);
                    from_el.find('.total_cost').val(total_cost.toFixed(2));
                    from_el.find('.rev').val(rev_val.toFixed(2));
                    from_el.find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    from_el.find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    from_el.find('.margins').val(margins.toFixed(2));


                }

                /*updateing counter*/
                counter++;

            });


        }




        jQuery(document).ready(function () {
            // initiate layout and plugins
            App.init();
            Admin.init();
            $('#cancel').click(function () {
                window.location.href = "https://finance.joeyco.com/joey-plan";
            });

            // time picker init
            jQuery('.timepicker').datetimepicker({
                format: 'HH:mm:ss'

            });
            $('.phone_us').mask('(000) 000-0000', {placeholder: "(___) ___-____"});
        });

        /*Plans From Handler trigger*/
        $('.plane-type').change(function(){


            // hide group by zone form
            groupbyZoneFormHendler(false);

            // reset from values on plan change
            let selected_value = $(this).val();
            let element = $(this)
            // block for bracket pricing
            if(selected_value == '')
            {
                // ressting all types  of forms
                $('.form-section-2').addClass('hide');
                $('.form-section-3').addClass('hide');
                $('.form-section-4').addClass('hide');
                ResetInnerFromInputs();


            }
            else if(selected_value == 'bracket_pricing|hourly|custom_route|big_box' || selected_value == 'bracket_pricing|per_drop|custom_route|big_box')
            {
                // hiding from 2
                $('.form-section-2').addClass('hide');
                $('.form-section-4').addClass('hide');
                ResetInnerFromInputs();

                // call bracket pricing form hendler
                form_hendler_bracket_pricing(selected_value,element);

            }
            else if(selected_value == 'group_zone_pricing_per_drop|custom_route|big_box')
            {
                // ressting all types  of forms
                $('.form-section-2').addClass('hide');
                $('.form-section-3').addClass('hide');
                ResetInnerFromInputs();

                // show group by zone form
                groupbyZoneFormHendler(true);

                // caling form_hendler_dynamic_section
                form_hendler_dynamic_section(selected_value,element);
            }
            else
            {
                // hiding from 3
                $('.form-section-3').addClass('hide');
                $('.form-section-4').addClass('hide');

                /*resting from value*/
                ResetInnerFromInputs();

                if(selected_value != "")
                {
                    //show second from
                    $('.form-section-2').removeClass('hide');
                    // show input by select value
                    PlansFromHendler(selected_value,element);
                }
                /*else
                {
                    //hideingsecond from
                    $('.form-section-2').addClass('hide');
                }*/
            }


        });



        /*reset inner froms input*/
        function ResetInnerFromInputs () {
            // form one resting
            $('.form-section-2 input').val('');
            $('.form-section-2 input').prop('required',false);
            $('.form-section-2 select').prop('required',false);
            $('.form-section-2 input').closest('.from-input-col').addClass('hide');
            $('.detail-form-wrap').addClass('hide');

            // bracket pricing form resting
            $('.bracket-pricing-input-box-inner input').val('');
            $('.bracket-pricing-input-box-inner input').prop('required',false);
            $('.bracket-pricing-input-box-inner select').prop('required',false);
            $('.bracket-pricing-input-box-inner .from-input-col').addClass('hide');

            // resting from 4
            //$('.form-section-4 input').val('');
            $('.form-section-4 input').prop('required',false);
            $('.form-section-4 select').prop('required',false);
            $('.form-section-4 .from-input-col').addClass('hide');

        }

        /*Plans From Handler*/
        function PlansFromHendler(value,select_el)
        {

            let original_value = value;

            // extra conditions making drop disabled on broker or sub contractor
            if(value == 'sub_contractor|sub_contractor_custom_routing|sub_contractor_big_box' || value == 'brooker|brooker_custom_routing|brooker_big_box'  || value == 'sub_per_drop|downtown_per_drop|sub_per_drop_custom_routing|downtown_per_drop_custom_routing|sub_per_drop_big_box|downtown_per_drop_big_box')
            {
                // making drops input default value
                let drops_input = $('.drops');
                drops_input.val(1);
                drops_input.prop( "readonly", true );
            }
            else if(value == 'sub_hourly|downtown_hourly|sub_hourly_custom_routing|downtown_hourly_custom_routing|sub_hourly_big_box|downtown_hourly_big_box') // hourly plan with custom route and big box
            {
                // making drops input default value
                let drops_input = $('.drops-3, .drops-4, .drops-5, .drops-6');
                drops_input.val(1);
                drops_input.prop( "readonly", true );
            }
            else
            {
                // making drops input default value
                let drops_input = $('.drops');
                drops_input.val(0);
                drops_input.prop( "readonly", false );
            }


            /*set inputs by select value */
            let selected_value =
                {
                    // sub contractor
                    "sub_contractor":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "sub_contractor_custom_routing":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "sub_contractor_big_box":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    // by area per drop
                    "sub_per_drop":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "downtown_per_drop":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "sub_per_drop_custom_routing":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "downtown_per_drop_custom_routing":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "sub_per_drop_big_box":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "downtown_per_drop_big_box":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                     // by area hourly rate
                    "sub_hourly":['.duration_per_drop','.drops','.buffer_time','.hourly_rate','.total_hours','.tax','.total_cost','.rev','.gas_truck_amount','.margins','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "downtown_hourly":['.duration_per_drop','.drops','.buffer_time','.hourly_rate','.total_hours','.tax','.total_cost','.rev','.gas_truck_amount','.margins','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "sub_hourly_custom_routing":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "downtown_hourly_custom_routing":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "sub_hourly_big_box":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    "downtown_hourly_big_box":['.drops','.rev','.amount','.margins','.gas_truck_amount','.tax','.total_cost','.sub_joey_charges','.tax_charges','.company_tax_charges','.company_total_charges'],
                    // brooker
                    "brooker":['.drops','.rev','.amount','.margins','.total_cost','.sub_joey_charges','.tax_charges','.tax','.company_tax_charges','.company_total_charges'],
                    "brooker_custom_routing":['.drops','.rev','.amount','.margins','.total_cost','.sub_joey_charges','.tax_charges','.tax','.company_tax_charges','.company_total_charges'],
                    "brooker_big_box":['.drops','.rev','.amount','.margins','.total_cost','.sub_joey_charges','.tax_charges','.tax','.company_tax_charges','.company_total_charges'],
                    // by priority
                    "low":['.duration','.daily_drops','.weekly_drops','.hourly_rate','.gas_truck_amount','.total_hours','.total_cost','.bracket-rev','.bracket-margins','.company_tax_charges','.company_total_charges'],
                    "high":['.duration','.daily_drops','.weekly_drops','.hourly_rate','.gas_truck_amount','.total_hours','.total_cost','.bracket-rev','.bracket-margins','.company_tax_charges','.company_total_charges'],
                };

            /*geting values of selection*/
            value = value.split("|");
            // getting seleted option names
            let sub_plan_names = select_el.find(':selected').attr('date-sub-section-names').split("|");
            let select_from_inputs = '';
            let counter = 1;
            /*showing form and its  input*/
            value.forEach((value, index) => {
                /*geting all inout classes array to be showen and converting them into string*/
                select_from_inputs =selected_value[value].toString();

                // showing details from
                $('.detail-form-wrap-'+counter).removeClass('hide');
                /*setting form title*/
                //$('.detail-form-wrap-'+counter).find('.detail-form-wrap-h4').text(value.replace(/([^a-zA-Z0-9]|\s\s+)/g, " "));
                $('.detail-form-wrap-'+counter).find('.detail-form-wrap-h4').text(sub_plan_names[index]);

                $('.detail-form-wrap-'+counter).find(select_from_inputs).closest('.from-input-col').removeClass('hide');

                // making all inputs requried
                $('.detail-form-wrap-'+counter).find(select_from_inputs).prop('required',true);

                // updating sorting order
                $('.detail-form-wrap-'+counter).find('.sorting_order').val(counter);

                // update section type
                $('.detail-form-wrap-'+counter).find('.block_section_type').val(value);

                // making inputs requried
                //$('.bracket-pricing-input-box-inner').find(select_from_inputs).prop('required',true);

                /*updateing counter*/
                counter++;

            });

            // tem work to show all inputs
//            $('.detail-form-wrap-1').removeClass('hide');
//            $('.detail-form-wrap-2').removeClass('hide');
//            $('.detail-form-wrap-1').find('.from-input-col').removeClass('hide');
//            $('.detail-form-wrap-2').find('.from-input-col').removeClass('hide');
        }

        // add bracket pricing
        $('.add-bracket-input-box').click(function(){

            // cloning the html
            let bracket_html = $('.bracket-pricing-input-box-org').clone();
            //empty inputs
            bracket_html.find('input').removeAttr('value');
            // converting object clone to html
            bracket_html = bracket_html.html().trim();

            //let bracket_html = $('.bracket-pricing-input-box-org').html().trim();
            let append_bracket_html = '<div class="col-sm-12 bracket-pricing-input-box-inner bracket-pricing-input-can-add bracket-pricing-input-box-appended">'+bracket_html+' <i class="fa fa-times-circle remove-bracket"></i></div>';
            $('.bracket-pricing-input-box').prepend(append_bracket_html);

            // attacing date pickeer picker init
            jQuery('.timepicker').datetimepicker({
                format: 'HH:mm:ss'

            });
        });



        // removing bracket
        $(document).on('click','.remove-bracket',function(){

            var confirmation = confirm("Are you sure you want to delete");
            if(confirmation)
            {
                $(this).parent('.bracket-pricing-input-box-appended').remove();
            }
        });

        // bracket calculation fn
        $('.bracket-calculate-btn').click(function () {
            calcualtion_validation_hendler(true);
            bracket_pricing_calculations();
        });

        // bracket pricing form hendler
        function form_hendler_bracket_pricing(form_type,select_el)
        {

            // //checking plan type is bracket pricing per drop show the section
            // if(form_type == 'bracket_pricing|per_drop|custom_route|big_box')
            // {
            //     // show bracket pricing section
            //     $('.bracket-pricing-static-box').removeClass('hide');
            //
            //     // making drops input default value
            //     let drops_min_input = $('.bracket-min-drops-custom-route');
            //     let drops_max_input = $('.bracket-max-drops-custom-route');
            //     drops_min_input.val(1);
            //     drops_max_input.val(1);
            //     drops_min_input.prop( "readonly", true );
            //     drops_max_input.prop( "readonly", true );
            // }
            // else
            // {
            //     // hide bracket pricing section
            //     $('.bracket-pricing-static-box').addClass('hide');
            // }


            // show bracket pricing section
            $('.bracket-pricing-static-box').removeClass('hide');

            // making drops input default value
            let drops_min_input = $('.bracket-min-drops-custom-route');
            let drops_max_input = $('.bracket-max-drops-custom-route');
            drops_min_input.val(1);
            drops_max_input.val(1);
            drops_min_input.prop( "readonly", true );
            drops_max_input.prop( "readonly", true );


            /*set inputs by select value */
            let selected_value =
                {
                    "bracket_pricing|hourly|custom_route|big_box":
                    [
                        '.bracket-total-hours',
                        '.bracket-min-drops',
                        '.bracket-max-drops',
                        '.bracket-hourly-rate',
                        '.bracket-gas-truck-amount',
                        '.bracket-total-cost',
                        '.bracket-rev',
                        '.bracket-margins',
                        '.tax',
                        '.tax_charges',
                        '.company_tax_charges',
                        '.company_total_charges',
                        '.bracket-sub-joey-charges',
                        '.bracket-joey-tax-charges',
                        // custom route columns
                        '.bracket-min-drops-custom-route',
                        '.bracket-max-drops-custom-route',
                        '.bracket-amount-custom-route',
                        '.bracket-gas-truck-amount-custom-route',
                        '.bracket-total-cost-min-max-custom-route',
                        '.bracket-rev-min-max-custom-route',
                        '.bracket-margins-min-max-custom-route',
                        '.tax-custom-route',
                        '.bracket-sub-joey-charges-min-max-custom-route',
                        '.bracket-joey-tax-min-max-charges-custom-route',
                        '.company-tax-charges-min-max-custom-route',
                        '.company-total-charges-min-max-custom-route',
                        '.bracket-joey-tax-min-max-charges-custom-route'
                    ],
                    "bracket_pricing|per_drop|custom_route|big_box":
                    [
                        '.bracket-min-drops',
                        '.bracket-max-drops',
                        '.bracket-amount',
                        '.bracket-gas-truck-amount',
                        '.bracket-total-cost-min-max',
                        '.bracket-rev-min-max',
                        '.bracket-margins-min-max',
                        '.tax',
                        '.bracket-joey-tax-min-max-charges',
                        '.company-tax-charges-min-max',
                        '.company-total-charges-min-max',
                        '.bracket-sub-joey-min-mix-charges',
                        // custom route columns
                        '.bracket-min-drops-custom-route',
                        '.bracket-max-drops-custom-route',
                        '.bracket-amount-custom-route',
                        '.bracket-gas-truck-amount-custom-route',
                        '.bracket-total-cost-min-max-custom-route',
                        '.bracket-rev-min-max-custom-route',
                        '.bracket-margins-min-max-custom-route',
                        '.tax-custom-route',
                        '.bracket-sub-joey-charges-min-max-custom-route',
                        '.bracket-joey-tax-min-max-charges-custom-route',
                        '.company-tax-charges-min-max-custom-route',
                        '.company-total-charges-min-max-custom-route',
                        '.bracket-joey-tax-min-max-charges-custom-route'
                    ],
                };

            /*geting all inout classes array to be showen and converting them into string*/

            let select_from_inputs =selected_value[form_type].toString();
            // showing title
            //$('.form-section-3').find('.section-heading').text(form_type.replace(/([^a-zA-Z0-9]|\s\s+)/g, " "));

            // showing inputs
            $('.bracket-pricing-input-box-inner').find(select_from_inputs).closest('.from-input-col').removeClass('hide');

            // making inputs requried
            $('.bracket-pricing-input-box-inner').find(select_from_inputs).prop('required',true);

            // toggleing class for make this input calculate able
            //$('.bracket-pricing-input-box-inner').find(select_from_inputs).toggleClass('calculate');

            // show form
            $('.form-section-3').removeClass('hide');
            $('.bracket-pricing-detail-form-wrap').removeClass('hide');

        }

        // bracket pricing calculations
        function bracket_pricing_calculations()
        {

            // base variable
            let base_amount = $('.baseamount').val();

            $('.baseamount').removeClass('error-input');

            // checking the plan base amount is not null
            if( 0 > base_amount ||  base_amount == '')
            {
                alert('Please set base amount for calculations');
                $('.baseamount').addClass('error-input');
                return ;
            }

            let selected_plan = $('.plane-type').val();
            let vehicle_using_type = $('.vehicle_using_type').val();

            // validations
            if(vehicle_using_type == ''){alert('Please select Category Vehicle Using Type'); return}
            else if(selected_plan ==''){alert('Please select Plan Type'); return}

            var total_hours ,
                bracket_min_drops ,
                bracket_max_drops ,
                bracket_hourly_rate ,
                bracket_amount ,
                bracket_gas_truck_amount ,
                bracket_total_cost ,
                total_hours_in_second ,
                hourly_rate_in_seconds,
                bracket_margins,
                bracket_rev_val,
                bracket_rev_val_max,
                sub_joey_charges_val,
                tax = 0,
                joey_tax_val = 0,
                company_tax_val = 0,
                company_rev_with_tax = 0
                ;

            let sub_plan_name_array = selected_plan.split("|");
            let bracket_pricing_static_lenth = $('.bracket-pricing-static-box').length;
            let bracket_pricing_normal_input_lenght =  $('.bracket-pricing-input-can-add').length + 2;


            // setting truck amount according to vehicle type
            /*if(vehicle_using_type == 0)
            {
                $('.bracket-pricing-input-box-inner').find('.bracket-gas-truck-amount').val(0);
            }*/

            if(selected_plan == 'bracket_pricing|hourly|custom_route|big_box') // calculation for hourly rate
            {
                // calculation input
                $('.bracket-pricing-input-can-add').each(function () {


                    total_hours = $(this).find('.bracket-total-hours').val();
                    bracket_min_drops = $(this).find('.bracket-min-drops').val();
                    bracket_max_drops = $(this).find('.bracket_max_drops').val();
                    bracket_hourly_rate = parseFloat($(this).find('.bracket-hourly-rate').val());
                    bracket_gas_truck_amount = parseFloat($(this).find('.bracket-gas-truck-amount').val());
                    bracket_gas_truck_amount = (bracket_gas_truck_amount > 0) ? bracket_gas_truck_amount : 0 ;
                    tax = parseFloat($(this).find('.tax').val());
                    tax = (tax > 0)? tax: 0;


                    // validation section
                    if(total_hours == '' || bracket_min_drops < 0 || bracket_min_drops == '' || bracket_max_drops < 0 || bracket_max_drops == '' || bracket_hourly_rate < 0  || isNaN(bracket_hourly_rate))
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Total Hours , Min Drops , Max Drops and Hourly Rate ,  " value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    // calculations
                    total_hours_in_second = convert_time_string_seconds(total_hours);
                    total_hours = (total_hours_in_second / 60 ) / 60;
                    sub_joey_charges_val = (total_hours * bracket_hourly_rate);
                    joey_tax_val = (tax / 100) * sub_joey_charges_val;
                    bracket_total_cost = joey_tax_val + sub_joey_charges_val ;
                    bracket_rev_val = bracket_min_drops*base_amount;
                    company_tax_val = (tax / 100) * bracket_rev_val;
                    company_rev_with_tax = company_tax_val + bracket_rev_val;
                    bracket_margins = (1-(sub_joey_charges_val/bracket_rev_val))*100;

                    // setting up values
                    $(this).find('.bracket_section_type').val(sub_plan_name_array[0]+'_'+sub_plan_name_array[1]);
                    $(this).find('.bracket_section_sort_order').val(bracket_pricing_normal_input_lenght);
                    $(this).find('.bracket-sub-joey-charges').val(sub_joey_charges_val.toFixed(3));
                    $(this).find('.bracket-joey-tax-charges').val(joey_tax_val.toFixed(3));
                    $(this).find('.bracket-total-cost').val(bracket_total_cost.toFixed(3));
                    $(this).find('.bracket-rev').val(bracket_rev_val.toFixed(3));
                    $(this).find('.company_tax_charges').val(company_tax_val.toFixed(3));
                    $(this).find('.company_total_charges').val(company_rev_with_tax.toFixed(3));
                    $(this).find('.bracket-margins').val(bracket_margins.toFixed(3));
                    $(this).find('.bracket-gas-truck-amount').val(bracket_gas_truck_amount);
                    $(this).find('.tax').val(tax);

                    // updating sorting no
                    bracket_pricing_normal_input_lenght--;

                });
            }
            else
            {
                // calculation on every bracket added
                $('.bracket-pricing-input-can-add').each(function () {

                    bracket_min_drops =  parseFloat($(this).find('.bracket-min-drops').val());
                    bracket_max_drops = parseFloat($(this).find('.bracket-max-drops').val());
                    bracket_amount =  parseFloat($(this).find('.bracket-amount').val());
                    bracket_gas_truck_amount =  parseFloat($(this).find('.bracket-gas-truck-amount').val());
                    bracket_gas_truck_amount = (bracket_gas_truck_amount > 0)? bracket_gas_truck_amount : 0 ;
                    tax = parseFloat($(this).find('.tax').val());
                    tax = (tax > 0)? tax: 0;


                    // validation section
                    if(isNaN(bracket_min_drops) == true|| bracket_min_drops < 0 || isNaN(bracket_max_drops) == true|| bracket_max_drops < 0 || isNaN(bracket_amount) == true|| bracket_amount < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Min Drops  , Max Drops and Per Drop Amount " value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    // calculations
                    var bracket_sub_joey_charges_min = (bracket_min_drops * bracket_amount);
                    var bracket_sub_joey_charges_max = (bracket_max_drops * bracket_amount);

                    var bracket_joey_tax_min = (tax / 100) * bracket_sub_joey_charges_min;
                    var bracket_joey_tax_max = (tax / 100) * bracket_sub_joey_charges_max;

                    var bracket_total_cost_min = bracket_sub_joey_charges_min + bracket_joey_tax_min;
                    var bracket_total_cost_max = bracket_sub_joey_charges_max + bracket_joey_tax_max;

                    bracket_rev_val = bracket_min_drops*base_amount;
                    bracket_rev_val_max = bracket_max_drops*base_amount;

                    var bracket_company_tax_val_min = (tax / 100) * bracket_rev_val;
                    var bracket_company_tax_val_max = (tax / 100) * bracket_rev_val_max;

                    var bracket_company_rev_with_tax_min  =  bracket_rev_val + bracket_company_tax_val_min;
                    var bracket_company_rev_with_tax_max  =  bracket_rev_val_max + bracket_company_tax_val_max;


                    var bracket_margins_min = (1-(bracket_sub_joey_charges_min / bracket_rev_val))*100;
                    var bracket_margins_max = (1-(bracket_sub_joey_charges_max / bracket_rev_val_max))*100;


                    $(this).find('.bracket_section_type').val(sub_plan_name_array[0]+'_'+sub_plan_name_array[1]);
                    $(this).find('.bracket_section_sort_order').val(bracket_pricing_normal_input_lenght);
                    $(this).find('.bracket-gas-truck-amount').val(bracket_gas_truck_amount);
                    $(this).find('.tax').val(tax);
                    $(this).find('.bracket-sub-joey-charges').val(bracket_sub_joey_charges_min);
                    $(this).find('.bracket-sub-joey-min-mix-charges').val( bracket_sub_joey_charges_min.toFixed(3) + ' / ' +bracket_sub_joey_charges_max.toFixed(3));
                    $(this).find('.bracket-joey-tax-charges').val(bracket_joey_tax_min);
                    $(this).find('.bracket-joey-tax-min-max-charges').val(bracket_joey_tax_min.toFixed(3) + ' / ' +bracket_joey_tax_max.toFixed(3));
                    $(this).find('.bracket-total-cost').val(bracket_total_cost_min.toFixed(3));
                    $(this).find('.bracket-total-cost-min-max').val( bracket_total_cost_min.toFixed(3) + ' / ' +bracket_total_cost_max.toFixed(3));
                    $(this).find('.bracket-rev').val(bracket_rev_val.toFixed(3));
                    $(this).find('.bracket-rev-min-max').val(bracket_rev_val.toFixed(3) + ' / ' + bracket_rev_val_max.toFixed(3));
                    $(this).find('.company_tax_charges').val( bracket_company_tax_val_min.toFixed(3));
                    $(this).find('.company-tax-charges-min-max').val( bracket_company_tax_val_min.toFixed(3) + ' / ' + bracket_company_tax_val_max.toFixed(3));
                    $(this).find('.company_total_charges').val( bracket_company_rev_with_tax_min.toFixed(3));
                    $(this).find('.company-total-charges-min-max').val( bracket_company_rev_with_tax_min.toFixed(3) + ' / ' + bracket_company_rev_with_tax_max.toFixed(3));
                    $(this).find('.bracket-margins').val(bracket_margins_min.toFixed(3));
                    $(this).find('.bracket-margins-min-max').val(bracket_margins_min.toFixed(3) + ' / ' + bracket_margins_max.toFixed(3));

                    // updating sorting no
                    bracket_pricing_normal_input_lenght--;

                });
            }


            // bracket pricing static box calculation
            $('.bracket-pricing-static-box').each(function () {

                let current_el = $(this);

                var sorting_no = bracket_pricing_static_lenth;
                var section_name = (sorting_no == 2)  ? sub_plan_name_array[0]+'_'+sub_plan_name_array[1]+'_'+'custom_route' : sub_plan_name_array[0]+'_'+sub_plan_name_array[1]+'_'+'big_box' ;

                // calculation for custom routing
                var bracket_min_drops_custom_route =  parseFloat(current_el.find('.bracket-min-drops-custom-route').val());
                var bracket_max_drops_custom_route = parseFloat(current_el.find('.bracket-max-drops-custom-route').val());
                var bracket_amount_custom_route =  parseFloat(current_el.find('.bracket-amount-custom-route').val());
                var bracket_gas_truck_amount_custom_route =  parseFloat(current_el.find('.bracket-gas-truck-amount-custom-route').val());
                bracket_gas_truck_amount_custom_route = (bracket_gas_truck_amount_custom_route > 0 )? bracket_gas_truck_amount_custom_route : 0 ;
                var tax_static_box = parseFloat($(this).find('.tax-custom-route').val());
                tax_static_box = (tax_static_box > 0)? tax_static_box: 0;

                if(isNaN(bracket_amount_custom_route) == true|| bracket_amount_custom_route < 0 )
                {
                    // show error  alert and breack the loop
                    ShowSessionAlert('danger','please enter the correct "Per Drop Amount " value and hit calculate then save',true);
                    calcualtion_validation_hendler(false);
                    return false;
                }

                // calculations
                var sub_joey_charges_min = (bracket_min_drops_custom_route * bracket_amount_custom_route);
                var sub_joey_charges_max = (bracket_max_drops_custom_route * bracket_amount_custom_route);

                var joey_tax_min = (tax_static_box / 100) * sub_joey_charges_min;
                var joey_tax_max = (tax_static_box / 100) * sub_joey_charges_max;

                var bracket_total_cost_min_custom_route = sub_joey_charges_min + joey_tax_min;
                var bracket_total_cost_max_custom_route = sub_joey_charges_max + joey_tax_max;

                var bracket_rev_val_custom_route = bracket_min_drops_custom_route * base_amount;
                var bracket_rev_val_max_custom_route = bracket_max_drops_custom_route * base_amount;

                var company_tax_val_min_custom_route = (tax_static_box / 100) * bracket_rev_val_custom_route;
                var company_tax_val_max_custom_route = (tax_static_box / 100) * bracket_rev_val_max_custom_route;

                var company_rev_with_tax_min_custom_route  =  company_tax_val_min_custom_route + bracket_rev_val_custom_route;
                var company_rev_with_tax_max_custom_route  =  company_tax_val_max_custom_route + bracket_rev_val_max_custom_route;

                var bracket_margins_min_custom_route = (1-( sub_joey_charges_min / bracket_rev_val_custom_route ) ) * 100;
                var bracket_margins_max_custom_route = (1-( sub_joey_charges_max / bracket_rev_val_max_custom_route ) ) * 100;


                current_el.find('.bracket_section_type').val(section_name);
                current_el.find('.bracket_section_sort_order').val(sorting_no);
                current_el.find('.bracket-gas-truck-amount-custom-route').val(bracket_gas_truck_amount_custom_route);
                current_el.find('.tax-custom-route').val(tax_static_box);
                current_el.find('.bracket-sub-joey-charges-custom-route').val(sub_joey_charges_min);
                current_el.find('.bracket-sub-joey-charges-min-max-custom-route').val(sub_joey_charges_min.toFixed(3) + ' / ' +sub_joey_charges_max.toFixed(3));
                current_el.find('.bracket-joey-tax-charges-custom-route').val(joey_tax_min);
                current_el.find('.bracket-joey-tax-min-max-charges-custom-route').val(joey_tax_min.toFixed(3) + ' / ' +joey_tax_max.toFixed(3));
                current_el.find('.bracket-total-cost-custom-route').val(bracket_total_cost_min_custom_route.toFixed(3));
                current_el.find('.bracket-total-cost-min-max-custom-route').val( bracket_total_cost_min_custom_route.toFixed(3) + ' / ' +bracket_total_cost_max_custom_route.toFixed(3));
                current_el.find('.bracket-rev-custom-route').val(bracket_rev_val_custom_route.toFixed(3));
                current_el.find('.bracket-rev-min-max-custom-route').val(bracket_rev_val_custom_route.toFixed(3) + ' / ' + bracket_rev_val_max_custom_route.toFixed(3));
                current_el.find('.company-tax-charges-custom-route').val(company_tax_val_min_custom_route.toFixed(3));
                current_el.find('.company-tax-charges-min-max-custom-route').val(company_tax_val_min_custom_route.toFixed(3) + ' / ' + company_tax_val_max_custom_route.toFixed(3));
                current_el.find('.company-total-charges-custom-route').val(company_rev_with_tax_min_custom_route.toFixed(3));
                current_el.find('.company-total-charges-min-max-custom-route').val(company_rev_with_tax_min_custom_route.toFixed(3) + ' / '+ company_rev_with_tax_max_custom_route.toFixed(3));
                current_el.find('.bracket-margins-custom-route').val(bracket_margins_min_custom_route.toFixed(3));
                current_el.find('.bracket-margins-min-max-custom-route').val(bracket_margins_min_custom_route.toFixed(3) + ' / ' + bracket_margins_max_custom_route.toFixed(3));

                // updating sorting number
                bracket_pricing_static_lenth--;

            });


        }


        // group by zone function
        let groupByzoneAvailableOptions = [];

        let group_by_zone_select_2 = $('.group_zones').select2({
            placeholder: "Select an option",
        });

        function groupbyZoneFormHendler(isShow = false)
        {
            if(isShow)
            {
                // show city selection
                $('.select-hub').removeClass('hide');
                $('.select-hub').find('select').prop('required',true);
                $('.select-hub').find('select').val('');

                // show form for group by zone
                let group_by_zone_form = $('.form-section-group-by-zone');
                group_by_zone_form.removeClass('hide');
            }
            else
            {
                // show city selection
                $('.select-hub').addClass('hide');
                $('.select-hub').find('select').prop('required',false);

                // show form for group by zone
                let group_by_zone_form = $('.form-section-group-by-zone');
                group_by_zone_form.addClass('hide');

            }

        }

        // fn for update group by zone form
        $(document).on('change','.hub-selection',function () {

            // default setting
            let confirmation = true;
            let el = $(this);
            let selected_hub_id = el.val();
            let selected_hub_text = el.find('option:selected').text();

            // checking the option are already selected
            if(groupByzoneAvailableOptions.length > 0)
            {
               confirmation = confirm('Are you Sure you want to change hub if there any data is selected will be reset! ');
            }
            else if(selected_hub_id == '')
            {
                alert('Please select an option! ');
                return false;
            }

            // now checking confirmation
            if(!confirmation)
            {   // returning without doing any thing
                return false;
            }


            // resting all dynamic section group selection
            emptyDynamicGroupZoneSelection();

            // resting dynamic section group selction
            groupZineFormRest();


            // showing loader
            showLoader();
            // getting selected hub zones data
            $.ajax({
                type: 'GET',
                url: 'https://finance.joeyco.com/get/selected/hub/zones',
                data:{"hub_id":selected_hub_id,"hub_name":selected_hub_text},
                success: function (response){
                    // init data
                    let data = response.body;
                    // creating group zone form table data
                    FillGroupZoneFormTableData(data.groups_list);
                    // fill group zone selection options
                    fillGroupZonesSelection(data.groups_list);
                    // create selection option
                    FillGroupZoneFormSelectOptions(data.zones_list);
                    // updating from hub
                    $('.group_zone_hub_name').val(data.hub_name);
                    $('.group_zone_hub_id').val(data.hub_id);

                    // hiding loader after some time because the selecet 2 new option is init
                    setTimeout(function () {  hideLoader() },1500);

                },
                error:function (error) {
                    ShowSessionAlert('danger','Something went wrong please try again !',true);
                    console.log(error);
                    hideLoader();
                }
            });


        });

        // fn for create or update group zones from form
        $('.group-zone-save-btn').click(function(){


            // request inputs
            let request_data = {};
            request_data['group_zone_id'] = $('.group_zone_id').val();
            request_data['hub_name'] = $('.group_zone_hub_name').val();
            request_data['hub_id'] = $('.group_zone_hub_id').val();
            request_data['group_name'] = $('.group_name').val();
            request_data['grouped_zones_ids'] = $('.group_zones').val();


            // validations
            if(request_data.group_name == '')
            {
                ShowSessionAlert('danger','Please enter the group name',true);
                return false;
            }
            else if(request_data.grouped_zones_ids  == null)
            {
                ShowSessionAlert('danger','Please Select any zone for this group',true);
                return false;
            }

            // showing loader
            showLoader();
            // sending data to ajax
            $.ajax({
                type: 'POST',
                url: 'https://finance.joeyco.com/save-or-update/zone-group',
                data:request_data,
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response){
                    // checking responce
                    if(!response.status)
                    {
                        // showing session alert
                        ShowSessionAlert('danger',response.message,true);
                        // hide loader
                        hideLoader();
                    }

                    let body = response.body;
                    // generate group zone form table
                    FillGroupZoneFormTableData(body.grouped_zones_by_hub);
                    // fill group zone selection options
                    fillGroupZonesSelection(body.grouped_zones_by_hub,response.metaData);

                    // reseting inputs
                    $('.group_zone_id').val('');
                    $('.group_name').val('');
                    $('.group_zones').val(null).trigger('change');

                    // hide loader
                    hideLoader();
                },
                error:function (error) {
                    ShowSessionAlert('danger','Something went wrong please try again !',true);
                    console.log(error);
                    hideLoader();
                }
            });


        });

        function FillGroupZoneFormTableData(data)
        {

            let table_html ='';
            let el = $('.group-by-zone-tbl');
            let class_for_span = "";

            // looping on data
            data.forEach(function (loop_data,index) {

                table_html+="<tr id='"+loop_data.id+"' class='tr-index-"+index+"'>";
                table_html+="<td class='group-name'>"+loop_data.group_name+"</td>";
                table_html+="<td class='group-zones'><div class='datatable-td-milti-data-show-box'><ul>";
                // creating zones drop down

                // init empty assigned zones var
                let assigned_zones_ids = '';

                // looping op zones data
                loop_data.zones_routing.forEach(function (zone,zone_index){
                    assigned_zones_ids+=zone.id+'|';
                    class_for_span = (zone_index == 0) ? 'datatable-td-milti-data-show-box-btn-li datatable-td-milti-data-show-li': 'datatable-td-milti-data-show-li hide' ;
                    table_html+=" <li class='"+class_for_span+"'> <span class='labels'>"+zone.title+"</span>";
                    // checking the should add button
                    table_html+=(zone_index == 0 && loop_data.zones_routing.length > 1) ?"<span href='#' class='show-datatable-td-list-btn btn btn-xs btn-primary orange'><i class='fa fa-angle-down'></i></span>":"";
                    table_html+="</li>";
                });
                table_html+="</ul></div></td>";
                table_html+="<td class='action-td'>" +
                    "<a href='#' data-id='"+loop_data.id+"' data-attached='"+assigned_zones_ids+"' data-name='"+loop_data.group_name+"' title='Edit' class='btn btn-xs btn-primary edit-group-zone-ajax'><i class='fa fa-pencil-square'></i></a>" +
//                    "<a href='#' title='Delete' class='btn btn-xs btn-danger delete-group-zone-ajax'><i class='fa fa-trash-o'></i></a>" +
                    "</td>";
                table_html+="</tr>";

            });

            // appending data to group zones form table
            el.find('tbody').html('').append(table_html);
        }

        function fillGroupZonesSelection(data,updateing_data = false) {

            let selection_el = $('.dynamic_section_group_zones');
            let options = '<option value="">Select an option</option>';

            // update existing list
            if(updateing_data != false)
            {
                let options_for_update = selection_el.find('option[value="'+updateing_data.updating_id+'"]');
                // checking option already exist
                if(options_for_update.length > 0)
                {
                    options_for_update.text(updateing_data.updating_value);
                }
                else
                {
                    selection_el.append('<option value="'+updateing_data.updating_id+'" >'+updateing_data.updating_value+'</option>');
                }


            }
            else //re init all selection
            {
                // empty group zone selction empty
                emptyDynamicGroupZoneSelection();


                // loopting on data
                data.forEach(function (loop_data,index) {
                    options+='<option value="'+loop_data.id+'">'+loop_data.group_name+'</option>';
                });

                // append options
                selection_el.find('option').remove().end().append(options);
            }

        }


        function FillGroupZoneFormSelectOptions(data)
        {
            let options =[];
            // looping on data
            data.forEach(function (loop_data) {
                options.push({id:loop_data.id,text:loop_data.title});
            });

            // checking the options are available
            if(options.length > 0)
            {
                // removing old data and adding new one
                groupByzoneAvailableOptions = options;

                // updating selecte 2
                group_by_zone_select_2.select2().empty();
                group_by_zone_select_2.select2({
                    data:groupByzoneAvailableOptions,
                    placeholder: "Select an option",
                });
            }
        }

        function  groupZineFormRest() {

            $('.group_zone_id').val('');
            $('.group_zone_hub_name').val('');
            $('.group_zone_hub_id').val('');
            $('.group_name').val('');
            $('.group_zones').val(null).trigger('change');
        }


        function emptyDynamicGroupZoneSelection() {
            // resting all dynamic section group selection
            $('.dynamic_section_group_zones')
                .find('option')
                .remove()
                .end()
                .append('<option value="">Select an option add</option>')
                .val('');
        }

        // group edit btn fn
        $(document).on('click','.edit-group-zone-ajax',function (e) {
            e.preventDefault();

            let el = $(this);
            let edit_group_zone_id = el.attr('data-id');
            let edit_group_zone_attached_ids = el.attr('data-attached').split("|");
            let edit_group_zone_name = el.attr('data-name');
            let edit_hub_id =  $('.group_zone_hub_id').val();
            let edit_hub_name =  $('.group_zone_hub_name').val();

            // resting form
            groupZineFormRest();

            // updating from data
            $('.group_zone_id').val(edit_group_zone_id);
            $('.group_zone_hub_name').val(edit_hub_name);
            $('.group_zone_hub_id').val(edit_hub_id);
            $('.group_name').val(edit_group_zone_name);
            $('.group_zones').val(edit_group_zone_attached_ids).trigger('change');



        });

        // group from reset btn fn
        $(document).on('click','.group-zone-reset-btn',function (e) {

            let edit_hub_id =  $('.group_zone_hub_id').val();
            let edit_hub_name =  $('.group_zone_hub_name').val();

            // resting form
            groupZineFormRest();

            // updating from data
            $('.group_zone_hub_name').val(edit_hub_name);
            $('.group_zone_hub_id').val(edit_hub_id);

        });


        // function for check the option is already selected
        $(document).on('change','.dynamic_section_group_zones',function(){
            let el =  $(this);
            let selected_value = el.val();
            let selected_value_zones_ids = (selected_value!= '') ? $('.group-by-zone-tbl #'+selected_value).find('.edit-group-zone-ajax').attr('data-attached').split("|").filter(e =>  e) : [];
            let selecetd_zones_ids = [];

            // looping on every element
            $('.dynamic_section_group_zones').not(el).each(function(index){
                let loop_val =  $(this).val();
                let name =  $(this).find('option:selected').text();
                // checking the value is not empty
                let loop_val_selected_value_zones_ids = [];
                if(loop_val != '')
                {
                    loop_val_selected_value_zones_ids = $('.group-by-zone-tbl #'+loop_val).find('.edit-group-zone-ajax').attr('data-attached').split("|").filter(e =>  e);
                }
                selecetd_zones_ids = selecetd_zones_ids.concat(loop_val_selected_value_zones_ids);
                // intersect array
                var filteredArray = selecetd_zones_ids.filter(value => selected_value_zones_ids.includes(value));
                // checking the zone is already selected
                if(filteredArray.length > 0)
                {
                    // show error  alert and breack the loop
                    ShowSessionAlert('danger','One of a zone in this group is already in another group named "'+name+'"',true);
                    // resseting current selection
                    el.val('');
                    return false;
                }

                if(loop_val == selected_value)
                {
                    // show error  alert and breack the loop
                    ShowSessionAlert('danger','This option is already selected to another box please try a different one ',true);
                    // resseting current selection
                    el.val('');
                    return false;
                }
            });
        });




        // add dynamic section pricing
        $('.add-dynamic-section-input-box').click(function(){

            // making calculation to false for recalculate
            calcualtion_validation_hendler(false);

            // cloning the html
            let bracket_html = $('.dynamic-section-input-box-org').clone();
            // getting dynamic_section_type name
            let dynamic_section_type = bracket_html.find('.dynamic_section_type').val();

            //empty inputs
            bracket_html.find('input').removeAttr('value');
            bracket_html.find('select').val('');

            // converting object clone to html
            bracket_html = bracket_html.html().trim();

            // html
            let append_bracket_html = '<div class="col-sm-12 dynamic-section-input-box-inner dynamic-section-input-can-add dynamic-section-input-box-appended">'+bracket_html+' <i class="fa fa-times-circle remove-dynamic-box"></i></div>';
            $('.dynamic-section-input-box').prepend(append_bracket_html);


            // updating section type
            $('.dynamic-section-input-can-add .dynamic_section_type').val(dynamic_section_type);

            // attacing date pickeer picker init
            jQuery('.timepicker').datetimepicker({
                format: 'HH:mm:ss'

            });
        });

        // dynamic section functions
        $('.dynamic-section-calculate-btn').click(function () {
            calcualtion_validation_hendler(true);
            dynamic_section_calculations();
        });

        // dynamic section calculations
        function dynamic_section_calculations()
        {

            // base variable
            let base_amount = $('.baseamount').val();

            $('.baseamount').removeClass('error-input');

            // checking the plan base amount is not null
            if( 0 > base_amount ||  base_amount == '')
            {
                alert('Please set base amount for calculations');
                $('.baseamount').addClass('error-input');
                return ;
            }

            let selected_plan = $('.plane-type').val();
            let vehicle_using_type = $('.vehicle_using_type').val();

            // validations
            if(vehicle_using_type == ''){alert('Please select Category Vehicle Using Type'); return}
            else if(selected_plan ==''){alert('Please select Plan Type'); return}

            // input varables
            let total_count_pricing_box = $('.dynamic-section-input-box-inner').length;
            let pricing_box = '';
            let section_prefix = '';
            let drop_val = 0;
            let amount_val = 0;
            let gas_val = 0;
            let tax = 0;
            let duration_Per_drop = 0;
            let buffer_time = 0;
            let daily_drops = 0;
            let duration = 0;
            let hourly_rate = 0;


            // output variable
            var rev_val = 0;
            var calculated_tax_val = 0;
            var company_tax_val = 0;
            var company_rev_with_tax = 0;
            var total_cost = 0;
            var margins = 0;
            var sub_joey_charges_val = 0;
            var duration_Per_drop_in_seconds = 0;
            var buffer_time_in_seconds = 0;
            var total_hours = '00:00:00';
            var total_hours_in_seconds = 0;
            var weekly_drops = 0;
            var duration_in_seconds = 0;

            // looping on every section
            $('.dynamic-section-input-box-inner').each(function () {

                // checking plan type for calculations
                if(selected_plan == 'group_zone_pricing_per_drop|custom_route|big_box')
                {

                    // getting form element
                    pricing_box = $(this);

                    // getting prefix for getting input
                    section_prefix = pricing_box.find('.dynamic_section_type').attr('data-prefix');


                    // getting values for calculation
                    drop_val = pricing_box.find('.'+section_prefix+'_drops').val();
                    tax = pricing_box.find('.'+section_prefix+'_tax').val();
                    amount_val = pricing_box.find('.'+section_prefix+'_amount').val();


                    // validations
                    if(amount_val == '' || amount_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the correct "Joey Charges" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }
                    else if( drop_val == '' ||  drop_val < 0)
                    {
                        // show error  alert and breack the loop
                        ShowSessionAlert('danger','please enter the "Drops" value and hit calculate then save',true);
                        calcualtion_validation_hendler(false);
                        return false;
                    }

                    // calclation section

                        // calculating company tax amount if tax value us enter
                        rev_val = drop_val * base_amount;
                        company_tax_val = (tax / 100) * rev_val;
                        company_rev_with_tax = company_tax_val + rev_val;


                        // doing calculation
                        sub_joey_charges_val = drop_val * amount_val;
                        calculated_tax_val =  (tax / 100) * sub_joey_charges_val;
                        total_cost = calculated_tax_val + sub_joey_charges_val;
                        margins = (1-(sub_joey_charges_val / rev_val))*100;


                    // setting up values
                    pricing_box.find('.'+section_prefix+'_rev').val(rev_val.toFixed(3));
                    pricing_box.find('.'+section_prefix+'_company_tax_charges').val(company_tax_val.toFixed(3));
                    pricing_box.find('.'+section_prefix+'_company_total_charges').val(company_rev_with_tax.toFixed(3));
                    pricing_box.find('.'+section_prefix+'_sub_joey_charges').val(sub_joey_charges_val.toFixed(3));
                    pricing_box.find('.'+section_prefix+'_tax_charges').val(calculated_tax_val.toFixed(3));
                    pricing_box.find('.'+section_prefix+'_total_cost').val(total_cost.toFixed(3));
                    pricing_box.find('.'+section_prefix+'_margins').val(margins.toFixed(3));
                    // updating box  sorting order
                    $(this).find('.dynamic_section_sort_order').val(total_count_pricing_box);
                    total_count_pricing_box--;

                }

            });



        }

        // bracket pricing form hendler
        function form_hendler_dynamic_section(form_type,select_el)
        {

            /*set inputs by select value */
            let selected_value =
                {
                    "group_zone_pricing_per_drop|custom_route|big_box":
                        [
                            //'.dynamic_section_type',
                            '.dynamic_section_group_name',
                            '.dynamic_section_group_zones',
                            '.dynamic_section_drops',
                            '.dynamic_section_rev',
                            '.dynamic_section_tax',
                            '.dynamic_section_company_tax_charges',
                            '.dynamic_section_company_total_charges',
                            '.dynamic_section_amount',
                            '.dynamic_section_sub_joey_charges',
                            '.dynamic_section_tax_charges',
                            '.dynamic_section_total_cost',
                            '.dynamic_section_margins',
                            // for custom routing sections
                            //'.custom_route_dynamic_section_type',
                            '.custom_route_dynamic_section_drops',
                            '.custom_route_dynamic_section_rev',
                            '.custom_route_dynamic_section_tax',
                            '.custom_route_dynamic_section_company_tax_charges',
                            '.custom_route_dynamic_section_company_total_charges',
                            '.custom_route_dynamic_section_amount',
                            '.custom_route_dynamic_section_sub_joey_charges',
                            '.custom_route_dynamic_section_tax_charges',
                            '.custom_route_dynamic_section_total_cost',
                            '.custom_route_dynamic_section_margins',
                            // for big box
                            //'.big_box_dynamic_section_type',
                            '.big_box_dynamic_section_drops',
                            '.big_box_dynamic_section_rev',
                            '.big_box_dynamic_section_tax',
                            '.big_box_dynamic_section_company_tax_charges',
                            '.big_box_dynamic_section_company_total_charges',
                            '.big_box_dynamic_section_amount',
                            '.big_box_dynamic_section_sub_joey_charges',
                            '.big_box_dynamic_section_tax_charges',
                            '.big_box_dynamic_section_total_cost',
                            '.big_box_dynamic_section_margins',

                        ],
                };

            /*geting all input classes array to be showen and converting them into string*/

            let select_from_inputs = selected_value[form_type].toString();

            // showing title
            //$('.form-section-3').find('.section-heading').text(form_type.replace(/([^a-zA-Z0-9]|\s\s+)/g, " "));

            // showing inputs
            $('.dynamic-section-input-box-inner').find(select_from_inputs).closest('.from-input-col').removeClass('hide');

            // making inputs requried
            $('.dynamic-section-input-box-inner').find(select_from_inputs).prop('required',true);

            // toggleing class for make this input calculate able
            //$('.bracket-pricing-input-box-inner').find(select_from_inputs).toggleClass('calculate');

            // show form
            $('.form-section-4').removeClass('hide');
            $('.dynamic-section-detail-form-wrap').removeClass('hide');
            // checking the sections should show
            let total_sections_names = form_type.split('|');
            //checking the plan type have count grater then 2
            if(total_sections_names.length > 2 )
            {
                total_sections_names.forEach(function(value , index){
                    let class_name = value.replace("_", "-");
                    $('.dynamic-section-'+class_name).removeClass('hide');
                });

            }

        }

        // removing bracket
        $(document).on('click','.remove-dynamic-box',function(){

            var confirmation = confirm("Are you sure you want to delete");
            if(confirmation)
            {
                $(this).parent('.dynamic-section-input-box-appended').remove();
            }
        });



    </script>
    <script src="https://finance.joeyco.com/assets/admin/scripts/custom/custom.js"></script>
@endsection