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

Route::get( '/', function () {
	return view( 'welcome' );
} );

Auth::routes();

Route::post( '/part/file/process', 'UploadController@upload' );
Route::delete( '/part/file/revert', 'UploadController@remove' );

Route::middleware( 'auth' )->group( function () {
	Route::get( '/home', 'HomeController@index' )->name( 'home' );
	Route::get( '/landing', 'LandingController@index' )->name( 'landing' );
	Route::get( '/parts', 'PartController@viewAll' )->name( 'part_view_all' );
	Route::get( '/part/create', 'PartController@create' )->name( 'part_create' );
	Route::get( '/part/edit/{id}', 'PartController@edit' )->name( 'part_edit' );
	Route::get( '/part/view/{id}', 'PartController@view' )->name( 'part_view' );

	Route::post( '/part/create/post', 'PartActionsController@createPart' );
	Route::post( '/part/set/add/post', 'PartActionsController@addPartSet' );
} );

Route::prefix( 'a' )->middleware( 'auth' )->group( function () {
	Route::post( 'parts', 'ApiParts@getPartsPage' );
	Route::post( 'sets', 'ApiParts@getPartSets' );

	Route::post( 'part/{id}/comment', 'ApiComments@postOne' );
	Route::get( 'part/{id}/comment', 'ApiComments@getAll' );

	Route::post( 'part/{id}/like', 'ApiLikes@like' );
	Route::get( 'part/{id}/like', 'ApiLikes@liked' );
	Route::get( 'user/likes', 'ApiLikes@likes' );

	Route::post( 'part/{id}/popularity', 'ApiPopularity@clicked' );
} );
