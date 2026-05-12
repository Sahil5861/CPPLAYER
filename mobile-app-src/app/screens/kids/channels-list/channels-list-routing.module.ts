import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ChannelsListPage } from './channels-list.page';

const routes: Routes = [
  {
    path: '',
    component: ChannelsListPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ChannelsListPageRoutingModule {}
