import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import { ScreenOrientation } from '@capacitor/screen-orientation';
// import {Exoplayer} from 'capacitor-pm-video-exoplayer';
interface Channel {
  banner: string;
  // Add other properties as needed
}

interface Networks {
  name: string;
  logo: string;
  id: string;
}

interface Category {
  id:number;
  text: string;
  channels: Channel[];
}
@Component({
  selector: 'app-home',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
})
export class HomePage implements OnInit {

  bannersList: any = [];

  categoriesList = [
    {
      id: '1',
      categoryImage: '../../../assets/images/category/action.png',
      category: 'Action',
    },
    {
      id: '2',
      categoryImage: '../../../assets/images/category/adventure.png',
      category: 'Adventure',
    },
    {
      id: '3',
      categoryImage: '../../../assets/images/category/comedy.png',
      category: 'Comedy',
    },
    {
      id: '4',
      categoryImage: '../../../assets/images/category/drama.png',
      category: 'Drama',
    },
    {
      id: '5',
      categoryImage: '../../../assets/images/category/horror.png',
      category: 'Horror',
    },
    {
      id: '6',
      categoryImage: '../../../assets/images/category/history.png',
      category: 'History',
    },
    {
      id: '7',
      categoryImage: '../../../assets/images/category/love.png',
      category: 'Love',
    },
  ];

  continueWatchingList = [
    {
      id: '1',
      watchingImage: '../../../assets/images/continueWatching/continueWatching1.png',
      watchingPercentage: 35,
    },
    {
      id: '2',
      watchingImage: '../../../assets/images/continueWatching/continueWatching2.png',
      watchingPercentage: 80,
    },
    {
      id: '3',
      watchingImage: '../../../assets/images/continueWatching/continueWatching3.png',
      watchingPercentage: 20,
    },
    {
      id: '4',
      watchingImage: '../../../assets/images/continueWatching/continueWatching4.png',
      watchingPercentage: 70,
    },
    {
      id: '5',
      watchingImage: '../../../assets/images/continueWatching/continueWatching5.png',
      watchingPercentage: 60,
    },
  ];

  popularWebSeriesList:any = [];

  popularMoviesList: any = [];

  featuredLive: any = [];

  mostSearched: any = [];

  trendingList = [
    {
      id: '1',
      image: '../../../assets/images/continueWatching/continueWatching2.png',
    },
    {
      id: '2',
      image: '../../../assets/images/continueWatching/continueWatching3.png',
    },
    {
      id: '3',
      image: '../../../assets/images/continueWatching/continueWatching4.png',
    },
    {
      id: '4',
      image: '../../../assets/images/continueWatching/continueWatching5.png',
    },
    {
      id: '5',
      image: '../../../assets/images/continueWatching/movie1.png',
    },
  ];

  networks: Networks[] = [];
  homeCategories: Category[] = [];

  constructor(private router: Router,public apiService: ApiService) {

    this.apiService.viewLoader = true

    
   }

  ngOnInit() {
    this.apiService.get('getCustomImageSlider').subscribe((res:any)=>{
      console.log(res);
      if(typeof res == 'object'){
        this.bannersList = res;
      }
    })

    

    this.apiService.get('getNetworks').subscribe((res:any)=>{
      console.log(res);
      if(typeof res == 'object'){
        this.networks = res;
      }
    })

    

    this.apiService.get('getSelectHomeCategory').subscribe((res:any)=>{
      console.log(res);
      if(typeof res == 'object'){
        let a: any = [];
        if(this.apiService.settings.enableAll == 1){
          this.homeCategories = res;
        }else{
          // alert(this.apiService.settings.enableAll)
          res.forEach((ele: any)=>{
            let c: any = [];
            ele.channels.forEach((ele1: any)=>{
              
              if(this.apiService.settings.channels.indexOf(Number(ele1.id)) > -1){
                c.push(ele1)
              }
            })
            this.homeCategories.push({id:ele.id,text: ele.text,channels:c});
          })
        }
        // this.homeCategories = res;
      }
    })
    
    // getSelectHomeCategory

    // this.apiService.get('getMostWatched/Movies/10').subscribe((res:any)=>{
    //   console.log(res);
    //   if(typeof res == 'object'){
    //     this.popularMoviesList = res;
    //   }
    // })

    // this.apiService.get('getMostWatched/WebSeries/10').subscribe((res:any)=>{
    //   console.log(res);
    //   if(typeof res == 'object'){
    //     this.popularWebSeriesList = res;
    //   }
    // })


    // this.apiService.get('getFeaturedLiveTV').subscribe((res:any)=>{
    //   console.log(res);
    //   if(typeof res == 'object'){
    //     this.featuredLive = res;
    //   }
    // })

    // this.apiService.get('getMostSearched').subscribe((res:any)=>{
    //   console.log(res);
    //   if(typeof res == 'object'){
    //     this.mostSearched = res;
    //   }
    // })

    setTimeout(()=>{
      this.apiService.viewLoader = false
    },2500)

    // 
  }

  ionViewWillEnter(){
      this.unlock()
  }

  _number(id: any){
    return Number(id);
  }


  goTo(screen: any) {
    // console.log(item)
    this.router.navigateByUrl(screen);
  }

  goToContent(id: any){
    // console.log(id)
    localStorage.setItem('networkId',id);
    this.router.navigateByUrl('popular-on-app/Network Content')
  }

  goToMovie(item: any){
    console.log(item)
  }

  goToWebSeries(item: any){
    console.log(item)
  }

  async lock(){
   
    await ScreenOrientation.lock({ orientation: 'landscape' });
    
  }

  seeAll(data: any,catname: any){
    localStorage.setItem('seealldata',JSON.stringify(data))
    localStorage.setItem('catname',catname)
    this.router.navigateByUrl('popular-on-app/See All')
  }

  async unlock(){
    await ScreenOrientation.lock({ orientation: 'portrait' });
  }

  goToLiveTV(item: any){
    this.apiService.initializeVlcPlayer(item,[]);
    // console.log(item)
    // localStorage.setItem('currentObject',JSON.stringify(item))
    // this.apiService.viewLoader = true;
    // // this.lock();
    // this.router.navigateByUrl('video-player')
  }
  handleRefresh(event: any) {
    setTimeout(() => {
      // Any calls to load data go here
      this.ngOnInit();
      event.target.complete();
    }, 2000);
  }

}
