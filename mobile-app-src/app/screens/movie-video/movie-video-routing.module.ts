import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { MovieVideoPage } from './movie-video.page';

const routes: Routes = [
  {
    path: '',
    component: MovieVideoPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class MovieVideoPageRoutingModule {}
