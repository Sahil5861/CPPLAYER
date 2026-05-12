import { Location } from '@angular/common';
import { Component, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { App } from '@capacitor/app';
import { StatusBar, Style } from '@capacitor/status-bar';
import { AlertController, NavController, Platform } from '@ionic/angular';
import { register } from 'swiper/element/bundle';
import { ApiService } from './api.service';
register();

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
})
export class AppComponent {
  tap = 0;
  private readonly handleNativeImageError = (event: Event) => {
    const target = event.target as HTMLElement | null;

    if (target instanceof HTMLImageElement) {
      this.apiService.handleImageErrorTarget(target);
    }
  };
  private readonly handleIonImageError = (event: Event) => {
    this.apiService.handleImageErrorTarget(event.target as any);
  };
  private readonly clearNativeImageFallback = (event: Event) => {
    const target = event.target as HTMLElement | null;

    if (target instanceof HTMLImageElement) {
      this.apiService.clearImageFallbackState(target);
    }
  };
  private readonly clearIonImageFallback = (event: Event) => {
    this.apiService.clearImageFallbackState(event.target as any);
  };

  constructor(
    private platform: Platform,
    public alertController: AlertController,
    private router: Router,
    private location: Location,
    private navCtrl: NavController,
    public apiService: ApiService
  ) {
    this.intializeApp();
    this.backButtonEvent();
    this.registerGlobalImageFallbacks();
  }

  ngOnInit() {
  }

  ngOnDestroy() {
    this.unregisterGlobalImageFallbacks();
  }

  backButtonEvent() {
    this.platform.backButton.subscribeWithPriority(10, (processNextHandler) => {
      if (
        this.location.isCurrentPathEqualTo('/bottom-tab-bar/home2') ||
        this.location.isCurrentPathEqualTo('/bottom-tab-bar/search') ||
        this.location.isCurrentPathEqualTo('/bottom-tab-bar/profile')||
        this.location.isCurrentPathEqualTo('/bottom-tab-bar/live')||
        this.location.isCurrentPathEqualTo('/bottom-tab-bar/movies')||
        this.location.isCurrentPathEqualTo('/bottom-tab-bar/webseries')||
        this.location.isCurrentPathEqualTo('/onboarding')||
        this.location.isCurrentPathEqualTo('/auth/login')
      ) {
        this.tap++;
        if (this.tap == 2) {
          App.exitApp();
        }
        else {
          setTimeout(() => {
            this.tap = 0;
          }, 2000);
        }
      }
      else {
        if (this.location.isCurrentPathEqualTo('/subscription-done')) {
          this.router.navigateByUrl('/bottom-tab-bar/home')
        }
        else {
          this.navCtrl.pop()
        }
      }
    });
  }

  intializeApp() {
    this.platform.ready().then(() => {
      StatusBar.setBackgroundColor({ color: '#000000' });
      StatusBar.setStyle({ style: Style.Dark });
    })
  }

  private registerGlobalImageFallbacks() {
    if (typeof document === 'undefined') {
      return;
    }

    document.addEventListener('error', this.handleNativeImageError, true);
    document.addEventListener('load', this.clearNativeImageFallback, true);
    document.addEventListener('ionError', this.handleIonImageError, true);
    document.addEventListener('ionImgDidLoad', this.clearIonImageFallback, true);
  }

  private unregisterGlobalImageFallbacks() {
    if (typeof document === 'undefined') {
      return;
    }

    document.removeEventListener('error', this.handleNativeImageError, true);
    document.removeEventListener('load', this.clearNativeImageFallback, true);
    document.removeEventListener('ionError', this.handleIonImageError, true);
    document.removeEventListener('ionImgDidLoad', this.clearIonImageFallback, true);
  }
}
