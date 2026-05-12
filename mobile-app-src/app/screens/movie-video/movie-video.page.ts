import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';

import { Router, ActivatedRoute } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
import { Share } from '@capacitor/share';
import { tick } from '@angular/core/testing';

@Component({
  selector: 'app-movie-video',
  templateUrl: './movie-video.page.html',
  styleUrls: ['./movie-video.page.scss'],
})
export class MovieVideoPage implements OnInit {

  @ViewChild('video') myVideo?: ElementRef;
  typeParam: any = '';
  itemId: any;
  details: any = {};
  videos: any = [];
  seasons: any = [];
  rating = 5;
  inBookmark = false;
  choosedVideo:any;
  relatedMovies:any = [];
  imageLoaded:any = [];
  similarMoviesList = [
    {
        id: '1',
        movieImage: '../../../assets/images/popularMovies/similar1.png',
    },
    {
        id: '2',
        movieImage: '../../../assets/images/popularMovies/similar2.png',
    },
    {
        id: '3',
        movieImage: '../../../assets/images/popularMovies/movie3.png',
    },
    {
        id: '4',
        movieImage: '../../../assets/images/popularMovies/similar3.png',
    },
    {
        id: '5',
        movieImage: '../../../assets/images/popularMovies/similar4.png',
    },
];

 moreLikeMoviesList = [
    {
        id: '1',
        movieImage: '../../../assets/images/popular/popular16.png',
    },
    {
        id: '2',
        movieImage: '../../../assets/images/popular/popular10.png',
    },
    {
        id: '3',
        movieImage: '../../../assets/images/popular/popular11.png',
    },
    {
        id: '4',
        movieImage: '../../../assets/images/popular/popular13.png',
    },
    {
        id: '5',
        movieImage: '../../../assets/images/popular/popular14.png',
    },
    {
        id: '6',
        movieImage: '../../../assets/images/popular/popular15.png',
    },
    {
        id: '7',
        movieImage: '../../../assets/images/popularMovies/movie6.png',
    },
];

constructor(private router: Router, public platform: Platform, private navCtrl: NavController,private route: ActivatedRoute, public apiService: ApiService) { }

ngOnInit() {
  this.route.paramMap.subscribe((params: any) => {
    this.typeParam = params.get('type');
    this.itemId = params.get('id');
    
    this.movies();
    
  });
}

async share(){
  await Share.share(this.apiService.getShareOptions(this.details.name));
}


  movies(){
    this.apiService.viewLoader = true;
      this.apiService.get('getMovieDetails/'+this.itemId).subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.details = res;
          this.apiService.post('getRelatedMovies/'+this.itemId+'/12',{"genres":this.details.genres}).subscribe((res:any)=>{
            console.log(res);
            if(typeof res == 'object'){
              this.relatedMovies = res;
              
              setTimeout(()=>{
                this.apiService.viewLoader = false;
              },500)
            }
          })
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })

      this.apiService.get('getMoviePlayLinks/'+this.itemId+'/0').subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.videos = res;
          if(this.videos[0]){
            this.choosedVideo = 0;
          }
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })
      
  }

  playVideo(){
    let obj = this.videos[this.choosedVideo];
    this.apiService.initializeVlcPlayer(obj,this.videos);

  }

  ionViewWillLeave() {
    this.myVideo?.nativeElement.pause()
  }

  ionViewWillEnter() {
    this.myVideo?.nativeElement.play()
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(item: any) {
    this.itemId = item.id;
    this.movies()
    // this.router.navigateByUrl(screen);
  }
}
