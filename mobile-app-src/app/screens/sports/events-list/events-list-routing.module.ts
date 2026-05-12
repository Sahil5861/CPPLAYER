import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { EventsListPage } from './events-list.page';

const routes: Routes = [
  {
    path: '',
    component: EventsListPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class EventsListPageRoutingModule {}
