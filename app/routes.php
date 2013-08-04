<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello')->with('title', 'hello');
});

Route::get('objekter/{id}.bibsys', 'ObjectController@showByBibsysId');
Route::resource('objekter', 'ObjectController');

Route::get('emner/{id}', 'SubjectController@showById')->where('id', '[0-9]+');
Route::get('emner/{system}/{label}', 'SubjectController@showBySystemAndLabel');
Route::get('emner/{label}', 'SubjectController@showByLabel');
Route::resource('emner', 'SubjectController');

Route::get('ontologi', function() {
	return Redirect::action('OntosaurController@index');
});
Route::resource('ontosaur', 'OntosaurController');



/*Route::get('/subjects/{label_nb}/{system}', function($label_nb, $system) {
	$subj = Subject::where('system','=',$system)->where('label_nb','=',$label_nb)->first();
	return $subj;
});
*/
