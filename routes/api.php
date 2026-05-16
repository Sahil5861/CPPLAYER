<?php

use App\Http\Controllers\AppApiController;
use App\Http\Controllers\AppApiControllerV2;
use App\Http\Controllers\AppApiControllerV3;
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
use App\Http\Controllers\SecureVideoController;

// routes/api.php

// Both routes point to same controller
Route::get('env-test', function () {
    return env('APP_NAME');
});

Route::get('video/play/{token}/{filename}', [SecureVideoController::class, 'playVideo'])
    ->where('filename', '.*\.m3u8')
    ->name('video.play.m3u8');
Route::get('stream/{token}', [SecureVideoController::class, 'stream'])
    ->where('token', '.*');
// ========== SOLUTION 1: Simple Time-Based Token ==========
Route::post('v3/tokenizeUrlNew', [SecureVideoController::class, 'tokenizeUrl']); // Add your auth middleware

// Batch tokenize
Route::post('v3/batchTokenize', [SecureVideoController::class, 'batchTokenize']);

// ========== SOLUTION 2: Secure One-Time Token ==========
Route::post('v3/generateSecureToken', [SecureVideoController::class, 'generateSecureToken']);

Route::post('v3/generateMobileStream', [SecureVideoController::class, 'generateMobileStream']);

// Video playback endpoint (public - token is the auth)
Route::get('video/play/{token}', [SecureVideoController::class, 'playVideo']);
Route::get('/video/play/{token}.m3u8', [SecureVideoController::class, 'playVideo']); // ✅ With extension

// Debug/Check token status
Route::get('video/check/{token}', [SecureVideoController::class, 'checkTokenStatus']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/userregister',[AppApiController::class,'userRegister']);

Route::post('/login',[AppApiController::class,'login_pin']);
Route::post('/login_app',[AppApiController::class,'login_pin_app']);
Route::get('/get-slider',[AppApiController::class,'getSlider']);
Route::get('/get-channels',[AppApiController::class,'getChannels']);
Route::get('/get-channels-with-genre',[AppApiController::class,'getChannelsWithGenre']);
Route::get('/get-channels-with-genre-new',[AppApiController::class,'getChannelsWithGenreNew']);
Route::get('/get-channels-with-genre-popular',[AppApiController::class,'getChannelsWithGenrePopular']);
Route::get('/pages',[AppApiController::class,'pages']);
Route::post('/upload-profile-image',[AppApiController::class,'uploadProfile']);
Route::post('/update-profile',[AppApiController::class,'updateProfile']);
Route::get('/get-active-plan',[AppApiController::class,'getActivePlan']);
Route::get('/check-plan',[AppApiController::class,'checkPlan']);


// new 11 june
Route::get('/getAllMovies', [AppApiController::class, 'getAllMovies']);
Route::get('/getAllWebSeries', [AppApiController::class, 'getAllWebSeries']);
Route::get('/getSeasons/{id}', [AppApiController::class, 'getSeasons']);
Route::get('/getEpisodes/{id}/0', [AppApiController::class, 'getEpisodes']);

Route::get('/getWebSeriesDetails/{webseriesId}', [AppApiController::class, 'getWebSeriesDetails']);





Route::get('/getMoviePlayLinks/{id}/0', [AppApiController::class, 'getMoviePlayLinks']);

// Route::get('/getNetworks', [AppApiController::class, 'getNetworks']);
Route::post('/getNetworks', [AppApiController::class, 'getNetworks']);
Route::get('/getAllContentsOfNetwork/{networkId}', [AppApiController::class, 'getAllContentsOfNetwork']);
Route::get('/getMovieDetails/{contentId}', [AppApiController::class, 'getMovieDetails']);
Route::get('/searchContent/{searchTerm}/0', [AppApiController::class, 'searchContent']);
Route::get('/getFeaturedLiveTV', [AppApiController::class, 'getFeaturedLiveTV']);

Route::get('/getCustomImageSlider', [AppApiController::class, 'getCustomImageSlider']);


Route::get('/getTvChannels', [AppApiController::class, 'getTvChannels']);
Route::get('/getTvShows/{id}', [AppApiController::class, 'getTvShows']);
Route::get('/getShowSeasons/{id}', [AppApiController::class, 'getTvShowSeasons']);
Route::get('/getShowSeasonsEpisodes/{id}', [AppApiController::class, 'getTvShowEpisodes']);

// 30 june 2025

Route::get('getsportCategories', [AppApiController::class, 'getsportCategories']);
Route::get('getsportTournament/{id}', [AppApiController::class, 'getsportTournament']);
Route::get('getTouranamentSeasons/{id}', [AppApiController::class, 'getTouranamentSeasons']);
Route::get('getTouranamentSeasonsEvents/{id}', [AppApiController::class, 'getTouranamentSeasonsEvents']);


Route::get('/getReligiousChannels', [AppApiController::class, 'getReligiousChannel']);
Route::get('/getReligiousShows/{id}', [AppApiController::class, 'getReligiousShows']);
Route::get('/getReligiousShowsEpisodes/{id}', [AppApiController::class, 'getReligiousShowsEpisodes']);

Route::post('/getAllAbove18Movies', [AppApiController::class, 'getAllAbove18Movies']);

// 18 july 2025

Route::get('/getKidsChannels', [AppApiController::class, 'getKidsChannels']);
Route::get('/getKidsShows/{id}', [AppApiController::class, 'getKidsShows']);
Route::get('/getKidsShowSeasons/{id}', [AppApiController::class, 'getKidsShowSeasons']);
Route::get('/getKidsShowSeasonsEpisodes/{id}', [AppApiController::class, 'getKidShowEpisodes']);

Route::get('/getTvChannelsPak', [AppApiController::class, 'getTvChannelsPak']);
Route::get('/getTvShowsPak/{id}', [AppApiController::class, 'getTvShowsPak']);
Route::get('/getShowSeasonsPak/{id}', [AppApiController::class, 'getTvShowSeasonsPak']);
Route::get('/getShowSeasonsEpisodesPak/{id}', [AppApiController::class, 'getTvShowEpisodesPak']);

Route::get('/getAllStageShowsPak', [AppApiController::class, 'getAllStageShowsPak']);
Route::get('/getAllLaughterShows', [AppApiController::class, 'getAllLaughterShows']);


// 5 August 2025
Route::get('/getGenreByContentNetwork/{network_id}', [AppApiController::class, 'getGenreByContentNetwork']);
Route::post('/getAllContentsOfNetworkNew', [AppApiController::class, 'getAllContentsOfNetworkNew']);

// 7 August 2025
Route::get('/getLiveTvGenreList', [AppApiController::class, 'getLiveTvGenreList']);
Route::post('/getAllLiveTV', [AppApiController::class, 'getAllLiveTV']);



Route::prefix('v2')->group(function (){
    Route::post('/login',[AppApiControllerV2::class,'login_pin']);
    Route::post('/login_app',[AppApiControllerV2::class,'login_pin_app']);
    Route::get('/get-slider',[AppApiControllerV2::class,'getSlider']);
    Route::get('/get-channels',[AppApiControllerV2::class,'getChannels']);
    Route::get('/get-channels-with-genre',[AppApiControllerV2::class,'getChannelsWithGenre']);
    Route::get('/get-channels-with-genre-new',[AppApiControllerV2::class,'getChannelsWithGenreNew']);
    Route::get('/get-channels-with-genre-popular',[AppApiControllerV2::class,'getChannelsWithGenrePopular']);
    Route::get('/pages',[AppApiControllerV2::class,'pages']);
    Route::post('/upload-profile-image',[AppApiControllerV2::class,'uploadProfile']);
    Route::post('/update-profile',[AppApiControllerV2::class,'updateProfile']);
    Route::get('/get-active-plan',[AppApiControllerV2::class,'getActivePlan']);
    Route::get('/check-plan',[AppApiControllerV2::class,'checkPlan']);


    // new 11 june
    Route::get('/getAllMovies', [AppApiControllerV2::class, 'getAllMovies']);
    Route::get('/getAllWebSeries', [AppApiControllerV2::class, 'getAllWebSeries']);
    Route::get('/getSeasons/{id}', [AppApiControllerV2::class, 'getSeasons']);
    Route::get('/getEpisodes/{id}/0', [AppApiControllerV2::class, 'getEpisodes']);

    Route::get('/getWebSeriesDetails/{webseriesId}', [AppApiControllerV2::class, 'getWebSeriesDetails']);

    Route::get('/getMoviePlayLinks/{id}/0', [AppApiControllerV2::class, 'getMoviePlayLinks']);

    // Route::get('/getNetworks', [AppApiControllerV2::class, 'getNetworks']);
    Route::post('/getNetworks', [AppApiControllerV2::class, 'getNetworks']);
    Route::get('/getAllContentsOfNetwork/{networkId}', [AppApiControllerV2::class, 'getAllContentsOfNetwork']);
    Route::get('/getMovieDetails/{contentId}', [AppApiControllerV2::class, 'getMovieDetails']);
    Route::get('/searchContent/{searchTerm}/0', [AppApiControllerV2::class, 'searchContent']);
    Route::get('/getFeaturedLiveTV', [AppApiControllerV2::class, 'getFeaturedLiveTV']);

    Route::get('/getCustomImageSlider', [AppApiControllerV2::class, 'getCustomImageSlider']);


    Route::get('/getTvChannels', [AppApiControllerV2::class, 'getTvChannels']);
    Route::get('/getTvShows/{id}', [AppApiControllerV2::class, 'getTvShows']);
    Route::get('/getShowSeasons/{id}', [AppApiControllerV2::class, 'getTvShowSeasons']);
    Route::get('/getShowSeasonsEpisodes/{id}', [AppApiControllerV2::class, 'getTvShowEpisodes']);

    // 30 june 2025

    Route::get('getsportCategories', [AppApiControllerV2::class, 'getsportCategories']);
    Route::get('getsportTournament/{id}', [AppApiControllerV2::class, 'getsportTournament']);
    Route::get('getTouranamentSeasons/{id}', [AppApiControllerV2::class, 'getTouranamentSeasons']);
    Route::get('getTouranamentSeasonsEvents/{id}', [AppApiControllerV2::class, 'getTouranamentSeasonsEvents']);


    Route::get('/getReligiousChannels', [AppApiControllerV2::class, 'getReligiousChannel']);
    Route::get('/getReligiousShows/{id}', [AppApiControllerV2::class, 'getReligiousShows']);
    Route::get('/getReligiousShowsEpisodes/{id}', [AppApiControllerV2::class, 'getReligiousShowsEpisodes']);

    Route::post('/getAllAbove18Movies', [AppApiControllerV2::class, 'getAllAbove18Movies']);

    // 18 july 2025

    Route::get('/getKidsChannels', [AppApiControllerV2::class, 'getKidsChannels']);
    Route::get('/getKidsShows/{id}', [AppApiControllerV2::class, 'getKidsShows']);
    Route::get('/getKidsShowSeasons/{id}', [AppApiControllerV2::class, 'getKidsShowSeasons']);
    Route::get('/getKidsShowSeasonsEpisodes/{id}', [AppApiControllerV2::class, 'getKidShowEpisodes']);

    Route::get('/getTvChannelsPak', [AppApiControllerV2::class, 'getTvChannelsPak']);
    Route::get('/getTvShowsPak/{id}', [AppApiControllerV2::class, 'getTvShowsPak']);
    Route::get('/getShowSeasonsPak/{id}', [AppApiControllerV2::class, 'getTvShowSeasonsPak']);
    Route::get('/getShowSeasonsEpisodesPak/{id}', [AppApiControllerV2::class, 'getTvShowEpisodesPak']);

    Route::get('/getAllStageShowsPak', [AppApiControllerV2::class, 'getAllStageShowsPak']);
    Route::get('/getAllLaughterShows', [AppApiControllerV2::class, 'getAllLaughterShows']);


    // 5 August 2025
    // Route::get('/getGenreByContentNetwork/{network_id}/{data_for?}', [AppApiControllerV2::class, 'getGenreByContentNetwork']);
    Route::post('/getGenreByContentNetwork', [AppApiControllerV2::class, 'getGenreByContentNetwork']);
    Route::get('/getAdultMoviesGenre', [AppApiControllerV2::class, 'getAdultMoviesGenre']);
    Route::post('/getAllContentsOfNetworkNew', [AppApiControllerV2::class, 'getAllContentsOfNetworkNew']);

    // 7 August 2025
    Route::get('/getLiveTvGenreList', [AppApiControllerV2::class, 'getLiveTvGenreList']);
    Route::post('/getAllLiveTV', [AppApiControllerV2::class, 'getAllLiveTV']);
    Route::post('/getWatchList', [AppApiControllerV2::class, 'getWatchList']);

    // 27 Aug
    Route::get('/showabove18', [AppApiControllerV2::class, 'showAbove18']);

    Route::post('/getSearchCategoryList', [AppApiControllerV2::class, 'getSearchCategoryList']);

    Route::post('/updateUserHistory', [AppApiControllerV2::class, 'updateUserHistory']);
    Route::get('/checkExpiryPlan', [AppApiControllerV2::class, 'checkExpiryPlan']);
    

    // 8 oct 2025

    Route::get('getAllLanguages', [AppApiControllerV2::class, 'getAllLanguages']);
});


Route::prefix('v3')->group(function (){
    Route::post('/login',[AppApiControllerV3::class,'login_pin']);
    Route::post('/login_app',[AppApiControllerV3::class,'login_pin_app']);


    Route::post('/login_new',[AppApiControllerV3::class,'login_pin_new']);
    Route::post('/login_app_new',[AppApiControllerV3::class,'login_pin_app_new']);


    Route::get('/get-slider',[AppApiControllerV3::class,'getSlider']);
    Route::get('/get-channels',[AppApiControllerV3::class,'getChannels']);
    Route::get('/get-channels-with-genre',[AppApiControllerV3::class,'getChannelsWithGenre']);
    Route::get('/get-channels-with-genre-new',[AppApiControllerV3::class,'getChannelsWithGenreNew']);
    Route::get('/get-channels-with-genre-popular',[AppApiControllerV3::class,'getChannelsWithGenrePopular']);
    Route::get('/pages',[AppApiControllerV3::class,'pages']);
    Route::post('/upload-profile-image',[AppApiControllerV3::class,'uploadProfile']);
    Route::post('/update-profile',[AppApiControllerV3::class,'updateProfile']);
    Route::get('/get-active-plan',[AppApiControllerV3::class,'getActivePlan']);
    Route::get('/check-plan',[AppApiControllerV3::class,'checkPlan']);


    // new 11 june
    Route::get('/getAllMovies', [AppApiControllerV3::class, 'getAllMovies']);
    Route::get('/getAllRecentSDMovies', [AppApiControllerV3::class, 'getAllRecentSDMovies']);


    Route::get('/getAllWebSeries', [AppApiControllerV3::class, 'getAllWebSeries']);
    Route::get('/getSeasons/{id}', [AppApiControllerV3::class, 'getSeasons']);
    Route::get('/getEpisodes/{id}/0', [AppApiControllerV3::class, 'getEpisodes']);

    Route::get('/getWebSeriesDetails/{webseriesId}', [AppApiControllerV3::class, 'getWebSeriesDetails']);

    Route::get('/getMoviePlayLinks/{id}/0', [AppApiControllerV3::class, 'getMoviePlayLinks']);

    // Route::get('/getNetworks', [AppApiControllerV3::class, 'getNetworks']);
    Route::post('/getNetworks', [AppApiControllerV3::class, 'getNetworks']);
    Route::get('/getAllContentsOfNetwork/{networkId}', [AppApiControllerV3::class, 'getAllContentsOfNetwork']);
    Route::get('/getMovieDetails/{contentId}', [AppApiControllerV3::class, 'getMovieDetails']);
    Route::get('/searchContent/{searchTerm}/0', [AppApiControllerV3::class, 'searchContent']);
    Route::get('/getFeaturedLiveTV', [AppApiControllerV3::class, 'getFeaturedLiveTV']);

    Route::get('/getCustomImageSlider', [AppApiControllerV3::class, 'getCustomImageSlider']);


    Route::get('/getTvChannels', [AppApiControllerV3::class, 'getTvChannels']);
    Route::get('/getTvShows/{id}', [AppApiControllerV3::class, 'getTvShows']);
    Route::get('/getShowSeasons/{id}', [AppApiControllerV3::class, 'getTvShowSeasons']);
    Route::get('/getShowSeasonsEpisodes/{id}', [AppApiControllerV3::class, 'getTvShowEpisodes']);

    // 30 june 2025

    Route::get('getsportCategories', [AppApiControllerV3::class, 'getsportCategories']);
    Route::get('getsportTournament/{id}', [AppApiControllerV3::class, 'getsportTournament']);
    Route::get('getTouranamentSeasons/{id}', [AppApiControllerV3::class, 'getTouranamentSeasons']);
    Route::get('getTouranamentSeasonsEvents/{id}', [AppApiControllerV3::class, 'getTouranamentSeasonsEvents']);


    Route::get('/getReligiousChannels', [AppApiControllerV3::class, 'getReligiousChannel']);
    Route::get('/getReligiousShows/{id}', [AppApiControllerV3::class, 'getReligiousShows']);
    Route::get('/getReligiousShowsEpisodes/{id}', [AppApiControllerV3::class, 'getReligiousShowsEpisodes']);

    Route::post('/getAllAbove18Movies', [AppApiControllerV3::class, 'getAllAbove18Movies']);

    // 18 july 2025

    Route::get('/getKidsChannels', [AppApiControllerV3::class, 'getKidsChannels']);
    Route::get('/getKidsShows/{id}', [AppApiControllerV3::class, 'getKidsShows']);
    Route::get('/getKidsShowSeasons/{id}', [AppApiControllerV3::class, 'getKidsShowSeasons']);
    Route::get('/getKidsShowSeasonsEpisodes/{id}', [AppApiControllerV3::class, 'getKidShowEpisodes']);

    Route::get('/getTvChannelsPak', [AppApiControllerV3::class, 'getTvChannelsPak']);
    Route::get('/getTvShowsPak/{id}', [AppApiControllerV3::class, 'getTvShowsPak']);
    Route::get('/getShowSeasonsPak/{id}', [AppApiControllerV3::class, 'getTvShowSeasonsPak']);
    Route::get('/getShowSeasonsEpisodesPak/{id}', [AppApiControllerV3::class, 'getTvShowEpisodesPak']);

    Route::get('/getAllStageShowsPak', [AppApiControllerV3::class, 'getAllStageShowsPak']);
    Route::get('/getAllLaughterShows', [AppApiControllerV3::class, 'getAllLaughterShows']);


    // 5 August 2025
    // Route::get('/getGenreByContentNetwork/{network_id}/{data_for?}', [AppApiControllerV3::class, 'getGenreByContentNetwork']);
    Route::post('/getGenreByContentNetwork', [AppApiControllerV3::class, 'getGenreByContentNetwork']);
    Route::get('/getAdultMoviesGenre', [AppApiControllerV3::class, 'getAdultMoviesGenre']);
    Route::post('/getAllContentsOfNetworkNew', [AppApiControllerV3::class, 'getAllContentsOfNetworkNew']);
    // Route::get('/getAllContentsOfNetworkNew', [AppApiControllerV3::class, 'getAllContentsOfNetworkNew']);

    // 7 August 2025
    Route::get('/getLiveTvGenreList', [AppApiControllerV3::class, 'getLiveTvGenreList']);
    Route::post('/getAllLiveTV', [AppApiControllerV3::class, 'getAllLiveTV']);
    Route::post('/getWatchList', [AppApiControllerV3::class, 'getWatchList']);

    // 27 Aug
    Route::get('/showabove18', [AppApiControllerV3::class, 'showAbove18']);

    Route::post('/getSearchCategoryList', [AppApiControllerV3::class, 'getSearchCategoryList']);

    Route::post('/updateUserHistory', [AppApiControllerV3::class, 'updateUserHistory']);
    Route::get('/checkExpiryPlan', [AppApiControllerV3::class, 'checkExpiryPlan']);
    

    // 8 oct 2025

    Route::get('getAllLanguages', [AppApiControllerV3::class, 'getAllLanguages']);


    Route::post('tokenizeUrl', [AppApiControllerV3::class, 'tokenizeUrl']);

    Route::get('getCDNSettings', [AppApiControllerV3::class, 'getCDNSettings']);


    Route::get('getAllSportsLive', [AppApiControllerV3::class, 'getAllSportsLive']);
    Route::get('getAllKidsLive', [AppApiControllerV3::class, 'getAllKidsLive']);

    
    Route::get('checkIsFrozen', [AppApiControllerV3::class, 'checkIsFrozen']);



    Route::get('check-price-and-update-by-cron', [AppApiControllerV3::class, 'checkPriceAndUpdate']);
});