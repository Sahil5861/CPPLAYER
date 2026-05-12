import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
})
export class RegisterPage implements OnInit {

  phoneNumber = '';
  fullName = '';
  email = '';

  constructor(private router: Router, private navCtrl: NavController) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.phoneNumber = '';
    this.email = '';
    this.fullName = '';
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

  goBack() {
    this.navCtrl.back()
  }

}
