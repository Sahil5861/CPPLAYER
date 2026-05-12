import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { ContentNetworksPageRoutingModule } from './content-networks-routing.module';

import { ContentNetworksPage } from './content-networks.page';
import { SharedComponentsModule } from 'src/app/components/shared-components.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    SharedComponentsModule,
    ContentNetworksPageRoutingModule
  ],
  declarations: [ContentNetworksPage],
  schemas:[CUSTOM_ELEMENTS_SCHEMA],
})
export class ContentNetworksPageModule {}
