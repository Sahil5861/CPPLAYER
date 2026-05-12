import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { Over18Page } from './over18.page';

const routes: Routes = [
  {
    path: '',
    component: Over18Page,
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class Over18PageRoutingModule {}
