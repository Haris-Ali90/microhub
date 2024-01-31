<?php


/**
 * Permissions config
 *
 * @date   23/10/2020
 */

return [
        'Management Portal'=>
        [
            'Management View' => 'statistics.index|statistics-day-otd.index|statistics-week-otd.index|statistics-month-otd.index|statistics-year-otd.index|statistics-all-counts.index|statistics-failed-counts.index|statistics-custom-counts.index|statistics-manual-counts.index|statistics-route-counts.index|statistics-route-detail.index|statistics-on-time-counts.index|statistics-top-ten-joeys.index|statistics-least-ten-joeys.index|statistics-graph.index|statistics-brooker.index|statistics-order.index|statistics-failed-order.index|statistics-brooker-detail.index|statistics-brooker-detail-day-otd.index|statistics-brooker-detail-week-otd.index|statistics-brooker-detail-month-otd.index|statistics-brooker-detail-year-otd.index|statistics-brooker-detail-all-counts.index|statistics-brooker-detail-failed-counts.index|statistics-brooker-detail-custom-counts.index|statistics-brooker-detail-manual-counts.index|statistics-brooker-detail-route-counts.index|statistics-brooker-detail-on-time-counts.index|statistics-brooker-detail-top-ten-joeys.index|statistics-brooker-detail-least-ten-joeys.index|statistics-brooker-detail-graph.index|statistics-brooker-detail-brooker.index|statistics-brooker-detail-order.index|statistics-brooker-detail-failed-order.index|statistics-brooker-detail-all-joeys-otd.index|statistics-brooker-detail-all-joeys-otd.index|statistics-joey-detail.index|statistics-joey-detail-day-otd.index|statistics-joey-detail-week-otd.index|statistics-joey-detail-month-otd.index|statistics-joey-detail-year-otd.index|statistics-joey-detail-all-counts.index|statistics-joey-detail-manual-counts.index|statistics-joey-detail-joey-time.index|statistics-joey-detail-graph.index|statistics-joey-detail-order.index|statistics-joey-detail-failed-order.index|statistics-flag-order-list-pie-chart-data',
            'Joey Management View' => 'joey-management.index|joey-management-joey-count.index|joey-management-joey-count.onduty|joey-management-orders-count.index|joey-management-otd-day.index|joey-management-otd-week.index|joey-management-otd-month.index|joey-management-list.index|joey-management-order-list.index|joey-management-all-joeys-otd.index|statistics-joey-detail.index|statistics-joey-detail-day-otd.index|statistics-joey-detail-week-otd.index|statistics-joey-detail-month-otd.index|statistics-joey-detail-year-otd.index|statistics-joey-detail-all-counts.index|statistics-joey-detail-manual-counts.index|statistics-joey-detail-joey-time.index|statistics-joey-detail-graph.index|statistics-joey-detail-order.index|statistics-joey-detail-failed-order.index',
            'Brooker Management View' => 'brooker-management.index|brooker-management-brooker-count.index|brooker-management-joey-count.index|brooker-management-joey-count.onduty|brooker-management-orders-count.index|brooker-management-otd-day.index|brooker-management-otd-week.index|brooker-management-otd-month.index|brooker-management-list.index|brooker-management-brooker-list.index|brooker-management-all-brooker-otd.index|joey-management-all-brooker-otd.index|statistics-brooker-detail.index|statistics-brooker-detail-day-otd.index|statistics-brooker-detail-week-otd.index|statistics-brooker-detail-month-otd.index|statistics-brooker-detail-year-otd.index|statistics-brooker-detail-all-counts.index|statistics-brooker-detail-failed-counts.index|statistics-brooker-detail-custom-counts.index|statistics-brooker-detail-manual-counts.index|statistics-brooker-detail-route-counts.index|statistics-brooker-detail-on-time-counts.index|statistics-brooker-detail-top-ten-joeys.index|statistics-brooker-detail-least-ten-joeys.index|statistics-brooker-detail-graph.index|statistics-brooker-detail-brooker.index|statistics-brooker-detail-order.index|statistics-brooker-detail-failed-order.index|statistics-brooker-detail-all-joeys-otd.index|statistics-brooker-detail-all-joeys-otd.index|statistics-joey-detail.index|statistics-joey-detail-day-otd.index|statistics-joey-detail-week-otd.index|statistics-joey-detail-month-otd.index|statistics-joey-detail-year-otd.index|statistics-joey-detail-all-counts.index|statistics-joey-detail-manual-counts.index|statistics-joey-detail-joey-time.index|statistics-joey-detail-graph.index|statistics-joey-detail-order.index|statistics-joey-detail-failed-order.index',
            'In Bound' =>'statistics-inbound.index|statistics-inbound-data.index|statistics-setup-time.index|statistics-sorting-time.index|statistics-inbound.wareHouseSorterUpdate',
            'Out Bound' =>'statistics-outbound.index|statistics-outbound-data.index|statistics-dispensing-time.index|statistics-outbound.wareHouseSorterUpdate',
            'Summary' =>'warehouse-summary.index|warehouse-summary-data.index',
            'Manager' =>'manager.index|manager.create|manager.store|manager.edit|manager.update|manager.show|check-for-hub',
            'Alert System' =>'alert-system.index|warehousesorter.index|warehousesorter.data|warehousesorter.add|warehousesorter.create|warehousesorter.profile|warehousesorter.edit|warehousesorter.update|warehousesorter.delete',

        ],
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
            'New Montreal Dashboard' => 'newmontreal.index|newmontreal.data|newmontreal.totalcards|newmontreal.mainfestcards|newmontreal.failedcards|newmontreal.customroutecards|newmontreal.yesterdaycards|newmontreal.route-list|newmontreal.joey-list|newmontreal-dashboard.index',
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
            'New Returned Orders' => 'newmontreal-returned.index|newmontrealReturned.data|newmontreal-notreturned.index|newmontrealNotReturned.data',
            'Montreal Returned View' => 'newmontreal_returned.profile|newmontreal_notreturned.profile',
            'Returned Excel' => 'newexport_MontrealReturned.excel|newexport_MontrealNotReturned.excel|newexport_MontrealNotReturned_Tracking.excel',
            'New Custom Route Orders' => 'newmontreal-custom-route.index|newmontrealCustomRoute.data',
            'Montreal Custom Route View' => 'newmontreal_customroute.profile',
            'Custom Route Excel' => 'newexport_MontrealCustomRoute.excel',
            'Route Information' => 'newmontreal-route-info.index|newmontreal_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Detail' => 'newmontreal_route.detail|newmontreal_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Order Detail' => 'newmontrealinfo_route.detail',
            'Route Info Excel' => 'newexport_MontrealRouteInfo.excel',
            'Notes'=>'newmontreal-route-info.addNote|newmontreal-route-info.getNotes',
        ],


    /*'Montreal Dashboard'=>
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
        ],*/

    'New Ottawa Dashboard'=>
        [
            'New Ottawa Dashboard' => 'newottawa.index|newottawa.data|newottawa.totalcards|newottawa.mainfestcards|newottawa.failedcards|newottawa.customroutecards|newottawa.yesterdaycards|newottawa.ottawa-route-list|newottawa.ottawa-joey-list|newottawa-dashboard.index',
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
            'New Returned Orders' => 'newottawa-returned.index|newottawaReturned.data|newottawa-notreturned.index|newottawaNotReturned.data',
            'Returned Excel' => 'newexport_OttawaReturned.excel|newexport_OttawaNotReturned.excel|newexport_OttawaNotReturned_tracking.excel',
            'Ottawa Returned View' => 'newottawa_returned.profile|newottawa_notreturned.profile',
            'New Custom Route Orders' => 'newottawa-custom-route.index|newottawaCustomRoute.data',
            'Custom Route Excel' => 'newexport_OttawaCustomRoute.excel',
            'Ottawa Custom Route View' => 'newottawa_CustomRoute.profile',
            'Route Information' => 'newottawa-route-info.index|newottawainfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Detail' => 'newottawa_route.detail|newottawainfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Order Detail' => 'newottawainfo_route.detail',
            'Route Info Excel' => 'newexport_OttawaRouteInfo.excel',
            'Notes'=>'newottawa-route-info.addNote|newottawa-route-info.getNotes',
        ],

   /* 'Ottawa Dashboard'=>
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
        ],*/
     'CTC Dashboard'=>
        [
            'CTC Dashboard' => 'ctc-dashboard.index|ctc-dashboard.data',
            'CTC View' => 'ctc-new.profile',
            'CTC Excel' => 'export_ctc_new_dashboard.excel',
            'OTD Report' => 'export_ctc_new_dashboard_otd_report.excel',
            'CTC Summary' => 'ctc_reporting.index|ctc_reporting_data.data',
			'CTC Summary View' => 'ctc-summary.profile',
			'CTC Graph' => 'ctc-graph.index|ctc-otd-day.index|ctc-otd-week.index|ctc-otd-month.index',
            'Route Information' => 'ctc-route-info.index|ctcinfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Detail' => 'ctc_route.detail|ctcinfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Mark Delay' => 'route-mark-delay',
            'Route Order Detail' => 'ctcinfo_route.detail',
            'Route Info Excel' => 'export_CTCRouteInfo.excel',
            'New CTC Dashboard' => 'new-order-ctc.data|new-order-ctc.index|new-ctc-card-dashboard.index|new-ctc.totalcards|new-ctc.customroutecards|new-ctc.yesterdaycards',
            'New CTC View' => 'new-ctc-detail-detail.profile',
            'New CTC Excel' => 'new-order-ctc-export.excel',
            'Sorted Order' => 'new-sort-ctc.index|new-sort-ctc.data',
            'CTC Sorted View' => 'new-ctc-sorted-detail.profile',
            'Sorted Excel' => 'new-sort-ctc-export.excel',
            'Pickup From Hub' => 'new-pickup-ctc.index|new-pickup-ctc.data',
            'CTC Pickup View' => 'new-ctc-pickup-detail.profile',
            'Pick Up Excel' => 'new-pickup-ctc-export.excel',
            'Not Scan' => 'new-not-scan-ctc.index|new-not-scan-ctc.data',
            'CTC Not Scan View' => 'new-ctc-notscan-detail.profile',
            'Not Scan Excel' => 'new-not-scan-ctc-export.excel',
            'Delivered Orders' => 'new-delivered-ctc.index|new-delivered-ctc.data',
            'CTC Delivered View' => 'new-ctc-delivered-detail.profile',
            'Delivered Excel' => 'new-delivered-ctc-export.excel',
            'Returned Orders' => 'new-returned-ctc.index|new-returned-ctc.data|new-notreturned-ctc.index|new-notreturned-ctc.data',
            'Returned Excel' => 'new-returned-ctc-export.excel|new-notreturned-ctc-export.excel|new-notreturned-ctc-tracking-export.excel',
            'CTC Returned View' => 'new-ctc-returned-detail.profile|new-ctc-notreturned-detail.profile',
            'Custom Route Orders' => 'new-custom-route-ctc.index|new-custom-route-ctc.data',
            'Custom Route Excel' => 'new-custom-route-ctc-export.excel',
            'CTC Custom Route View' => 'new-ctc-CustomRoute-detail.profile',
            'Notes'=>'new-ctc-route-info.addNote|new-ctc-route-info.getNotes',
        ],

'Borderless Dashboard'=>
        [
            'Borderless Dashboard' => 'borderless-dashboard.index|borderless-dashboard.data',
            'Borderless View' => 'borderless-order.profile',
            'Borderless Excel' => 'borderless-dashboard-export.excel',
            'OTD Report' => 'borderless-dashboard-export-otd-report.excel',
            'Borderless Summary' => 'borderless_reporting.index|new_borderless_reporting_data.data',
            'Borderless Summary View' => 'borderless-summary.profile',
            'Borderless Graph' => 'borderless-graph.index|borderless-otd-day.index|borderless-otd-week.index|borderless-otd-month.index',
            'Route Information' => 'borderless-route-info.index|borderlessinfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Detail' => 'borderless_route.detail|borderlessinfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Mark Delay' => 'borderless-route-mark-delay',
            'Route Order Detail' => 'borderlessinfo_route.detail',
            'Route Info Excel' => 'export_BorderlessRouteInfo.excel',
            'New Borderless Dashboard' => 'new-order-borderless.data|new-order-borderless.index|new-borderless-card-dashboard.index|new-borderless.totalcards|new-borderless.customroutecards|new-borderless.yesterdaycards',
            'New Borderless View' => 'new-borderless-detail-detail.profile',
            'New Borderless Excel' => 'new-order-borderless-export.excel',
            'Sorted Order' => 'new-sort-borderless.index|new-sort-borderless.data',
            'Borderless Sorted View' => 'new-borderless-sorted-detail.profile',
            'Sorted Excel' => 'new-sort-borderless-export.excel',
            'Pickup From Hub' => 'new-pickup-borderless.index|new-pickup-borderless.data',
            'Borderless Pickup View' => 'new-borderless-pickup-detail.profile',
            'Pick Up Excel' => 'new-pickup-borderless-export.excel',
            'Not Scan' => 'new-not-scan-borderless.index|new-not-scan-borderless.data',
            'Borderless Not Scan View' => 'new-borderless-notscan-detail.profile',
            'Not Scan Excel' => 'new-not-scan-borderless-export.excel',
            'Delivered Orders' => 'new-delivered-borderless.index|new-delivered-borderless.data',
            'Borderless Delivered View' => 'new-borderless-delivered-detail.profile',
            'Delivered Excel' => 'new-delivered-borderless-export.excel',
            'Returned Orders' => 'new-returned-borderless.index|new-returned-borderless.data|new-notreturned-borderless.index|new-notreturned-borderless.data',
            'Returned Excel' => 'new-returned-borderless-export.excel|new-notreturned-borderless-export.excel|new-notreturned-borderless-tracking-export.excel',
            'Borderless Returned View' => 'new-borderless-returned-detail.profile|new-borderless-notreturned-detail.profile',
            'Custom Route Orders' => 'new-custom-route-borderless.index|new-custom-route-borderless.data',
            'Custom Route Excel' => 'new-custom-route-borderless-export.excel',
            'Borderless Custom Route View' => 'new-borderless-CustomRoute-detail.profile',
            'Notes'=>'new-borderless-route-info.addNote|new-borderless-route-info.getNotes',
        ],


    /*'New CTC Dashboard'=>
        [
            'CTC Dashboard' => 'new-ctc-dashboard.index|new-ctc-dashboard.data',
            'CTC View' => 'new-ctc-order.profile',
            'CTC Excel' => 'new-ctc-dashboard-export.excel',
            'OTD Report' => 'new-ctc-dashboard-export-otd-report.excel',
            'CTC Summary' => 'new-ctc_reporting.index|new-ctc_reporting_data.data',
            'CTC Summary View' => 'new-ctc-order.profile',
            'CTC Graph' => 'new-ctc-graph.index|new-ctc-otd-day.index|new-ctc-otd-week.index|new-ctc-otd-month.index',
            'Route Information' => 'new-ctc-route-info.index|new-ctcinfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Route Detail' => 'new-ctc_route.detail|new-ctcinfo_route.route-details.flag-history-model-html-render|flag.create|un-flag',
            'Mark Delay' => 'new-route-mark-delay',
            'Route Order Detail' => 'new-ctcinfo_route.detail',
            'Route Info Excel' => 'new-export_CTCRouteInfo.excel',
            'New CTC Dashboard' => 'new-order-ctc.data|new-order-ctc.index|new-ctc-card-dashboard.index|new-ctc.totalcards|new-ctc.customroutecards|new-ctc.yesterdaycards',
            'New CTC View' => 'new-ctc-detail-detail.profile',
            'New CTC Excel' => 'new-order-ctc-export.excel',
            'Sorted Order' => 'new-sort-ctc.index|new-sort-ctc.data',
            'CTC Sorted View' => 'new-ctc-sorted-detail.profile',
            'Sorted Excel' => 'new-sort-ctc-export.excel',
            'Pickup From Hub' => 'new-pickup-ctc.index|new-pickup-ctc.data',
            'CTC Pickup View' => 'new-ctc-pickup-detail.profile',
            'Pick Up Excel' => 'new-pickup-ctc-export.excel',
            'Not Scan' => 'new-not-scan-ctc.index|new-not-scan-ctc.data',
            'CTC Not Scan View' => 'new-ctc-notscan-detail.profile',
            'Not Scan Excel' => 'new-not-scan-ctc-export.excel',
            'Delivered Orders' => 'new-delivered-ctc.index|new-delivered-ctc.data',
            'CTC Delivered View' => 'new-ctc-delivered-detail.profile',
            'Delivered Excel' => 'new-delivered-ctc-export.excel',
            'Returned Orders' => 'new-returned-ctc.index|new-returned-ctc.data|new-notreturned-ctc.index|new-notreturned-ctc.data',
            'Returned Excel' => 'new-returned-ctc-export.excel|new-notreturned-ctc-export.excel|new-notreturned-ctc-tracking-export.excel',
            'CTC Returned View' => 'new-ctc-returned-detail.profile|new-ctc-notreturned-detail.profile',
            'Custom Route Orders' => 'new-custom-route-ctc.index|new-custom-route-ctc.data',
            'Custom Route Excel' => 'new-custom-route-ctc-export.excel',
            'CTC Custom Route View' => 'new-ctc-CustomRoute-detail.profile',
        ],*/

    'Return Dashboard'=>
        [
            'Return Route Information' => 'return-route-info.index|return-route-order.detail|return-route-info-order.detail',
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
		 'Good Food Dashboard'=>
        [
            'Good Food Dashboard' => 'goodfood.index|goodfood_order.index|goodfood_otd_charts.index|goodfood_ota_charts.index|goodfood-new-count',
            'Good Food Reporting' => 'goodfood-dashboard-reporting-csv|generate-goodfood-report-csv',
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
             'Flag / Un-flag Orders' => 'flag.create|un-flag',
            'Upload Image' => 'sprint-image-upload',
			'Manual Status History' => 'manual-status.index|manual-status.data',
			
			'Generate Csv' => 'generate-csv',
			'Manual Tracking Report' => 'manual-tracking-report.index|manual-tracking.data',
			'Manual Tracking Report Excel' => 'manual-tracking.excel|download-file-tracking',
            'Tracking'=>'search-tracking.index|searchtrackingdetails.show',

        ],
		
		'DNR Reporting'=>
        [
            'DNR Reporting' => 'dnr.index|dnr.data',
            'DNR Excel' => 'dnr.export',
        ],
        'Customer Support'=>
        [
			'Customer Support' => 'order-confirmation-list.index|orderConfirmation.transfer|Column.Update|add-notes|show-notes',
            'History' => 'order-confirmation.history|show-notes',
            'Return To Merchant' => 'expired-order.history|return-order.update|show-notes',
			'Returned Order' => 'returned-order.index|show-notes',
			 
        ],
        'Flag Order Details'=>
        [
            'Flag Order List' => 'flag-order-list.index|flag-order-list.data|flag-order-list-pie-chart-data',
            'Flag Order Detail' => 'flag-order.details',
            'Approved Flag List' => 'approved-flag-list.index|approved-flag-list.data|flag-order.details',
            'Un-Approved Flag List' => 'un-approved-flag-list.index|un-approved-flag-list.data',
            'Mark Approved' => 'joey-performance-status.update',
            'Blocked Joey List' => 'block-joey-flag-list.index|block-joey-flag-list.data',
            'Unblock Joey' => 'unblock-joey-flag.update',

        ],
      'Reason'=>
        [
            'Reason' => 'reason.index|reason.data',
            'Create' => 'reason.add|reason.create',
            'Edit' => 'reason.edit|reason.update',
            'Delete' => 'reason.delete',
        ],
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

/*    'WareHouse Performance Report '=>
        [
            'Warehouse Performance' => 'warehouse-performance.index|warehouse-performance.data',
        ],
  'WareHouse Settings'=>
        [
            'Warehouse Settings List' => 'warehousesorter.index|warehousesorter.data',
            'Create' => 'warehousesorter.add|warehousesorter.create',
            'Edit' => 'warehousesorter.edit|warehousesorter.update'
        ],*/
		'Rights'=>
    [
        'Right List' => 'right.index',
        'Create' => 'right.create|right.store',
        'Edit' => 'right.edit|right.update',
        'View' => 'right.show',
    ],
];
