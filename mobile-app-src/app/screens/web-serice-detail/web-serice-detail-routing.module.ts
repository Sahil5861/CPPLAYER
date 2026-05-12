import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { WebSericeDetailPage } from './web-serice-detail.page';

const routes: Routes = [
  {
    path: '',
    component: WebSericeDetailPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class WebSericeDetailPageRoutingModule {}
