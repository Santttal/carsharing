<?php

Route::get('/', 'CarsharingController@index')->name('home');
Route::get('/radius', 'CarsharingController@editRadius')->name('radius');
Route::get('/logs', 'CarsharingController@logs')->name('logs');
Route::get('/cars', 'CarsharingController@cars')->name('cars');
Route::put('/radius', 'CarsharingController@updateRadius');
Route::put('/state', 'CarsharingController@state');
Route::put('/filters', 'CarsharingController@filters');
Route::put('/polygon/{polygon}', 'CarsharingController@updatePolygon');


