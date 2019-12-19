<?php
Route::get('/dashboard',  'DashboardController@index')->name('dashboard');


Route::get('/login', 'HomeController@index');
// --------------- //
// Manage Events  //
// ------------- //
Route::get('/event',  'EventController@index')->name('event');
Route::group(['prefix' => 'event', 'as' => 'event.'], function () {
	Route::get('/create', 'EventController@create')->name('create');
	Route::get('/details', 'EventController@details')->name('details');

	Route::get('/create_session', 'SessionController@index')->name('create_session');
	Route::get('/create_channel', 'ChannelController@index')->name('create_channel');
	Route::get('/room_capacity', 'RoomController@index')->name('room_capacity');
	Route::get('/create_room', 'RoomController@create')->name('create_room');

});

// --------------- //
// Manage Ticket  //
// ------------- //
Route::get('/ticket',  'TicketController@index')->name('ticket');
Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
	Route::get('/create', 'TicketController@create')->name('create');
});

Route::get('/dashboard',  'DashboardController@index')->name('dashboard');
Route::get('/dashboard',  'DashboardController@index')->name('dashboard');
Route::get('/dashboard',  'DashboardController@index')->name('dashboard');


Route::get('/create_channel', function () {
    return view('CreateChannel');
});
Route::get('/create_session', function () {
    return view('CreateSession');
});
Route::get('/create_room', function () {
    return view('CreateRoom');
});
Route::get('/room_capacity', function () {
    return view('RoomCapacity');
});

Route::get('/sign_in', function() {
    return view('sign_in');
});

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
