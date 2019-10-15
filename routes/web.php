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

// Module M01 - Authentication and Profile
Route::fallback(function () { return redirect('error'); });


Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

Route::get('users/{username}', 'UserController@getProfile')->name('users');
Route::get('users/{username}/edit', 'UserController@editProfile')->name('users.edit');
Route::post('users/{username}', 'UserController@updateProfile');
Route::delete('users/{username}', 'UserController@deleteUser');

//Module M02 - Game Area

Route::get('/games/sell', 'GameController@getSellForm')->name('games.sell');
Route::post('/games/sell', 'GameController@addNewGame');
Route::get('/games/{id}', 'GameController@getGame')->name('games');
Route::delete('/games/{id}', 'GameController@deleteGame');
Route::get('/api/games/{id}/reviews/{load}', 'GameController@loadReviews');
Route::get('/games/{id}/edit', 'GameController@editGame')->name('games.edit');
Route::post('/games/{id}/edit', 'GameController@updateGame');
Route::post('/games/{id}/reviews', 'GameController@addReview');
Route::post('/games/{id}/reviews/edit', 'GameController@editReview');
Route::delete('/games/{id}/reviews/delete', 'GameController@deleteReview')->name('reviews.delete');
Route::post('/api/games/{id}/reviews/{user}', 'GameController@voteReview');

//Module M03 - Search Area

Route::get('/', 'SearchController@getHomepage')->name('homepage');
Route::get('/search', 'SearchController@getSearchPage')->name('search');

//Module M04 - User Area

Route::get('/users/{username}/library', 'UserAreaController@getLibrary')->name('users.library');
Route::post('/users/{username}/library/{game_id}', 'UserAreaController@downloadGame')->name('download');

Route::get('/users/{username}/favorites', 'UserAreaController@getFavorites')->name('users.favorites');
Route::post('/api/users/{username}/favorites/{game_id}', 'UserAreaController@addToFavorites');
Route::delete('/api/users/{username}/favorites/{game_id}', 'UserAreaController@removeFromFavorites');

Route::get('/users/{username}/sell', 'UserAreaController@getGamesForSale')->name('users.sell');
Route::delete('api/users/{username}/sell/{game_id}', 'UserAreaController@deleteGameRequest');

Route::get('users/{username}/purchases', 'UserAreaController@getPurchaseHistory')->name('users.purchases');

Route::get('/users/{username}/cart', 'UserAreaController@getCart')->name('users.cart');
Route::post('/api/users/{username}/cart/{game_id}', 'UserAreaController@addToCart');
Route::delete('/api/users/{username}/cart/{game_id}', 'UserAreaController@removeFromCart');
Route::post('/users/{username}/cart', 'UserAreaController@checkoutCart')->name('checkout');

// Module M05: Administration’s area

Route::get('admin/sales', 'AdminController@sales')->name('admin.sales');
Route::get('admin/users', 'AdminController@searchUsers')->name('admin.search_users');
Route::post('api/admin/users', 'AdminController@getUsers')->name('admin.getUsers');
Route::post('api/admin/users/{username}', 'AdminController@banUser');
Route::get('admin/pending', 'AdminController@pendingGames')->name('admin.pending_games');
Route::post('admin/games/{game_id}', 'AdminController@changeGameState');
Route::get('admin/categories', 'AdminController@categories')->name('admin.categories');
Route::post('api/admin/categories/add', 'AdminController@categoriesAdd');
Route::post('api/admin/categories/remove/{category_id}', 'AdminController@categoriesRemove');


//Module M06: Static Pages

Route::get('about', 'StaticController@about')->name('about');
Route::get('faq', 'StaticController@faq')->name('faq');
Route::get('error', 'StaticController@error404')->name('error');

//Password Recovery

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');