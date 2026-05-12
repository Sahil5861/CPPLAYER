import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { WebPlayerPage } from './web-player.page';

const routes: Routes = [
  {
    path: '',
    component: WebPlayerPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class WebPlayerPageRoutingModule {}
