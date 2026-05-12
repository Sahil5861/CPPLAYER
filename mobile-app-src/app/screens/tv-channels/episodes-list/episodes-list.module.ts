import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { EpisodesListPageRoutingModule } from './episodes-list-routing.module';

import { EpisodesListPage } from './episodes-list.page';
import { SharedComponentsModule } from 'src/app/components/shared-components.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    EpisodesListPageRoutingModule,
    SharedComponentsModule
  ],
  declarations: [EpisodesListPage]
})
export class EpisodesListPageModule {}
