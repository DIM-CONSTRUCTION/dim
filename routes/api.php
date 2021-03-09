<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register','Api\AuthController@register');
Route::post('/login','Api\AuthController@login');
Route::post('/loginFb','Api\AuthController@loginFb');
Route::post('/logout','Api\AuthController@logout');


Route::post('/getProjectsList','Api\MobileController@getProjectsList');
Route::post('/getReportList','Api\MobileController@getReportList');
Route::post('/getActivitiesList','Api\MobileController@getActivitiesList');
Route::post('/getAreaList','Api\MobileController@getAreaList');
Route::post('/getFormActivitiesList','Api\MobileController@getFormActivitiesList');
Route::post('/setDailyReport','Api\MobileController@setDailyReport');
Route::post('/getFormActivity','Api\MobileController@getFormActivity');
Route::post('/getGangDetails','Api\MobileController@getGangDetails');
Route::post('/getEquipmentDetails','Api\MobileController@getEquipmentDetails');
Route::post('/setFormActivity','Api\MobileController@setFormActivity');
Route::post('/getFormsList','Api\MobileController@getFormsList');

Route::post('/getDashboard','Voyager\VoyagerDashboardController@getDashboard');
Route::post('/getDashboardData','Voyager\VoyagerDashboardController@getDashboardData');
Route::post('/getChildDashboard','Voyager\VoyagerDashboardController@getChildDashboard');


Route::post('/getAreas','Voyager\VoyagerFormController@getAreas');

Route::post('/getDivisions','Voyager\VoyagerFormController@getDivisions');
Route::post('/addActivity','Voyager\VoyagerFormController@addActivity');
Route::post('/getActivties','Voyager\VoyagerFormController@getActivties');
Route::post('/updateActivity','Voyager\VoyagerFormController@updateActivity');
Route::get('/duplicate/{slug}','Voyager\VoyagerFormController@duplicateFields');


Route::post('/getGangs','Voyager\VoyagerFormController@getGangs');
Route::post('/addGang','Voyager\VoyagerFormController@addGang');
Route::post('/seachGangs','Voyager\VoyagerFormController@seachGangs');
Route::post('/updateGang','Voyager\VoyagerFormController@updateGang');
Route::post('/deleteActivity','Voyager\VoyagerFormController@deleteActivity');


Route::post('/getEquipments','Voyager\VoyagerFormController@getEquipments');
Route::post('/addEquipment','Voyager\VoyagerFormController@addEquipment');
Route::post('/seachEquipments','Voyager\VoyagerFormController@seachEquipments');
Route::post('/updateEquipment','Voyager\VoyagerFormController@updateEquipment');

Route::post('/getBenchmarks','Voyager\VoyagerInstantAnalysisController@getBenchmarks');
Route::post('/getForms','Voyager\VoyagerInstantAnalysisController@getForms');
Route::post('/getInstant','Voyager\VoyagerInstantAnalysisController@getInstant');
Route::post('/getData','Voyager\VoyagerInstantAnalysisController@getData');
Route::post('/getActivities','Voyager\VoyagerInstantAnalysisController@getActivities');
Route::post('/getAnalysisActivities','Voyager\VoyagerInstantAnalysisController@getAnalysisActivities');
Route::post('/getActivityAnalysis','Voyager\VoyagerInstantAnalysisController@getActivityAnalysis');

Route::post('/getChartsBenchmarks','Voyager\VoyagerChartsAnalysisController@getBenchmarks');
Route::post('/getChartsForms','Voyager\VoyagerChartsAnalysisController@getForms');
Route::post('/getChartsAnalysisActivities','Voyager\VoyagerChartsAnalysisController@getChartsAnalysisActivities');
Route::post('/getChartsActivityAnalysis','Voyager\VoyagerChartsAnalysisController@getChartsActivityAnalysis');
Route::post('/getChartAnalysis','Voyager\VoyagerChartsAnalysisController@getChartAnalysis');

Route::post('/getChildData','Voyager\VoyagerBenchmarkController@getChildData');
