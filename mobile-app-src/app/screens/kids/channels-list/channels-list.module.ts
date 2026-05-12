import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ChannelsListPageRoutingModule } from './channels-list-routing.module';

import { ChannelsListPage } from './channels-list.page';
import { SharedComponentsModule } from 'src/app/components/shared-components.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    ChannelsListPageRoutingModule,
    SharedComponentsModule
  ],
  declarations: [ChannelsListPage],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class ChannelsListPageModule {}
