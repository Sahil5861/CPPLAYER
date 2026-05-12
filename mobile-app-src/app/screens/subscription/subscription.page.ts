import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { IonRouterOutlet, NavController } from '@ionic/angular';

@Component({
  selector: 'app-subscription',
  templateUrl: './subscription.page.html',
  styleUrls: ['./subscription.page.scss'],
})
export class SubscriptionPage implements OnInit {

  subscriptionPlansList = [
    {
      id: '1',
      planeTitle: 'Starter Pack',
      planPeriod: '3 Month',
      planAmount: 10.99,
    },
    {
      id: '2',
      planeTitle: 'Standard Pack',
      planPeriod: '6 Month',
      planAmount: 14.99,
    },
    {
      id: '3',
      planeTitle: 'Super Saver Pack',
      planPeriod: '12 Month',
      planAmount: 24.99,
    }
  ];

  subscriptionAllowsList = [
    'Watch all espisodes of every series',
    'Download every contenet available on app',
    'Full HD contenet download option',
    'Any time subscription cancelation facility',
  ];

  constructor(private routerOutlet: IonRouterOutlet, private router: Router, private navCtrl: NavController) { }

  ngOnInit() {
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

  ionViewDidEnter() {
    this.routerOutlet.swipeGesture = false;
  }

  ionViewWillLeave() {
    this.routerOutlet.swipeGesture = true;
  }
}
