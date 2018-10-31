<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PagesController@getIndex');
Route::get('/projekt', 'PagesController@getChoice');
Route::get('/form', 'PagesController@getForm');
Route::get('/badania', 'PagesController@getResearch');
Route::get('/wysylka', 'PagesController@getShipment');
Route::get('/getCity', 'PagesController@getCity');
Route::post('/storageResearch', 'PagesController@storageResearch');
Route::post('/gererateCSV', 'PagesController@gererateCSV');
Route::post('/searchFromData', 'PagesController@searchFromData');
Route::post('/getDepname', 'PagesController@getDepname');
Route::get('/getWoj', 'PagesController@getWoj');
Auth::routes();
Route::get('/home', 'HomeController@index');
Route::get('/getWojByCity', 'PagesController@getWojByCity');


//Do pobierania CSV Historia
Route::get('/historia', 'packageController@getPackage');
Route::post('/historyCSV', 'packageController@HistoryCSV');
Route::get('/historyCSVDownload', 'packageController@historyCSVDownload');
Route::post('/getCSVFile', 'packageController@getCSVFile');

// Odblokowywanie rekordów
Route::get('/odblokowanie', 'UnlockController@getRecords');
Route::POST('/odblokowanie', 'UnlockController@getRecordsPost');
Route::POST('/unlockredord', 'UnlockController@UnlockRecordsPost');
//Blokowanie rekordów przez csv
Route::Post('/zablokowanieCSV', 'UnlockController@showDate');
Route::Post('/zablokowanieCSVsave', 'UnlockController@LockData');

//Sciezki nowej bazy zgod
Route::get('/zgody', 'AgreesController@getNewBase');
Route::post('/storageResearchAgree', 'AgreesController@storageResearchAgree');
Route::get('/gererateCSVAgree', 'AgreesController@gererateCSVAgree');
Route::post('/searchFromDataAgree', 'AgreesController@searchFromData');




//Ściezki do wgrywania danych do bazy przez csv
Route::get('/wgrajEvent', 'UploadsController@getUploadEvent');
Route::get('/wgrajZgody', 'UploadsController@getUploadAgree');
Route::get('/wgrajBisnode', 'UploadsController@getUploadBisnode');
Route::get('/wgrajPomylki', 'UploadsController@getUploadMistake');

Route::post('/wgrajZgody', 'UploadsController@showDate');
Route::post('/wgrajEvent', 'UploadsController@showDate');
Route::post('/wgrajBisnode', 'UploadsController@showDate');
Route::post('/wgrajPomylki', 'UploadsController@showDate');

Route::post('/save', 'UploadsController@save');

//*************************RAPORTY*******************************


Route::get('/raport', 'RaportsController@getraport');
Route::post('/raport', 'RaportsController@setRaport');

Route::get('/getRaportDayAPI/{id}', 'RaportsController@getRaportDayAPI');


Route::get('/getRaportCityInfoAPI/{id}', 'RaportsController@getRaportCityInfoAPI');

Route::get('/raportuzytkownika', 'RaportsController@getraportuser');
Route::post('/raportuzytkownika', 'RaportsController@setraportuser');


Route::get('/raportplus', 'RaportsController@getraportplus');
Route::post('/raportplus', 'RaportsController@setraportplus');

Route::get('/raportuserplus', 'RaportsController@getraportuserplus');
Route::post('/raportuserplus', 'RaportsController@setraportuserplus');

Route::get('/setdata', 'RaportsController@setdata');

Route::get('/getRaportNewBaseWeek', 'RaportsController@getRaportNewBaseWeek');
Route::get('/getRaportNewBaseMonth', 'RaportsController@getRaportNewBaseMonth');
//*************************Planer*******************************
Route::get('/planer', 'PlanerController@addroute');

//************************Inne*********************************
Route::get('/tempInsertData', 'UploadsController@tempInsertDataGet');
Route::post('/tempInsertData', 'UploadsController@tempInsertDataPost');

Route::get('/tempInsertData', 'UploadsController@tempInsertDataGet');

Route::get('/locking', 'LockingController@lockGet');
Route::post('/lock', 'LockingController@lockPost')->name('api.lockPostAjax');
Route::post('/lockMultiAjax', 'LockingController@lockMultiAjax')->name('api.lockMultiAjax');
Route::post('/lockMultiSecondAjax', 'LockingController@lockMultiSecondAjax')->name('api.lockMultiSecondAjax');
Route::post('/datatableLockAjax', 'LockingController@datatableLockAjax')->name('api.datatableLockAjax');

Route::get('/phonenumberZipCodes', 'AnalizeController@getPhonenumberZipCodes');
Route::post('/postPhonenumberZipCodes', 'AnalizeController@postPhonenumberZipCodes');

Route::get('/phoneNumberText', 'AnalizeController@phoneNumberTextGet');
Route::post('/phoneNumberText', 'AnalizeController@phoneNumberTextPost');
