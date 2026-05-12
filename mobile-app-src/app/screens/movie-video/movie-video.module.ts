import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { MovieVideoPageRoutingModule } from './movie-video-routing.module';

import { MovieVideoPage } from './movie-video.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    MovieVideoPageRoutingModule
  ],
  declarations: [MovieVideoPage]
})
export class MovieVideoPageModule {}
