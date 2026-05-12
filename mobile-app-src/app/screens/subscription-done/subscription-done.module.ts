import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { SubscriptionDonePageRoutingModule } from './subscription-done-routing.module';

import { SubscriptionDonePage } from './subscription-done.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    SubscriptionDonePageRoutingModule
  ],
  declarations: [SubscriptionDonePage]
})
export class SubscriptionDonePageModule {}
