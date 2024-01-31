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
            'Change Password' => 'sub-admin-change.password|sub-admin-create.password',
			'Account Security' => 'account-security.edit|account-security.update',
            'Delete' => 'subAdmin.delete',
        ],

    'Ctc Sub Admin'=>
        [
            'Sub Admins' => 'ctc-subadmin.index|ctc-subadmin.data',
            'Create' => 'ctc-subadmin.add|ctc-subadmin.create',
            'Edit' => 'ctc-subadmin.edit|ctc-subadmin.update',
            'Status change' => 'ctc-subadmin.active|ctc-subadmin.inactive',
            'View' => 'ctc-subadmin.profile',
            'Delete' => 'ctc-subadmin.delete',
        ],

    'New Montreal Dashboard'=>
        [
            'New Montreal Dashboard' => 'newmontreal.index|newmontreal.data',
            'Montreal View' => 'newmontreal.profile',
            'Montreal Excel' => 'newexport_Montreal.excel',
            'New Sorted Order' => 'newmontreal-sort.index|newmontrealSorted.data',
            'Montreal Sorted View' => 'newmontreal_sorted.profile',
            'Sorted Excel' => 'newexport_MontrealSorted.excel',
            'New Pickup From Hub' => 'newmontreal-pickup.index|newmontrealPickedUp.data',
            'Montreal Pickup View' => 'newmontreal_pickup.profile',
            'Pick Up Excel' => 'newexport_MontrealPickedUp.excel',
            'New Not Scan' => 'newmontreal-not-scan.index|newmontrealNotScan.data',
            'Montreal Not Scan View' => 'newmontreal_notscan.profile',
            'Not Scan Excel' => 'newexport_MontrealNotScan.excel',
            'New Delivered Orders' => 'newmontreal-delivered.index|newmontrealDelivered.data',
            'Montreal Delivered View' => 'newmontreal_delivered.profile',
            'Delivered Excel' => 'newexport_MontrealDelivered.excel',
            'New Returned Orders' => 'newmontreal-returned.index|newmontrealReturned.data',
            'Montreal Returned View' => 'newmontreal_returned.profile',
            'Returned Excel' => 'newexport_MontrealReturned.excel',
            'New Custom Route Orders' => 'newmontreal-custom-route.index|newmontrealCustomRoute.data',
            'Montreal Custom Route View' => 'newmontreal_customroute.profile',
            'Custom Route Excel' => 'newexport_MontrealCustomRoute.excel',

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
            'Returned Orders' => 'montreal-returned.index|montrealReturned.data',
            'Montreal Returned View' => 'montreal_returned.profile',
            'Returned Excel' => 'export_MontrealReturned.excel',
            'Route Information' => 'montreal-route-info.index',
            'Route Detail' => 'montreal_route.detail',
            'Route Order Detail' => 'montrealinfo_route.detail',
            'Route Info Excel' => 'export_MontrealRouteInfo.excel',
        ],

    'New Ottawa Dashboard'=>
        [
            'New Ottawa Dashboard' => 'newottawa.index|newottawa.data',
            'Ottawa View' => 'newottawa.profile',
            'Ottawa Excel' => 'newexport_Ottawa.excel',
            'New Sorted Order' => 'newottawa-sort.index|newottawaSorted.data',
            'Ottawa Sorted View' => 'newottawa_sorted.profile',
            'Sorted Excel' => 'newexport_OttawaSorted.excel',
            'New Pickup From Hub' => 'newottawa-pickup.index|newottawaPickedUp.data',
            'Ottawa Pickup View' => 'newottawa_pickup.profile',
            'Pick Up Excel' => 'newexport_OttawaPickedUp.excel',
            'New Not Scan' => 'newottawa-not-scan.index|newottawaNotScan.data',
            'Ottawa Not Scan View' => 'newottawa_notscan.profile',
            'Not Scan Excel' => 'newexport_OttawaNotScan.excel',
            'New Delivered Orders' => 'newottawa-delivered.index|newottawaDelivered.data',
            'Ottawa Delivered View' => 'newottawa_delivered.profile',
            'Delivered Excel' => 'newexport_OttawaDelivered.excel',
            'New Returned Orders' => 'newottawa-returned.index|newottawaReturned.data',
            'Returned Excel' => 'newexport_OttawaReturned.excel',
            'Ottawa Returned View' => 'newottawa_returned.profile',
            'New Custom Route Orders' => 'newottawa-custom-route.index|newottawaCustomRoute.data',
            'Custom Route Excel' => 'newexport_OttawaCustomRoute.excel',
            'Ottawa Custom Route View' => 'newottawa_CustomRoute.profile',
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
            'Returned Excel' => 'export_OttawaReturned.excel',
            'Ottawa Returned View' => 'ottawa_returned.profile',
            'Returned Orders' => 'ottawa-returned.index|ottawaReturned.data',
            'Route Information' => 'ottawa-route-info.index',
            'Route Detail' => 'ottawa_route.detail',
            'Route Order Detail' => 'ottawainfo_route.detail',
            'Route Info Excel' => 'export_OttawaRouteInfo.excel',
        ],
     'CTC Dashboard'=>
        [
            'CTC Dashboard' => 'ctc-dashboard.index|ctc-dashboard.data',
            'CTC View' => 'ctc-new.profile',
            'CTC Excel' => 'export_ctc_new_dashboard.excel',
            'CTC Summary' => 'ctc_reporting.index|ctc_reporting_data.data',
			'CTC Summary View' => 'ctc-summary.profile',
			'CTC Graph' => 'ctc-graph.index|ctc-otd-day.index|ctc-otd-week.index|ctc-otd-month.index',
            'Route Information' => 'ctc-route-info.index',
            'Route Detail' => 'ctc_route.detail',
            'Mark Delay' => 'route-mark-delay',
            'Route Order Detail' => 'ctcinfo_route.detail',
            'Route Info Excel' => 'export_CTCRouteInfo.excel',
            //'CTC Dashboard' => 'ctc.index|ctc.data',
            //'CTC View' => 'ctc.profile',
            //'CTC Excel' => 'export_ctc.excel',
            //'Sorted Order' => 'ctc-sort.index|ctcSorted.data',
            //'CTC Sorted View' => 'ctc_sorted.profile',
            //'Sorted Excel' => 'export_CTCSorted.excel',
            //'Pickup From Hub' => 'ctc-pickup.index|ctcPickedUp.data',
            //'CTC Pickup View' => 'ctc_pickup.profile',
            //'Pick Up Excel' => 'export_CTCPickedUp.excel',
            //'Not Scan' => 'ctc-not-scan.index|ctcNotScan.data',
            //'CTC Not Scan View' => 'ctc_notscan.profile',
            //'Not Scan Excel' => 'export_CTCNotScan.excel',
            //'Delivered Orders' => 'ctc-delivered.index|ctcDelivered.data',
            //'CTC Delivered View' => 'ctc_delivered.profile',
            //'Delivered Excel' => 'export_CTCDelivered.excel',
            //'Route Information' => 'ctc-route-info.index',
            //'Route Detail' => 'ctc_route.detail',
            //'Route Order Detail' => 'ctcinfo_route.detail',
            //'Route Info Excel' => 'export_CTCRouteInfo.excel',
        ],
    'Toronto Flower Dashboard'=>
        [
            'Route Information' => 'toronto-flower-route-info.index',
            'Route Detail' => 'toronto_flower_route.detail',
            'Route Order Detail' => 'toronto_flower_info_route.detail',
            'Route Info Excel' => 'export_toronto_flower_route_info.excel',
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
            'Loblaws Calgary' => 'loblawscalgary.index|loblawscalgary_orders.index|loblawscalgary_otd_charts.index|loblawscalgary_ota_charts.index|loblawscalgary_total_order.index',
            'Loblaws Home Delivery' => 'loblawshome.index|loblawshome_order.index|loblawshome_otd_charts.index|loblawshome_ota_charts.index|loblawshome_total_order.index',
			'Loblaws Re-Processing' => 'loblaws.order-reprocessing|loblaws.order-reprocessing-update',
			 'Loblaws Home Delivery Reporting' => 'loblaws-homedelivery-dashboard-reporting-csv|generate-loblaws-homedelivery-report-csv',
			 'Loblaws Dashboard Reporting' => 'loblaws-dashboard-reporting-csv|generate-loblaws-report-csv',
			 'Loblaws Calgary Reporting' => 'loblaws-calgary-dashboard-reporting-csv|generate-calgary-loblaws-report-csv',
        ],
    'Grocery Dashboard'=>
        [
            'Grocery Dashboard' => 'grocerydashboard.index|groceryajaxorder.index|groceryotdcharts.index',

        ],
   /* 'Other Action'=>
        [
            'Update Status' => 'hub-routific.index|hub-routific-update.Update',
            'Update Multiple Status' => 'multiple-tracking-id.index|multiple-tracking-id.update',
            'Search Order' => 'searchorder.index',
            'Search Order Update' => 'update-order.update',
            'Search By Multiple Order' => 'search-multiple-tracking.index',
            'Order Detail' => 'searchorder.show',
            'Delete Route' => 'route.index|route.destroy',
        ],*/
		 'Other Action'=>
        [


            'Update Orders' => 'multiple-tracking-id.index|multiple-tracking-id.update',
             'Search Order' => 'search-multiple-tracking.index|searchorder.show|update-order.update',
            'Upload Image' => 'sprint-image-upload',
			'Manual Status History' => 'manual-status.index|manual-status.data',
			
			'Generate Csv' => 'generate-csv',


        ],
		
		'DNR Reporting'=>
        [
            'DNR Reporting' => 'dnr.index|dnr.data',
            'DNR Excel' => 'dnr.export',
        ],
        'Customer Support'=>
        [
            'Customer Support' => 'order-confirmation-list.index|orderConfirmation.transfer|Column.Update|add-notes',
            'History' => 'order-confirmation.history|add-notes',
            'Return To Merchant' => 'expired-order.history|return-order.update|add-notes',
            'Returned Order' => 'returned-order.index',
        ],
      'Reason'=>
        [
            'Reason' => 'reason.index|reason.data',
            'Create' => 'reason.add|reason.create',
            'Edit' => 'reason.edit|reason.update',
            'Delete' => 'reason.delete',
        ]
    /* 'Vendor Reporting'=>
         [
             'Vendor Reporting' => 'reporting.index|reporting.data',
             'Vendor Reporting Excel' => 'export_reporting.excel',
         ],*/

   /* 'CTC Summary '=>
        [
            'CTC Summary' => 'ctc_reporting.index|ctc_reporting_data.data',
			 'CTC Reporting Excel' => 'export_ctc_reporting.excel',
        ],*/

];
