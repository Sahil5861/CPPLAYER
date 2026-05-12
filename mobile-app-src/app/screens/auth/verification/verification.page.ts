import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { IonRouterOutlet, ModalController, NavController } from '@ionic/angular';

@Component({
  selector: 'app-verification',
  templateUrl: './verification.page.html',
  styleUrls: ['./verification.page.scss'],
})
export class VerificationPage implements OnInit {

  otp = '';
  showLoadingDialog = false;

  constructor(private router: Router,
    private modalCtrl: ModalController,
    private routerOutlet: IonRouterOutlet,
    private navCtrl: NavController
  ) { }

  ngOnInit() {
  }

  ionViewDidEnter() {
    this.routerOutlet.swipeGesture = false;
  }

  ionViewWillLeave() {
    this.routerOutlet.swipeGesture = true;
  }

  ionViewWillEnter() {
    this.otp = '';
  }

  buttonPress() {
    this.showLoadingDialog = true;
    setTimeout(() => {
      this.modalCtrl.dismiss();
      this.showLoadingDialog = false;
      this.router.navigateByUrl('bottom-tab-bar/home');
    }, 2000);
  }

  goBack() {
    this.navCtrl.back()
  }

}
