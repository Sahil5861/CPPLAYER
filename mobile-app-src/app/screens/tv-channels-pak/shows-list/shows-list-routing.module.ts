import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ShowsListPage } from './shows-list.page';

const routes: Routes = [
  {
    path: '',
    component: ShowsListPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ShowsListPageRoutingModule {}
