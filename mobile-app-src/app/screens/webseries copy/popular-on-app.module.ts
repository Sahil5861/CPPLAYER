import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { PopularOnAppPageRoutingModule } from './popular-on-app-routing.module';

import { PopularOnAppPage } from './popular-on-app.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    PopularOnAppPageRoutingModule
  ],
  declarations: [PopularOnAppPage]
})
export class PopularOnAppPageModule {}
