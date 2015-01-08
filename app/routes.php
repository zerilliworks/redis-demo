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

use RedisDemo\Helpers\Enumerator;

Route::get('/', ['as' => 'home', function()
{
	return Redirect::to('interactive');
}]);

Route::get('register-guest', ['as' => 'register_guest', function()
{
    return View::make('register_guest');
}]);

Route::post('register-guest', ['before' => 'csrf', 'as' => 'register_guest', function()
{
    $user = new User();

    $user->name = Input::get('name');
    $user->save();
}]);

Route::get('interactive', ['as' => 'interactive', function()
{
    return View::make('interactive');
}]);

Route::post('query', ['as' => 'query_room', function()
{
    try {
        Config::set('database.redis.default.database', Session::get('current_db', 0));
        // See if there's a script to be run
        if(Input::get('script')) {
            Log::debug('Submitted Script: ' . Input::get('script'));
            $result = Redis::command('EVAL', [Input::get('script'), 0]);
            return json_encode($result);
        }

        $queryString = Input::get('query');

        $quotedMatches = [];

        // Pull out quoted strings
        preg_match_all('/".*"/U', $queryString, $quotedMatches);
        $replacements = ['\\\\_qs_1', '\\\\_qs_2', '\\\\_qs_3', '\\\\_qs_4', '\\\\_qs_5'];
        $queryString = preg_replace(array_map(function($item) { return "/$item/"; }, $quotedMatches[0]), $replacements, $queryString);
        // Explode args
        $queryBits = explode(' ', $queryString);

        // Rebuild array of command and args
        array_walk($queryBits, function(&$item, $idx) use ($quotedMatches) {
            $m = [];
            if(preg_match("/\\_qs_([0-9])/", $item, $m))
            {
                $item = $quotedMatches[0][$m[1] - 1];
            }
        });

        Log::debug(json_encode($queryBits));

        $commandName = array_shift($queryBits);
        if(strtolower($commandName) == "select") {
            Session::put('current_db', $queryBits[0]);
        }
        $result = Redis::command($commandName, $queryBits);

        return json_encode($result);
    } catch (Exception $e) {
        return Response::make("Error: " . $e->getMessage(), 500);
    }

}]);


Route::group(['before' => 'auth|auth.activated'], function()
{


    Route::get('rooms/{id}', ['as' => 'room_interactive', function($id)
    {
        return View::make('interactive')->with('room', Room::find($id));
    }]);


});


Route::group(['before' => 'auth|auth.admin'], function()
{
    Route::get('admin/approve', ['as' => 'admin_approve', function()
    {
        return View::make('admin.approve');
    }]);

    Route::get('admin/pending', ['as' => 'admin_pending', function()
    {
        if(Request::isAjax())
        {

        } else {
            return Redirect::route('admin_approve');
        }
    }]);
});

