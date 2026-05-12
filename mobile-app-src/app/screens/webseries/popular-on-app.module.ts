import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { PopularOnAppPageRoutingModule } from './popular-on-app-routing.module';

import { PopularOnAppPage } from './popular-on-app.page';
import { SharedComponentsModule } from 'src/app/components/shared-components.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    SharedComponentsModule,
    PopularOnAppPageRoutingModule
  ],
  declarations: [PopularOnAppPage],
  schemas:[CUSTOM_ELEMENTS_SCHEMA],
})
export class PopularOnAppPageModule {}
