import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { LiveDetailsPageRoutingModule } from './live-details-routing.module';

import { LiveDetailsPage } from './live-details.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    LiveDetailsPageRoutingModule
  ],
  declarations: [LiveDetailsPage]
})
export class LiveDetailsPageModule {}
