import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { WebPlayerPageRoutingModule } from './web-player-routing.module';

import { WebPlayerPage } from './web-player.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    WebPlayerPageRoutingModule
  ],
  declarations: [WebPlayerPage]
})
export class WebPlayerPageModule {}
