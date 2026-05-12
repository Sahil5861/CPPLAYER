import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { TournamentListPage } from './tournament-list.page';

const routes: Routes = [
  {
    path: '',
    component: TournamentListPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TournamentListPageRoutingModule {}
