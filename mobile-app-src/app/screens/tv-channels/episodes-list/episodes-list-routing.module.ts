import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { EpisodesListPage } from './episodes-list.page';

const routes: Routes = [
  {
    path: '',
    component: EpisodesListPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class EpisodesListPageRoutingModule {}
