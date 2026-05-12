import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { EpisodesListPageRoutingModule } from './episodes-list-routing.module';

import { EpisodesListPage } from './episodes-list.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    EpisodesListPageRoutingModule
  ],
  declarations: [EpisodesListPage]
})
export class EpisodesListPageModule {}
