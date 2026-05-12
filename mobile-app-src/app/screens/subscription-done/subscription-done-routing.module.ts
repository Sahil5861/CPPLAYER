import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { SubscriptionDonePage } from './subscription-done.page';

const routes: Routes = [
  {
    path: '',
    component: SubscriptionDonePage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class SubscriptionDonePageRoutingModule {}
