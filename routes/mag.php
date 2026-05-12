<?php

use App\Http\Controllers\MagPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/c', [MagPortalController::class, 'frontend'])
    ->name('mag.frontend');

Route::get('/public/c', [MagPortalController::class, 'frontend'])
    ->name('mag.frontend.compat');

Route::get('/c/api/bootstrap', [MagPortalController::class, 'frontendBootstrap'])
    ->name('mag.frontend.api.bootstrap');

Route::get('/public/c/api/bootstrap', [MagPortalController::class, 'frontendBootstrap'])
    ->name('mag.frontend.api.bootstrap.compat');

Route::get('/c/api/section/{section}', [MagPortalController::class, 'frontendSection'])
    ->name('mag.frontend.api.section');

Route::get('/public/c/api/section/{section}', [MagPortalController::class, 'frontendSection'])
    ->name('mag.frontend.api.section.compat');

Route::get('/c/api/network/{network}', [MagPortalController::class, 'frontendNetwork'])
    ->name('mag.frontend.api.network');

Route::get('/public/c/api/network/{network}', [MagPortalController::class, 'frontendNetwork'])
    ->name('mag.frontend.api.network.compat');

Route::get('/c/api/content/{type}/{id}', [MagPortalController::class, 'frontendContentDetail'])
    ->name('mag.frontend.api.content');

Route::get('/public/c/api/content/{type}/{id}', [MagPortalController::class, 'frontendContentDetail'])
    ->name('mag.frontend.api.content.compat');

Route::get('/c/api/play/{type}/{id}', [MagPortalController::class, 'frontendPlay'])
    ->name('mag.frontend.api.play');

Route::get('/public/c/api/play/{type}/{id}', [MagPortalController::class, 'frontendPlay'])
    ->name('mag.frontend.api.play.compat');

Route::get('/c/api/search', [MagPortalController::class, 'frontendSearch'])
    ->name('mag.frontend.api.search');

Route::get('/public/c/api/search', [MagPortalController::class, 'frontendSearch'])
    ->name('mag.frontend.api.search.compat');

Route::get('/c/{asset}', [MagPortalController::class, 'frontendAsset'])
    ->where('asset', '.*')
    ->name('mag.frontend.asset');

Route::get('/public/c/{asset}', [MagPortalController::class, 'frontendAsset'])
    ->where('asset', '.*')
    ->name('mag.frontend.asset.compat');

Route::match(['GET', 'POST'], '/mag/portal.php', [MagPortalController::class, 'handle'])
    ->name('mag.portal');

Route::match(['GET', 'POST'], '/mag/server/load.php', [MagPortalController::class, 'handle'])
    ->name('mag.server.load');

Route::get('/mag/stream/{token}', [MagPortalController::class, 'stream'])
    ->name('mag.stream');

// Android-TV Live TV portal APIs
Route::get('/mag/api/slider',       [MagPortalController::class, 'apiSlider'])->name('mag.api.slider');
Route::get('/mag/api/languages',    [MagPortalController::class, 'apiLanguages'])->name('mag.api.languages');
Route::get('/mag/api/genres',       [MagPortalController::class, 'apiGenres'])->name('mag.api.genres');
Route::post('/mag/api/channels',    [MagPortalController::class, 'apiChannels'])->name('mag.api.channels');
Route::get('/mag/api/ott-networks',     [MagPortalController::class, 'apiOttNetworks'])->name('mag.api.ott.networks');
Route::get('/mag/api/movies/networks',      [MagPortalController::class, 'apiMovieNetworks'])->name('mag.api.movies.networks');
Route::get('/mag/api/movies/genres',        [MagPortalController::class, 'apiMovieGenres'])->name('mag.api.movies.genres');
Route::get('/mag/api/movies/contents',      [MagPortalController::class, 'apiMovieContents'])->name('mag.api.movies.contents');
Route::get('/mag/api/webseries/networks',   [MagPortalController::class, 'apiWebSeriesNetworks'])->name('mag.api.webseries.networks');
Route::get('/mag/api/webseries/genres',     [MagPortalController::class, 'apiWebSeriesGenres'])->name('mag.api.webseries.genres');
Route::get('/mag/api/webseries/contents',   [MagPortalController::class, 'apiWebSeriesContents'])->name('mag.api.webseries.contents');
Route::get('/mag/api/tvshows/networks',     [MagPortalController::class, 'apiTvShowNetworks'])->name('mag.api.tvshows.networks');
Route::get('/mag/api/tvshows/genres',       [MagPortalController::class, 'apiTvShowGenres'])->name('mag.api.tvshows.genres');
Route::get('/mag/api/tvshows/contents',     [MagPortalController::class, 'apiTvShowContents'])->name('mag.api.tvshows.contents');
Route::get('/mag/api/kids/networks',        [MagPortalController::class, 'apiKidsNetworks'])->name('mag.api.kids.networks');
Route::get('/mag/api/kids/genres',          [MagPortalController::class, 'apiKidsGenres'])->name('mag.api.kids.genres');
Route::get('/mag/api/kids/contents',        [MagPortalController::class, 'apiKidsContents'])->name('mag.api.kids.contents');
