<?php


/**
 * Permissions config
 *
 * @date   23/10/2020
 */

return [
    'Roles'=>
        [
            'Roles List' => 'role.index',
            'Create' => 'role.create|role.store',
            'Edit' => 'role.edit|role.update',
            'View' => 'role.show',
            'Set permissions' => 'role.set-permissions|role.set-permissions.update',
        ],
    'Sub Admin'=>
        [
            'Sub Admins' => 'sub-admin.index|subAdmin.data',
            'Create' => 'subAdmin.add|subAdmin.create',
            'Edit' => 'subAdmin.edit|subAdmin.update',
            'Status change' => 'sub-admin.active|sub-admin.inactive',
            'View' => 'subAdmin.profile',
            'Change Password' => 'sub-admin-change.password',
            'Delete' => 'subAdmin.delete',
        ],

    'Montreal Dashboard'=>
        [
            'Montreal Dashboard' => 'montreal.index|montreal.data',
            'Montreal View' => 'montreal.profile',
            'Montreal Excel' => 'export_Montreal.excel',
            'Sorted Order' => 'montreal-sort.index|montrealSorted.data',
            'Montreal Sorted View' => 'montreal_sorted.profile',
            'Sorted Excel' => 'export_MontrealSorted.excel',
            'Pickup From Hub' => 'montreal-pickup.index|montrealPickedUp.data',
            'Montreal Pickup View' => 'montreal_pickup.profile',
            'Pick Up Excel' => 'export_MontrealPickedUp.excel',
            'Not Scan' => 'montreal-not-scan.index|montrealNotScan.data',
            'Montreal Not Scan View' => 'montreal_notscan.profile',
            'Not Scan Excel' => 'export_MontrealNotScan.excel',
            'Delivered Orders' => 'montreal-delivered.index|montrealDelivered.data',
            'Montreal Delivered View' => 'montreal_delivered.profile',
            'Delivered Excel' => 'export_MontrealDelivered.excel',
            'Route Information' => 'montreal-route-info.index',
            'Route Detail' => 'montreal_route.detail',
            'Route Order Detail' => 'montrealinfo_route.detail',
            'Route Info Excel' => 'export_MontrealRouteInfo.excel',
        ],

    'Ottawa Dashboard'=>
        [
            'Ottawa Dashboard' => 'ottawa.index|ottawa.data',
            'Ottawa View' => 'ottawa.profile',
            'Ottawa Excel' => 'export_Ottawa.excel',
            'Sorted Order' => 'ottawa-sort.index|ottawaSorted.data',
            'Ottawa Sorted View' => 'ottawa_sorted.profile',
            'Sorted Excel' => 'export_OttawaSorted.excel',
            'Pickup From Hub' => 'ottawa-pickup.index|ottawaPickedUp.data',
            'Ottawa Pickup View' => 'ottawa_pickup.profile',
            'Pick Up Excel' => 'export_OttawaPickedUp.excel',
            'Not Scan' => 'ottawa-not-scan.index|ottawaNotScan.data',
            'Ottawa Not Scan View' => 'ottawa_notscan.profile',
            'Not Scan Excel' => 'export_OttawaNotScan.excel',
            'Delivered Orders' => 'ottawa-delivered.index|ottawaDelivered.data',
            'Ottawa Delivered View' => 'ottawa_delivered.profile',
            'Delivered Excel' => 'export_OttawaDelivered.excel',
            'Route Information' => 'ottawa-route-info.index',
            'Route Detail' => 'ottawa_route.detail',
            'Route Order Detail' => 'ottawainfo_route.detail',
            'Route Info Excel' => 'export_OttawaRouteInfo.excel',
        ],
    'CTC Dashboard'=>
        [
            'CTC Dashboard' => 'ctc.index|ctc.data',
            'CTC View' => 'ctc.profile',
            'CTC Excel' => 'export_ctc.excel',
            'Sorted Order' => 'ctc-sort.index|ctcSorted.data',
            'CTC Sorted View' => 'ctc_sorted.profile',
            'Sorted Excel' => 'export_CTCSorted.excel',
            'Pickup From Hub' => 'ctc-pickup.index|ctcPickedUp.data',
            'CTC Pickup View' => 'ctc_pickup.profile',
            'Pick Up Excel' => 'export_CTCPickedUp.excel',
            'Not Scan' => 'ctc-not-scan.index|ctcNotScan.data',
            'CTC Not Scan View' => 'ctc_notscan.profile',
            'Not Scan Excel' => 'export_CTCNotScan.excel',
            'Delivered Orders' => 'ctc-delivered.index|ctcDelivered.data',
            'CTC Delivered View' => 'ctc_delivered.profile',
            'Delivered Excel' => 'export_CTCDelivered.excel',
            'Route Information' => 'ctc-route-info.index',
            'Route Detail' => 'ctc_route.detail',
            'Route Order Detail' => 'ctcinfo_route.detail',
            'Route Info Excel' => 'export_CTCRouteInfo.excel',
        ],

    'Walmart Dashboard'=>
        [
            // 'Walmart' => 'walmart.index|walmart.data',
            // 'Walmart View' => 'walmart.profile',
            // 'Walmart Excel' => 'export_walmart.excel',
            'Walmart Dashboard' => 'walmartdashboard.index|walmartotdajax.index|walmartshortsummary.index|walmartrenderorder.index|walmartontimeorder.index|walmartstoresdata.index|walmartordersummary.index',
            'Walmart Dashboard Excel' => 'walmartdashboard.excel',
            'Walmart Dashboard Reporting' => 'download-walmart-report-csv-view|generate-walmart-report-csv',
        ],

        'Loblaws Dashboard'=>
        [
            'Loblaws Dashboard' => 'loblawsdashboard.index|loblawsajaxorder.index|loblawsotdcharts.index|loblawsajaxotacharts.index|loblawstotalorder.index',
            'Loblaws Dashboard Reporting' => 'loblaws-dashboard-reporting-csv|generate-loblaws-report-csv',
            'Loblaws Calgary' => 'loblawscalgary.index|loblawscalgary_orders.index|loblawscalgary_otd_charts.index|loblawscalgary_ota_charts.index|loblawscalgary_total_order.index',
            'Loblaws Home Deliveryb' => 'loblawshome.index|loblawshome_order.index|loblawshome_otd_charts.index|loblawshome_ota_charts.index|loblawshome_total_order.index',
            'Loblaws Home Delivery Reporting' => 'loblaws-homedelivery-dashboard-reporting-csv|generate-loblaws-homedelivery-report-csv',
        ],

    'Other Action'=>
        [
            'Update Status' => 'hub-routific.index|hub-routific-update.Update',
            'Update Multiple Status' => 'multiple-tracking-id.index|multiple-tracking-id.update',
            'Search Order' => 'searchorder.index',
            'Search Order Update' => 'update-order.update',
            'Search By Multiple Order' => 'search-multiple-tracking.index',
            'Order Detail' => 'searchorder.show',
            'Delete Route' => 'route.index|route.destroy',
        ],
     'Vendor Reporting'=>
         [
             'Vendor Reporting' => 'reporting.index|reporting.data',
             'Vendor Reporting Excel' => 'export_reporting.excel',
         ],

    'CTC Reporting'=>
        [
            'CTC Reporting' => 'ctc_reporting.index',
        ],

];
