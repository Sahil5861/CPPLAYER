import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { IonRouterOutlet } from '@ionic/angular';
import { ApiService } from '../../../api.service';
@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {

  loginpin: any = '';
  domain: any = '';

  constructor(private router: Router,private routerOutlet:IonRouterOutlet,public apiService:ApiService) { }

  ngOnInit() {
    this.domain = this.apiService.resolveDomain(
      localStorage.getItem('domain') || 'dash.getplaybox.com'
    );
  }
  login(){
    const activeDomain = this.apiService.setDomain(this.domain);
    this.domain = activeDomain;

    this.apiService
      .post(
        'login_app_new',
        {
          token: '',
          login_pin_app: this.loginpin,
          mac_address_app: this.apiService.deviceId,
          domain: activeDomain,
        },
        activeDomain
      )
      .subscribe(
        (res: any) => {
          if (res.status) {
            this.completeLogin(res, activeDomain);
            return;
          }
          this.apiService.showPopup('Error', res.msg);
        },
        () => {
          this.apiService.showPopup(
            'Error',
            'Server connection failed. Please check internet or entered domain.'
          );
        }
      );
  }

  private completeLogin(res: any, activeDomain: string) {
    this.apiService.domain = activeDomain;
    localStorage.setItem('auth_key', res.result_auth_key);
    localStorage.setItem('userData', JSON.stringify(res.data));
    localStorage.setItem('domain', activeDomain);
    this.apiService.setDomainBranding(res.data?.domain_content || null);
    localStorage.setItem('isLogin', '1');
    this.router.navigateByUrl('bottom-tab-bar/home2');
  }

  ionViewDidEnter() {
    this.routerOutlet.swipeGesture = false;
  }

  ionViewWillLeave() {
    this.routerOutlet.swipeGesture = true;
  }

  ionViewWillEnter() {
    this.loginpin = '';
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

}
