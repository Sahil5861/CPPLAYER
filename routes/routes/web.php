<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Welcome;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManageRole;
use App\Http\Controllers\ManageAdmin;
use App\Http\Controllers\NetAdminController;
use App\Http\Controllers\ManageUser;
use App\Http\Controllers\ManageGenre;
use App\Http\Controllers\ManageSlider;
use App\Http\Controllers\ManageLanguage;
use App\Http\Controllers\Channels;
use App\Http\Controllers\SuperAdminPlans;
use App\Http\Controllers\SuperAdminWallet;
use App\Http\Controllers\AdminWallets;
use App\Http\Controllers\AdminPlans;
use App\Http\Controllers\AuthAdmin;
use App\Http\Controllers\ManageReseller;
use App\Http\Controllers\ResellerPlanController;
use App\Http\Controllers\ResellerWallets;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Movies;
use App\Http\Controllers\AdultMovies;
use App\Http\Controllers\Webserieses;
use App\Http\Controllers\MovieLinks;
use App\Http\Controllers\WebseriesSeasons;
use App\Http\Controllers\WebseriesEpisodes;
use App\Http\Controllers\ContentNetworks;
use App\Http\Controllers\ManageTvChannel;
use App\Http\Controllers\ManageTvChannelPak;

use App\Http\Controllers\ManageTvShow;
use App\Http\Controllers\ManageTvShowPak;

use App\Http\Controllers\ManageTvShowSeason;
use App\Http\Controllers\ManageTvShowSeasonPak;

use App\Http\Controllers\ManageTvShowEpisode;
use App\Http\Controllers\ManageTvShowEpisodePak;


use App\Http\Controllers\ManageSportsCategory;
use App\Http\Controllers\ManageSportsChannel;
use App\Http\Controllers\ManageSportsTournament;
use App\Http\Controllers\ManageTournamentSeason;
use App\Http\Controllers\ManageTournamentMatches;

use App\Http\Controllers\ManageKidsChannel;
use App\Http\Controllers\ManageKidsShows;
use App\Http\Controllers\KidsShowSeasons;
use App\Http\Controllers\KidsShowEpisodes;
use App\Http\Controllers\ManageRelChannel;
use App\Http\Controllers\ManageRelShows;
use App\Http\Controllers\RelShowEpisodes;
use App\Http\Controllers\SuperAdminAdPlan;
use App\Http\Controllers\AdminAdPlan;
use App\Http\Controllers\AdminAd;
use App\Http\Controllers\ResellerAd;
use App\Http\Controllers\StageShowsPak;
use App\Http\Controllers\LaughterShows;















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



Route::group(['namespace' => 'App\Http\Controllers'], function()
{
    /**
     * Home Routes
     */
    Route::get('/', 'LoginController@show')->name('login.show');

    Route::group(['middleware' => ['guest']], function() {
        /**
         * Register Routes
         */
        Route::get('/register', 'RegisterController@show')->name('register.show');
        Route::post('/register', 'RegisterController@register')->name('register.perform');

        /**
         * Login Routes
         */
        Route::get('/login', 'LoginController@show')->name('login.show');
        Route::post('/login', 'LoginController@login')->name('login.perform');

    });

    Route::group(['middleware' => ['auth']], function() {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'LogoutController@perform')->name('logout.perform');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard-filter/{startDate}/{endDate}',[HomeController::class,'filter'])->name('dashboard-filter');



        //role
        Route::get('/roles', [ManageRole::class, 'index'])->name('admin.roles');
        Route::get('/getRoleList', [ManageRole::class, 'getRoleList'])->name('getRoleList');
        Route::get('/add-role', [ManageRole::class,'addRole'])->name('addRole');
        Route::post('/addRole', [ManageRole::class,'add'])->name('saveRole');

        Route::get('/edit-role/{id}', [ManageRole::class,'editRole'])->name('edit-role');
        Route::post('/role/destroy', [ManageRole::class,'destroy'])->name('role.destroy');

        Route::get('/role/update-status/{id}', [ManageRole::class,'updateStatus'])->name('role.update-status');

        //Admin
        Route::get('/admins', [ManageAdmin::class, 'index'])->name('admin.admins');
        Route::get('/getAdminList', [ManageAdmin::class, 'getAdminList'])->name('getAdminList');
        Route::get('/add-admin', [ManageAdmin::class,'addAdmin'])->name('addAdmin');
        Route::post('/addAdmin', [ManageAdmin::class,'add'])->name('saveAdmin');

        Route::get('/edit-admin/{id}', [ManageAdmin::class,'editAdmin'])->name('edit-admin');
        Route::post('/admin/destroy', [ManageAdmin::class,'destroy'])->name('admin.destroy');
        Route::get('/admin/update-status/{id}', [ManageAdmin::class,'updateStatus'])->name('admin.update-status');

        Route::get('/admin-signin/{id}', [ManageAdmin::class,'adminLogin'])->name('admin-signin');
        Route::get('/back-to-admin/{id}', [ManageAdmin::class,'superAdminLogin'])->name('back-to-admin');

        //Net Admin
        Route::get('/netadmins', [NetAdminController::class, 'index'])->name('admin.netadmins');
        Route::get('/getNetAdminList', [NetAdminController::class, 'getNetAdminList'])->name('getNetAdminList');
        Route::get('/add-netadmin', [NetAdminController::class,'addNetAdmin'])->name('addNetAdmin');
        Route::post('/addNetAdmin', [NetAdminController::class,'add'])->name('saveNetAdmin');
        Route::get('/edit-netadmin/{id}', [NetAdminController::class,'editNetAdmin'])->name('edit-netadmin');
        Route::post('/netadmin/destroy', [NetAdminController::class,'destroy'])->name('netadmin.destroy');
        Route::get('/netadmin/update-status/{id}', [NetAdminController::class,'updateStatus'])->name('netadmin.update-status');
        Route::get('/netadmin-signin/{id}', [NetAdminController::class,'netadminLogin'])->name('netadmin-signin');
        Route::get('/channels-list', [NetAdminController::class,'channels'])->name('netadmin.channels');
        Route::get('/netadmin/update-channel-status/{id}', [NetAdminController::class,'updateChannelStatus'])->name('netadmin.update-channel-status');
        Route::post('/update-channel-link', [NetAdminController::class,'updateLink'])->name('netadmin.updateLink');
        Route::get('/netadmin/wallet', [NetAdminController::class,'wallet'])->name('netadmin.wallet');
        Route::get('/getNetAdminWalletList', [NetAdminController::class, 'getNetAdminWalletList'])->name('getNetAdminWalletList');
        Route::get('/channel-filter/{id}', [NetAdminController::class, 'channelFilter'])->name('channelFilter');

        //User
        Route::get('/users', [ManageUser::class, 'index'])->name('user.users');
        Route::get('/getUserList', [ManageUser::class, 'getUserList'])->name('getUserList');
        Route::get('/add-user', [ManageUser::class,'addUser'])->name('addUser');
        Route::post('/addUser', [ManageUser::class,'add'])->name('saveUser');

        Route::get('/edit-user/{id}', [ManageUser::class,'editUser'])->name('edit-user');
        Route::get('/user-history/{id}', [ManageUser::class,'userHistory'])->name('user-history');
        Route::post('/user/destroy', [ManageUser::class,'destroy'])->name('user.destroy');
        Route::get('/user/update-status/{id}', [ManageUser::class,'updateStatus'])->name('user.update-status');

        Route::post('check-email',[ManageReseller::class, 'checkEmail'])->name('check-email');
        Route::post('check-user-email',[ManageUser::class, 'checkEmail'])->name('check-user-email');

        //Reseller
        Route::get('/resellers', [ManageReseller::class, 'index'])->name('reseller.resellers');
        Route::get('/getResellerList', [ManageReseller::class, 'getResellerList'])->name('getResellerList');
        Route::post('/getResellerListByAdmin', [ManageReseller::class, 'getResellerListByAdmin'])->name('getResellerListByAdmin');
        Route::get('/add-reseller', [ManageReseller::class,'addReseller'])->name('addReseller');
        Route::post('/addReseller', [ManageReseller::class,'add'])->name('saveReseller');

        Route::get('/edit-reseller/{id}', [ManageReseller::class,'editReseller'])->name('edit-reseller');
        Route::post('/reseller/destroy', [ManageReseller::class,'destroy'])->name('reseller.destroy');
        Route::get('/reseller/update-status/{id}', [ManageReseller::class,'updateStatus'])->name('reseller.update-status');

        // genre
        Route::get('/genres', [ManageGenre::class, 'index'])->name('admin.genres');
        Route::get('/getGenreList', [ManageGenre::class, 'getGenreList'])->name('getGenreList');
        Route::get('/add-genre', [ManageGenre::class,'addGenre'])->name('addGenre');
        Route::post('/addGenre', [ManageGenre::class,'add'])->name('saveGenre');

        Route::get('/edit-genre/{id}', [ManageGenre::class,'editGenre'])->name('edit-genre');
        Route::post('/genre/destroy', [ManageGenre::class,'destroy'])->name('genre.destroy');

        Route::get('/genre/update-status/{id}', [ManageGenre::class,'updateStatus'])->name('genre.update-status');
        Route::get('/genre-order', [ManageGenre::class, 'getGenreOrderList'])->name('admin.genre.order');
        
        Route::post('/save-genre-orders', [ManageGenre::class,'saveGenreOrders'])->name('saveGenreOrders');
        
        // tvchannel
        Route::get('/tvchannel', [ManageTvChannel::class, 'index'])->name('admin.tvchannel');
        Route::get('/getTvChannelList', [ManageTvChannel::class, 'getTvChannelList'])->name('getTvChannelList');
        Route::get('/add-tvchannel', [ManageTvChannel::class,'addTvChannel'])->name('addTvChannel');
        Route::post('/addTvChannel', [ManageTvChannel::class,'add'])->name('saveTvChannel');
        Route::get('/edit-tvchannel/{id}', [ManageTvChannel::class, 'editTvChannel'])->name('editTvChannel');
        Route::post('/tvchannel/destroy', [ManageTvChannel::class,'destroy'])->name('tvchannel.destroy');
        

        Route::get('/tvchannel/update-status/{id}', [ManageTvChannel::class,'updateStatus'])->name('tvchannel.update-status');
        Route::get('/tvchannel-order', [ManageTvChannel::class, 'getTvChannelOrderList'])->name('admin.tvchannel.order');
        Route::post('/save-tvchannel-orders', [ManageTvChannel::class,'saveTvChannelOrder'])->name('saveTvChannelOrder');
        
        // tvshow
        Route::get('/tvshow/{id}', [ManageTvShow::class, 'index'])->name('admin.tvshow');
        Route::get('/getTvShowList/{id}', [ManageTvShow::class, 'getTvShowList'])->name('getTvShowList');
        Route::get('/add-tvshow/{id}', [ManageTvShow::class,'addTvShow'])->name('addTvShow');
        Route::post('/addTvShow', [ManageTvShow::class,'add'])->name('saveTvShow');
        Route::get('/edit-tvshow/{id}', [ManageTvShow::class, 'editTvShow'])->name('editTvShow');
        Route::post('/tvshow/destroy', [ManageTvShow::class,'destroy'])->name('tvshow.destroy');
        Route::get('/tvshow/update-status/{id}', [ManageTvShow::class,'updateStatus'])->name('tvshow.update-status');        

        Route::get('/tvshow/update-status/{id}', [ManageTvShow::class,'updateStatus'])->name('tvshow.update-status');
        Route::get('/tvshow-order/{id}', [ManageTvShow::class, 'getTvShowOrderList'])->name('admin.tvshow.order');
        Route::post('/save-tvshow-orders', [ManageTvShow::class, 'saveTvshowOrder'])->name('saveTvshowOrder');


        // TV Show seasons
        Route::get('/tvshow-season/{id}', [ManageTvShowSeason::class, 'index'])->name('admin.tvshow.season');
        Route::get('/getTvShowSeasonList/{id}', [ManageTvShowSeason::class, 'getshowSeasonList'])->name('getTvShowSeasonList');
        Route::get('/add-tvshow-season/{id}', [ManageTvShowSeason::class,'addTvShow'])->name('addTvShowSeason');
        Route::post('/add-tvshow-season', [ManageTvShowSeason::class,'add'])->name('saveTvShowSeson');
        Route::get('/edit-tvshow-season/{id}', [ManageTvShowSeason::class, 'editTvShowSeason'])->name('editTvShowSeason');
        Route::get('/tvshow-season/update-status/{id}', [ManageTvShowSeason::class,'updateStatus'])->name('tvshowseason.update-status');        
        Route::post('/tvshows-season/destroy', [ManageTvShowSeason::class,'destroy'])->name('tvshowseason.destroy');
        Route::get('/tvshowseason-order/{id}', [ManageTvShowSeason::class, 'getShowSeasonsOrderList'])->name('admin.tvshowseason.order');
        Route::post('/save-tvshowseasons-orders', [ManageTvShowSeason::class,'saveTvShowSeasonOrders'])->name('saveTvShowSeasonOrders');


        // TV Show episodes
        Route::get('/tvshow-episode/{id}', [ManageTvShowEpisode::class, 'index'])->name('admin.tvshow.episode');
        Route::get('/getTvShowEpisodeList/{id}', [ManageTvShowEpisode::class, 'getshowSeasonList'])->name('getTvShowEpisodeList');
        Route::get('/add-tvshow-episode/{id}', [ManageTvShowEpisode::class,'addTvShow'])->name('addTvShowEpisode');
        Route::post('/add-tvshow-episode', [ManageTvShowEpisode::class,'add'])->name('saveTvShowEpisode');
        Route::get('/edit-tvshow-episode/{id}', [ManageTvShowEpisode::class, 'editTvShowEpisode'])->name('editTvShowEpisode');
        Route::get('/tvshow-episode/update-status/{id}', [ManageTvShowEpisode::class,'updateStatus'])->name('tvshowepisode.update-status');        
        Route::post('/tvshows-episode/destroy', [ManageTvShowEpisode::class,'destroy'])->name('tvshowepisode.destroy');
        
        Route::get('/tvshowepisode-order/{id}', [ManageTvShowEpisode::class, 'getTvShowEpisodesOrderList'])->name('admin.tvshowepisode.order');
        Route::post('/save-tvshowepisode-orders', [ManageTvShowEpisode::class,'saveTvShowEpisodesOrders'])->name('saveTvShowEpisodesOrders');

        Route::post('import-tvshows-episode-playlits', [ManageTvShowEpisode::class, 'importPlaylist'])->name('importtvshowsepisodeplaylits');


        // Tv channels pak
        Route::get('/pak-tv-channel', [ManageTvChannelPak::class, 'index'])->name('admin.tvchannelpak');
        Route::get('/getTvChannelpakList', [ManageTvChannelPak::class, 'getTvChannelpakList'])->name('getTvChannelpakList');
        Route::get('/add-tv-channel-pak', [ManageTvChannelPak::class,'addTvChannel'])->name('addTvChannelpak');
        Route::post('/addTvChannel-pak', [ManageTvChannelPak::class,'add'])->name('saveTvChannelpak');
        Route::get('/edit-tv-channel-pak/{id}', [ManageTvChannelPak::class, 'editTvChannelPak'])->name('editTvChannelpak');
        Route::post('/tvchannel-pak/destroy', [ManageTvChannelPak::class,'destroy'])->name('tvchannelpak.destroy');
        
        Route::get('/tv-channel-pak/update-status/{id}', [ManageTvChannelPak::class,'updateStatus'])->name('tvchannel-pak.update-status');
        Route::get('/tv-channel-pak-order', [ManageTvChannelPak::class, 'getTvChannelPakOrderList'])->name('admin.tvchannelpak.order');
        Route::post('/save-tvchannel-pak-orders', [ManageTvChannelPak::class,'saveTvChannelPakOrder'])->name('saveTvChannelPakOrder');


        // tvshow pak
        Route::get('/tv-show-pak/{id}', [ManageTvShowPak::class, 'index'])->name('admin.tvshowpak');
        Route::get('/getTvShowPakList/{id}', [ManageTvShowPak::class, 'getTvShowPakList'])->name('getTvShowPakList');
        Route::get('/add-tv-show-pak/{id}', [ManageTvShowPak::class,'addTvShow'])->name('addTvShowpak');
        Route::post('/addTvShowpak', [ManageTvShowPak::class,'add'])->name('saveTvShowpak');
        Route::get('/edit-tv-show-pak/{id}', [ManageTvShowPak::class, 'editTvShow'])->name('editTvShowpak');
        Route::post('/tv-show-pak/destroy', [ManageTvShowPak::class,'destroy'])->name('tvshowpak.destroy');
        Route::get('/tv-show-pak/update-status/{id}', [ManageTvShowPak::class,'updateStatus'])->name('tvshowpak.update-status');        

        Route::get('/tv-show-pak/update-status/{id}', [ManageTvShowPak::class,'updateStatus'])->name('tvshowpak.update-status');
        Route::get('/tv-show-pak-order/{id}', [ManageTvShowPak::class, 'getTvShowOrderList'])->name('admin.tvshowpak.order');
        Route::post('/save-tv-show-pak-orders', [ManageTvShowPak::class, 'saveTvShowPakOrder'])->name('saveTvShowPakOrder');


        // TV Show seasons Pak
        Route::get('/tv-show-season-pak/{id}', [ManageTvShowSeasonPak::class, 'index'])->name('admin.tvshowpak.season');
        Route::get('/getTvShowSeasonPakList/{id}', [ManageTvShowSeasonPak::class, 'getTvShowSeasonPakList'])->name('getTvShowSeasonPakList');
        Route::get('/add-tv-show-season-pak/{id}', [ManageTvShowSeasonPak::class,'addTvShow'])->name('addTvShowSeasonpak');
        Route::post('/add-tvs-how-season-pak', [ManageTvShowSeasonPak::class,'add'])->name('saveTvShowSesonpak');
        Route::get('/edit-tv-show-season-pak/{id}', [ManageTvShowSeasonPak::class, 'editTvShowSeason'])->name('editTvShowSeasonpak');
        Route::get('/tv-show-season-pak/update-status/{id}', [ManageTvShowSeasonPak::class,'updateStatus'])->name('tvshowseasonpak.update-status');        
        Route::post('/tv-shows-season-pak/destroy', [ManageTvShowSeasonPak::class,'destroy'])->name('tvshowseasonpak.destroy');
        Route::get('/tv-show-season-pak-order/{id}', [ManageTvShowSeasonPak::class, 'getShowSeasonsPakOrderList'])->name('admin.tvshowseasonpak.order');
        Route::post('/save-tv-showseasons-pak-orders', [ManageTvShowSeasonPak::class,'saveTvShowPakSeasonOrders'])->name('saveTvShowPakSeasonOrders');

        // TV Show episodes Pak
        Route::get('/tv-show-pak-episode/{id}', [ManageTvShowEpisodePak::class, 'index'])->name('admin.tvshowpak.episode');
        Route::get('/getTvShowEpisodePakList/{id}', [ManageTvShowEpisodePak::class, 'getshowSeasonList'])->name('getTvShowEpisodeListPak');
        Route::get('/add-tv-show-pak-episode/{id}', [ManageTvShowEpisodePak::class,'addTvShow'])->name('addTvShowEpisodepak');
        Route::post('/add-tv-show-pak-episode', [ManageTvShowEpisodePak::class,'add'])->name('saveTvShowEpisodepak');
        Route::get('/edit-tv-show-pak-episode/{id}', [ManageTvShowEpisodePak::class, 'editTvShowEpisode'])->name('editTvShowEpisodepak');
        Route::get('/tv-show-pak-episode/update-status/{id}', [ManageTvShowEpisodePak::class,'updateStatus'])->name('tvshowepisodepak.update-status');        
        Route::post('/tv-shows-pak-episode/destroy', [ManageTvShowEpisodePak::class,'destroy'])->name('tvshowepisodepak.destroy');
        
        Route::get('/tvshowepisodepak-order/{id}', [ManageTvShowEpisodePak::class, 'getTvShowEpisodesOrderList'])->name('admin.tvshowepisodepak.order');
        Route::post('/save-tvshowepisodepak-orders', [ManageTvShowEpisodePak::class,'saveTvShowEpisodesOrders'])->name('saveTvShowEpisodesOrdersPak');

        Route::post('import-tv-shows-pak-episode-playlits', [ManageTvShowEpisodePak::class, 'importPlaylist'])->name('importtvshowsepisodepakplaylits');




        
        // sportscategory
        Route::get('/sportscategory', [ManageSportsCategory::class, 'index'])->name('admin.sportscategory');
        Route::get('/getsportscategorylList', [ManageSportsCategory::class, 'getSportsCategoryList'])->name('getsportscategoryList');
        Route::get('/add-sports-category', [ManageSportsCategory::class,'add'])->name('addsportscategory');
        Route::post('/sports-category-save', [ManageSportsCategory::class,'save'])->name('savesportscategory');
        Route::get('/editSportsCategory/{id}', [ManageSportsCategory::class, 'edit'])->name('editsportscategory');
        Route::post('/sports-category/destroy', [ManageSportsCategory::class,'destroy'])->name('sportscategory.destroy');
        Route::get('/sports-category/update-status/{id}', [ManageSportsCategory::class,'updateStatus'])->name('sportscategory.update-status');        
        Route::get('/sports-category-order', [ManageSportsCategory::class, 'getSportsCategoryOrderList'])->name('admin.sportscategory.order');
        Route::post('/save-sports-category-orders', [ManageSportsCategory::class,'saveSportsCategoryOrder'])->name('saveSportsCategoryOrder');
       

        
        // // sportschannel
        // Route::get('/sportschannels', [ManageSportsChannel::class, 'index'])->name('admin.sportschannel');
        // Route::get('/getsportschannellList', [ManageSportsChannel::class, 'getSportsCategoryList'])->name('getsportschannelList');
        // Route::get('/add-sportschannel', [ManageSportsChannel::class,'add'])->name('addsportschannel');
        // Route::post('/sportschannel-save', [ManageSportsChannel::class,'save'])->name('savesportschannel');
        // Route::get('/editsportschannel/{id}', [ManageSportsChannel::class, 'edit'])->name('editsportschannel');
        // Route::post('/sportschannel/destroy', [ManageSportsChannel::class,'destroy'])->name('sportschannel.destroy');
        // Route::get('/sportschannel/update-status/{id}', [ManageSportsChannel::class,'updateStatus'])->name('sportschannel.update-status');        

        // sportstournament
        Route::get('/sports-tournament/{id}', [ManageSportsTournament::class, 'index'])->name('admin.sporttournament');
        Route::get('/getsportstournamentList/{id}', [ManageSportsTournament::class, 'getSportsTournamentList'])->name('getsportstournamentList');
        Route::get('/add-sports-tournament/{id}', [ManageSportsTournament::class,'add'])->name('addsportstournament');
        Route::post('/sportstournament-save', [ManageSportsTournament::class,'save'])->name('savesportstournament');
        Route::get('/edit-sportstournament/{id}', [ManageSportsTournament::class, 'edit'])->name('editsportstournament');
        Route::post('/sportstournament/destroy', [ManageSportsTournament::class,'destroy'])->name('sportstournament.destroy');
        Route::get('/tournament/update-status/{id}', [ManageSportsTournament::class,'updateStatus'])->name('sportschannel.update-status');        
        Route::get('/sports-tournament-order/{id}', [ManageSportsTournament::class, 'getSportsTournamentOrderList'])->name('admin.sportsctournament.order');
        Route::post('/save-sports-tournament-orders', [ManageSportsTournament::class,'saveSportsTournamentOrder'])->name('saveSportsTournamentOrder');
       

        // tournament sesons
        Route::get('/sports-tournament-seasons/{id}', [ManageTournamentSeason::class, 'index'])->name('admin.sporttournamentseasons');
        Route::get('/getsportstournamentseasonList/{id}', [ManageTournamentSeason::class, 'getSportsTournamentSeasonList'])->name('getsportstournamentseasonList');
        Route::get('/add-sports-tournament-season/{id}', [ManageTournamentSeason::class,'add'])->name('addsportstournamentseason');
        Route::post('/sportstournamentseason-save', [ManageTournamentSeason::class,'save'])->name('savesportstournamentseason');
        Route::get('/edit-sports-tournament-season/{id}', [ManageTournamentSeason::class, 'edit'])->name('editsportstournamentseason');
        Route::post('/sportstournamentseason/destroy', [ManageTournamentSeason::class,'destroy'])->name('sportstournamentseason.destroy');
        Route::get('/tournament-season/update-status/{id}', [ManageTournamentSeason::class,'updateStatus'])->name('sportschannelseason.update-status');        
        Route::get('/tournament-season-order/{id}', [ManageTournamentSeason::class, 'getTournamentSeasonOrderList'])->name('admin.tournamentseason.order');
        Route::post('/save-tournament-seasons-orders', [ManageTournamentSeason::class,'savetournamentSeasonOrders'])->name('savetournamentSeasonOrders');

        
        
        // tournament season matches/episodes
        Route::get('/sports-tournament-seasons-episodes/{id}', [ManageTournamentMatches::class, 'index'])->name('admin.sporttournamentseasonsepisodes');
        Route::get('/get-sportstournament-episodesList/{id}', [ManageTournamentMatches::class, 'getSportsTournamentSeasonEpisodeList'])->name('get-sports-tournament-episodesList');
        Route::get('/add-sports-tournament-season-episodes/{id}', [ManageTournamentMatches::class,'add'])->name('addsportstournamentseasonepisodes');
        Route::post('/sportstournamentseasonepisodes-save', [ManageTournamentMatches::class,'save'])->name('savesportstournamentseasonepisodes');
        Route::get('/edit-sports-tournament-season-episodes/{id}', [ManageTournamentMatches::class, 'edit'])->name('editsportstournamentseasonepisodes');
        Route::post('/sportstournamentseasonepisodes/destroy', [ManageTournamentMatches::class,'destroy'])->name('sportstournamentseasonepisodes.destroy');
        Route::get('/tournament-season-episodes/update-status/{id}', [ManageTournamentMatches::class,'updateStatus'])->name('sportschannelseasonepisodes.update-status');
        Route::get('/tournament-match-order/{id}', [ManageTournamentMatches::class, 'getTournamentMatchesOrderList'])->name('admin.tournamentmatches.order');
        Route::post('/save-tournament-match-orders', [ManageTournamentMatches::class,'saveTournamentMatchesOrders'])->name('saveTournamentMatchesOrders');

        Route::post('import-sports-playlits', [ManageTvShowEpisodePak::class, 'importPlaylist'])->name('importsportsplaylits');

        

        // slider
        Route::get('/sliders', [ManageSlider::class, 'index'])->name('admin.sliders');
        Route::get('/getSliderList', [ManageSlider::class, 'getSliderList'])->name('getSliderList');
        Route::get('/add-slider', [ManageSlider::class,'addSlider'])->name('addSlider');
        Route::post('/addSlider', [ManageSlider::class,'add'])->name('saveSlider');

        Route::get('/edit-slider/{id}', [ManageSlider::class,'editSlider'])->name('edit-slider');
        Route::post('/slider/destroy', [ManageSlider::class,'destroy'])->name('slider.destroy');

        Route::get('/slider/update-status/{id}', [ManageSlider::class,'updateStatus'])->name('genre.update-status');
        Route::get('/get-channel-by-id', [ManageSlider::class,'getChannelById'])->name('get-channel-by-id');
        Route::get('/get-series-by-id', [ManageSlider::class,'getSeriesById'])->name('get-sereis-by-id');
        Route::get('/get-movie-by-id', [ManageSlider::class,'getMovieById'])->name('get-movie-by-id');
        

        //Language
        Route::get('/languages', [ManageLanguage::class, 'index'])->name('admin.languages');
        Route::get('/getLanguageList', [ManageLanguage::class, 'getLanguageList'])->name('getLanguageList');
        Route::get('/add-language', [ManageLanguage::class,'addLanguage'])->name('addLanguage');
        Route::post('/addLanguage', [ManageLanguage::class,'add'])->name('saveLanguage');
        Route::get('/edit-language/{id}', [ManageLanguage::class,'editLanguage'])->name('edit-role');
        Route::post('/language/destroy', [ManageLanguage::class,'destroy'])->name('language.destroy');

        Route::get('/language/update-status/{id}', [ManageLanguage::class,'updateStatus'])->name('language.update-status');

        //Channels
        Route::get('/channels', [Channels::class, 'index'])->name('admin.channels');
        Route::get('/getChannelList', [Channels::class, 'getChannelList'])->name('getChannelList');
        Route::get('/channels-order', [Channels::class, 'getChannelOrderList'])->name('admin.channels.order');
        Route::get('/deleted-channel', [Channels::class, 'deletedChannel'])->name('admin.channels.deleted');
        Route::get('/getDeletedChannelList', [Channels::class, 'getDeletedChannelList'])->name('getDeletedChannelList');
        Route::get('/recover-channel/{id}', [Channels::class, 'recoverChannel'])->name('admin.channel.recoverChannel');
        Route::get('/add-channel', [Channels::class,'addChannel'])->name('addChannel');
        Route::post('/addChannel', [Channels::class,'add'])->name('saveChannel');
        Route::post('/save-channel-orders', [Channels::class,'saveChannelOrders'])->name('saveChannelOrders');
        Route::get('/edit-channel/{id}', [Channels::class,'editChannel'])->name('edit-channel');
        Route::post('/channel/destroy', [Channels::class,'destroy'])->name('channel.destroy');
        Route::get('/channel/update-status/{id}', [Channels::class,'updateStatus'])->name('channel.update-status');
        Route::post('/check-channel-name', [Channels::class,'checkChannelName'])->name('check-channel-name');
        Route::post('/check-channel-number', [Channels::class,'checkChannelNumber'])->name('check-channel-number');

        Route::post('update-column', [movies::class, 'updateColumn'])->name('update-column');

        //Movies
        Route::get('/movies', [Movies::class, 'index'])->name('admin.movies');
        Route::get('/getMoviesList', [Movies::class, 'getMoviesList'])->name('getMovieList');
        Route::get('/movie-order', [Movies::class, 'getMovieOrderList'])->name('admin.movie.order');
        Route::get('/deleted-movie', [Movies::class, 'deletedChannel'])->name('admin.movie.deleted');
        Route::get('/getDeletedChannelList', [Movies::class, 'getDeletedChannelList'])->name('getDeletedMovieList');
        Route::get('/recover-channel/{id}', [Movies::class, 'recoverChannel'])->name('admin.movie.recoverMovie');
        Route::get('/add-movie', [Movies::class,'addChannel'])->name('addMovie');
        Route::post('/addMovie', [Movies::class,'add'])->name('saveMovie');
        Route::post('/save-movie-orders', [Movies::class,'saveMovieOrder'])->name('saveMovieOrder');
        Route::get('/edit-movie/{id}', [Movies::class,'editChannel'])->name('edit-movie');
        Route::post('/movie/destroy', [Movies::class,'destroy'])->name('movie.destroy');
        Route::get('/movie/update-status/{id}', [Movies::class,'updateStatus'])->name('movie.update-status');
        Route::post('/check-channel-name', [Movies::class,'checkChannelName'])->name('check-channel-name');
        Route::post('/check-channel-number', [Movies::class,'checkChannelNumber'])->name('check-channel-number');
        Route::post('import-movies-playlits', [Movies::class, 'importmovies'])->name('importmovies');

        

        //Stage Shows
        Route::get('/stage-shows', [StageShowsPak::class, 'index'])->name('admin.stage-shows');
        Route::get('/getStageShowsPakList', [StageShowsPak::class, 'getStageShowsPakList'])->name('getStageShowsPakList');
        Route::get('/stage-show-order', [StageShowsPak::class, 'getMovieOrderList'])->name('admin.stageshow.order');
        Route::get('/deleted-stage-show', [StageShowsPak::class, 'deletedChannel'])->name('admin.stageshow.deleted');
        Route::get('/getDeletedChannelList', [StageShowsPak::class, 'getDeletedChannelList'])->name('getDeletedMovieList');
        Route::get('/recover-channel/{id}', [StageShowsPak::class, 'recoverChannel'])->name('admin.stageshow.recoverMovie');
        Route::get('/add-stage-show', [StageShowsPak::class,'addChannel'])->name('addstageshow');
        Route::post('/addstageshow', [StageShowsPak::class,'add'])->name('savestageshow');
        Route::post('/save-stage-show-orders', [StageShowsPak::class,'savestageshowOrder'])->name('saveStageShowsPakOrder');
        Route::get('/edit-stage-show/{id}', [StageShowsPak::class,'editstageshow'])->name('edit-stageshow');
        Route::post('/stage-show/destroy', [StageShowsPak::class,'destroy'])->name('stageshow.destroy');
        
        Route::get('/stageshowpak-order', [StageShowsPak::class, 'getStageshowPakOrderList'])->name('admin.stageshowpak.order');
        Route::post('/save-stageshowpak-orders', [StageShowsPak::class,'saveStageShowPakOrder'])->name('saveStageShowPakOrder');
        
        Route::get('/stage-show/update-status/{id}', [StageShowsPak::class,'updateStatus'])->name('stageshow.update-status');        
        Route::post('import-stage-show-playlits', [StageShowsPak::class, 'importstageshows'])->name('importstageshows');


        //Laughter Shows
        Route::get('/laughter-shows', [LaughterShows::class, 'index'])->name('admin.laughter-shows');
        Route::get('/getLaughterShowsList', [LaughterShows::class, 'getLaughterShowsList'])->name('getLaughterShowsList');
        Route::get('/laughter-show-order', [LaughterShows::class, 'getMovieOrderList'])->name('admin.laughtershow.order');
        Route::get('/deleted-laughter-show', [LaughterShows::class, 'deletedChannel'])->name('admin.laughtershow.deleted');
        Route::get('/getDeletedChannelList', [LaughterShows::class, 'getDeletedChannelList'])->name('getDeletedMovieList');
        Route::get('/recover-channel/{id}', [LaughterShows::class, 'recoverChannel'])->name('admin.laughtershow.recoverMovie');
        Route::get('/add-laughter-show', [LaughterShows::class,'addChannel'])->name('addlaughtershow');
        Route::post('/addlaughtershow', [LaughterShows::class,'add'])->name('savelaughtershow');
        Route::post('/save-laughter-show-orders', [LaughterShows::class,'savelaughtershowOrder'])->name('saveLaughterShowOrder');
        Route::get('/edit-laughter-show/{id}', [LaughterShows::class,'editlaughtershow'])->name('edit-laughtershow');
        Route::post('/laughter-show/destroy', [LaughterShows::class,'destroy'])->name('laughtershow.destroy');
        
        Route::get('/laughtershow-order', [LaughterShows::class, 'getLaughterShowOrderList'])->name('admin.laughtershow.order');
        Route::post('/save-laughtershow-orders', [LaughterShows::class,'saveLaughterShowOrder'])->name('saveLaughterShowOrder');
        
        Route::get('/laughter-show/update-status/{id}', [LaughterShows::class,'updateStatus'])->name('laughtershow.update-status');        
        Route::post('import-laughter-show-playlits', [LaughterShows::class, 'importlaughtershows'])->name('importlaughtershows');

        


        //Adult Movies

        Route::middleware(['adultmovies.email'])->group(function (){
            Route::get('/above-18', [AdultMovies::class, 'index'])->name('admin.adultmovies');
            Route::get('/getAdultMoviesList', [AdultMovies::class, 'getAdultMoviesList'])->name('getAdultMoviesList');
            Route::get('/adultmovies-order', [AdultMovies::class, 'getChannelOrderList'])->name('admin.add-above-18.order');
            Route::get('/deleted-above-18', [AdultMovies::class, 'deletedChannel'])->name('admin.add-above-18.deleted');
            Route::get('/getDeletedChannelList', [AdultMovies::class, 'getDeletedChannelList'])->name('getDeletedadd-above-18List');
            Route::get('/recover-channel/{id}', [AdultMovies::class, 'recoverChannel'])->name('admin.add-above-18.recoverMovie');
            Route::get('/add-above-18', [AdultMovies::class,'add'])->name('add-above-18');
            Route::post('/addabove-18', [AdultMovies::class,'save'])->name('save-above-18');
            Route::post('/save-above-18-orders', [AdultMovies::class,'saveChannelOrders'])->name('saveAdultMovieOrders');
            Route::get('/edit-above-18-movie/{id}', [AdultMovies::class,'editChannel'])->name('edit-above-18-movie');
            Route::post('/adultmovies/destroy', [AdultMovies::class,'destroy'])->name('above-18.destroy');
            Route::get('/above-18/update-status/{id}', [AdultMovies::class,'updateStatus'])->name('above-18.update-status');        
            Route::post('import-above18movies-playlits', [AdultMovies::class, 'importmovies'])->name('importadultmovies');

            Route::get('/adult-movie-order', [AdultMovies::class, 'getAdultMovieOrderList'])->name('admin.adultmovie.order');
            Route::post('/save-adult-movie-orders', [AdultMovies::class,'saveAdultMovieOrder'])->name('saveAdultMovieOrder');
        
        });
        

        // Manage Movie Links
        Route::get('/manage-movie-links/{id}', [MovieLinks::class, 'index'])->name('admin.movie.links');
        Route::get('/getMoviesLinkList/{id}', [MovieLinks::class, 'getMoviesLinkList'])->name('getMovieLinkList');
        Route::get('/add-movie-link/{id}', [MovieLinks::class,'addMovieLink'])->name('addMovielink');
        Route::post('/addMovieLink/{id}', [MovieLinks::class,'add'])->name('saveMovieLink');
        Route::get('/edit-movie-link/{movie_id}/{id}', [MovieLinks::class,'edit'])->name('edit-movie-link');
        Route::get('/movie-link/update-status/{id}', [MovieLinks::class,'updateStatus'])->name('movie-link.updatestatus');

        Route::post('/movieLink/destroy', [MovieLinks::class,'destroy'])->name('movie-link.destroy');



        //Web series
        Route::get('/webseries', [Webserieses::class, 'index'])->name('admin.webseries');
        Route::get('/getWebseriesList', [Webserieses::class, 'getWebseriesList'])->name('getWebseriesList');        
        Route::get('/add-webseries', [Webserieses::class,'create'])->name('addWebseries');
        Route::post('/addWebseries', [Webserieses::class,'save'])->name('saveWebseries');        
        Route::get('/edit-webseries/{id}', [Webserieses::class,'edit'])->name('edit-movie');
        Route::get('/webseries-order', [Webserieses::class, 'getWebseriesOrderList'])->name('admin.webseries.order');       
        Route::post('/save-webseries-orders', [Webserieses::class, 'saveWebseriesOrder'])->name('saveWebseriesOrder');
        Route::post('/web-seies/destroy', [Webserieses::class,'destroy'])->name('webseries.destroy');
        Route::get('/web-seies/update-status/{id}', [Webserieses::class,'updateStatus'])->name('movie.update-status');        


        // Web series Seasons

        Route::get('/web-series-season/{id}', [WebseriesSeasons::class, 'index'])->name('admin.webseries.seasons');
        Route::get('/getWebseriesSeasonList/{id}', [WebseriesSeasons::class, 'getWebseriesSeasonList'])->name('getWebseriesSeasonList');
        Route::get('/add-web-series-season/{id}', [WebseriesSeasons::class,'create'])->name('addWebseriesSeason');
        Route::post('/addWebseriesSeason', [WebseriesSeasons::class,'save'])->name('saveWebseriesSeason');        
        Route::get('/edit-web-series-season/{id}', [WebseriesSeasons::class,'edit'])->name('edit-webseries-season');
        Route::get('/web-seies-season/update-status/{id}', [WebseriesSeasons::class,'updateStatus'])->name('webseries.season.update-status');        

        Route::post('/websersiseason-destroy', [WebseriesSeasons::class,'destroy'])->name('webseries-season.destroy');
        Route::get('/webseriesseasons-order/{id}', [WebseriesSeasons::class, 'getWebseriesSeasonsOrderList'])->name('admin.webseriesseason.order');
        Route::post('/save-webseriesseasons-orders', [WebseriesSeasons::class,'saveWebseriesSeasonsOrder'])->name('saveWebseriesSeasonsOrder');



        // Web series Episodes

        Route::get('/web-series-episode/{id}', [WebseriesEpisodes::class, 'index'])->name('admin.webseries.seasons.episodes');
        Route::get('/getWebseriesEpisodesList/{id}', [WebseriesEpisodes::class, 'getWebseriesEpisodeList'])->name('getWebseriesEpisodesList');
        Route::get('/add-web-series-season-episode/{id}', [WebseriesEpisodes::class,'create'])->name('addWebseriesEpisode');
        Route::post('/save-webseries-season-episode', [WebseriesEpisodes::class,'save'])->name('saveWebseriesEpisode');        
        Route::get('/edit-episode/{id}', [WebseriesEpisodes::class,'edit'])->name('episode.edit');
        Route::get('/web-seies-episode/update-status/{id}', [WebseriesEpisodes::class,'updateStatus'])->name('webseries.episode.update-status');        
        Route::post('/websersiepisode/destroy', [WebseriesEpisodes::class,'destroy'])->name('webseries-episode.destroy');
        Route::get('/webseriesepisodes-order/{id}', [WebseriesEpisodes::class, 'getWebseriesEpisodesOrderList'])->name('admin.webseriesepisode.order');
        Route::post('/save-webseriesepisodes-orders', [WebseriesEpisodes::class,'saveWebseriesEpisodesOrder'])->name('saveWebseriesEpisodesOrder');
        Route::post('import-series-episodes-playlits', [WebseriesEpisodes::class, 'importPlaylist'])->name('importPlaylistsereis');



        // 11 june
        // content networks

        Route::get('/content-network', [ContentNetworks::class, 'index'])->name('admin.contentnetwork');
        Route::get('/getWebseriesEpisodesList', [ContentNetworks::class, 'getNetworkList'])->name('getContentnetworkList');
        Route::get('/add-content-network', [ContentNetworks::class,'create'])->name('addcontentnetwork');
        Route::post('/add-content-network-post', [ContentNetworks::class,'save'])->name('savecontentnetwork');        
        Route::get('/edit-content-network', [ContentNetworks::class,'edit'])->name('editNetwork');
        Route::get('/content-network/update-status/{id}', [ContentNetworks::class,'updateStatus'])->name('contentnetwork.update-status');        
        Route::post('/network/destroy', [ContentNetworks::class,'destroy'])->name('contentnetwork.destroy');




        //SuperAdminPlans
        Route::get('/super-admin-plans', [SuperAdminPlans::class, 'index'])->name('admin.superAdminPlans');
        Route::get('/get-sadmin-plan-list', [SuperAdminPlans::class, 'getSadminPlanList'])->name('getSadminPlanList');
        Route::get('/add-s-admin-plan', [SuperAdminPlans::class,'addSadminPlan'])->name('addSadminPlan');
        Route::post('/addSadminPlan', [SuperAdminPlans::class,'add'])->name('saveSadminPlan');
        Route::get('/edit-sadminPlan/{id}', [SuperAdminPlans::class,'editSadminPlan'])->name('edit-sadminPlan');
        Route::post('/sadminPlan/destroy', [SuperAdminPlans::class,'destroy'])->name('sadminPlan.destroy');
        Route::get('/sadminPlan/update-status/{id}', [SuperAdminPlans::class,'updateStatus'])->name('sadminPlan.update-status');
        Route::post('/get-channel-by-genre', [SuperAdminPlans::class,'getChannelByGenre'])->name('get-channel-by-genre');

        //SuperAdminWallet
        Route::get('/super-admin-wallets', [SuperAdminWallet::class, 'index'])->name('admin.superAdminWallet');
        Route::get('/get-sadmin-wallet-list', [SuperAdminWallet::class, 'getSadminWalletList'])->name('getSadminWalletList');
        Route::get('/add-s-admin-wallet', [SuperAdminWallet::class,'addSadminWallet'])->name('addSadminWallet');
        Route::post('/addSadminWallet', [SuperAdminWallet::class,'add'])->name('saveSadminWallet');
        Route::get('/edit-sadminWallet/{id}', [SuperAdminWallet::class,'editSadminWallet'])->name('edit-sadminWallet');
        Route::post('/sadminWallet/destroy', [SuperAdminWallet::class,'destroy'])->name('sadminWallet.destroy');
        Route::get('/sadminWallet/update-status/{id}', [SuperAdminWallet::class,'updateStatus'])->name('sadminWallet.update-status');

        //AdminWallets
        Route::get('/admin-wallets', [AdminWallets::class, 'index'])->name('admin.AdminWallet');
        Route::get('/get-admin-wallet-list', [AdminWallets::class, 'getAdminWalletList'])->name('getAdminWalletList');
        Route::get('/add-admin-wallet', [AdminWallets::class,'addAdminWallet'])->name('addAdminWallet');
        Route::post('/addAdminWallet', [AdminWallets::class,'add'])->name('saveAdminWallet');
        Route::get('/edit-adminWallet/{id}', [AdminWallets::class,'editAdminWallet'])->name('edit-adminWallet');
        Route::post('/adminWallet/destroy', [AdminWallets::class,'destroy'])->name('adminWallet.destroy');
        Route::get('/adminWallet/update-status/{id}', [AdminWallets::class,'updateStatus'])->name('adminWallet.update-status');

        //AdminPlans
        Route::get('/admin-plans', [AdminPlans::class, 'index'])->name('admin.adminPlans');
        Route::get('/get-admin-plan-list', [AdminPlans::class, 'getAdminPlanList'])->name('getAdminPlanList');
        Route::get('/add-admin-plan', [AdminPlans::class,'addAdminPlan'])->name('addAdminPlan');
        Route::post('/addAdminPlan', [AdminPlans::class,'add'])->name('saveAdminPlan');
        Route::get('/edit-adminPlan/{id}', [AdminPlans::class,'editadminPlan'])->name('edit-adminPlan');
        Route::post('/adminPlan/destroy', [AdminPlans::class,'destroy'])->name('adminPlan.destroy');
        Route::get('/adminPlan/update-status/{id}', [AdminPlans::class,'updateStatus'])->name('adminPlan.update-status');
        Route::post('/get-channels-of-plan', [AdminPlans::class,'checkChannelsOfPlan'])->name('get-channels-of-plan');

        Route::post('/get-price', [AdminPlans::class,'getPrice'])->name('get-price');
        Route::post('/get-plan-details', [AdminPlans::class,'getPlanDetails'])->name('get-plan-details');
        Route::post('/check-max-selling-price-for-admin', [AdminPlans::class,'checkMaxSellingPriceForAdmin'])->name('check-max-selling-price-for-admin');
        Route::post('/check-wallet-admin', [ManageUser::class,'checkWalletAdmin'])->name('check-wallet-admin');

        //ResellerWallets
        Route::get('/reseller-wallets', [ResellerWallets::class, 'index'])->name('reseller.resellerWallet');
        Route::get('/get-reseller-wallet-list', [ResellerWallets::class, 'getResellerWalletList'])->name('getResellerWalletList');
        Route::get('/add-reseller-wallet', [ResellerWallets::class,'addResellerWallet'])->name('addResellerWallet');
        Route::post('/saveResellerWallet', [ResellerWallets::class,'add'])->name('saveResellerWallet');
        Route::get('/edit-adminWallet/{id}', [ResellerWallets::class,'editAdminWallet'])->name('edit-adminWallet');
        Route::post('/adminWallet/destroy', [ResellerWallets::class,'destroy'])->name('adminWallet.destroy');
        Route::get('/adminWallet/update-status/{id}', [ResellerWallets::class,'updateStatus'])->name('adminWallet.update-status');

        //Reseller Plan
        Route::get('/reseller-plans', [ResellerPlanController::class, 'index'])->name('reseller.resellerPlans');
        Route::get('/get-reseller-plan-list', [ResellerPlanController::class, 'getResellerPlanList'])->name('getResellerPlanList');
        Route::get('/add-reseller-plan', [ResellerPlanController::class,'addResellerPlan'])->name('addResellerPlan');
        Route::get('/edit-resellerPlan/{id}', [ResellerPlanController::class,'editResellerPlan'])->name('edit-resellerPlan');
        Route::post('/addResellerPlan', [ResellerPlanController::class,'add'])->name('saveResellerPlan');
        Route::get('/resellerPlan/update-status/{id}', [ResellerPlanController::class,'updateStatus'])->name('resellerPlan.update-status');
        Route::post('/get-admin-plan-price', [ResellerPlanController::class,'getPrice'])->name('get-admin-plan-price');
        Route::post('/get-admin-plan-details', [ResellerPlanController::class,'getPlanDetails'])->name('get-admin-plan-details');
        Route::post('/resellerPlan/destroy', [ResellerPlanController::class,'destroy'])->name('resellerPlan.destroy');
        Route::post('/check-max-selling-price-for-reseller', [ResellerPlanController::class,'checkMaxSellingPriceForReseller'])->name('check-max-selling-price-for-reseller');
        Route::post('/get-channels-of-plan-reseller', [ResellerPlanController::class,'checkChannelsOfPlan'])->name('get-channels-of-plan-reseller');

        Route::get('/plan-max-price', [Controller::class,'planMaxPrice'])->name('plan-max-price');
        Route::post('/update-plan-max-price', [Controller::class,'updatePlanMaxPrice'])->name('updatePlanMaxPrice');

        // Kids channel
        Route::get('/kids-channel', [ManageKidsChannel::class, 'index'])->name('admin.kidschannel');
        Route::get('/getKidsChannelList', [ManageKidsChannel::class, 'getKidsChannelList'])->name('getKidsChannelList');
        Route::get('/kids-channel-add', [ManageKidsChannel::class, 'add'])->name('admin.kidschannel.add');
        Route::post('/kids-channel-save', [ManageKidsChannel::class, 'save'])->name('admin.kidschannel.save');
        Route::get('/kids-channel-edit/{id}', [ManageKidsChannel::class, 'edit'])->name('admin.kidschannel.edit');
        Route::post('/kids-channel-delete', [ManageKidsChannel::class, 'destroy'])->name('admin.kidschannel.destroy');
        Route::get('/kids-channel-update-status/{id}', [ManageKidsChannel::class,'updateStatus'])->name('admin.kidschannel.update-status');
        Route::get('/kids-channel-order', [ManageKidsChannel::class, 'getKidsChannelOrderList'])->name('admin.kidschannel.order');
        Route::post('/save-kidschannel-orders', [ManageKidsChannel::class,'saveKidsChannelOrder'])->name('saveKidsChannelOrder');
        


        // 

        
        // Kids channel shows
        Route::get('/kids-shows/{id}', [ManageKidsShows::class, 'index'])->name('admin.kidsshows');
        Route::get('/getKidsShowList/{id}', [ManageKidsShows::class, 'getKidsShowList'])->name('getKidsShowList');
        Route::get('/kids-shows-add/{id}', [ManageKidsShows::class,'add'])->name('admin.kidsshows.add');
        Route::post('/kids-shows-save', [ManageKidsShows::class,'save'])->name('admin.kidsshows.save');        
        Route::get('/kids-shows-edit/{id}', [ManageKidsShows::class,'edit'])->name('admin.kidsshows.edit');
        Route::get('/kids-shows-update-status/{id}', [ManageKidsShows::class,'updateStatus'])->name('admin.kidsshows.update-status');        
        Route::post('/kids-shows-destroy', [ManageKidsShows::class,'destroy'])->name('admin.kidsshows.destroy');
        Route::get('/kids-show-order/{id}', [ManageKidsShows::class, 'getKidsShowOrderList'])->name('admin.kidsshow.order');
        Route::post('/save-kids-show-orders', [ManageKidsShows::class, 'saveKidsShowOrder'])->name('saveKidsShowOrder');

        
        // Kid shows Seasons

        Route::get('/kid-shows-season/{id}', [KidsShowSeasons::class, 'index'])->name('admin.kidshowsseason');
        Route::get('/getKidShowsSeasonList/{id}', [KidsShowSeasons::class, 'getKidShowsSeasonList'])->name('getKidShowsSeasonList');
        Route::get('/add-kid-shows-season/{id}', [KidsShowSeasons::class,'create'])->name('addKidShowsSeason');
        Route::post('/addKidshowsSeason', [KidsShowSeasons::class,'save'])->name('saveKidShowsSeason');        
        Route::get('/edit-kid-shows-season/{id}', [KidsShowSeasons::class,'edit'])->name('edit-tvshows-season');
        Route::get('/kids-shows-season/update-status/{id}', [KidsShowSeasons::class,'updateStatus'])->name('tvshows.season.update-status');        

        Route::post('/websersiseason/destroy', [KidsShowSeasons::class,'destroy'])->name('tvshows-season.destroy');
        Route::get('/kids-showseason-order/{id}', [KidsShowSeasons::class, 'getKidsShowSeasonsOrderList'])->name('admin.kidsshowseason.order');
        Route::post('/save-kids-showseasons-orders', [KidsShowSeasons::class,'saveKidsShowSeasonOrders'])->name('saveKidsShowSeasonOrders');


        // Kid shows Episodes
        Route::get('/kid-shows-episode/{id}', [KidsShowEpisodes::class, 'index'])->name('admin.kid-shows.episodes');
        Route::get('/getKidsShowEpisodesList/{id}', [KidsShowEpisodes::class, 'getWebseriesEpisodeList'])->name('getKidsShowEpisodesList');
        Route::get('/add-kid-shows-episode/{id}', [KidsShowEpisodes::class,'create'])->name('addkidshowsEpisode');
        Route::post('/addWebseriesSeasonEpisode', [KidsShowEpisodes::class,'save'])->name('savekidshowsEpisode');        
        Route::get('/edit-kid-shows-episode/{id}', [KidsShowEpisodes::class,'edit'])->name('kid-shows.edit');
        Route::get('/kid-shows-episode/update-status/{id}', [KidsShowEpisodes::class,'updateStatus'])->name('kid-shows.episode.update-status');        
        Route::post('/kid-shows-episode/destroy', [KidsShowEpisodes::class,'destroy'])->name('kid-shows-episode.destroy');
        Route::get('/kids-showepisode-order/{id}', [KidsShowEpisodes::class, 'getKidsShowEpisodesOrderList'])->name('admin.kidsshowepisode.order');
        Route::post('/save-kids-showepisode-orders', [KidsShowEpisodes::class,'saveKidsShowEpisodesOrders'])->name('saveKidsShowEpisodesOrders');

        Route::post('import-kidshows-episode-playlits', [KidsShowEpisodes::class, 'importPlaylist'])->name('importkidshowsepisode');


        // Religious Channels
        Route::get('/religious-channel', [ManageRelChannel::class, 'index'])->name('admin.RelChannel');
        Route::get('/getRelChannelList', [ManageRelChannel::class, 'getRelChannelList'])->name('getRelChannelList');
        Route::get('/religious-channel-add', [ManageRelChannel::class, 'add'])->name('admin.RelChannel.add');
        Route::post('/religious-channel-save', [ManageRelChannel::class, 'save'])->name('admin.RelChannel.save');
        Route::get('/religious-channel-edit/{id}', [ManageRelChannel::class, 'edit'])->name('admin.RelChannel.edit');
        Route::post('/religious-channel-delete', [ManageRelChannel::class, 'destroy'])->name('admin.RelChannel.destroy');
        Route::get('/religious-channel-update-status/{id}', [ManageRelChannel::class,'updateStatus'])->name('admin.RelChannel.update-status');
        Route::get('/religious-channel-order', [ManageRelChannel::class, 'getRelChannelOrderList'])->name('admin.relchannel.order');
        Route::post('/save-religious-channel-orders', [ManageRelChannel::class,'saveRelChannelOrder'])->name('saveRelChannelOrder');
                

        // Religious shows 
        
        Route::get('/religious-shows/{id}', [ManageRelShows::class, 'index'])->name('admin.Relshows');
        Route::get('/getRelShowList/{id}', [ManageRelShows::class, 'getRelShowList'])->name('getRelShowList');
        Route::get('/religious-shows-add/{id}', [ManageRelShows::class,'add'])->name('admin.Relshows.add');
        Route::post('/religious-shows-save', [ManageRelShows::class,'save'])->name('admin.Relshows.save');        
        Route::get('/religious-shows-edit/{id}', [ManageRelShows::class,'edit'])->name('admin.Relshows.edit');
        Route::get('/religious-shows-update-status/{id}', [ManageRelShows::class,'updateStatus'])->name('admin.Relshows.update-status');        
        Route::post('/religious-shows-destroy', [ManageRelShows::class,'destroy'])->name('admin.Relshows.destroy');
        Route::get('/relegious-show-order/{id}', [ManageRelShows::class, 'getRelShowOrderList'])->name('admin.relshows.order');
        Route::post('/save-relegious-show-orders', [ManageRelShows::class, 'saveRelShowOrder'])->name('saveRelShowOrder');

        // Religious Episodes
        Route::get('/religious-episodes/{id}', [RelShowEpisodes::class, 'index'])->name('admin.rel_episodes.episodes');
        Route::get('/getRelShowEpisodesList/{id}', [RelShowEpisodes::class, 'getRelShowEpisodesList'])->name('getRelShowEpisodesList');
        Route::get('/add-religious-episode/{id}', [RelShowEpisodes::class,'create'])->name('addrel_episodes');
        Route::post('/saveReligiousEpisode', [RelShowEpisodes::class,'save'])->name('saverel_episodes');        
        Route::get('/edit-religious-episode/{id}', [RelShowEpisodes::class,'edit'])->name('rel_episodes.edit');
        Route::get('/religious-episode/update-status/{id}', [RelShowEpisodes::class,'updateStatus'])->name('rel_episodes.update-status');        
        Route::post('/religious-episode/destroy', [RelShowEpisodes::class,'destroy'])->name('rel_episodes.destroy');
        Route::get('/relshowepisode-order/{id}', [RelShowEpisodes::class, 'getRelShowEpisodesOrderList'])->name('admin.relshowepisode.order');
        Route::post('/save-relshowepisode-orders', [RelShowEpisodes::class,'saveRelShowEpisodesOrders'])->name('saveRelShowEpisodesOrders');


        Route::post('import-religious-episode-playlits', [RelShowEpisodes::class, 'importreligiousepisode'])->name('importreligiousepisode');


        // Super Admin Ads
        Route::get('/super-admin-ads', [SuperAdminAdPlan::class, 'index'])->name('admin.supreradmin.ads');
        Route::get('/getSuperAdminAdsList', [SuperAdminAdPlan::class, 'getSuperAdminAdsList'])->name('getSuperAdminAdsList');
        Route::get('/super-admin-ads-add', [SuperAdminAdPlan::class, 'create'])->name('admin.supreradmin.ads.add');
        Route::get('/super-admin-ads-edit/{id}', [SuperAdminAdPlan::class, 'edit'])->name('admin.supreradmin.ads.edit');
        Route::post('/super-admin-ads-save', [SuperAdminAdPlan::class, 'save'])->name('admin.supreradmin.ads.save');
        Route::post('/super-admin-ads-remove', [SuperAdminAdPlan::class, 'destroy'])->name('admin.supreradmin.ads.remove');
        Route::get('/super-admin-update-status/{id}', [SuperAdminAdPlan::class, 'updateStatus'])->name('admin.supreradmin.ads.update_status');

        // Admin Ads plan
        Route::get('/admin-ads-plan', [AdminAd::class, 'index'])->name('admin.admin.adsplan');
        Route::get('/getAdminAdsPlanList', [AdminAd::class, 'getAdminAdsPlanList'])->name('getAdminAdsPlanList');
        Route::get('/admin-ads-plan-add', [AdminAd::class, 'create'])->name('admin.admin.adsplan.add');
        Route::get('/admin-ads-plan-edit/{id}', [AdminAd::class, 'edit'])->name('admin.admin.adsplan.edit');
        Route::post('/admin-ads-plan-save', [AdminAd::class, 'save'])->name('admin.admin.adsplan.save');
        Route::post('/admin-ads-plan-remove', [AdminAd::class, 'destroy'])->name('admin.admin.adsplan.remove');
        Route::get('/admin-ads-plan-update-status/{id}', [AdminAd::class, 'updateStatus'])->name('admin.admin.adsplan.update_status');

        // Admin adss
        Route::get('/admin-ads', [AdminAdPlan::class, 'index'])->name('admin.admin.ads');
        Route::get('/getAdminAdsList', [AdminAdPlan::class, 'getAdminAdsList'])->name('getAdminAdsList');
        Route::get('/admin-ads-add', [AdminAdPlan::class, 'create'])->name('admin.admin.ad.add');
        Route::get('/admin-ads-edit/{id}', [AdminAdPlan::class, 'edit'])->name('admin.admin.ads.edit');
        Route::post('/admin-ads-save', [AdminAdPlan::class, 'save'])->name('admin.admin.ads.save');
        Route::post('/admin-ads-remove', [AdminAdPlan::class, 'destroy'])->name('admin.admin.ads.remove');
        Route::get('/admin-ads-update-status/{id}', [AdminAdPlan::class, 'updateStatus'])->name('admin.admin.ads.update_status');


        // Admin Ads
        // Route::get('/admin-ads', [AdminAd::class, 'index'])->name('admin.admin.ads');
        // Route::get('/getAdminAdsList', [AdminAd::class, 'getAdminAdsList'])->name('getAdminAdsList');
        // Route::get('/admin-ads-add', [AdminAd::class, 'create'])->name('admin.admin.ads.add');
        // Route::get('/admin-ads-edit/{id}', [AdminAd::class, 'edit'])->name('admin.admin.ads.edit');
        // Route::post('/admin-ads-save', [AdminAd::class, 'save'])->name('admin.admin.ads.save');
        // Route::post('/admin-ads-remove', [AdminAd::class, 'destroy'])->name('admin.admin.ads.remove');
        // Route::get('/admin-ads-update-status/{id}', [AdminAd::class, 'updateStatus'])->name('admin.admin.ads.update_status');

        Route::get('/get-ad-details', [AdminAd::class, 'getAdDetails'])->name('get-ad-details');


        // Reseller ads 
        Route::get('/reseller-ads', [ResellerAd::class, 'index'])->name('admin.reseller.ads');
        Route::get('/getResellerAdsList', [ResellerAd::class, 'getResellerAdsList'])->name('getResellerAdsList');
        Route::get('/reseller-ads-add', [ResellerAd::class, 'create'])->name('reseller.ad.add');
        Route::get('/reseller-ads-edit/{id}', [ResellerAd::class, 'edit'])->name('reseller.ads.edit');
        Route::post('/reseller-ads-save', [ResellerAd::class, 'save'])->name('reseller.ads.save');
        Route::post('/reseller-ads-remove', [ResellerAd::class, 'destroy'])->name('reseller.ads.remove');
        Route::get('/reseller-ads-update-status/{id}', [ResellerAd::class, 'updateStatus'])->name('reseller.ads.update_status');
        Route::get('/get-ad-details-for-reseller', [ResellerAd::class, 'getAdDetails'])->name('get-ad-details-for-reseller');

        
    });
     /*admin forgot password*/
     Route::get('forget-password', [AuthAdmin::class, 'showForgetPasswordForm'])->name('forget.password.get');
     Route::post('forget-password', [AuthAdmin::class, 'submitForgetPasswordForm'])->name('forget.password.post');
     Route::get('reset-password/{token}', [AuthAdmin::class, 'showResetPasswordForm'])->name('reset.password.get');
     Route::post('reset-password', [AuthAdmin::class, 'submitResetPasswordForm'])->name('reset.password.post');
});





