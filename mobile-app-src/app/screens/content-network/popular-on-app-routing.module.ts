import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { PopularOnAppPage } from './popular-on-app.page';

const routes: Routes = [
  {
    path: '',
    component: PopularOnAppPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class PopularOnAppPageRoutingModule {}
