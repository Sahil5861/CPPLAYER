import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { Over18PageRoutingModule } from './over18-routing.module';
import { Over18Page } from './over18.page';
import { SharedComponentsModule } from 'src/app/components/shared-components.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    SharedComponentsModule,
    Over18PageRoutingModule,
  ],
  declarations: [Over18Page],
})
export class Over18PageModule {}
