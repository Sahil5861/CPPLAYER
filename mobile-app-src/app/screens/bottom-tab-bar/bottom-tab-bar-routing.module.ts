import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { BottomTabBarPage } from './bottom-tab-bar.page';
import { AuthloggedGuard } from 'src/app/guards/authlogged.guard';

const routes: Routes = [
  {
    path: '',
    component: BottomTabBarPage,
    children:[
      
      {
        path: 'home2',
        loadChildren: () => import('../home2/home2.module').then( m => m.HomePage2Module)
      },
      {
        path: 'search',
        loadChildren: () => import('../../screens/search/search.module').then( m => m.SearchPageModule)
      },
      {
        path: 'movies',
        loadChildren: () => import('../movies/popular-on-app.module').then( m => m.PopularOnAppPageModule)
      },
      {
        path: 'webseries',
        loadChildren: () => import('../webseries/popular-on-app.module').then( m => m.PopularOnAppPageModule)
      },
      {
        path: 'live',
        loadChildren: () => import('../live/popular-on-app.module').then( m => m.PopularOnAppPageModule)
      },
      {
        path: 'profile',
        loadChildren: () => import('../../screens/profile/profile.module').then( m => m.ProfilePageModule)
      },
      {
        path:'',
        redirectTo:'home2',
        pathMatch: 'full',
      //   canActivate:[AuthloggedGuard]
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class BottomTabBarPageRoutingModule {}
