<?php
/*

  |--------------------------------------------------------------------------

  | Application Routes


  |--------------------------------------------------------------------------

  |

  | Here is where you can register all of the routes for an application.

  | It's a breeze. Simply tell Laravel the URIs it should respond to

  | and give it the controller to call when that URI is requested.

  |

 */
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

Route::get('/', function () {
    return redirect('login');
});

Route::get('testnewmontreal/totalcards/{date?}/{type?}', 'Backend\NewMontrealController@montrealTotalCardsForLoop')->name('testnewmontreal.totalcards');

###Right Permissions###
Route::get('dashboard-permissions', 'Backend\DashboardPermissionController@getDashboardPermissions')->name('dashboard-permissions');

###Right Permissions###
Route::get('domain-right', 'Backend\DashboardPermissionController@getAllDomainRights')->name('domain-right');

Route::get('termsconditions', 'UserController@termsConditions');

Route::group(['middleware' => 'web', 'namespace' => 'Backend'], function () {


### ajax based file download route
    Route::get('/download-file', function () {
        // getting file path
        $file_path = public_path() . '/' . request()->file_path;
        // getting file name
        $file_name = explode('/', $file_path);
        $file_name = explode('-', end($file_name))[0];
        // getting file extension
        $file_extension = explode('.', $file_path);
        $file_extension = end($file_extension);
        return response()->download($file_path, $file_name . '.' . $file_extension);
    })->name('download-file');

    Route::match(['GET', 'POST'], 'login', 'Auth\AuthController@adminLogin')->name('login');

    Route::match(['GET', 'POST'], 'reset-password/{token?}', 'Auth\PasswordController@resetPasswordAction');

    Route::post('reset-password-finally', 'Auth\PasswordController@reset');


    ###Reset Password###
    Route::post('/password/email', 'Auth\PasswordController@send_reset_link_email')->name('password.email');
    Route::post('/password/reset', 'Auth\PasswordController@reset_password_update')->name('reset.password.update');
    Route::get('/password/reset', 'Auth\PasswordController@showLinkRequestForm')->name('password.request');
    Route::get('/password/reset/{email}/{token}/{role_id}', 'Auth\PasswordController@reset_password_from_show')->name('password.reset');

    Route::post('google/auth', 'Auth\AuthController@postgoogleAuth');
    Route::get('google-auth', 'Auth\AuthController@getgoogleAuth');
    Route::post('verify/code', 'Auth\AuthController@postverifycode');
    Route::get('verify-code', 'Auth\AuthController@getverifycode');
    Route::post('type/auth', 'Auth\AuthController@posttypeauth');
    Route::get('type-auth', 'Auth\AuthController@getType');

    Route::group(['middleware' => ['admin']], function () {

        Route::post('logout', 'Auth\AuthController@logout');


        ###Micro Hub Dashboard ###
        Route::get('dashboard/{graph?}', 'DashboardController@getIndex');

        ###Incharge Micro Hub Dashboard ###
        Route::get('incharge/dashboard/{graph?}', 'InchargeDashboardController@getIndex');


        ###First Mile Order Details ###
        Route::get('firstmileorder/', 'InchargeDashboardController@FirstMileDetails');
        ###First Mile Total Order Details###
        Route::get('incharge/firstmile/orders', 'InchargeDashboardController@getOrders');
        ###First Mile Total Vendors Details###
        Route::get('incharge/firstmile/vendors', 'InchargeDashboardController@getVendors');
        ###First Mile Total Picked Orders Details###
        Route::get('incharge/firstmile/picked', 'InchargeDashboardController@getPickedOrders');
        ###First Mile Total Remaining Orders Details###
        Route::get('incharge/firstmile/remaining', 'InchargeDashboardController@getRemainingOrders');
        ###First Mile Total Routes Details###
        Route::get('incharge/firstmile/routes', 'InchargeDashboardController@getFirstMileRoutes');
        ###First Mile Total Joeys Details###
        Route::get('incharge/firstmile/joeys', 'InchargeDashboardController@getFirstMileJoeys');

        Route::get('incharge/firstmileorderdetail/', 'InchargeDashboardController@FirstMileOrdersDetails');


        ###Mid Mile Order Details ###
        Route::get('midmileorder/', 'InchargeDashboardController@MidMileDetails');
        ###Mid Mile Total Order Details###
        Route::get('incharge/midmile/orders', 'InchargeDashboardController@getMidMileOrders');
        ###Mid Mile Total Vendors Details###
        Route::get('incharge/midmile/vendors', 'InchargeDashboardController@getMidMileVendors');
        ###Mid Mile Total Picked Orders Details###
        Route::get('incharge/midmile/picked', 'InchargeDashboardController@getMidMilePickedOrders');
        ###Mid Mile My Total Picked Orders Details###
        Route::get('incharge/midmile/mypicked', 'InchargeDashboardController@getMidMileMyPickedOrders');
        ###Mid Mile Total Remaining Orders Details###
        Route::get('incharge/midmile/remaining', 'InchargeDashboardController@getMidMileRemainingOrders');
        ###Mid Mile Total Routes Details###
            Route::get('incharge/midmile/routes', 'InchargeDashboardController@getMidMileRoutes');
        ###Mid Mile Total Joeys Details###
        Route::get('incharge/midmile/joeys', 'InchargeDashboardController@getMidMileJoeys');
        ###My orders on other Hub Details###
        Route::get('incharge/midmile/onotherhub', 'InchargeDashboardController@getMidMileRemainingOrders');
        ###Other Orders on My Hub Details###
        Route::get('incharge/midmile/otherorder', 'InchargeDashboardController@getOtherOrder');
        ###Order needs to be deivered on CC###
        Route::get('incharge/midmile/rccorder', 'InchargeDashboardController@getRemainCCOrder');
        ###Order needs to be picked from CC###
        Route::get('incharge/midmile/pccorder', 'InchargeDashboardController@getPickCCOrder');


        ###Last Mile Order Details ###
        Route::get('lastmileorder/', 'InchargeDashboardController@LastMileDetails');
        ###Last Mile Total Order Details###
        Route::get('incharge/lastmile/orders', 'InchargeDashboardController@getLastMileOrders');
        ###Last Mile Total Vendors Details###
        Route::get('incharge/lastmile/vendors', 'InchargeDashboardController@getLastMileVendors');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/picked', 'InchargeDashboardController@getLastMilePickedOrders');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/complete', 'InchargeDashboardController@getLastMileCompletedOrders');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/return', 'InchargeDashboardController@getLastMileReturnOrders');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/unattempt', 'InchargeDashboardController@getLastMileUnattemptOrders');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/sort', 'InchargeDashboardController@getLastMileSortOrders');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/delay', 'InchargeDashboardController@getLastMileDelayOrders');
        ###Last Mile Total Picked Orders Details###
        Route::get('incharge/lastmile/customorder', 'InchargeDashboardController@getLastMileCustomOrders');
        ###Last Mile Total Remaining Orders Details###
        Route::get('incharge/lastmile/remaining', 'InchargeDashboardController@getLastMileRemainingOrders');
        ###Last Mile Total Routes Details###
        Route::get('incharge/lastmile/routes', 'InchargeDashboardController@getLastMileRoutes');
        ###Last Mile Total Joeys Details###
        Route::get('incharge/lastmile/joeys', 'InchargeDashboardController@getLastMileJoeys');


        ###First Mile Complete Routes Details###
        Route::get('incharge/firstmile/completeroutes', 'InchargeDashboardController@getFirstMileCompleteRoutes');
        ###First Mile Ongoing Routes Details###
        Route::get('incharge/firstmile/ongoingroutes', 'InchargeDashboardController@getFirstMileOngoingRoutes');
        ###First Mile Delay Routes Details###
        Route::get('incharge/firstmile/delayroutes', 'InchargeDashboardController@getFirstMileDelayRoutes');

        ###Mid Mile Complete Routes Details###
        Route::get('incharge/midmile/completeroutes', 'InchargeDashboardController@getMidMileCompleteRoutes');
        ###Mid Mile Ongoing Routes Details###
        Route::get('incharge/midmile/ongoingroutes', 'InchargeDashboardController@getMidMileOngoingRoutes');
        ###Mid Mile Delay Routes Details###
        Route::get('incharge/midmile/delayroutes', 'InchargeDashboardController@getMidMileDelayRoutes');

        ###Last Mile Complete Routes Details###
        Route::get('incharge/lastmile/completeroutes', 'InchargeDashboardController@getLastMileCompleteRoutes');
        ###Last Mile Ongoing Routes Details###
        Route::get('incharge/lastmile/ongoingroutes', 'InchargeDashboardController@getLastMileOngoingRoutes');
        ###Last Mile Delay Routes Details###
        Route::get('incharge/lastmile/delayroutes', 'InchargeDashboardController@getLastMileDelayRoutes');

        ###Micro Hub Privacy Page ###
        Route::get('privacy-policy', 'PrivacyController@getIndex');


        ###Micro Hub Terms Condition ###
        Route::get('terms-conditions', 'TermsController@getIndex');


        ###Micro Hub Agreement Page ###
        Route::get('agreement', 'AgreementController@getIndex');


        ###Blank Landing Page ###
        Route::get('landing', 'MicroHubLandingController@getIndex');

        ###Get Password For The First Time###
        Route::get('microhub/change/password', 'MicroHubChangePassword@getNewPassword');
        ###Update Password For The First Time###
        Route::post('microhub/change/newpassword', 'MicroHubChangePassword@passwordUpdate');


        ###Micro Hub Routing ###
        Route::get('routing', 'MicroHubRoutingController@getIndex');


        ###Micro Hub Finance ###
        Route::get('finance', 'MicroHubFinanceController@getIndex');


        ###Micro Hub Finance_payout ###
        Route::get('finance/payout', 'MicroHubFinancePayoutController@getIndex');


        ###Micro Hub Finance_create ###
        Route::get('Finance/Create', 'MicroHubFinanceCreateController@add')->name('FinanceCreate');

        ###Permission Data###
        Route::get('getPermission', 'DashboardController@getPermission');

        ###SideBar Data###
        Route::get('side', 'DashboardController@sendDataToSideBar');
        ###Setting Data###
        Route::get('microhub/setting', 'MicroHubSettingController@getIndex');
        ###All Packages ###
        Route::get('getAllPackages', 'MicroHubSettingController@getAllPackages');
        ###All Requested Package ###
        Route::get('getRequestedPackage', 'MicroHubSettingController@getRequestedPackage');
        ###New Package Request Create ###
        Route::post('microhub/RequestNewPackage', 'MicroHubSettingController@RequestNewPackage');


        ###profile###
        Route::get('microhub/profile', 'MicroHubProfileController@getIndex');

        Route::get('microhub/temporarypassword', 'MicroHubTemporaryPasswordController@getIndex');
        Route::get('microhub/getGeneratedPassword', 'MicroHubTemporaryPasswordController@getGeneratedPassword');

        ###generate temporary password###
        Route::post('microhub/temp/password', 'MicroHubTemporaryPasswordController@postGeneratedPassword');

        ###profile###
            Route::get('microhub/newprofile', 'MicroHubNewProfileController@getIndex');
        ###new training###
        Route::get('microhub/newtraining', 'MicroHubNewProfileController@training');

        ###new training###
        Route::get('microhub/newdocument/{category}', 'MicroHubNewProfileController@document');
        ###new cookies###
        Route::get('microhub/cookies', 'MicroHubCookiesController@getIndex');

        ###profile update###
        Route::post('microhub/profile/update', 'MicroHubProfileController@profileUpdate');


        Route::post('microhub/newprofile/update', 'MicroHubNewProfileController@profileUpdate');



        ###Document index route###
        Route::get('microhub/document', 'MicroHubDocumentController@document');
        ###training index route###
        Route::get('microhub/training', 'MicroHubTrainingController@training');
        ###Save Document###
        Route::post('microhub/document/save', 'MicroHubDocumentController@addDocument');
        ###Getting Video Category###
        Route::get('microhub/training/{category}', 'MicroHubTrainingController@getTrainingVideos');
        ###Getting Document Category###/
        Route::get('microhub/training-documents/{category}', 'MicroHubTrainingController@getTrainingDocuments');
        ###Getting Quiz Category###
        Route::get('microhub/training-quiz/{category}', 'MicroHubTrainingController@getTrainingQuiz');

        Route::post('microhub/training-quiz-submit/{category}', 'MicroHubTrainingController@getTrainingQuizSubmit');

        Route::get('microhub/training-quiz-result/{category}', 'MicroHubTrainingController@getTrainingQuizResult');

        Route::get('microhub/training-quiz-result-failed/{category}', 'MicroHubTrainingController@getTrainingQuizResultFailed');

        ###New document###
        Route::get('microhub/training-newdocuments/', 'MicroHubNewDocumentController@document');


        Route::get('/microhub/training-view/{id}', 'MicroHubTrainingController@trainingView');

        ###Financial Summary route###
        Route::get('microhub/financial-summary', 'MicroHubFinancialController@getSummary');
        ###Financial Information route###
        Route::get('microhub/financial-information', 'MicroHubFinancialController@getInformation');


        ###All Defined Permissions ###
        Route::get('getAllDefinedPermissions', 'MicroHubSettingController@getAllDefinedPermissions');


            ###All Requested Permission ###
        Route::get('getRequestedPermissions', 'MicroHubSettingController@getRequestedPermissions');


        ###New Permission Request Create ###
        Route::post('microhub/RequestNewPermission', 'MicroHubSettingController@RequestNewPermission');

        ###Posting Data to MicroHubPermissons###
        Route::post('microhub/PostToMicroHubPermissons', 'MicroHubSettingController@PostToMicroHubPermissons');


        Route::get('microhub/service/payment/hub_id/{hub_id}/process/{process_id}', 'MicroHubSettingController@servicePaymentView');
        Route::post('/dopay', 'MicroHubSettingController@handleonlinepay')->name('dopay');
        ###Posting Data to Agreement###
        Route::post('microhub/PostToAgreement', 'MicroHubCookiesController@PostToAgreement');

        ###Save Document###
        Route::post('microhub/newdocument/save', 'MicroHubNewDocumentController@addDocument');

        ### Statistics ###
        Route::get('statistics', 'StatisticsController@getStatistics')->name('statistics.index');
        Route::get('statistics/flag-order-list-pie-chart-data', 'StatisticsController@statisticsFlagOrderListPieChartData')->name('statistics-flag-order-list-pie-chart-data');
        Route::get('statistics/day/otd', 'StatisticsController@getDayOtd')->name('statistics-day-otd.index');
        Route::get('statistics/week/otd', 'StatisticsController@getWeekOtd')->name('statistics-week-otd.index');
        Route::get('statistics/month/otd', 'StatisticsController@getMonthOtd')->name('statistics-month-otd.index');
        Route::get('statistics/year/otd', 'StatisticsController@getYearOtd')->name('statistics-year-otd.index');
        Route::get('statistics/all/counts', 'StatisticsController@getAllCounts')->name('statistics-all-counts.index');
        Route::get('statistics/inprogress', 'StatisticsController@getInprogress')->name('statistics-inprogress.index');
        Route::get('statistics/failed/counts', 'StatisticsController@getFailedCounts')->name('statistics-failed-counts.index');
        Route::get('statistics/custom/counts', 'StatisticsController@getCustomCounts')->name('statistics-custom-counts.index');
        Route::get('statistics/manual/counts', 'StatisticsController@getManualCounts')->name('statistics-manual-counts.index');
        Route::get('statistics/route/counts', 'StatisticsController@getRouteDataCounts')->name('statistics-route-counts.index');
        Route::get('statistics/on-time/counts', 'StatisticsController@getOnTimeCounts')->name('statistics-on-time-counts.index');
        Route::get('statistics/top-ten/joeys', 'StatisticsController@getTopTenJoeys')->name('statistics-top-ten-joeys.index');
        Route::get('statistics/least-ten/joeys', 'StatisticsController@getLeastTenJoeys')->name('statistics-least-ten-joeys.index');
        Route::get('statistics/graph', 'StatisticsController@getGraph')->name('statistics-graph.index');
        Route::get('statistics/brooker', 'StatisticsController@getBroker')->name('statistics-brooker.index');

        Route::get('statistics/orders', 'StatisticsController@getOrders')->name('statistics-order.index');
        Route::get('statistics/failed/orders', 'StatisticsController@getFailedOrders')->name('statistics-failed-order.index');
        Route::get('statistics/route/detail', 'StatisticsController@getRouteDataDetail')->name('statistics-route-detail.index');


        ### Incharge Statistics ###
        Route::get('incharge/statistics', 'StatisticsController@getStatistics');
        Route::get('incharge/statistics/flag-order-list-pie-chart-data', 'InchargeDashboardController@statisticsFlagOrderListPieChartData');
        Route::get('incharge/statistics/day/otd', 'InchargeDashboardController@getInchargeDayOtd');
        Route::get('incharge/statistics/week/otd', 'InchargeDashboardController@getInchargeWeekOtd');
        Route::get('incharge/statistics/month/otd', 'InchargeDashboardController@getInchargeMonthOtd');
        Route::get('incharge/statistics/sixmonth/otd', 'InchargeDashboardController@getInchargesixMonthOtd');
        Route::get('incharge/statistics/all/counts', 'InchargeDashboardController@getAllCounts');
        Route::get('incharge/statistics/inprogress', 'InchargeDashboardController@getInprogress');
        Route::get('incharge/statistics/failed/counts', 'InchargeDashboardController@getFailedCounts');
        Route::get('incharge/statistics/custom/counts', 'InchargeDashboardController@getCustomCounts')->name('statistics-custom-counts.index');
        Route::get('incharge/statistics/manual/counts', 'InchargeDashboardController@getManualCounts')->name('statistics-manual-counts.index');
        Route::get('incharge/statistics/route/counts', 'InchargeDashboardController@getRouteDataCounts')->name('statistics-route-counts.index');
        Route::get('incharge/statistics/on-time/counts', 'InchargeDashboardController@getOnTimeCounts')->name('statistics-on-time-counts.index');
        Route::get('incharge/statistics/top-ten/joeys', 'InchargeDashboardController@getTopTenJoeys')->name('statistics-top-ten-joeys.index');
        Route::get('incharge/statistics/least-ten/joeys', 'InchargeDashboardController@getLeastTenJoeys')->name('statistics-least-ten-joeys.index');
        Route::get('incharge/statistics/graph', 'InchargeDashboardController@getGraph')->name('statistics-graph.index');
        Route::get('incharge/statistics/brooker', 'InchargeDashboardController@getBroker')->name('statistics-brooker.index');

        Route::get('incharge/statistics/orders', 'StatisticsController@getOrders')->name('statistics-order.index');
        Route::get('incharge/statistics/failed/orders', 'StatisticsController@getFailedOrders')->name('statistics-failed-order.index');
        Route::get('incharge/statistics/route/detail', 'StatisticsController@getRouteDataDetail')->name('statistics-route-detail.index');

        Route::get('incharge/statistics/joey-detail/', 'JoeyController@getStatistics')->name('statistics-joey-detail.index');

        ### Joey Management ###
        Route::get('joey-management', 'JoeyController@getJoeyManagement')->name('joey-management.index');
        Route::get('joey-management/joey-count', 'JoeyController@getAllJoeyCounts')->name('joey-management-joey-count.index');
        Route::post('joey-management/joey-count-onduty', 'JoeyController@getOnDutyJoeyCounts')->name('joey-management-joey-count.onduty');
        Route::get('joey-management/orders-count', 'JoeyController@getAllOrderCounts')->name('joey-management-orders-count.index');
        Route::get('joey-management/otd-day', 'JoeyController@getJoeyManagementOtdDay')->name('joey-management-otd-day.index');
        Route::get('joey-management/otd-week', 'JoeyController@getJoeyManagementOtdWeek')->name('joey-management-otd-week.index');
        Route::get('joey-management/otd-month', 'JoeyController@getJoeyManagementOtdMonth')->name('joey-management-otd-month.index');
        Route::get('joey-management/list', 'JoeyController@getAllJoeysList')->name('joey-management-list.index');
        Route::get('joey-management/order-list', 'JoeyController@getAllOrderList')->name('joey-management-order-list.index');
        Route::get('joey-management/all-joeys-otd', 'JoeyController@getAllJoeysOTD')->name('joey-management-all-joeys-otd.index');

        ### Brooker Management ###
        Route::get('brooker-management', 'BrookerController@getBrookerManagement')->name('brooker-management.index');
        Route::get('brooker-management/brooker-count', 'BrookerController@getAllBrookerCounts')->name('brooker-management-brooker-count.index');
        Route::get('brooker-management/joey-count', 'BrookerController@getAllJoeyCounts')->name('brooker-management-joey-count.index');
        Route::post('brooker-management/joey-count-onduty', 'BrookerController@getOnDutyJoeyCounts')->name('brooker-management-joey-count.onduty');
        Route::get('brooker-management/orders-count', 'BrookerController@getAllOrderCounts')->name('brooker-management-orders-count.index');
        Route::get('brooker-management/otd-day', 'BrookerController@getBrookerManagementOtdDay')->name('brooker-management-otd-day.index');
        Route::get('brooker-management/otd-week', 'BrookerController@getBrookerManagementOtdWeek')->name('brooker-management-otd-week.index');
        Route::get('brooker-management/otd-month', 'BrookerController@getBrookerManagementOtdMonth')->name('brooker-management-otd-month.index');
        Route::get('brooker-management/list', 'BrookerController@getAllJoeysList')->name('brooker-management-list.index');
        Route::get('brooker-management/brooker-list', 'BrookerController@getAllBrookerList')->name('brooker-management-brooker-list.index');
        Route::get('brooker-management/all-brooker-otd', 'BrookerController@getAllBrookerOTD')->name('brooker-management-all-brooker-otd.index');

        ### Brooker Statistics ###
        // Route::get('statistics/brooker-detail', 'BrookerController@getStatistics')->name('statistics-brooker-detail.index');
        Route::get('statistics/brooker-detail/day/otd', 'BrookerController@getDayOtd')->name('statistics-brooker-detail-day-otd.index');
        Route::get('statistics/brooker-detail/week/otd', 'BrookerController@getWeekOtd')->name('statistics-brooker-detail-week-otd.index');
        Route::get('statistics/brooker-detail/month/otd', 'BrookerController@getMonthOtd')->name('statistics-brooker-detail-month-otd.index');
        Route::get('statistics/brooker-detail/year/otd', 'BrookerController@getYearOtd')->name('statistics-brooker-detail-year-otd.index');
        Route::get('statistics/brooker-detail/all/counts', 'BrookerController@getAllCounts')->name('statistics-brooker-detail-all-counts.index');
        Route::get('statistics/brooker-detail/failed/counts', 'BrookerController@getFailedCounts')->name('statistics-brooker-detail-failed-counts.index');
        Route::get('statistics/brooker-detail/custom/counts', 'BrookerController@getCustomCounts')->name('statistics-brooker-detail-custom-counts.index');
        Route::get('statistics/brooker-detail/manual/counts', 'BrookerController@getManualCounts')->name('statistics-brooker-detail-manual-counts.index');
        Route::get('statistics/brooker-detail/route/counts', 'BrookerController@getRouteDataCounts')->name('statistics-brooker-detail-route-counts.index');
        Route::get('statistics/brooker-detail/on-time/counts', 'BrookerController@getOnTimeCounts')->name('statistics-brooker-detail-on-time-counts.index');
        Route::get('statistics/brooker-detail/top-ten/joeys', 'BrookerController@getTopTenJoeys')->name('statistics-brooker-detail-top-ten-joeys.index');
        Route::get('statistics/brooker-detail/least-ten/joeys', 'BrookerController@getLeastTenJoeys')->name('statistics-brooker-detail-least-ten-joeys.index');
        Route::get('statistics/brooker-detail/graph', 'BrookerController@getGraph')->name('statistics-brooker-detail-graph.index');
        Route::get('statistics/brooker-detail/brooker', 'BrookerController@getBroker')->name('statistics-brooker-detail-brooker.index');
        Route::get('statistics/brooker-detail/orders', 'BrookerController@getOrders')->name('statistics-brooker-detail-order.index');
        Route::get('statistics/brooker-detail/failed/orders', 'BrookerController@getFailedOrders')->name('statistics-brooker-detail-failed-order.index');
        Route::get('statistics/brooker-detail/all-joeys-otd', 'BrookerController@getAllJoeysOTD')->name('statistics-brooker-detail-all-joeys-otd.index');

        ### Joey Statistics ###
        Route::get('statistics/joey-detail/', 'JoeyController@getStatistics')->name('statistics-joey-detail.index');
        Route::get('statistics/joey-detail/day/otd', 'JoeyController@getDayOtd')->name('statistics-joey-detail-day-otd.index');
        Route::get('statistics/joey-detail/week/otd', 'JoeyController@getWeekOtd')->name('statistics-joey-detail-week-otd.index');
        Route::get('statistics/joey-detail/month/otd', 'JoeyController@getMonthOtd')->name('statistics-joey-detail-month-otd.index');
        Route::get('statistics/joey-detail/year/otd', 'JoeyController@getYearOtd')->name('statistics-joey-detail-year-otd.index');
        Route::get('statistics/joey-detail/all/counts', 'JoeyController@getAllCounts')->name('statistics-joey-detail-all-counts.index');
        Route::get('statistics/joey-detail/manual/counts', 'JoeyController@getManualCounts')->name('statistics-joey-detail-manual-counts.index');
        Route::get('statistics/joey-detail/joey/time', 'JoeyController@getJoeysTime')->name('statistics-joey-detail-joey-time.index');
        Route::get('statistics/joey-detail/graph', 'JoeyController@getGraph')->name('statistics-joey-detail-graph.index');
        Route::get('statistics/joey-detail/orders', 'JoeyController@getOrders')->name('statistics-joey-detail-order.index');
        Route::get('statistics/joey-detail/failed/orders', 'JoeyController@getFailedOrders')->name('statistics-joey-detail-failed-order.index');

        Route::get('inbound/', 'InboundController@getInbound')->name('statistics-inbound.index');
        Route::get('inbound/data', 'InboundController@getInboundData')->name('statistics-inbound-data.index');
        Route::get('inbound/setup-time', 'InboundController@inboundSetupTime')->name('statistics-setup-time.index');
        Route::get('inbound/sorting-time', 'InboundController@inboundSortingTime')->name('statistics-sorting-time.index');
        Route::post('inbound/warehousesorter/update', 'InboundController@wareHouseSorterUpdate')->name('statistics-inbound.wareHouseSorterUpdate');

        Route::get('outbound/', 'OutboundController@getOutbound')->name('statistics-outbound.index');
        Route::get('outbound/data', 'OutboundController@getOutboundData')->name('statistics-outbound-data.index');
        Route::get('outbound/dispensing-time', 'OutboundController@outboundDispensingTime')->name('statistics-dispensing-time.index');
        Route::post('outbound/warehousesorter/update', 'OutboundController@wareHouseSorterUpdate')->name('statistics-outbound.wareHouseSorterUpdate');

        Route::get('microhub/summary', 'SummaryController@getSummary')->name('warehouse-summary.index');
        Route::get('warehouse/summary/data', 'SummaryController@getSummaryData')->name('warehouse-summary-data.index');


        Route::get('newmontreal/totalcards/{date?}/{type?}', 'NewMontrealController@montrealTotalCards')->name('newmontreal.totalcards');
        Route::get('newmontreal/inprogress/{date?}/{type?}', 'NewMontrealController@montrealInProgressOrders')->name('newmontreal.inprogress');
        Route::get('newmontreal/mainfestcards/{date?}', 'NewMontrealController@getMainfestOrderData')->name('newmontreal.mainfestcards');
        Route::get('newmontreal/failedcards/{date?}', 'NewMontrealController@getFailedOrderData')->name('newmontreal.failedcards');
        Route::get('newmontreal/customroutecards/{date?}', 'NewMontrealController@getCustomRouteData')->name('newmontreal.customroutecards');
        Route::get('newmontreal/yesterdaycards/{date?}', 'NewMontrealController@getYesterdayOrderData')->name('newmontreal.yesterdaycards');

        Route::get('newottawa/totalcards/{date?}/{type?}', 'NewOttawaController@ottawaTotalCards')->name('newottawa.totalcards');
        Route::get('newottawa/inprogress/{date?}/{type?}', 'NewOttawaController@ottawaInProgressOrders')->name('newottawa.inprogress');
        Route::get('newottawa/mainfestcards/{date?}', 'NewOttawaController@getMainfestOrderData')->name('newottawa.mainfestcards');
        Route::get('newottawa/failedcards/{date?}', 'NewOttawaController@getFailedOrderData')->name('newottawa.failedcards');
        Route::get('newottawa/customroutecards/{date?}', 'NewOttawaController@getCustomRouteData')->name('newottawa.customroutecards');
        Route::get('newottawa/yesterdaycards/{date?}', 'NewOttawaController@getYesterdayOrderData')->name('newottawa.yesterdaycards');

        Route::get('new/last-mile/totalcards/{date?}/{type?}', 'CtcEntriesController@ctcTotalCards')->name('new-ctc.totalcards');
        Route::get('new/last-mile/inprogress/{date?}/{type?}', 'CtcEntriesController@ctcInProgressOrders')->name('new-ctc.inprogress');
        Route::get('new/last-mile/customroutecards/{date?}', 'CtcEntriesController@getCtcCustomRouteData')->name('new-ctc.customroutecards');
        Route::get('new/last-mile/yesterdaycards/{date?}', 'CtcEntriesController@getCtcYesterdayOrderData')->name('new-ctc.yesterdaycards');

        Route::get('borderless/totalcards/{date?}/{type?}', 'BorderlessController@boradlessTotalCards')->name('borderless.totalcards');
        Route::get('borderless/inprogress/{date?}/{type?}', 'BorderlessController@boradlessInProgressOrders')->name('borderless.inprogress');
        Route::get('borderless/customroutecards/{date?}', 'BorderlessController@getBoradlessCustomRouteData')->name('borderless.customroutecards');
        Route::get('borderless/yesterdaycards/{date?}', 'BorderlessController@getBoradlessYesterdayOrderData')->name('borderless.yesterdaycards');


        ### MICRO HUB STATISTICS DATA ROUTES ###
        Route::get('statistics', 'StatisticsController@getStatistics')->name('statistics.index');
        Route::get('statistics/flag-order-list-pie-chart-data', 'StatisticsController@statisticsFlagOrderListPieChartData')->name('statistics-flag-order-list-pie-chart-data');
        Route::get('statistics/day/otd', 'StatisticsController@getDayOtd')->name('statistics-day-otd.index');
        Route::get('statistics/week/otd', 'StatisticsController@getWeekOtd')->name('statistics-week-otd.index');
        Route::get('statistics/month/otd', 'StatisticsController@getMonthOtd')->name('statistics-month-otd.index');
        Route::get('statistics/year/otd', 'StatisticsController@getYearOtd')->name('statistics-year-otd.index');
        Route::get('all/counts', 'DashboardController@getAllCounts')->name('statistics-all-counts.index');
        Route::get('statistics/inprogress', 'StatisticsController@getInprogress')->name('statistics-inprogress.index');
        Route::get('statistics/failed/counts', 'StatisticsController@getFailedCounts')->name('statistics-failed-counts.index');
        Route::get('statistics/custom/counts', 'StatisticsController@getCustomCounts')->name('statistics-custom-counts.index');
        Route::get('statistics/manual/counts', 'StatisticsController@getManualCounts')->name('statistics-manual-counts.index');
        Route::get('statistics/route/counts', 'StatisticsController@getRouteDataCounts')->name('statistics-route-counts.index');
        Route::get('statistics/on-time/counts', 'StatisticsController@getOnTimeCounts')->name('statistics-on-time-counts.index');
        Route::get('statistics/top-ten/joeys', 'StatisticsController@getTopTenJoeys')->name('statistics-top-ten-joeys.index');
        Route::get('statistics/least-ten/joeys', 'StatisticsController@getLeastTenJoeys')->name('statistics-least-ten-joeys.index');
        Route::get('statistics/graph', 'StatisticsController@getGraph')->name('statistics-graph.index');
        Route::get('statistics/brooker', 'StatisticsController@getBroker')->name('statistics-brooker.index');

        Route::get('statistics/orders', 'StatisticsController@getOrders')->name('statistics-order.index');
        Route::get('statistics/failed/orders', 'StatisticsController@getFailedOrders')->name('statistics-failed-order.index');
        Route::get('statistics/route/detail', 'StatisticsController@getRouteDataDetail')->name('statistics-route-detail.index');


        ###ShipHero Routes###
        Route::get('ship-hero/totalcards/{date?}/{type?}', 'ShipHeroController@shipHeroTotalCards')->name('shipHero.totalcards');
        Route::get('ship-hero/inprogress/{date?}/{type?}', 'ShipHeroController@shipHeroInProgressOrders')->name('shipHero.inprogress');
        Route::get('ship-hero/customroutecards/{date?}', 'ShipHeroController@shipHeroCustomRouteData')->name('shipHero.customroutecards');
        Route::get('ship-hero/yesterdaycards/{date?}', 'ShipHeroController@getShipHeroYesterdayOrderData')->name('shipHero.yesterdaycards');

        /*Route::group(['middleware' => ['PermissionHandler']], function () {*/


        Route::get('montreal-dashboard', 'MontrealController@getNewMontreal')->name('montreal-dashboard.index');
        Route::get('montreal-dashboard/data', 'MontrealController@montrealNewData')->name('montreal-dashboard.data');
        Route::get('montreal/new/detail/{id}', 'MontrealController@montrealNewProfile')->name('montreal-new.profile');

        Route::get('route-list/{date?}/{type?}', 'NewMontrealController@getRoutes')->name('newmontreal.route-list');
        Route::get('joey-list/{date?}/{type?}', 'NewMontrealController@getJoeys')->name('newmontreal.joey-list');

        ### Montreal Cards ###
        Route::get('newmontreal-dashboard', 'NewMontrealController@getMontrealCards')->name('newmontreal-dashboard.index');
        ### New Montreal Dashboard ###
        Route::get('newmontreal/data', 'NewMontrealController@montrealData')->name('newmontreal.data');
        Route::get('reporting/dashboard', 'NewMontrealController@getMontreal')->name('newmontreal.index');
        Route::get('newmontreal/list/{date?}', 'NewMontrealController@montrealExcel')->name('newexport_Montreal.excel');
        ### Montreal Sorted ###
        Route::get('reporting/sorted', 'NewMontrealController@getSorter')->name('newmontreal-sort.index');
        Route::get('newmontreal/sorted/data', 'NewMontrealController@montrealSortedData')->name('newmontrealSorted.data');
        Route::get('newmontreal/sorted/list/{date?}', 'NewMontrealController@montrealSortedExcel')->name('newexport_MontrealSorted.excel');
        ### Montreal Hub ###
        Route::get('reporting/picked/up', 'NewMontrealController@getMontrealhub')->name('newmontreal-pickup.index');
        Route::get('newmontreal/picked/up/data', 'NewMontrealController@montrealPickedUpData')->name('newmontrealPickedUp.data');
        Route::get('newmontreal/picked/up/list/{date?}', 'NewMontrealController@montrealPickedupExcel')->name('newexport_MontrealPickedUp.excel');
        ### Montreal Not Scan ###
        Route::get('reporting/not/scan', 'NewMontrealController@getMontnotscan')->name('newmontreal-not-scan.index');
        Route::get('newmontreal/not/scan/data', 'NewMontrealController@montrealNotScanData')->name('newmontrealNotScan.data');
        Route::get('newmontreal/not/scan/list/{date?}', 'NewMontrealController@montrealNotscanExcel')->name('newexport_MontrealNotScan.excel');
        ### Montreal Delivered ###
        Route::get('reporting/delivered', 'NewMontrealController@getMontdelivered')->name('newmontreal-delivered.index');
        Route::get('newmontreal/delivered/data', 'NewMontrealController@montrealDeliveredData')->name('newmontrealDelivered.data');
        Route::get('newmontreal/delivered/list/{date?}', 'NewMontrealController@montrealDeliveredExcel')->name('newexport_MontrealDelivered.excel');
        ### Montreal Returned ###
        Route::get('reporting/returned', 'NewMontrealController@getMontreturned')->name('newmontreal-returned.index');
        Route::get('newmontreal/returned/data', 'NewMontrealController@montrealReturnedData')->name('newmontrealReturned.data');
        Route::get('newmontreal/returned/list/{date?}', 'NewMontrealController@montrealReturnedExcel')->name('newexport_MontrealReturned.excel');


        Route::get('reporting/returned-not-hub', 'NewMontrealController@getMontNotreturned')->name('newmontreal-notreturned.index');
        Route::get('reporting/returned-not-hub/data', 'NewMontrealController@montrealNotReturnedData')->name('newmontrealNotReturned.data');
        Route::get('reporting/returned-not-hub/list/{date?}', 'NewMontrealController@montrealNotReturnedExcel')->name('newexport_MontrealNotReturned.excel');
        Route::get('reporting/returned-not-hub/tracking/list/{date?}', 'NewMontrealController@montrealNotReturnedExcelTrackingIds')->name('newexport_MontrealNotReturned_Tracking.excel');

        ### Montreal Custom Route ###
        Route::get('reporting/custom-route', 'NewMontrealController@getMontCustomRoute')->name('newmontreal-custom-route.index');
        Route::get('newmontreal/custom-route/data', 'NewMontrealController@montrealCustomRouteData')->name('newmontrealCustomRoute.data');
        Route::get('newmontreal/custom-route/list/{date?}', 'NewMontrealController@montrealCustomRouteExcel')->name('newexport_MontrealCustomRoute.excel');

        ### Montreal Route Information ###
        Route::get('reporting/route-info', 'NewMontrealController@getRouteinfo')->name('newmontreal-route-info.index');
        Route::get('newmontreal/route-info/list/{date?}', 'NewMontrealController@montrealRouteinfoExcel')->name('newexport_MontrealRouteInfo.excel');
        Route::get('newmontreal/route/{di}/edit/hub/{id}', 'NewMontrealController@montrealHubRouteEdit')->name('newmontreal_route.detail');
        Route::post('newmontreal/route-details/flag-history-model-html-render', 'NewMontrealController@flagHistoryModelHtmlRender')->name('newmontreal_route.route-details.flag-history-model-html-render');
        Route::get('newmontreal/route/orders/trackingid/{id}/details', 'NewMontrealController@getMontrealtrackingorderdetails')->name('newmontrealinfo_route.detail');
        Route::post('newmontreal/route-info/add-note', 'NewMontrealController@addNote')->name('newmontreal-route-info.addNote');
        Route::get('newmontreal/route-info/get-notes', 'NewMontrealController@getNotes')->name('newmontreal-route-info.getNotes');

        ### Montreal Profile ###
        Route::get('newmontreal/detail/{id}', 'NewMontrealController@montrealProfile')->name('newmontreal.profile');
        Route::get('newmontreal/sorted/detail/{id}', 'NewMontrealController@montrealsortedDetail')->name('newmontreal_sorted.profile');
        Route::get('newmontreal/pickup/detail/{id}', 'NewMontrealController@montrealpickupDetail')->name('newmontreal_pickup.profile');
        Route::get('newmontreal/notscan/detail/{id}', 'NewMontrealController@montrealnotscanDetail')->name('newmontreal_notscan.profile');
        Route::get('newmontreal/delivered/detail/{id}', 'NewMontrealController@montrealdeliveredDetail')->name('newmontreal_delivered.profile');
        Route::get('newmontreal/returned/detail/{id}', 'NewMontrealController@montrealreturnedDetail')->name('newmontreal_returned.profile');
        Route::get('newmontreal/returned-not-hub/detail/{id}', 'NewMontrealController@montrealNotReturnedDetail')->name('newmontreal_notreturned.profile');
        Route::get('newmontreal/custom-route/detail/{id}', 'NewMontrealController@montrealCustomRouteDetail')->name('newmontreal_customroute.profile');

        ### Montreal Dashboard ###
        Route::get('montreal/data', 'MontrealController@montrealData')->name('montreal.data');
        Route::get('montreal', 'MontrealController@getMontreal')->name('montreal.index');
        Route::get('montreal/list/{date?}', 'MontrealController@montrealExcel')->name('export_Montreal.excel');
        ### Montreal Sorted ###
        Route::get('montreal/sorted', 'MontrealController@getSorter')->name('montreal-sort.index');
        Route::get('montreal/sorted/data', 'MontrealController@montrealSortedData')->name('montrealSorted.data');
        Route::get('montreal/sorted/list/{date?}', 'MontrealController@montrealSortedExcel')->name('export_MontrealSorted.excel');
        ### Montreal Hub ###
        Route::get('montreal/picked/up', 'MontrealController@getMontrealhub')->name('montreal-pickup.index');
        Route::get('montreal/picked/up/data', 'MontrealController@montrealPickedUpData')->name('montrealPickedUp.data');
        Route::get('montreal/picked/up/list/{date?}', 'MontrealController@montrealPickedupExcel')->name('export_MontrealPickedUp.excel');
        ### Montreal Not Scan ###
        Route::get('montreal/not/scan', 'MontrealController@getMontnotscan')->name('montreal-not-scan.index');
        Route::get('montreal/not/scan/data', 'MontrealController@montrealNotScanData')->name('montrealNotScan.data');
        Route::get('montreal/not/scan/list/{date?}', 'MontrealController@montrealNotscanExcel')->name('export_MontrealNotScan.excel');
        ### Montreal Delivered ###
        Route::get('montreal/delivered', 'MontrealController@getMontdelivered')->name('montreal-delivered.index');
        Route::get('montreal/delivered/data', 'MontrealController@montrealDeliveredData')->name('montrealDelivered.data');
        Route::get('montreal/delivered/list/{date?}', 'MontrealController@montrealDeliveredExcel')->name('export_MontrealDelivered.excel');
        ### Montreal Profile ###
        Route::get('montreal/detail/{id}', 'MontrealController@montrealProfile')->name('montreal.profile');
        Route::get('montreal/sorted/detail/{id}', 'MontrealController@montrealsortedDetail')->name('montreal_sorted.profile');
        Route::get('montreal/pickup/detail/{id}', 'MontrealController@montrealpickupDetail')->name('montreal_pickup.profile');
        Route::get('montreal/notscan/detail/{id}', 'MontrealController@montrealnotscanDetail')->name('montreal_notscan.profile');
        Route::get('montreal/delivered/detail/{id}', 'MontrealController@montrealdeliveredDetail')->name('montreal_delivered.profile');
        ### Montreal Route Information ###
        Route::get('montreal/route-info', 'MontrealController@getRouteinfo')->name('montreal-route-info.index');
        Route::get('montreal/route-info/list/{date?}', 'MontrealController@montrealRouteinfoExcel')->name('export_MontrealRouteInfo.excel');

        Route::get('montreal/route/{di}/edit/hub/{id}', 'MontrealController@montrealHubRouteEdit')->name('montreal_route.detail');
        //Route::get('route/{id}/delete/hub','MontrealController@montrealDeleteRoute');
        Route::get('montreal/route/orders/trackingid/{id}/details', 'MontrealController@getMontrealtrackingorderdetails')->name('montrealinfo_route.detail');
        Route::get('testmontreal/route/orders/trackingid/{id}/details', 'MontrealController@getTestMontrealtrackingorderdetails')->name('testmontrealinfo_route.detail');


        ### Montreal Returned ###
        Route::get('montreal/returned', 'MontrealController@getMontreturned')->name('montreal-returned.index');
        Route::get('montreal/returned/data', 'MontrealController@montrealReturnedData')->name('montrealReturned.data');
        Route::get('montreal/returned/list/{date?}', 'MontrealController@montrealReturnedExcel')->name('export_MontrealReturned.excel');
        Route::get('montreal/returned/detail/{id}', 'MontrealController@montrealreturnedDetail')->name('montreal_returned.profile');


        Route::get('direct/montreal/data', 'MontrealController@directMontrealData')->name('directmontreal.data');
        Route::get('direct/montreal', 'MontrealController@getDirectMontreal')->name('directmontreal.index');


        Route::get('ottawa-route-list/{date?}/{type?}', 'NewOttawaController@getRoutes')->name('newottawa.ottawa-route-list');
        Route::get('ottawa-joey-list/{date?}/{type?}', 'NewOttawaController@getJoeys')->name('newottawa.ottawa-joey-list');

        ### Ottawa Cards ###
        Route::get('newottawa-dashboard', 'NewOttawaController@getOttawaCards')->name('newottawa-dashboard.index');
        ### New Ottawa Dashboard ###
        Route::get('newottawa', 'NewOttawaController@getOttawa')->name('newottawa.index');
        Route::get('newottawa/data', 'NewOttawaController@ottawaData')->name('newottawa.data');
        Route::get('newottawa/list/{date?}', 'NewOttawaController@ottawaExcel')->name('newexport_Ottawa.excel');
        ### Ottawa Sorted ###
        Route::get('newottawa/sorted', 'NewOttawaController@getOttawatsort')->name('newottawa-sort.index');
        Route::get('newottawa/sorted/data', 'NewOttawaController@ottawaSortedData')->name('newottawaSorted.data');
        Route::get('newottawa/sorted/list/{date?}', 'NewOttawaController@ottawaSortedExcel')->name('newexport_OttawaSorted.excel');
        ### Ottawa Picked Up ###
        Route::get('newottawa/picked/up', 'NewOttawaController@getOttawathub')->name('newottawa-pickup.index');
        Route::get('newottawa/picked/up/data', 'NewOttawaController@ottawaPickedUpData')->name('newottawaPickedUp.data');
        Route::get('newottawa/picked/up/list/{date?}', 'NewOttawaController@ottawaPickedUpExcel')->name('newexport_OttawaPickedUp.excel');
        ### Ottawa Not Scan ###
        Route::get('newottawa/not/scan', 'NewOttawaController@getOttawatnotscan')->name('newottawa-not-scan.index');
        Route::get('newottawa/not/scan/data', 'NewOttawaController@ottawaNotScanData')->name('newottawaNotScan.data');
        Route::get('newottawa/not/scan/list/{date?}', 'NewOttawaController@ottawaNotscanExcel')->name('newexport_OttawaNotScan.excel');
        ### Ottawa Delivered ###
        Route::get('newottawa/delivered', 'NewOttawaController@getOttawadelivered')->name('newottawa-delivered.index');
        Route::get('newottawa/delivered/data', 'NewOttawaController@ottawaDeliveredData')->name('newottawaDelivered.data');
        Route::get('newottawa/delivered/list/{date?}', 'NewOttawaController@ottawaDeliveredExcel')->name('newexport_OttawaDelivered.excel');
        ### Ottawa Returned ###
        Route::get('newottawa/returned', 'NewOttawaController@getOttawareturned')->name('newottawa-returned.index');
        Route::get('newottawa/returned/data', 'NewOttawaController@ottawaReturnedData')->name('newottawaReturned.data');
        Route::get('newottawa/returned/list/{date?}', 'NewOttawaController@ottawaReturnedExcel')->name('newexport_OttawaReturned.excel');
        ### Ottawa Not Returned At Hub ###
        Route::get('newottawa/returned-not-hub', 'NewOttawaController@getOttawaNotReturned')->name('newottawa-notreturned.index');
        Route::get('newottawa/returned-not-hub/data', 'NewOttawaController@ottawaNotReturnedData')->name('newottawaNotReturned.data');
        Route::get('newottawa/returned-not-hub/list/{date?}', 'NewOttawaController@ottawaNotReturnedExcel')->name('newexport_OttawaNotReturned.excel');
        Route::get('newottawa/returned-not-hub/tracking/list/{date?}', 'NewOttawaController@ottawaNotReturnedExcelTrackingIds')->name('newexport_OttawaNotReturned_tracking.excel');
        ### Ottawa Custom Route ###
        Route::get('newottawa/custom-route', 'NewOttawaController@getOttawaCustomRoute')->name('newottawa-custom-route.index');
        Route::get('newottawa/custom-route/data', 'NewOttawaController@ottawaCustomRouteData')->name('newottawaCustomRoute.data');
        Route::get('newottawa/custom-route/list/{date?}', 'NewOttawaController@ottawaCustomRouteExcel')->name('newexport_OttawaCustomRoute.excel');

        ### Ottawa Route Information ###
        Route::get('newottawa/route-info', 'NewOttawaController@getRouteinfo')->name('newottawa-route-info.index');
        Route::get('newottawa/route-info/list/{date?}', 'NewOttawaController@ottawaRouteinfoExcel')->name('newexport_OttawaRouteInfo.excel');
        Route::get('newottawa/route/{di}/edit/hub/{id}', 'NewOttawaController@ottawaHubRouteEdit')->name('newottawa_route.detail');
        Route::post('newottawa/route-details/flag-history-model-html-render', 'NewOttawaController@flagHistoryModelHtmlRender')->name('newottawainfo_route.route-details.flag-history-model-html-render');
        Route::get('newottawa/route/orders/trackingid/{id}/details', 'NewOttawaController@getOttawatrackingorderdetails')->name('newottawainfo_route.detail');
        Route::post('newottawa/route-info/add-note', 'NewOttawaController@addNote')->name('newottawa-route-info.addNote');
        Route::get('newottawa/route-info/get-notes', 'NewOttawaController@getNotes')->name('newottawa-route-info.getNotes');

        ### Ottawa Profile ###
        Route::get('newottawa/detail/{id}', 'NewOttawaController@ottawaProfile')->name('newottawa.profile');
        Route::get('newottawa/sorted/detail/{id}', 'NewOttawaController@ottawasortedDetail')->name('newottawa_sorted.profile');
        Route::get('newottawa/pickup/detail/{id}', 'NewOttawaController@ottawapickupDetail')->name('newottawa_pickup.profile');
        Route::get('newottawa/notscan/detail/{id}', 'NewOttawaController@ottawanotscanDetail')->name('newottawa_notscan.profile');
        Route::get('newottawa/delivered/detail/{id}', 'NewOttawaController@ottawadeliveredDetail')->name('newottawa_delivered.profile');
        Route::get('newottawa/returned/detail/{id}', 'NewOttawaController@ottawareturnedDetail')->name('newottawa_returned.profile');
        Route::get('newottawa/returned-not-hub/detail/{id}', 'NewOttawaController@ottawaNotReturnedDetail')->name('newottawa_notreturned.profile');
        Route::get('newottawa/custom-route/detail/{id}', 'NewOttawaController@ottawaCustomRouteDetail')->name('newottawa_CustomRoute.profile');

        /*rights module routes*/
        Route::resource('right', 'RightsController');
        Route::post('right/duplicate', 'RightsController@rightDuplicate')->name('right.duplicate');

        ### Ottawa Dashboard ###
        Route::get('ottawa', 'OttawaController@getOttawa')->name('ottawa.index');
        Route::get('ottawa/data', 'OttawaController@ottawaData')->name('ottawa.data');
        Route::get('ottawa/list/{date?}', 'OttawaController@ottawaExcel')->name('export_Ottawa.excel');
        ### Ottawa Sorted ###
        Route::get('ottawa/sorted', 'OttawaController@getOttawatsort')->name('ottawa-sort.index');
        Route::get('ottawa/sorted/data', 'OttawaController@ottawaSortedData')->name('ottawaSorted.data');
        Route::get('ottawa/sorted/list/{date?}', 'OttawaController@ottawaSortedExcel')->name('export_OttawaSorted.excel');
        ### Ottawa Picked Up ###
        Route::get('ottawa/picked/up', 'OttawaController@getOttawathub')->name('ottawa-pickup.index');
        Route::get('ottawa/picked/up/data', 'OttawaController@ottawaPickedUpData')->name('ottawaPickedUp.data');
        Route::get('ottawa/picked/up/list/{date?}', 'OttawaController@ottawaPickedUpExcel')->name('export_OttawaPickedUp.excel');
        ### Ottawa Not Scan ###
        Route::get('ottawa/not/scan', 'OttawaController@getOttawatnotscan')->name('ottawa-not-scan.index');
        Route::get('ottawa/not/scan/data', 'OttawaController@ottawaNotScanData')->name('ottawaNotScan.data');
        Route::get('ottawa/not/scan/list/{date?}', 'OttawaController@ottawaNotscanExcel')->name('export_OttawaNotScan.excel');
        ### Ottawa Delivered ###
        Route::get('ottawa/delivered', 'OttawaController@getOttawadelivered')->name('ottawa-delivered.index');
        Route::get('ottawa/delivered/data', 'OttawaController@ottawaDeliveredData')->name('ottawaDelivered.data');
        Route::get('ottawa/delivered/list/{date?}', 'OttawaController@ottawaDeliveredExcel')->name('export_OttawaDelivered.excel');
        ### Ottawa Profile ###
        Route::get('ottawa/detail/{id}', 'OttawaController@ottawaProfile')->name('ottawa.profile');
        Route::get('ottawa/sorted/detail/{id}', 'OttawaController@ottawasortedDetail')->name('ottawa_sorted.profile');
        Route::get('ottawa/pickup/detail/{id}', 'OttawaController@ottawapickupDetail')->name('ottawa_pickup.profile');
        Route::get('ottawa/notscan/detail/{id}', 'OttawaController@ottawanotscanDetail')->name('ottawa_notscan.profile');
        Route::get('ottawa/delivered/detail/{id}', 'OttawaController@ottawadeliveredDetail')->name('ottawa_delivered.profile');
        ### Ottawa Route Information ###
        Route::get('ottawa/route-info', 'OttawaController@getRouteinfo')->name('ottawa-route-info.index');
        Route::get('ottawa/route-info/list/{date?}', 'OttawaController@ottawaRouteinfoExcel')->name('export_OttawaRouteInfo.excel');

        ### Ottawa Returned ###
        Route::get('ottawa/returned', 'OttawaController@getOttawareturned')->name('ottawa-returned.index');
        Route::get('ottawa/returned/data', 'OttawaController@ottawaReturnedData')->name('ottawaReturned.data');
        Route::get('ottawa/returned/list/{date?}', 'OttawaController@ottawaReturnedExcel')->name('export_OttawaReturned.excel');
        Route::get('ottawa/returned/detail/{id}', 'OttawaController@ottawareturnedDetail')->name('ottawa_returned.profile');


        Route::get('ottawa/route/{di}/edit/hub/{id}', 'OttawaController@ottawaHubRouteEdit')->name('ottawa_route.detail');
        //Route::get('route/{id}/delete/hub','MontrealController@montrealDeleteRoute');
        Route::get('ottawa/route/orders/trackingid/{id}/details', 'OttawaController@getOttawatrackingorderdetails')->name('ottawainfo_route.detail');


        ###Shiphero Dashboard###
        Route::get('ship-hero-dashboard', 'ShipHeroController@getShipHeroDashboard')->name('shipHero-dashboard.index');
        Route::get('ship-hero-dashboard/data', 'ShipHeroController@getShipHeroDashboardData')->name('shipHero-dashboard.data');
        Route::get('ship-hero/dashboard/list/{date?}', 'ShipHeroController@shipHeroDashboardExcel')->name('shipHero-dashboard-export.excel');
        Route::get('ship-hero/dashboard/otd/report/{date?}', 'ShipHeroController@shipHeroDashboardExcelOtdReport')->name('shipHero-dashboard-export-otd-report.excel');
        Route::get('ship-hero/order/detail/{id}', 'ShipHeroController@shipHeroProfile')->name('shipHero-order.profile');
        ###Shiphero Cards###
        Route::get('ship-hero/card-dashboard', 'ShipHeroController@getShipHeroCards')->name('shipHero-card-dashboard.index');

        ### New Shiphero  Dashboard ###
        Route::get('ship-hero/order/data', 'ShipHeroController@getShipHeroData')->name('new-order-shipHero.data');
        Route::get('ship-hero/order', 'ShipHeroController@getShipHero')->name('new-order-shipHero.index');

        ### Shiphero Returned ###
        Route::get('ship-hero/returned', 'ShipHeroController@getShipHeroReturned')->name('new-returned-shipHero.index');
        Route::get('ship-hero/returned/data', 'ShipHeroController@shipHeroReturnedData')->name('new-returned-shipHero.data');

        ### Shiphero Not Returned At Hub ###
        Route::get('ship-hero/returned-not-hub', 'ShipHeroController@getShipHeroNotReturned')->name('new-notReturned-shipHero.index');
        Route::get('ship-hero/returned-not-hub/data', 'ShipHeroController@shipHeroNotReturnedData')->name('new-notReturned-shipHero.data');

        ### Shiphero Sorted ###
        Route::get('ship-hero/sorted', 'ShipHeroController@getShipHeroSorter')->name('new-sort-shipHero.index');
        Route::get('ship-hero/sorted/data', 'ShipHeroController@shipHeroSortedData')->name('new-sort-shipHero.data');
        Route::get('ship-hero/sorted/list/{date?}', 'ShipHeroController@shipHeroSortedExcel')->name('new-sort-shipHero-export.excel');

        ### Shiphero Hub ###
        Route::get('ship-hero/picked/up', 'ShipHeroController@getShipHeroHub')->name('new-pickup-shipHero.index');
        Route::get('ship-hero/picked/up/data', 'ShipHeroController@shipHeroPickedUpData')->name('new-pickup-shipHero.data');
        Route::get('ship-hero/picked/up/list/{date?}', 'ShipHeroController@shipHeroPickedUpExcel')->name('new-pickup-shipHero-export.excel');


        ###Borderless Dashboard###
        Route::get('borderless-dashboard', 'BorderlessController@getBoradlessDashboard')->name('borderless-dashboard.index');
        Route::get('borderless-dashboard/data', 'BorderlessController@getBoradlessDashboardData')->name('borderless-dashboard.data');
        Route::get('borderless/order/detail/{id}', 'BorderlessController@boradlessProfile')->name('borderless-order.profile');
        Route::get('borderless/dashboard/list/{date?}', 'BorderlessController@boradlessDashboardExcel')->name('borderless-dashboard-export.excel');
        Route::get('borderless/dashboard/otd/report/{date?}', 'BorderlessController@boradlessDashboardExcelOtdReport')->name('borderless-dashboard-export-otd-report.excel');

        ### Borderless  Cards ###
        Route::get('borderless/card-dashboard', 'BorderlessController@getBoradlessCards')->name('new-borderless-card-dashboard.index');

        ### New Borderless  Dashboard ###
        Route::get('borderless/order/data', 'BorderlessController@getBoradlessData')->name('new-order-borderless.data');
        Route::get('borderless/order', 'BorderlessController@getBoradless')->name('new-order-borderless.index');
        Route::get('borderless/order/list/{date?}', 'BorderlessController@getBoradlessExcel')->name('new-order-borderless-export.excel');
        ### Borderless Sorted ###
        Route::get('borderless/sorted', 'BorderlessController@getBoradlessSorter')->name('new-sort-borderless.index');
        Route::get('borderless/sorted/data', 'BorderlessController@boradlessSortedData')->name('new-sort-borderless.data');
        Route::get('borderless/sorted/list/{date?}', 'BorderlessController@boradlessSortedExcel')->name('new-sort-borderless-export.excel');
        ### Borderless Hub ###
        Route::get('borderless/picked/up', 'BorderlessController@getBoradlesshub')->name('new-pickup-borderless.index');
        Route::get('borderless/picked/up/data', 'BorderlessController@boradlessPickedUpData')->name('new-pickup-borderless.data');
        Route::get('borderless/picked/up/list/{date?}', 'BorderlessController@boradlessPickedupExcel')->name('new-pickup-borderless-export.excel');
        ### Borderless Not Scan ###
        Route::get('borderless/not/scan', 'BorderlessController@getBoradlessscan')->name('new-not-scan-borderless.index');
        Route::get('borderless/not/scan/data', 'BorderlessController@boradlessNotScanData')->name('new-not-scan-borderless.data');
        Route::get('borderless/not/scan/list/{date?}', 'BorderlessController@boradlessNotscanExcel')->name('new-not-scan-borderless-export.excel');
        ### Borderless Delivered ###
        Route::get('borderless/delivered', 'BorderlessController@getBoradlessdelivered')->name('new-delivered-borderless.index');
        Route::get('borderless/delivered/data', 'BorderlessController@boradlessDeliveredData')->name('new-delivered-borderless.data');
        Route::get('borderless/delivered/list/{date?}', 'BorderlessController@boradlessDeliveredExcel')->name('new-delivered-borderless-export.excel');
        ### Borderless Returned ###
        Route::get('borderless/returned', 'BorderlessController@getBoradlessreturned')->name('new-returned-borderless.index');
        Route::get('borderless/returned/data', 'BorderlessController@boradlessReturnedData')->name('new-returned-borderless.data');
        Route::get('borderless/returned/list/{date?}', 'BorderlessController@boradlessReturnedExcel')->name('new-returned-borderless-export.excel');
        ### Borderless Not Returned At Hub ###
        Route::get('borderless/returned-not-hub', 'BorderlessController@getBoradlessNotreturned')->name('new-notreturned-borderless.index');
        Route::get('borderless/returned-not-hub/data', 'BorderlessController@boradlessNotReturnedData')->name('new-notreturned-borderless.data');
        Route::get('borderless/returned-not-hub/list/{date?}', 'BorderlessController@boradlessNotReturnedExcel')->name('new-notreturned-borderless-export.excel');
        Route::get('borderless/returned-not-hub/tracking/list/{date?}', 'BorderlessController@boradlessNotReturnedExcelTrackingIds')->name('new-notreturned-borderless-tracking-export.excel');
        ### Borderless Custom Route ###
        Route::get('borderless/custom-route', 'BorderlessController@getBoradlessCustomRoute')->name('new-custom-route-borderless.index');
        Route::get('borderless/custom-route/data', 'BorderlessController@boradlessCustomRouteData')->name('new-custom-route-borderless.data');
        Route::get('borderless/custom-route/list/{date?}', 'BorderlessController@boradlessCustomRouteExcel')->name('new-custom-route-borderless-export.excel');

        ### Borderless Reporting###
        Route::get('/borderless/reporting', 'BorderlessController@getBoradlessReporting')->name('borderless_reporting.index');
        Route::get('yajra/borderless/reporting', 'BorderlessController@getBoradlessReportingData')->name('new_borderless_reporting_data.data');

        ### Borderless OTD ###
        Route::get('borderless/graph', 'BorderlessController@statistics_otd_index')->name('borderless-graph.index');
        Route::get('borderless/dashboard/statistics/ajax/otd-day', 'BorderlessController@ajax_render_boradless_otd_day')->name('borderless-otd-day.index');
        Route::get('borderless/dashboard/statistics/ajax/otd-week', 'BorderlessController@ajax_render_boradless_otd_week')->name('borderless-otd-week.index');
        Route::get('borderless/dashboard/statistics/ajax/otd-month', 'BorderlessController@ajax_render_boradless_otd_month')->name('borderless-otd-month.index');

        ### Borderless Route Info ###
        Route::get('borderless/route-info', 'BorderlessController@getRouteinfo')->name('borderless-route-info.index');
        Route::get('borderless/route-info/list/{date?}', 'BorderlessController@boradlessRouteinfoExcel')->name('new-export_BorderlessRouteInfo.excel');
        Route::get('borderless/route/{di}/edit/hub/{id}', 'BorderlessController@boradlessHubRouteEdit')->name('borderless_route.detail');
        Route::post('borderless/route-details/flag-history-model-html-render', 'BorderlessController@flagHistoryModelHtmlRender')->name('borderlessinfo_route.route-details.flag-history-model-html-render');
        Route::get('borderless/route/orders/trackingid/{id}/details', 'BorderlessController@getBoradlesstrackingorderdetails')->name('borderlessinfo_route.detail');
        Route::post('borderless/route/mark/delay', 'BorderlessController@routeMarkDelay')->name('borderless-route-mark-delay');
        Route::post('borderless/route-info/add-note', 'BorderlessController@addNote')->name('borderless-route-info.addNote');
        Route::get('borderless/route-info/get-notes', 'BorderlessController@getNotes')->name('borderless-route-info.getNotes');


        ### Borderless Profile ###
        Route::get('borderless/detail/{id}', 'BorderlessController@getBoradlessProfile')->name('borderless-detail-detail.profile');
        Route::get('borderless/sorted/detail/{id}', 'BorderlessController@boradlesssortedDetail')->name('borderless-sorted-detail.profile');
        Route::get('borderless/pickup/detail/{id}', 'BorderlessController@boradlesspickupDetail')->name('borderless-pickup-detail.profile');
        Route::get('borderless/notscan/detail/{id}', 'BorderlessController@boradlessnotscanDetail')->name('borderless-notscan-detail.profile');
        Route::get('borderless/delivered/detail/{id}', 'BorderlessController@boradlessdeliveredDetail')->name('borderless-delivered-detail.profile');
        Route::get('borderless/returned/detail/{id}', 'BorderlessController@boradlessreturnedDetail')->name('borderless-returned-detail.profile');
        Route::get('borderless/returned-not-hub/detail/{id}', 'BorderlessController@boradlessNotReturnedDetail')->name('borderless-notreturned-detail.profile');
        Route::get('borderless/custom-route/detail/{id}', 'BorderlessController@boradlessCustomRouteDetail')->name('borderless-CustomRoute-detail.profile');


        ###CTC Entries ###
        Route::get('new/ctc-dashboard', 'CtcEntriesController@getCtcDashboard')->name('new-ctc-dashboard.index');
        Route::get('new/ctc-dashboard/data', 'CtcEntriesController@getCtcDashboardData')->name('new-ctc-dashboard.data');
        Route::get('new/ctc/order/detail/{id}', 'CtcEntriesController@ctcProfile')->name('new-ctc-order.profile');
        Route::get('new/ctc/dashboard/list/{date?}', 'CtcEntriesController@ctcDashboardExcel')->name('new-ctc-dashboard-export.excel');
        Route::get('new/ctc/dashboard/otd/report/{date?}', 'CtcEntriesController@ctcDashboardExcelOtdReport')->name('new-ctc-dashboard-export-otd-report.excel');
        Route::get('new/ctc/missing/id/{date?}', 'CtcEntriesController@ctcMissingExcelReport')->name('new-ctc-missing-id-export.excel');

        ### CTC Entries Cards ###
        Route::get('new/last-mile/card-dashboard', 'CtcEntriesController@getCtcCards')->name('new-ctc-card-dashboard.index');

        ### New CTC Entries Dashboard ###
        Route::get('new/last-mile/order/data', 'CtcEntriesController@getCtcData')->name('new-order-last-mile.data');
        Route::get('new/last-mile/order', 'CtcEntriesController@getCtc')->name('new-order-ctc.index');
        Route::get('new/last-mile/order/list/{date?}', 'CtcEntriesController@getCtcExcel')->name('new-order-ctc-export.excel');
        ### CTC Entries Sorted ###
        Route::get('new/last-mile/sorted', 'CtcEntriesController@getCtcSorter')->name('new-sort-ctc.index');
        Route::get('new/last-mile/sorted/data', 'CtcEntriesController@ctcSortedData')->name('new-sort-ctc.data');
        Route::get('new/last-mile/sorted/list/{date?}', 'CtcEntriesController@ctcSortedExcel')->name('new-sort-ctc-export.excel');
        ### CTC Entries Hub ###
        Route::get('new/last-mile/picked/up', 'CtcEntriesController@getCtchub')->name('new-pickup-ctc.index');
        Route::get('new/last-mile/picked/up/data', 'CtcEntriesController@ctcPickedUpData')->name('new-pickup-ctc.data');
        Route::get('new/last-mile/picked/up/list/{date?}', 'CtcEntriesController@ctcPickedupExcel')->name('new-pickup-ctc-export.excel');
        ### CTC Entries Not Scan ###
        Route::get('new/last-mile/not/scan', 'CtcEntriesController@getCtcscan')->name('new-not-scan-ctc.index');
        Route::get('new/last-mile/not/scan/data', 'CtcEntriesController@ctcNotScanData')->name('new-not-scan-ctc.data');
        Route::get('new/last-mile/not/scan/list/{date?}', 'CtcEntriesController@ctcNotscanExcel')->name('new-not-scan-ctc-export.excel');
        ### CTC Entries Delivered ###
        Route::get('new/last-mile/delivered', 'CtcEntriesController@getCtcdelivered')->name('new-delivered-ctc.index');
        Route::get('new/last-mile/delivered/data', 'CtcEntriesController@ctcDeliveredData')->name('new-delivered-ctc.data');
        Route::get('new/last-mile/delivered/list/{date?}', 'CtcEntriesController@ctcDeliveredExcel')->name('new-delivered-ctc-export.excel');
        ### CTC Entries Returned ###
        Route::get('new/last-mile/returned', 'CtcEntriesController@getCtcreturned')->name('new-returned-ctc.index');
        Route::get('new/last-mile/returned/data', 'CtcEntriesController@ctcReturnedData')->name('new-returned-ctc.data');
        Route::get('new/last-mile/returned/list/{date?}', 'CtcEntriesController@ctcReturnedExcel')->name('new-returned-ctc-export.excel');
        ### CTC Entries Not Returned At Hub ###
        Route::get('new/ctc/returned-not-hub', 'CtcEntriesController@getCtcNotreturned')->name('new-notreturned-ctc.index');
        Route::get('new/ctc/returned-not-hub/data', 'CtcEntriesController@ctcNotReturnedData')->name('new-notreturned-ctc.data');
        Route::get('new/ctc/returned-not-hub/list/{date?}', 'CtcEntriesController@ctcNotReturnedExcel')->name('new-notreturned-ctc-export.excel');
        Route::get('new/ctc/returned-not-hub/tracking/list/{date?}', 'CtcEntriesController@ctcNotReturnedExcelTrackingIds')->name('new-notreturned-ctc-tracking-export.excel');
        ### CTC Entries Custom Route ###
        Route::get('new/last-mile/custom-route', 'CtcEntriesController@getCtcCustomRoute')->name('new-custom-route-ctc.index');
        Route::get('new/last-mile/custom-route/data', 'CtcEntriesController@ctcCustomRouteData')->name('new-custom-route-ctc.data');
        Route::get('new/last-mile/custom-route/list/{date?}', 'CtcEntriesController@ctcCustomRouteExcel')->name('new-custom-route-ctc-export.excel');

        ### CTC Entries Profile ###
        Route::get('new/last-mile/detail/{id}', 'CtcEntriesController@getCtcProfile')->name('new-ctc-detail-detail.profile');
        Route::get('new/last-mile/sorted/detail/{id}', 'CtcEntriesController@ctcsortedDetail')->name('new-ctc-sorted-detail.profile');
        Route::get('new/last-mile/pickup/detail/{id}', 'CtcEntriesController@ctcpickupDetail')->name('new-ctc-pickup-detail.profile');
        Route::get('new/last-mile/notscan/detail/{id}', 'CtcEntriesController@ctcnotscanDetail')->name('new-ctc-notscan-detail.profile');
        Route::get('new/last-mile/delivered/detail/{id}', 'CtcEntriesController@ctcdeliveredDetail')->name('new-ctc-delivered-detail.profile');
        Route::get('new/last-mile/returned/detail/{id}', 'CtcEntriesController@ctcreturnedDetail')->name('new-ctc-returned-detail.profile');
        Route::get('new/last-mile/returned-not-hub/detail/{id}', 'CtcEntriesController@ctcNotReturnedDetail')->name('new-ctc-notreturned-detail.profile');
        Route::get('new/last-mile/custom-route/detail/{id}', 'CtcEntriesController@ctcCustomRouteDetail')->name('new-ctc-CustomRoute-detail.profile');

        ### CTC Entries Reporting###
        Route::get('new/ctcreporting', 'CtcEntriesController@getCtcReporting')->name('new-ctc_reporting.index');
        Route::get('new/yajractcreporting', 'CtcEntriesController@getCtcReportingData')->name('new-ctc_reporting_data.data');
        ### CTC Entries OTD ###
        Route::get('new/ctc/graph', 'CtcEntriesController@statistics_otd_index')->name('new-ctc-graph.index');
        Route::get('new/ctc/dashboard/statistics/ajax/otd-day', 'CtcEntriesController@ajax_render_ctc_otd_day')->name('new-ctc-otd-day.index');
        Route::get('new/ctc/dashboard/statistics/ajax/otd-week', 'CtcEntriesController@ajax_render_ctc_otd_week')->name('new-ctc-otd-week.index');
        Route::get('new/ctc/dashboard/statistics/ajax/otd-month', 'CtcEntriesController@ajax_render_ctc_otd_month')->name('new-ctc-otd-month.index');

        ###CTC Entries Route Info ###
        Route::get('new/ctc/route-info', 'CtcEntriesController@getRouteinfo')->name('new-ctc-route-info.index');
        Route::get('new/ctc/route-info/list/{date?}', 'CtcEntriesController@ctcRouteinfoExcel')->name('new-export_CTCRouteInfo.excel');
        Route::get('new/ctc/route/{di}/edit/hub/{id}', 'CtcEntriesController@ctcHubRouteEdit')->name('new-ctc_route.detail');
        Route::post('new/ctc/route-details/flag-history-model-html-render', 'CtcEntriesController@flagHistoryModelHtmlRender')->name('new-ctcinfo_route.route-details.flag-history-model-html-render');
        Route::get('new/ctc/route/orders/trackingid/{id}/details', 'CtcEntriesController@getCtctrackingorderdetails')->name('new-ctcinfo_route.detail');
        Route::post('new/route/mark/delay', 'CtcEntriesController@routeMarkDelay')->name('new-route-mark-delay');
        Route::post('new/ctc/route-info/add-note', 'CtcEntriesController@addNote')->name('new-ctc-route-info.addNote');
        Route::get('new/ctc/route-info/get-notes', 'CtcEntriesController@getNotes')->name('new-ctc-route-info.getNotes');

        Route::get('allroute/{id}/location/joey','LastMileRouteController@getLocationMap');
        Route::post('route/map/location','LastMileRouteController@getRouteMapLocation');

        Route::get('route/{id}/map','CtcEntriesController@RouteMap');
        Route::get('route/{id}/remaining','CtcEntriesController@remainigrouteMap');

        ###CTC Client Dashboard Broker###
        Route::get('ctc-dashboard-broker', 'CtcEntriesController@getCtcDashboardBroker')->name('ctc-dashboard-broker.index');
        Route::get('ctc-dashboard-broker/data', 'CtcEntriesController@getCtcDashboardBrokerData')->name('ctc-dashboard-broker.data');
        Route::get('ctc-broker-detail/{id}', 'CtcEntriesController@ctcBrokerProfile')->name('ctc-broker.profile');

        ###New CTC ###
        Route::get('newctc-dashboard', 'NewCtcController@getCtcNewDashboard')->name('newctc-dashboard.index');
        Route::get('newctc-dashboard/data', 'NewCtcController@getCtcNewDashboardData')->name('newctc-dashboard.data');
        Route::get('newctc/new/detail/{id}', 'NewCtcController@ctcNewProfile')->name('newctc-new.profile');
        Route::get('newctc/new/dashboard/list/{date?}', 'NewCtcController@CtcNewDashboardExcel')->name('newexport_ctc_new_dashboard.excel');

        ###Current CTC ###
        Route::get('last-mile-dashboard', 'CtcEntriesController@getCtcDashboard')->name('last-mile-dashboard.index');
        Route::get('last-mile-dashboard/data', 'CtcEntriesController@getCtcDashboardData')->name('last-mile-dashboard.data');
        Route::get('last-mile/new/detail/{id}', 'CtcEntriesController@ctcProfile')->name('last-mile-new.profile');
        Route::get('last-mile/new/dashboard/list/{date?}', 'CtcEntriesController@ctcDashboardExcel')->name('export_ctc_new_dashboard.excel');
        Route::get('last-mile/new/dashboard/list/test/{date?}', 'CtcController@CtcNewDashboardExcelTest')->name('export_ctc_new_dashboard_test.excel');
        Route::get('last-mile/new/dashboard/otd/report/{date?}', 'CtcEntriesController@ctcDashboardExcelOtdReport')->name('export_ctc_new_dashboard_otd_report.excel');
        Route::get('last-mile/missing/id/{date?}', 'CtcEntriesController@CtcMissingExcelReport')->name('export_last-mile_missing_id.excel');

        ###CTC Dashboard###
        Route::get('ctc', 'CtcController@getCtc')->name('ctc.index');
        Route::get('ctc/data', 'CtcController@ctcData')->name('ctc.data');
        Route::get('ctc/list/{date?}', 'CtcController@CtcExcel')->name('export_ctc.excel');
        ###CTC Sorted###
        Route::get('ctc/sorted', 'CtcController@getCtcSort')->name('ctc-sort.index');
        Route::get('ctc/sorted/data', 'CtcController@ctcSortedData')->name('ctcSorted.data');
        Route::get('ctc/sorted/list/{date?}', 'CtcController@otcSortedExcel')->name('export_CTCSorted.excel');
        ###CTC Picked Up###
        Route::get('ctc/picked/up', 'CtcController@getCtcthub')->name('ctc-pickup.index');
        Route::get('ctc/picked/up/data', 'CtcController@ctcPickedUpData')->name('ctcPickedUp.data');
        Route::get('ctc/picked/up/list/{date?}', 'CtcController@ctcPickedUpExcel')->name('export_CTCPickedUp.excel');
        ### CTC Not Scan ###
        Route::get('ctc/not/scan', 'CtcController@getCtcnotscan')->name('ctc-not-scan.index');
        Route::get('ctc/not/scan/data', 'CtcController@ctcNotScanData')->name('ctcNotScan.data');
        Route::get('ctc/not/scan/list/{date?}', 'CtcController@ctcNotscanExcel')->name('export_CTCNotScan.excel');
        ### CTC Delivered ###
        Route::get('ctc/delivered', 'CtcController@getCtcDelivered')->name('ctc-delivered.index');
        Route::get('ctc/delivered/data', 'CtcController@ctcDeliveredData')->name('ctcDelivered.data');
        Route::get('ctc/delivered/list/{date?}', 'CtcController@ctcDeliveredExcel')->name('export_CTCDelivered.excel');
        ### Ottawa Route Information ###
        Route::get('last-mile/route-info', 'CtcEntriesController@getRouteinfo')->name('ctc-route-info.index');
        Route::get('last-mile/route-info/list/{date?}', 'CtcEntriesController@ctcRouteinfoExcel')->name('export_CTCRouteInfo.excel');
        Route::get('ctc/route/{di}/edit/hub/{id}', 'CtcEntriesController@ctcHubRouteEdit')->name('ctc_route.detail');
        Route::post('ctc/route-details/flag-history-model-html-render', 'CtcEntriesController@flagHistoryModelHtmlRender')->name('ctcinfo_route.route-details.flag-history-model-html-render');
        Route::get('ctc/route/orders/trackingid/{id}/details', 'CtcEntriesController@getCtctrackingorderdetails')->name('ctcinfo_route.detail');
        Route::post('route/mark/delay', 'CtcEntriesController@routeMarkDelay')->name('route-mark-delay');

        ###CTC Order Detail###
        Route::get('ctc/detail/{id}', 'CtcController@ctcProfile')->name('ctc.profile');
        Route::get('ctc/sorted/detail/{id}', 'CtcController@ctcsortedDetail')->name('ctc_sorted.profile');
        Route::get('ctc/pickup/detail/{id}', 'CtcController@ctcpickupDetail')->name('ctc_pickup.profile');
        Route::get('ctc/notscan/detail/{id}', 'CtcController@ctcnotscanDetail')->name('ctc_notscan.profile');
        Route::get('ctc/delivered/detail/{id}', 'CtcController@ctcdeliveredDetail')->name('ctc_delivered.profile');

        ### Return Route Information ###
        Route::get('return/route-info', 'ReturnDashboardController@getReturnRouteinfo')->name('return-route-info.index');
        Route::get('return/route/{di}/{type}', 'ReturnDashboardController@returnRouteOrder')->name('return-route-order.detail');
        Route::get('return/route/orders/trackingid/{id}/details', 'ReturnDashboardController@getReturnTrackingOrderDetails')->name('return-route-info-order.detail');


        ### Toronto Flower company ###
        Route::get('toronto/flower/route-info', 'FlowerController@getRouteinfo')->name('toronto-flower-route-info.index');
        Route::get('toronto/flower/route-info/list/{date?}', 'FlowerController@torontoFlowerRouteInfoExcel')->name('export_toronto_flower_route_info.excel');
        Route::get('toronto/flower/route/{di}/edit/hub/{id}', 'FlowerController@torontoFlowerHubRouteEdit')->name('toronto_flower_route.detail');
        Route::get('toronto/flower/route/orders/trackingid/{id}/details', 'FlowerController@getTorontoFlowerTrackingOrderDetails')->name('toronto_flower_info_route.detail');


        ###CTC Reporting###
        Route::get('ctcreporting', 'CtcEntriesController@getCtcReporting')->name('ctc_reporting.index');
        Route::get('yajractcreporting', 'CtcEntriesController@getCtcReportingData')->name('ctc_reporting_data.data');
        Route::get('ctc/summary/detail/{id}', 'CtcController@ctcNewProfile')->name('ctc-summary.profile');
        Route::get('ctc/reporting/excel/{data_for?}/{fromdate?}/{todate?}', 'CtcController@ctcReportingExcel')->name('export_ctc_reporting.excel');


        ### CTC OTD ###
        Route::get('last-mile/graph', 'CtcEntriesController@statistics_otd_index')->name('ctc-graph.index');
        Route::get('dashboard/statistics/ajax/otd-day', 'CtcEntriesController@ajax_render_ctc_otd_day')->name('ctc-otd-day.index');
        Route::get('dashboard/statistics/ajax/otd-week', 'CtcEntriesController@ajax_render_ctc_otd_week')->name('ctc-otd-week.index');
        Route::get('dashboard/statistics/ajax/otd-month', 'CtcEntriesController@ajax_render_ctc_otd_month')->name('ctc-otd-month.index');

        ### WareHouse Performance ###
        Route::get('warehouse-performance', 'WarehousePerformanceController@getWarehousePerformance')->name('warehouse-performance.index');
        Route::get('warehouse-performance/data', 'WarehousePerformanceController@getWarehousePerformanceData')->name('warehouse-performance.data');

        ###Reattempt Order###
        Route::get('reattempt/order', 'ReattemptOrdersController@getIndex')->name('reattempt-order.index');
        ###Scan Tracking Id ####
        Route::get('reattempt/order/search', 'ReattemptOrdersController@searchTrackingId')->name('tracking-order.search');
        ###Transfer Order###
        Route::get('transfer/order/{id}', 'ReattemptOrdersController@transferOrder')->name('transfer-order');
        ###Reattempt Order###
        Route::get('reattempt/order/{id}', 'ReattemptOrdersController@reattemptOrder')->name('reattempt-order');
        ###Update status for reattempt ###
        Route::get('update-status-of-scanned-order-for-reattempt', 'ReattemptOrdersController@updateStatusOfScannedOrder')->name('update-status-of-scanned-order');
        ###Multiple Reattempt Order###
        Route::get('multiple/reattempt/order', 'ReattemptOrdersController@multipleReattemptOrder')->name('multiple-reattempt-order');
        ###Update Column###
        Route::get('reattempt/order/column/update', 'ReattemptOrdersController@reattemptOrderColumnUpdate');
        ###Return Order###
        Route::get('Return/order/{id}', 'ReattemptOrdersController@returnOrder');
        ###Delete Data From Return And Reattempt Order###
        Route::get('Reattempt/delete/{id}', 'ReattemptOrdersController@deleteReattempt');
        ###Reattempt Order List###
        Route::get('reattempt/history', 'ReattemptOrdersController@reattemptOrderList');
        ###Show Notes###
        Route::get('notes/{id}', 'ReattemptOrdersController@showNotes')->name('show-notes');
        ###Approved Order By Customer Support List###
        Route::get('customer/support/approved', 'ReattemptOrdersController@approvedOrderList');
        //Customer Order Confirmation
        Route::get('order/under-review', 'CustomerSupportController@getIndex')->name('order-confirmation-list.index');
        Route::get('order/history', 'CustomerSupportController@getOrderHistory')->name('order-confirmation.history');
        Route::post('order/approval', 'CustomerSupportController@orderConfirtmation')->name('orderConfirmation.transfer');
        ###Update Column###
        Route::get('reattempt/order/column/update', 'CustomerSupportController@reattemptOrderColumnUpdate')->name('Column.Update');
        ###Expired Order###
        Route::get('return/order', 'CustomerSupportController@expiredOrder')->name('expired-order.history');
        ###Return Order###
        Route::get('Return/order/{id}', 'CustomerSupportController@returnOrder')->name('return-order.update');
        ###Add Notes###
        Route::post('add/notes', 'CustomerSupportController@addNotes')->name('add-notes');
        Route::get('notes/{id}', 'CustomerSupportController@showNotes')->name('show-notes');
        ###Returned Order###
        Route::get('returned/order', 'CustomerSupportController@returnedOrder')->name('returned-order.index');
        ## Customer Support ton ton
        Route::get('order/under-review/count', 'CustomerSupportController@getCustomerCount')->name('order-confirmation-list.count');
        ## ## Approved Customer Order Count
        Route::get('approved/customer/order/count', 'ReattemptOrdersController@approvedCustomerOrderCount');
        ###Walmart Dashboard###
        Route::get('walmart/dashboard', 'WalmartController@statistics_wm')->name('walmartdashboard.index');
        // Route::get('dashboard/statistics/walmart', 'WalmartController@statistics_wm');
        Route::get('dashboard/statistics/ajax/otd', 'WalmartController@ajax_render_otd_charts')->name('walmartotdajax.index');
        Route::get('dashboard/statistics/ajax/short-summary', 'WalmartController@ajax_render_short_summary')->name('walmartshortsummary.index');
        Route::get('dashboard/statistics/ajax/walmart-orders', 'WalmartController@ajax_render_walmart_order')->name('walmartrenderorder.index');
        Route::get('dashboard/statistics/ajax/walmart-on-time-orders', 'WalmartController@ajax_render_walmart_on_time_orders')->name('walmartontimeorder.index');;
        Route::get('dashboard/statistics/ajax/walmart-stores-data', 'WalmartController@ajax_render_walmart_stores_data')->name('walmartstoresdata.index');
        Route::get('dashboard/statistics/ajax/total-orders-summary', 'WalmartController@ajax_render_total_orders_summary')->name('walmartordersummary.index');
        Route::get('report/export', 'WalmartController@getWmExport')->name('walmartdashboard.excel');
        Route::get('walmart/new-count', 'WalmartController@walmartNewCount')->name('walmart-new-count');
        Route::get('generate/walmart/orders/report/csv ', 'WalmartController@download_walmart_report_csv_view')->name('download-walmart-report-csv-view');
        Route::post('generate/walmart/orders/report/csv/ajax', 'WalmartController@generate_walmart_report_csv')->name('generate-walmart-report-csv');


        ### New Walmart Dashboard###
        Route::get('new/walmart/dashboard', 'NewWalmartController@statistics_wm')->name('new-walmartdashboard.index');
        Route::get('new/dashboard/statistics/ajax/otd', 'NewWalmartController@ajax_render_otd_charts')->name('new-walmartotdajax.index');
        Route::get('new/dashboard/statistics/ajax/short-summary', 'NewWalmartController@ajax_render_short_summary')->name('new-walmartshortsummary.index');
        Route::get('new/dashboard/statistics/ajax/walmart-orders', 'NewWalmartController@ajax_render_walmart_order')->name('new-walmartrenderorder.index');
        Route::get('new/dashboard/statistics/ajax/walmart-on-time-orders', 'NewWalmartController@ajax_render_walmart_on_time_orders')->name('new-walmartontimeorder.index');;
        Route::get('new/dashboard/statistics/ajax/walmart-stores-data', 'NewWalmartController@ajax_render_walmart_stores_data')->name('new-walmartstoresdata.index');
        Route::get('new/dashboard/statistics/ajax/total-orders-summary', 'NewWalmartController@ajax_render_total_orders_summary')->name('new-walmartordersummary.index');
        Route::get('new/report/export', 'NewWalmartController@getWmExport')->name('new-walmartdashboard.excel');


        Route::get('walmart', 'WalmartController@getwalmart')->name('walmart.index');
        Route::get('walmart/data', 'WalmartController@walmartdata')->name('walmart.data');
        Route::get('walmart/list/{date?}', 'WalmartController@walmartexcel')->name('export_walmart.excel');
        Route::get('walmart/profile/{id}', 'WalmartController@walmartprofile')->name('walmart.profile');


        ###Grocery Dashboard###
        Route::get('grocery/dashboard', 'GroceryDashboardController@statistics_grocery_index')->name('grocerydashboard.index');
        Route::get('dashboard/statistics/ajax/grocery-orders', 'GroceryDashboardController@ajax_render_grocery_orders')->name('groceryajaxorder.index');
        Route::get('dashboard/statistics/ajax/grocery-otd', 'GroceryDashboardController@ajax_render_otd_charts')->name('groceryotdcharts.index');
        Route::get('grocery/new-count', 'GroceryDashboardController@groceryNewCount')->name('grocery-new-count');

### New Loblaws Dashboard###
        Route::get('loblaws/dashboard', 'NewLoblawsController@statistics_loblaws_index')->name('loblawsdashboard.index');
        Route::get('dashboard/statistics/ajax/loblaws-orders', 'NewLoblawsController@ajax_render_loblaws_orders')->name('loblawsajaxorder.index');
        Route::get('dashboard/statistics/ajax/loblaws-otd', 'NewLoblawsController@ajax_render_otd_charts')->name('loblawsotdcharts.index');
        Route::get('dashboard/statistics/ajax/loblaws-ota', 'NewLoblawsController@ajax_render_ota_charts')->name('loblawsajaxotacharts.index');
        Route::get('dashboard/statistics/ajax/loblaws-total-order', 'NewLoblawsController@ajax_render_total_order')->name('loblawstotalorder.index');
        Route::get('loblaws/new-count', 'NewLoblawsController@loblawsNewCount')->name('loblaws-new-count');


###New Loblaws Calgary Dashboard###
        Route::get('loblawscalgary/dashboard', 'NewLoblawsCalgaryController@statistics_loblaws_index')->name('loblawscalgary.index');
        Route::get('dashboard/statistics/ajax/loblawscalgary-orders', 'NewLoblawsCalgaryController@ajax_render_loblaws_orders')->name('loblawscalgary_orders.index');
        Route::get('dashboard/statistics/ajax/loblawscalgary-otd', 'NewLoblawsCalgaryController@ajax_render_otd_charts')->name('loblawscalgary_otd_charts.index');
        Route::get('dashboard/statistics/ajax/loblawscalgary-ota', 'NewLoblawsCalgaryController@ajax_render_ota_charts')->name('loblawscalgary_ota_charts.index');
        Route::get('dashboard/statistics/ajax/loblawscalgary-total-order', 'NewLoblawsCalgaryController@ajax_render_total_order')->name('loblawscalgary_total_order.index');
        Route::get('loblawscalgary/new-count', 'NewLoblawsCalgaryController@loblawsNewCount')->name('loblawscalgary-new-count');



        ### New Loblaws Home delivery Dashboard###
        Route::get('loblawshomedelivery/dashboard', 'NewLoblawsHomeDeliveryController@statistics_loblaws_index')->name('loblawshome.index');
        Route::get('dashboard/statistics/ajax/loblawshomedelivery-orders', 'NewLoblawsHomeDeliveryController@ajax_render_loblaws_orders')->name('loblawshome_order.index');
        Route::get('dashboard/statistics/ajax/loblawshomedelivery-otd', 'NewLoblawsHomeDeliveryController@ajax_render_otd_charts')->name('loblawshome_otd_charts.index');
        Route::get('dashboard/statistics/ajax/loblawshomedelivery-ota', 'NewLoblawsHomeDeliveryController@ajax_render_ota_charts')->name('loblawshome_ota_charts.index');
        Route::get('dashboard/statistics/ajax/loblawshomedelivery-total-order', 'NewLoblawsHomeDeliveryController@ajax_render_total_order')->name('loblawshome_total_order.index');
        Route::get('loblawshomedelivery/new-count', 'NewLoblawsHomeDeliveryController@loblawsNewCount')->name('loblawshomedelivery-new-count');


        ### Loblaws Dashboard Order Reporting Csv ###
        Route::get('loblaws/dashboard/reporting', 'NewLoblawsController@loblaws_dashboard_csv_index')->name('loblaws-dashboard-reporting-csv');
        Route::get('loblaws/dashboard/reporting/csv', 'NewLoblawsController@loblaws_dashboard_csv_download')->name('generate-loblaws-report-csv');
        ### Loblaws Calgary Dashboard Order Reporting Csv ###
        Route::get('loblaws/calgary/dashboard/reporting', 'NewLoblawsCalgaryController@loblaws_calgary_dashboard_csv_index')->name('loblaws-calgary-dashboard-reporting-csv');
        Route::get('loblaws/calgary/dashboard/reporting/csv', 'NewLoblawsCalgaryController@loblaws_calgary_dashboard_csv_download')->name('generate-calgary-loblaws-report-csv');
        ### Loblaws Dashboard Order Reporting Csv ###
        Route::get('loblaws/homedelivery/dashboard/reporting', 'NewLoblawsHomeDeliveryController@loblaws_homedelivery_dashboard_csv_index')->name('loblaws-homedelivery-dashboard-reporting-csv');
        Route::get('loblaws/homedelivery/dashboard/reporting/csv', 'NewLoblawsHomeDeliveryController@loblaws_homedelivery_dashboard_csv_download')->name('generate-loblaws-homedelivery-report-csv');

        ### Good Food Dashboard###
        Route::get('good-food/dashboard', 'NewGoodFoodController@statistics_goodfood_index')->name('goodfood.index');
        Route::get('dashboard/statistics/ajax/good-food-orders', 'NewGoodFoodController@ajax_render_goodfood_orders')->name('goodfood_order.index');
        Route::get('dashboard/statistics/ajax/good-food-otd', 'NewGoodFoodController@ajax_render_goodfood_otd_charts')->name('goodfood_otd_charts.index');
        Route::get('dashboard/statistics/ajax/good-food-ota', 'NewGoodFoodController@ajax_render_goodfood_ota_charts')->name('goodfood_ota_charts.index');
        Route::get('good-food/new-count', 'NewGoodFoodController@goodFoodCount')->name('goodfood-new-count');
        Route::get('good-food/dashboard/reporting', 'NewGoodFoodController@goodfood_dashboard_csv_index')->name('goodfood-dashboard-reporting-csv');
        Route::get('good-food/dashboard/reporting/csv', 'NewGoodFoodController@goodfood_dashboard_csv_download')->name('generate-goodfood-report-csv');


        //WarehouseSorterController routes
        Route::get('alert-system', 'WarehouseSorterController@getindex')->name('alert-system.index');
        Route::get('warehouse/sorter', 'WarehouseSorterController@getindex')->name('warehousesorter.index');
        Route::get('warehouse/sorter/data', 'WarehouseSorterController@warehousesorterlist')->name('warehousesorter.data');
        Route::get('warehouse/sorter/add', 'WarehouseSorterController@add')->name('warehousesorter.add');
        Route::post('warehouse/sorter/create', 'WarehouseSorterController@create')->name('warehousesorter.create');
        Route::get('warehouse/sorter/{id}', 'WarehouseSorterController@profile')->name('warehousesorter.profile');
        Route::get('warehouse/sorter/edit/{id}', 'WarehouseSorterController@edit')->name('warehousesorter.edit');
        Route::put('warehouse/sorter/update/{id}', 'WarehouseSorterController@update')->name('warehousesorter.update');
        Route::delete('warehouse/sorter/{id}', 'WarehouseSorterController@destroy')->name('warehousesorter.delete');


        //Setting routes
        Route::get('setting', 'SettingController@getIndex')->name('setting.index');
        Route::get('setting/data', 'SettingController@getListData')->name('setting.data');
        Route::get('setting/add', 'SettingController@add')->name('setting.add');
        Route::post('setting/create', 'SettingController@create')->name('setting.create');
        Route::get('setting/{id}', 'SettingController@profile')->name('setting.profile');
        Route::get('setting/edit/{id}', 'SettingController@edit')->name('setting.edit');
        Route::put('setting/update/{id}', 'SettingController@update')->name('setting.update');


        ### Toronto Routing Module Routes###  -- Daniyal Khan
        Route::post('returnroute/transfer/hub', 'ReturnRouteController@RouteTransfer');//Return Route Transfer hub
        Route::get('returnroute/{di}/edit/hub/{id}', 'ReturnRouteController@hubRouteEdit');//Return Route List Edit
        Route::get('return/routes', 'ReturnRouteController@ctcRoutificControls');//Return Routes List
        Route::get('returnroute/{id}/details','ReturnRouteController@routeDetails');//Return Routes List Detail
        Route::get('returnroute/{id}/delete/hub','ReturnRouteController@deleteRoute');//Return Route Delete
        Route::get('returnroute/{id}/map','ReturnRouteController@RouteMap');//Return Route Map
        Route::post('returnroutific/job/delete','ReturnRouteController@droutificjob');//Return Job Delete
        Route::get('returncreateCustom/{id}/route', 'ReturnRouteController@createCustomRoute');//Return Route Create
        Route::get('returnroute/delete', 'ReturnRouteController@getdeleteRouteview');//Return Route Delete
        Route::get('returncustom/routing/{id}/hub', 'ReturnRouteController@getCustomRouteIndex'); //Return Custom Routing
        Route::get('returntracking/detail','ReturnRouteController@getTrackingIdDetail');//Return Tracking Detail
        Route::post('returncreate/route/custom/routing','ReturnRouteController@postCreateRoute');//Return Route Job Create
        Route::get('returnroutific/{id}/job', 'ReturnRouteController@getroutificjob');//Return Route get Job
        Route::post('returnremove/multipletrackingid','ReturnRouteController@multipleRemoveTrackingid');//Reomve Return Trackings
        Route::post('returnremove/trackingid','ReturnRouteController@removeTrackingid');//Remove return single tracking
        Route::post('returncustom/add/joey/count','ReturnRouteController@addJoeyCount');//Add Return Joey Count
        Route::get('returncustom/joey/count','ReturnRouteController@getJoeyCountDetail');//Get Return Joey Count
        Route::post('returncustom/edit/joey/count','ReturnRouteController@updateJoeyCountDetail');//Edit return joey count
        Route::post('returnremove/joeycount','ReturnRouteController@deleteJoeyCount');//delete return joey count
        Route::post('returnremove/order/inroute','ReturnRouteController@removeOrderInRoute');//Remove Return Order In route



        ###Other Action###
        Route::post('hub/routific/updatestatus', 'RoutificController@poststatusupdate')->name('hub-routific-update.Update');
        Route::get('hub/routific/status', 'RoutificController@getstatus')->name('hub-routific.index');
        route::post('update/multiple/trackingid', 'SearchOrdersController@post_multiorderupdates')->name('multiple-tracking-id.update');
        route::get('update/multiple/trackingid', 'SearchOrdersController@get_multiorderupdates')->name('multiple-tracking-id.index');

        Route::post('update/order/status', 'SearchOrdersController@updatestatus')->name('update-order.update');
        route::get('search/orders/trackingid/{id}/details', 'SearchOrdersController@get_trackingorderdetails')->name('searchorder.show');

        Route::get('manual/route', 'ManualRouteController@getManualRoute')->name('manual-route.index');
        Route::post('update/manual/route', 'ManualRouteController@postUpdateManualRoute');

        Route::get('search/trackingid/multiple', 'SearchOrdersController@get_multipletrackingid')->name('search-multiple-tracking.index');
        Route::post('sprint/image/upload', 'SearchOrdersController@sprintImageUpload')->name('sprint-image-upload');

        ###Create Flags###
        Route::get('flag/create/{id}', 'FlagOrdersController@createFlag')->name('flag.create');
        ###Un-Flag Flags###
        Route::get('un-flag/{id}', 'FlagOrdersController@unFlag')->name('un-flag');
        ###Flag orders list###
        Route::get('flag-order-list/data', 'FlagOrdersController@FlagOrderListData')->name('flag-order-list.data');
        Route::get('flag-order-list-pie-chart-data', 'FlagOrdersController@FlagOrderListPieChartData')->name('flag-order-list-pie-chart-data');
        Route::get('flag-order-list', 'FlagOrdersController@FlagOrderList')->name('flag-order-list.index');
        Route::get('flag-orders-list/details/{id}', 'FlagOrdersController@FlagOrderDetails')->name('flag-order.details');

        Route::get('approved-flag-list/data', 'FlagOrdersController@ApprovedFlagListData')->name('approved-flag-list.data');
        Route::get('approved-flag-list', 'FlagOrdersController@ApprovedFlagList')->name('approved-flag-list.index');

        Route::get('un-approved-flag-list/data', 'FlagOrdersController@UnApprovedFlagListData')->name('un-approved-flag-list.data');
        Route::get('un-approved-flag-list', 'FlagOrdersController@UnApprovedFlagList')->name('un-approved-flag-list.index');

        ###Blocked Joey List To Un-blocked###
        Route::get('block-joey-flag-list/data', 'FlagOrdersController@BlockJoeyFlagListData')->name('block-joey-flag-list.data');
        Route::get('block-joey-flag-list', 'FlagOrdersController@BlockJoeyFlagList')->name('block-joey-flag-list.index');
        Route::get('block-joey-flag/unblock/{id}', 'FlagOrdersController@UnblockJoeyFlag')->name('unblock-joey-flag.update');
        Route::get('joey-flag/performance/{id}', 'FlagOrdersController@JoeyPerformanceStatus')->name('joey-performance-status.update');

        ### Manual Status Update ###
        Route::get('manual/status', 'ManualStatusController@getManualStatus')->name('manual-status.index');
        Route::get('manual/status/data', 'ManualStatusController@ManualStatusData')->name('manual-status.data');

        ### Manual Tracking Report ###
        Route::get('manual/tracking/report', 'ManualTrackingReportController@getManualTrackingReport')->name('manual-tracking-report.index');
        Route::get('manual/tracking/data', 'ManualTrackingReportController@ManualTrackingData')->name('manual-tracking.data');

        Route::post('manual/tracking/csv-download', 'ManualTrackingReportController@downloadCsv')->name('manual-tracking.excel');
        Route::get('/download-file-tracking', function () {
            // getting file path
            $file_path = public_path() . '/' . request()->file_path;
            // getting file name
            $file_name = explode('/', $file_path);
            $file_name = explode('-', end($file_name))[0];
            // getting file extension
            $file_extension = explode('.', $file_path);
            $file_extension = end($file_extension);
            return response()->download($file_path, $file_name . '.' . $file_extension);
        })->name('download-file-tracking');

        ## CTC Reporting order ##
        Route::get('reporting', 'ReportingController@getReporting')->name('reporting.index');
        Route::get('reporting/data', 'ReportingController@reportingdata')->name('reporting.data');
        Route::get('reporting/excel/{vendor?}/{fromdate?}/{todate?}', 'ReportingController@reportingexcel')->name('export_reporting.excel');


## DNR Report ##
        Route::get('dnr/reporting', 'DnrController@getDnr')->name('dnr.index');
        Route::get('dnr/data', 'DnrController@dnrData')->name('dnr.data');
        Route::get('dnr/excel/{tracking_id?}', 'DnrController@dnrExcel')->name('dnr.export');


        Route::get('activity', 'ActivityController@getindex')->name('activity');


        ###Ctc Sub Admins###
        Route::get('ctc/subadmins', 'CtcSubAdminController@getIndex')->name('ctc-subadmin.index');
        Route::get('ctc/subadmin/list', 'CtcSubAdminController@subAdminList')->name('ctc-subadmin.data');
        Route::post('ctc/subadmin/create', 'CtcSubAdminController@create')->name('ctc-subadmin.create');
        Route::get('ctc/subadmin/profile/{id}', 'CtcSubAdminController@profile')->name('ctc-subadmin.profile');
        Route::get('ctc/subadmin/edit/{user}', 'CtcSubAdminController@edit')->name('ctc-subadmin.edit');
        Route::put('ctc/subadmin/update/{user}', 'CtcSubAdminController@update')->name('ctc-subadmin.update');
        Route::delete('ctc/subadmin/{user}', 'CtcSubAdminController@destroy')->name('ctc-subadmin.delete');
        Route::get('ctc/subadmin/active/{record}', 'CtcSubAdminController@active')->name('ctc-subadmin.active');
        Route::get('ctc/subadmin/inactive/{record}', 'CtcSubAdminController@inactive')->name('ctc-subadmin.inactive');

        ### Micro Hub First Mile ###

        //View micro hub first mile hub
        Route::get('first/mile/hub/list', 'FirstMileHubController@fisrtMileHubList')->name('first.mile.hub.list');
        //create job id for first mile route by routific
        Route::post('first/mile/routes/store', 'FirstMileRoutingController@storeFirstMileRoute')->name('first.mile.route.store');
        //first mile order count
        Route::get('first/mile/order/count/hub_id/{id}/date/{date}', 'FirstMileHubController@getFirstMileOrderCount')->name('first.mile.order.count');
        //list first mile slots
        Route::get('first/mile/slots/list/hub_id/{id}', 'FirstMileHubController@slotsListData')->name('first.mile.slots.list');
        //create first mile slots
        Route::post('first/mile/slots/create', 'FirstMileSlotController@storeFirstMileSlot')->name('first.mile.slot.store');
        //get data of first mile slots
        Route::get('first/mile/slot/{id}/edit', 'FirstMileSlotController@getFirstMileEditSlot')->name('first.mile.slot.edit');
        // update data of first mile slot
        Route::post('first/mile/slot/update', 'FirstMileSlotController@firstMileSlotUpdate')->name('first.mile.slot.update');
        //delete first mile slot
        Route::post('first/mile/slot/delete', 'FirstMileSlotController@firstMileSlotDelete')->name('first.mile.slot.delete');
        //get detail of First mile slot
        Route::get('first/mile/slot/{id}/detail', 'FirstMileSlotController@getDetailOfFirstMile')->name('first.mile.slot.detail');
        //First mile job list
        Route::get('first/mile/jobs', 'FirstMileJobController@getFirstMileJobList')->name('first.mile.job.list');
        //create route for first mile
        Route::get('first/mile/create/{id}/route', 'FirstMileJobController@createRouteForFirstMile')->name('first.mile.create.route');
        //delete job of first mile
        Route::post('first/mile/job/delete', 'FirstMileJobController@deleteFirstMileJob')->name('first.mile.job.delete');
        //route list of first mile
        Route::get('first/mile/routes/list', 'FirstMileRouteController@firstMileRoutesList')->name('first.mile.route.list');
        // get route detail for first mile
        Route::get('first/mile/route/{id}/details', 'FirstMileRouteController@getRouteDetail')->name('first.mile.route.detail');
        // edit route of first mile
        Route::get('first/mile/route/{di}/edit/hub/{id}', 'FirstMileRouteController@firstMileRouteEdit')->name('first.mile.routes.edit');
        //transfer route to joey
        Route::post('first/mile/route/transfer', 'FirstMileRouteController@routeTransfer')->name('first.mile.transfer.route');
        // get route in map for first mile
        Route::get('first/mile/route/{id}/map', 'FirstMileRouteController@RouteMap')->name('first.mile.route-map');
        // delete route
        Route::get('first/mile/route/{id}/delete', 'FirstMileRoutingController@firstMileDeleteRoute')->name('first.mile.route.delete');
        // re route for first mile
        Route::get('first/mile/hub/{hubId}/re_route/{id}', 'FirstMileRouteController@reRoute')->name('first.mile.re.route');
        // get route history Of first mile
        Route::get('first/mile/route/{id}/history', 'FirstMileRoutingController@getFirstMileRouteHistory')->name('first.mile.route.history');
        // get vendor order of route
        Route::get('first/mile/vendor/{vendorId}/orders/{routeId}', 'FirstMileRouteController@vendorOrderDetails')->name('first.mile.vendor.orders');
        ######
        Route::get('first-mile-summary', 'FirstMileReportingController@getFirstMileReporting')->name('first_mile_reporting.index');
        Route::get('last-mile-reporting-data', 'FirstMileReportingController@getFirstMileReportingData')->name('first_mile_reporting_data.data');

        ### Micro Hub First Mile

        ### Mid Mile Work Start 2022-05-24

//        Mid Mile Hubs List With their Orders
//        Route::get('mid/mile', 'MidMileController@index')->name('mid.mile.index');
        //get microhub mid mile mi job
        Route::get('mid/mile/mi/job', 'MidMileController@miJob')->name('mid.mile.mi.job');
        // get detail of mi job
        Route::get('mid/mile/mi/job/{mi_job}/detail', 'MidMileController@detail')->name('mid.mile.mi.job.detail');
        //mid mile submit for route
        Route::post('mid/mile/create/route', 'MidMileController@createRouteForMidMile')->name('mid.mile.create.route');
        // mid mile route list
        Route::get('mid/mile/routes/list', 'MidMileController@midMileRoutesList')->name('mid.mile.route.list');
        // mid mile route detail.
        Route::get('mid/mile/route/{id}/detail', 'MidMileController@getRouteDetail')->name('mid.mile.route.detail');
        // edit route of mid mile
        Route::get('mid/mile/route/{id}/edit', 'MidMileController@midMileRouteEdit')->name('mid.mile.route.edit');
        // detail of bundle orders in edit page
        Route::get('mid/mile/hub/{hub_id}/order/{bundle_id}', 'MidMileController@bundleOrderDetails')->name('mi.job.edit');
        // mid mile route  transfer to joey
        Route::post('mid/mile/route/transfer', 'MidMileController@routeTransfer')->name('mid.mile.route.transfer');
        // mid mile route on map
        Route::get('mid/mile/route/{id}/map', 'MidMileController@RouteMap')->name('mid.mile.route.map');
        // delete route
        Route::get('mid/mile/route/{id}/delete', 'MidMileController@midMileDeleteRoute');

//        Route::put('mi_job/{miJob}/update', 'MiJobController@update')->name('mi.job.update');
  
        // create job by mi job request
//        Route::post('mid/mile/mi/job/create/route', 'MidMileController@createJob')->name('mid.mile.mi.job.create.route');
//        // delete mi job
//        Route::get('mid/mile/mi_job/delete/{id}', 'MidMileController@destroy')->name('mi.job.destroy');
//        //first mile order count
//        Route::get('mid/mile/order/count/hub_id/{id}/date/{date}', 'MidMileController@getMidMileOrderCount')->name('mid.mile.order.count');
//        //create job id for first mile route by routific
//        Route::post('mid/mile/job/store', 'MidMileController@createJobIdForMidMile')->name('mid.mile.job.id');
//        //list first mile slots
//        Route::get('mid/mile/slots/list/hub_id/{id}', 'MidMileController@slotsListData')->name('first.mile.slots.list');
//        //create first mile slots
//        Route::post('mid/mile/slots/create', 'MidMileController@storeMidMileSlot')->name('mid.mile.slot.store');
//        //get data of mid mile slots
//        Route::get('mid/mile/slot/{id}/edit', 'MidMileController@getMidMileEditSlot')->name('mid.mile.slot.edit');
//        // update data of mid mile slot
//        Route::post('mid/mile/slot/update', 'MidMileController@midMileSlotUpdate')->name('mid.mile.slot.update');
//        //delete mid mile slot
//        Route::post('mid/mile/slot/delete', 'MidMileController@midMileSlotDelete')->name('mid.mile.slot.delete');
//        //get detail of First mile slot
//        Route::get('mid/mile/slot/{id}/detail', 'MidMileController@getDetailOfMidMile')->name('mid.mile.slot.detail');
        //First mile job list
//        Route::get('mid/mile/jobs', 'MidMileController@getMidMileJobList')->name('mid.mile.job.list');
        //create route for mid mile

//        //delete job of mid mile
//        Route::post('mid/mile/job/delete', 'MidMileController@deleteMidMileJob')->name('mid.mile.job.delete');
//        //route list of first mile

//        // route detail.
//        Route::get('mid/mile/route/{id}/details', 'MidMileController@getRouteDetail')->name('mid.mile.route.detail');
//
//
//        // route history of mid mile
//        Route::get('mid/mile/route/{id}/history', 'MidMileController@getMidMileRouteHistory');
//        // edit route of mid mile
//        Route::get('mid/mile/route/{di}/edit/hub/{id}', 'MidMileController@midMileRouteEdit');
//        // delete route
//        Route::get('mid/mile/route/{id}/delete', 'MidMileController@midMileDeleteRoute');


        ### Mid MIle Work

        ### Last Mile ###
        // last mile zones listing
        Route::get('last/mile/zones', 'LastMileController@getZonesLastMile')->name('last.mile.zone.index');
        // last mile zone create
        Route::post('last/mile/zone/create', 'LastMileController@lastMileZonesCreation')->name('last.mile.zone.create');
        // last mile zone edit modal
        Route::get('last/mile/zone/{id}', 'LastMileController@getLastMileZoneInModal')->name('last.mile.zone.edit');
        // last mile zone update
        Route::post('last/mile/zone/update', 'LastMileController@lastMileZoneUpdate')->name('last.mile.zone.update');
        //last mile zone delete
        Route::post('last/mile/zone/delete', 'LastMileController@lastMileZoneDelete')->name('last.mile.zone.delete');
        // last mile order count
        Route::get('last/mile/zone/order/count/{date}/{del_id}', 'LastMileController@lastMileZoneOrderCount')->name('last.mile.zone.order.count');
        // get total order count not in route of last mile
        Route::post('last/mile/total/order/notinroute', 'LastMileController@listMileTotalOrderNotInRoute')->name('last.mile.total.order.not_in_route');
        // last mile slots list
        Route::get('last/mile/slots/list/hubid/{id}/zoneid/{zoneid}', 'LastMileSlotController@lastMileSlotList')->name('last.mile.slot.index');
        // last mile slots create
        Route::post('last/mile/slot/create', 'LastMileSlotController@createLastMileSlot')->name('last.mile.slot.create');
        //get edit detail route of last mile
        Route::get('last/mile/slot/{id}', 'LastMileSlotController@getLastMileEdit')->name('last.mile.slot.edit');
        //update record of last mile slot
        Route::post('last/mile/slot/update', 'LastMileSlotController@lastMileSlotUpdate')->name('last.mile.slot.update');
        //get detail of last mile slot
        Route::get('last/mile/slot/{id}/detail', 'LastMileSlotController@getDetailLastMile')->name('last.mile.slot.detail');
        // delete last mile slot
        Route::post('last/mile/slot/delete', 'LastMileSlotController@deleteLastMileSlot')->name('last.mile.slot.delete');
        // create job id for last mile route
        Route::post('last/mile/routes/add', 'LastMileJobController@createLastMileJobId')->name('last.mile.job.id');
        //get routific job list of last mile
        Route::get('last/mile/job/list', 'LastMileJobController@getLastMileJobList')->name('last.mile.job.index');
        // deleted Last Mile Job
        Route::post('last/mile/job/delete', 'LastMileJobController@deleteLastMileJob')->name('last.mile.job.delete');
        // create route for last mile
        Route::get('last/mile/create/{id}/route', 'LastMileJobController@createRouteForLastMile')->name('last.mile.create.route');
        // get routes for last mile
        Route::get('last/mile/routes/list', 'LastMileRouteController@lastMileRoutesList')->name('last.mile.route.list');
        //get route detail of last mile
        Route::get('last/mile/route/{id}/details', 'LastMileRouteController@getRouteDetail')->name('last.mile.route.detail');
       // get routes for last mile only
       Route::get('last/mile/routes/only/list', 'LastMileRouteController@lastMileRoutesOnlyList')->name('last.mile.route.list');
       Route::get('last/mile/route/{id}/details', 'LastMileRouteController@getRouteDetail')->name('last.mile.route.detail');
        // edit route of first mile
        Route::get('last/mile/route/{di}/edit/hub/{id}', 'LastMileRouteController@lastMileRouteEdit')->name('last.mile.routes.edit');
        // get route in map for first mile
        Route::get('last/mile/route/{id}/map', 'LastMileRouteController@RouteMap')->name('last.mile.route.map');
        // get remaining route of last mile
        Route::get('last/mile/route/{id}/remaining', 'LastMileRouteController@remainingRouteMap')->name('last.mile.remaining.route.map');
        // delete route
        Route::get('last/mile/route/{id}/delete', 'LastMileRouteController@lastMileDeleteRoute')->name('last.mile.route.delete');
        // re route for last mile
        Route::get('last/mile/hub/{hubId}/re_route/{id}', 'LastMileRouteController@reRoute')->name('first.mile.re.route');
        // route history of last mile
        Route::get('last/mile/route/{id}/history', 'LastMileRouteController@getLastMileRouteHistory')->name('last.mile.route.history');
        // route transfer of last mile
        Route::post('last/mile/route/transfer', 'LastMileRouteController@routeTransfer')->name('last.mile.route.transfer');

        // unavailable orders route
        Route::get('mark/incomplete', 'RemoveUnavailableOrdersController@getMarkIncomplete');
        Route::post('mark/incomplete', 'RemoveUnavailableOrdersController@markIncomplete')->name('mark-incomplete.update');

        ###Order Label###
        Route::get('label-order-index', 'OrderLabelController@getIndex');
        Route::get('label-order-data', 'OrderLabelController@getOrderLabelData')->name('label-order.data');
        Route::get('label-order/{id}', 'OrderLabelController@labelOrderPrint')->name('label-order.printLabel');

        ###Client Setting###
        Route::get('label-setting/{id}','ClientSettingController@labelSetting')->name('label-setting');
        Route::post('label-setting', 'ClientSettingController@labelSizeCreate')->name('label-size-create');

        ###claims open###
        route::get('claims/search-orders/details/{id}', 'JoeycoClaimController@get_trackingorderdetails')->name('searchorder.show');
        Route::post('claims/get-reasons', 'JoeycoClaimController@getReasons')->name('claims.getReasons');
        Route::post('claims/status-upload-image', 'JoeycoClaimController@uploadImage')->name('claims.uploadImage');
        Route::post('claims/validate-TrackingId', 'JoeycoClaimController@validateTrackingId')->name('claims.validateTrackingId');
        Route::get('claims/pending-list', 'JoeycoClaimController@pendingList')->name('claims.pendingList');
        Route::get('claims/pending-list-data', 'JoeycoClaimController@pendingListData')->name('claims.pendingList-data');
        Route::get('claims/approved-list', 'JoeycoClaimController@approvedList')->name('claims.approvedList');
        Route::get('claims/approved-list-data', 'JoeycoClaimController@approvedListData')->name('claims.approvedList-data');
        Route::get('claims/not-approved-list', 'JoeycoClaimController@notApprovedList')->name('claims.notApprovedList');
        Route::get('claims/not-approved-list-data', 'JoeycoClaimController@notApprovedListData')->name('claims.notApprovedList-data');
        Route::get('claims/re-submitted-list', 'JoeycoClaimController@reSubmittedList')->name('claims.reSubmittedList');
        Route::get('claims/re-submitted-list-data', 'JoeycoClaimController@reSubmittedListData')->name('claims.reSubmittedList-data');
        Route::post('claims/status-update', 'JoeycoClaimController@statusUpdate')->name('claims.statusUpdate');
        Route::post('claims/reason-update', 'JoeycoClaimController@validateTrackingId')->name('claims.reasonUpdate');

        Route::get('claims/delete/{id}', 'JoeycoClaimController@destroy')->name('pendingClaims.delete');
        Route::get('claims/approved/delete/{id}', 'JoeycoClaimController@destroy')->name('approvedClaims.delete');
        Route::get('claims/reject/delete/{id}', 'JoeycoClaimController@destroy')->name('rejectClaims.delete');
        Route::get('claims/re-submitted/delete/{id}', 'JoeycoClaimController@destroy')->name('re-submittedClaims.delete');

        Route::resource('claims', 'JoeycoClaimController');
        ###claims close###

        ### joeys open ###

        ###joeys_management ###
        Route::get('joeys', 'MicroHubJoeysManagementController@statistics')->name('joeysManagement.index');
        Route::get('joey/joeysOnDuty', 'MicroHubJoeysManagementController@joeysOnDuty')->name('joeysOnDuty');
        Route::get('joey/totalApplicationSubmissionTable', 'MicroHubJoeysManagementController@totalApplicationSubmissionTable')->name('joeys.totalApplicationSubmissionTable');
        Route::get('joey/totalQuizPassedTable', 'MicroHubJoeysManagementController@totalQuizPassedTable')->name('joeys.totalQuizPassedTable');
        Route::post('assign/joeys', 'MicroHubJoeysManagementController@addJoey')->name('addJoey');
        Route::get('joeysList', 'MicroHubJoeysManagementController@joeysList');

        ### joeys close ###


        ###Mark Delay Reason###
        Route::get('reason', 'ReasonController@getIndex')->name('reason.index');
        Route::get('reason/list', 'ReasonController@ReasonList')->name('reason.data');
        Route::get('reason/add', 'ReasonController@add')->name('reason.add');
        Route::post('reason/create', 'ReasonController@create')->name('reason.create');
        Route::get('reason/edit/{reason}', 'ReasonController@edit')->name('reason.edit');
        Route::put('reason/update/{reason}', 'ReasonController@update')->name('reason.update');
        Route::delete('reason/{reason}', 'ReasonController@destroy')->name('reason.delete');

        ###Sub Admins###
        Route::get('subadmins', 'SubadminController@getIndex')->name('sub-admin.index');
        Route::get('sub/admins/list', 'SubadminController@subAdminList')->name('subAdmin.data');
        Route::get('subadmin/add', 'SubadminController@add')->name('subAdmin.add');

        Route::post('subadmin/create', 'SubadminController@create')->name('subAdmin.create');

        Route::get('sub/admin/profile/{id}', 'SubadminController@profile')->name('subAdmin.profile');

        Route::get('subadmin/edit/{user}', 'SubadminController@edit')->name('subAdmin.edit');

        Route::post('subadmin/update/{user}', 'SubadminController@update')->name('subAdmin.update');

        Route::delete('sub/admin/{user}', 'SubadminController@destroy')->name('subAdmin.delete');
        Route::delete('changeStatus', 'SubadminController@changeStatus');
        Route::get('sub-admin/active/{record}', 'SubadminController@active')->name('sub-admin.active');
        Route::get('sub-admin/inactive/{record}', 'SubadminController@inactive')->name('sub-admin.inactive');

        Route::get('account/security/edit/{user}', 'SubadminController@accountSecurityEdit')->name('account-security.edit');

        Route::put('account/security/{user}', 'SubadminController@accountSecurityUpdate')->name('account-security.update');

        Route::get('changepwd', 'SubadminController@getChangePwd')->name('sub-admin-change.password');

        //add route name for change password issue
        Route::post('changepwd/create', 'SubadminController@changepwd')->name('sub-admin-create.password');

        //Admin Edit Route
        Route::get('adminedit/{user}', 'SubadminController@adminedit');

        Route::put('admin/update/{user}', 'SubadminController@adminupdate');

        /*role management routes*/
        Route::resource('role', 'RoleController');
        Route::get('role/set-permissions/{role}', 'RoleController@setpermissions')->name('role.set-permissions');
        Route::post('role/set-permissions/update/{role}', 'RoleController@setpermissionsupdate')->name('role.set-permissions.update');

//        Route::get('adminupdate/{user}', 'SubadminController@adminupdate');

        //Loblaws Batch Order Re-Processing 
        Route::get('loblaws/order/reprocessing', 'LoblawsController@get_scheduleOrders')->name('loblaws.order-reprocessing');
        Route::post('loblaws/order/reprocessing', 'LoblawsController@post_resheduledOrder')->name('loblaws.order-reprocessing-update');

        Route::resource('manager', 'ManagerController');
        Route::post('warehouse/check-for-hub', 'WarehouseSorterController@checkForHub')->name('check-for-hub');
        //permission middleware
        /*   });*/
        ###Scanning Bundle Order With Respect Hub ###
        Route::get('scanning-bundle/order', 'ScanningBundleOrdersController@getIndex')->name('scanning-bundle.index');
        ###Scan Tracking Id ####
        Route::get('scanning-bundle/order/search', 'ScanningBundleOrdersController@searchTrackingId')->name('scanning-bundle.search');
        Route::get('scanning-bundle/label/{id}', 'ScanningBundleOrdersController@scanningOrder')->name('scanning-bundle.printLabel');
        Route::get('scanning-bundle/detail/{id}', 'ScanningBundleOrdersController@scanningOrderDetail')->name('scanning-bundle.detail');

        ###Scanning Bundle Order With Respect Hub ###
        Route::get('bundle-scanning/list', 'ScanningBundleOrdersController@getScannedBundles')->name('bundle-scanning.list');
        Route::get('bundle-scanning/search', 'ScanningBundleOrdersController@searchBundlesId')->name('bundle-scanning.search');

        Route::get('search/tracking', 'SearchOrdersController@SearchTracking')->name('search-tracking.index');
        Route::get('search/tracking-details/{id}', 'SearchOrdersController@SearchTrackingDetails')->name('searchtrackingdetails.show');

        // Custom Routing for last mile
        Route::get('custom/routing/{id}/hub', 'CustomRoutingController@getIndex');
        Route::get('tracking/detail','CustomRoutingController@getTrackingIdDetail');
        Route::post('create/route/custom/routing','CustomRoutingController@postCreateRoute'); 
        Route::post('custom/create/order','CustomRoutingController@postCreateOrder');
        Route::post('custom/edit/order','CustomRoutingController@editOrder');
        Route::post('custom/add/joey/count','CustomRoutingController@addJoeyCount');
        Route::get('custom/joey/count','CustomRoutingController@getJoeyCountDetail');
        Route::post('custom/edit/joey/count','CustomRoutingController@updateJoeyCountDetail');
        Route::post('remove/joeycount','CustomRoutingController@deleteJoeyCount');
        Route::post('remove/multipletrackingid','CustomRoutingController@multipleRemoveTrackingid');
    });

});

Route::get('dashboard-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('php-info', static function () {
});

Route::get('reset-cache', static function () {
    Artisan::call('cache:clear');
    dd('Reset Cache');
});

Route::get('config-clear', static function () {
    Artisan::call('config:clear');
    dd('Config Clear');
});


Route::get('/test', function () {

    $userCreated = App\User::where('email', 'abk@gmail.com');

    $emailBody = 'test';

    \Mail::raw($emailBody, function ($m) use ($userCreated) {

        $m->to($userCreated->email)->from(env('MAIL_USERNAME'))->subject('Welcome on Board - ValuationApp');

    });

});





       