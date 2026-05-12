import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ContentNetworksPage } from './content-networks.page';

const routes: Routes = [
  {
    path: '',
    component: ContentNetworksPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class ContentNetworksPageRoutingModule {}
