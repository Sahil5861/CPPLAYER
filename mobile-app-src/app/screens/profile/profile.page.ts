import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Share } from '@capacitor/share';
import { ModalController } from '@ionic/angular';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.page.html',
  styleUrls: ['./profile.page.scss'],
})
export class ProfilePage implements OnInit {

  premiumBenifitsList = [
    {
      id: '1',
      benifitBgImage: '../../../assets/images/benifitBg/benifitBg1.png',
      benifit: `Get Access to\nAll Full HD\nContents`,
    },
    {
      id: '2',
      benifitBgImage: '../../../assets/images/benifitBg/benifitBg2.png',
      benifit: `Enable\nDownload Movies`,
    },
    {
      id: '3',
      benifitBgImage: '../../../assets/images/benifitBg/benifitBg3.png',
      benifit: `Watch\nPremium Contents`,
    },
  ];

  showLogoutDialog = false;
  constructor(
    private router: Router,
    private modalCtrl: ModalController,
    public apiService: ApiService
  ) {}

  ngOnInit() {
  }

  logout(){
    this.apiService.clearDomainBranding();
    setTimeout(()=>{
      window.location.reload();
    },700)
    localStorage.clear()
    this.modalCtrl.dismiss(); this.goTo('auth/login')
  }

  async share(){
    await Share.share(this.apiService.getShareOptions());
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

}
