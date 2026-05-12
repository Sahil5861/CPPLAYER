import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { ApiService } from './api.service';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'splash',
    pathMatch: 'full',
  },
  {
    path: 'splash',
    loadChildren: () =>
      import('./screens/splash/splash.module').then((m) => m.SplashPageModule),
  },
  {
    path: 'bottom-tab-bar',
    loadChildren: () =>
      import('./screens/bottom-tab-bar/bottom-tab-bar.module').then(
        (m) => m.BottomTabBarPageModule
      ),
  },
  {
    path: 'web-serice-detail',
    loadChildren: () =>
      import('./screens/web-serice-detail/web-serice-detail.module').then(
        (m) => m.WebSericeDetailPageModule
      ),
  },
  {
    path: 'episodes/:id',
    loadChildren: () =>
      import('./screens/episodes/episodes.module').then(
        (m) => m.EpisodesPageModule
      ),
  },
  {
    path: 'popular-on-app/:any',
    loadChildren: () =>
      import('./screens/popular-on-app/popular-on-app.module').then(
        (m) => m.PopularOnAppPageModule
      ),
  },
  {
    path: 'movie-detail/:id/:type',
    loadChildren: () =>
      import('./screens/movie-video/movie-video.module').then(
        (m) => m.MovieVideoPageModule
      ),
  },
  // {
  //   path: 'movie-detail/:id/:type',
  //   loadChildren: () => import('./screens/movie-detail/movie-detail.module').then( m => m.MovieDetailPageModule)
  // },
  {
    path: 'movie-video',
    loadChildren: () =>
      import('./screens/movie-video/movie-video.module').then(
        (m) => m.MovieVideoPageModule
      ),
  },
  {
    path: 'edit-profile',
    loadChildren: () =>
      import('./screens/edit-profile/edit-profile.module').then(
        (m) => m.EditProfilePageModule
      ),
  },
  {
    path: 'notifications',
    loadChildren: () =>
      import('./screens/notifications/notifications.module').then(
        (m) => m.NotificationsPageModule
      ),
  },
  {
    path: 'watchlist',
    loadChildren: () =>
      import('./screens/watchlist/watchlist.module').then(
        (m) => m.WatchlistPageModule
      ),
  },
  {
    path: 'downloads',
    loadChildren: () =>
      import('./screens/downloads/downloads.module').then(
        (m) => m.DownloadsPageModule
      ),
  },
  {
    path: 'subscription',
    loadChildren: () =>
      import('./screens/subscription/subscription.module').then(
        (m) => m.SubscriptionPageModule
      ),
  },
  {
    path: 'subscription-payment',
    loadChildren: () =>
      import('./screens/subscription-payment/subscription-payment.module').then(
        (m) => m.SubscriptionPaymentPageModule
      ),
  },
  {
    path: 'subscription-done',
    loadChildren: () =>
      import('./screens/subscription-done/subscription-done.module').then(
        (m) => m.SubscriptionDonePageModule
      ),
  },
  {
    path: 'settings',
    loadChildren: () =>
      import('./screens/settings/settings.module').then(
        (m) => m.SettingsPageModule
      ),
  },
  {
    path: 'terms-and-condition/:any',
    loadChildren: () =>
      import('./screens/terms-and-condition/terms-and-condition.module').then(
        (m) => m.TermsAndConditionPageModule
      ),
  },
  {
    path: 'support',
    loadChildren: () =>
      import('./screens/support/support.module').then(
        (m) => m.SupportPageModule
      ),
  },
  {
    path: 'onboarding',
    loadChildren: () =>
      import('./screens/onboarding/onboarding.module').then(
        (m) => m.OnboardingPageModule
      ),
    canActivate: [ApiService],
  },
  {
    path: 'player',
    loadChildren: () =>
      import('./screens/player/player.module').then((m) => m.PlayerPageModule),
  },
  {
    path: 'auth',
    loadChildren: () =>
      import('./screens/auth/auth.module').then((m) => m.AuthPageModule),
  },
  {
    path: 'video-player',
    loadChildren: () =>
      import('./screens/video-player/video-player.module').then(
        (m) => m.VideoPlayerPageModule
      ),
  },
  {
    path: 'over18',
    loadChildren: () =>
      import('./screens/over18/over18.module').then(
        (m) => m.Over18PageModule
      ),
  },
  {
    path: 'web-player',
    loadChildren: () =>
      import('./web-player/web-player.module').then(
        (m) => m.WebPlayerPageModule
      ),
  },
  {
    path: 'live-details',
    loadChildren: () =>
      import('./screens/live-details/live-details.module').then(
        (m) => m.LiveDetailsPageModule
      ),
  },
  {
    path: 'channels-list',
    loadChildren: () =>
      import('./screens/religious/channels-list/channels-list.module').then(
        (m) => m.ChannelsListPageModule
      ),
  },
  {
    path: 'show-list',
    loadChildren: () =>
      import('./screens/religious/show-list/show-list.module').then(
        (m) => m.ShowListPageModule
      ),
  },
  {
    path: 'episodes-list',
    loadChildren: () =>
      import('./screens/religious/episodes-list/episodes-list.module').then(
        (m) => m.EpisodesListPageModule
      ),
  },
  {
    path: 'stageshow',
    loadChildren: () =>
      import('./screens/stageshow/popular-on-app.module').then(
        (m) => m.PopularOnAppPageModule
      ),
  },
  {
    path: 'religious',
    children: [
      {
        path: 'channels-list',
        loadChildren: () =>
          import('./screens/religious/channels-list/channels-list.module').then(
            (m) => m.ChannelsListPageModule
          ),
      },
      {
        path: 'show-list/:channelId',
        loadChildren: () =>
          import('./screens/religious/show-list/show-list.module').then(
            (m) => m.ShowListPageModule
          ),
      },
      {
        path: 'episodes-list/:episodeId',
        loadChildren: () =>
          import('./screens/religious/episodes-list/episodes-list.module').then(
            (m) => m.EpisodesListPageModule
          ),
      },
    ],
  },
  {
    path: 'tv-channels',
    children: [
      {
        path: 'channels-list',
        loadChildren: () =>
          import(
            './screens/tv-channels/channels-list/channels-list.module'
          ).then((m) => m.ChannelsListPageModule),
      },
      {
        path: 'shows-list/:channelId',
        loadChildren: () =>
          import('./screens/tv-channels/shows-list/shows-list.module').then(
            (m) => m.ShowsListPageModule
          ),
      },
      {
        path: 'episodes-list/:seasonsId',
        loadChildren: () =>
          import(
            './screens/tv-channels/episodes-list/episodes-list.module'
          ).then((m) => m.EpisodesListPageModule),
      },
    ],
  },
  {
    path: 'tv-channels-pak',
    children: [
      {
        path: 'channels-list',
        loadChildren: () =>
          import(
            './screens/tv-channels-pak/channels-list/channels-list.module'
          ).then((m) => m.ChannelsListPageModule),
      },
      {
        path: 'shows-list/:channelId',
        loadChildren: () =>
          import('./screens/tv-channels-pak/shows-list/shows-list.module').then(
            (m) => m.ShowsListPageModule
          ),
      },
      {
        path: 'episodes-list/:seasonsId',
        loadChildren: () =>
          import(
            './screens/tv-channels-pak/episodes-list/episodes-list.module'
          ).then((m) => m.EpisodesListPageModule),
      },
    ],
  },
  {
    path: 'kids',
    children: [
      {
        path: 'channels-list',
        loadChildren: () =>
          import('./screens/kids/channels-list/channels-list.module').then(
            (m) => m.ChannelsListPageModule
          ),
      },
      {
        path: 'shows-list/:channelId',
        loadChildren: () =>
          import('./screens/kids/shows-list/shows-list.module').then(
            (m) => m.ShowsListPageModule
          ),
      },
      {
        path: 'episodes-list/:seasonsId',
        loadChildren: () =>
          import('./screens/kids/episodes-list/episodes-list.module').then(
            (m) => m.EpisodesListPageModule
          ),
      },
    ],
  },
  {
    path: 'sports',
    children: [
      {
        path: 'category-list',
        loadChildren: () =>
          import('./screens/sports/category-list/category-list.module').then(
            (m) => m.CategoryListPageModule
          ),
      },
      {
        path: 'tournament-list/:categoryId',
        loadChildren: () =>
          import(
            './screens/sports/tournament-list/tournament-list.module'
          ).then((m) => m.TournamentListPageModule),
      },
      {
        path: 'events-list/:seasonId',
        loadChildren: () =>
          import('./screens/sports/events-list/events-list.module').then(
            (m) => m.EventsListPageModule
          ),
      },
    ],
  },
  {
    path: 'content-networks/:networkId',
    loadChildren: () => import('./screens/content-networks/content-networks.module').then( m => m.ContentNetworksPageModule)
  },

];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules }),
  ],
  exports: [RouterModule],
})
export class AppRoutingModule {}
