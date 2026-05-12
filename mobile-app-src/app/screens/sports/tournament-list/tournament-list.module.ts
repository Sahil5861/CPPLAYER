import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { TournamentListPageRoutingModule } from './tournament-list-routing.module';

import { TournamentListPage } from './tournament-list.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    TournamentListPageRoutingModule
  ],
  declarations: [TournamentListPage]
})
export class TournamentListPageModule {}
