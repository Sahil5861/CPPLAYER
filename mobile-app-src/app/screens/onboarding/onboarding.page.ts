import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { IonRouterOutlet, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
@Component({
  selector: 'app-onboarding',
  templateUrl: './onboarding.page.html',
  styleUrls: ['./onboarding.page.scss'],
})
export class OnboardingPage implements OnInit {

  @ViewChild('swiper') swiperRef: ElementRef | undefined;

  currentIndex = 0;
  screenHeight = window.innerHeight;
  onboardingScreenList = [
    {
      "id": "1",
      "onboardingImage": "../../../assets/images/onboarding/onboarding1.png",
      "title": "Watch Live TV, Movies, & Web Series",
      "description": "Stream a wide variety of live TV channels, blockbuster movies, and trending web series all in one place. Enjoy unlimited entertainment anytime, anywhere."
    },
    {
      "id": "2",
      "onboardingImage": "../../../assets/images/onboarding/onboarding2.png",
      "title": "Entertainment",
      "description": "Dive into a world of entertainment with an extensive collection of shows, movies, and series."
    },
    {
      "id": "3",
      "onboardingImage": "../../../assets/images/onboarding/onboarding3.png",
      "title": "Watch on any Device",
      "description": "Enjoy your favorite content on the go. Our platform is compatible with all devices, ensuring you can watch your shows, movies, and live TV anytime, anywhere."
    }    
  ];

  constructor(public platform:Platform, private routerOutlet: IonRouterOutlet, private router: Router,public apiService: ApiService) {
    if(this.apiService.settings.enableAll == 0){
      this.onboardingScreenList = [
        {
          "id": "1",
          "onboardingImage": "../../../assets/images/onboarding/onboarding1.png",
          "title": "Watch Live TV, Movies, & Web Series",
          "description": "Stream a wide variety of live TV channels. Enjoy unlimited entertainment anytime, anywhere."
        },
        {
          "id": "3",
          "onboardingImage": "../../../assets/images/onboarding/onboarding3.png",
          "title": "Watch on any Device",
          "description": "Enjoy your favorite content on the go. Our platform is compatible with all devices, ensuring you can watch your shows, movies, and live TV anytime, anywhere."
        }    
      ];
    }
  }

  ionViewDidEnter() {
    this.routerOutlet.swipeGesture = false;
  }

  ionViewWillLeave() {
    this.routerOutlet.swipeGesture = true;
  }

  ngOnInit() {
  }

  slideChangeCall() {
    this.currentIndex = this.swiperRef?.nativeElement.swiper.activeIndex;
  }

  goTo(screen: any) {
    localStorage.setItem('firstTime','1')
    this.router.navigateByUrl(screen)
  }

  handleButtonPress() {
    if(this.apiService.settings.enableAll == 0){
      // alert('j')
      if (this.currentIndex === 1) {
        localStorage.setItem('firstTime','1')
        this.router.navigateByUrl('/auth/login')
      }
      else {
        this.currentIndex = 1;
        this.swiperRef?.nativeElement.swiper.slideTo(this.currentIndex)
        
      }
    }else{
      if (this.currentIndex === 2) {
        localStorage.setItem('firstTime','1')
        this.router.navigateByUrl('/auth/login')
      }
      else {
        // alert(this.currentIndex)
        if(this.currentIndex == 0){
          this.currentIndex = 1;
          this.swiperRef?.nativeElement.swiper.slideTo(this.currentIndex)
        }else{
          this.currentIndex = 2;
          this.swiperRef?.nativeElement.swiper.slideTo(this.currentIndex)
        }
        // this.swiperRef?.nativeElement.swiper.slideTo(this.currentIndex == 0 ? 1 : 2);
      }
    }
  }

}
