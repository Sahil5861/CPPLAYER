import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { WebSericeDetailPageRoutingModule } from './web-serice-detail-routing.module';

import { WebSericeDetailPage } from './web-serice-detail.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    WebSericeDetailPageRoutingModule
  ],
  declarations: [WebSericeDetailPage]
})
export class WebSericeDetailPageModule {}
