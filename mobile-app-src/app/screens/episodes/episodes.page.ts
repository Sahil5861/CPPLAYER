import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
import { Share } from '@capacitor/share';
@Component({
  selector: 'app-episodes',
  templateUrl: './episodes.page.html',
  styleUrls: ['./episodes.page.scss'],
})
export class EpisodesPage implements OnInit {

  @ViewChild('video') myVideo?: ElementRef;
  selected:string='season1';

  typeParam: any = '';
  itemId: any;
  details: any = {};
  videos: any = [];
  seasons: any = [];
  imageLoaded: any = [];
  relatedWebseries: any = [];
  choosedVideo:any;
 

  pageData: any = [];
  items: any[] = [];
  allItems: any[] = [];
  pageSize = 50;
  currentPage = 1;

  constructor(private router: Router, public platform: Platform, private navCtrl: NavController,private route: ActivatedRoute, public apiService: ApiService) { }

ngOnInit() {
  this.route.paramMap.subscribe((params: any) => {
    // this.typeParam = params.get('type');
    this.itemId = params.get('id');
    
    this.movies();
    this.loadMoreSeasons()
  });
}

async share(){
  await Share.share(this.apiService.getShareOptions(this.details.name));
}


  movies(){
    this.apiService.viewLoader = true;
      this.apiService.get('getWebSeriesDetails/'+this.itemId).subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.details = res;
          // this.apiService.post('getRelatedWebseries/'+this.itemId+'/12',{"genres":this.details.genres}).subscribe((res:any)=>{
          //   console.log(res);
          //   if(typeof res == 'object'){
          //     this.relatedWebseries = res;
              
          //     setTimeout(()=>{
          //       this.apiService.viewLoader = false;
          //     },500)
          //   }
          // })
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })

      
      
  }

  loadMoreSeasons(event?: any) {
    const url = `getSeasons/${this.itemId}`;

    this.apiService.get(url).subscribe((res: any) => {
      if (Array.isArray(res) && res.length > 0) {
        this.seasons = this.seasons.concat(res);

        if (this.currentPage === 1 && res[0].Session_Name) {
          this.selected = res[0].Session_Name;
          this.getSeasionEpisodes(res[0].id, 0);
        }

        this.currentPage++;
      }

      if (event) {
        event.target.complete();
        if (res.length < this.pageSize) {
          event.target.disabled = true;  // Disable further scrolling
        }
      }

      setTimeout(() => {
        this.apiService.viewLoader = false;
      }, 500);
    });
  }

  getSeasionEpisodes(seasonId: any, index: any, event?: any) {
    const season = this.seasons[index];

    if (season.allEpisodesLoaded) {
      if (event) event.target.disabled = true;
      return;
    }

    this.apiService.get(`getEpisodes/${seasonId}/0?page=${this.currentPage}&records=20`)
      .subscribe((res: any) => {
        if (Array.isArray(res)) {
          if (!season.episodesList) season.episodesList = [];

          season.episodesList = season.episodesList.concat(res);

          if (res.length < 20) {
            season.allEpisodesLoaded = true;
            if (event) event.target.disabled = true;
          } else {
            this.currentPage = this.currentPage + 1;
          }

          if (event) event.target.complete();
        }

        setTimeout(() => {
          this.apiService.viewLoader = false;
        }, 500);
      });
  }


  // getSeasionEpisodes(seasonId: any,index: any){
  //   if(!this.seasons[index]['episodesList'] ||  this.seasons[index]['episodesList'].length ==0){
  //     this.apiService.get('getEpisodes/'+seasonId+'/0').subscribe((res:any)=>{
  //       console.log(res);
  //       if(typeof res == 'object'){
          
  //         this.seasons[index]['episodesList'] = res;
          
  //         setTimeout(()=>{
  //           this.apiService.viewLoader = false;
  //         },500)
  //       }
  //     })
  //   }
  // }

  ionViewWillLeave() {
    this.myVideo?.nativeElement.pause()
  }

  ionViewWillEnter() {
    this.myVideo?.nativeElement.play()
  }

  playVideo(obj: any,items: any){
    console.log(obj)
    this.apiService.updateViewHistory({
        "user_id" : this.apiService.getUserDetail('id'),
        "content_type" : this.details.content_type,
        "event_id" : obj.id,
        "event_title" : obj.name || obj.title || obj.Episoade_Name,
        "url" : obj.url || obj.movie_url,
        "category_id" : localStorage.getItem('categoryId')
    })
    if(obj.source == "youtube"){
      this.router.navigate(['/player'], { 
        queryParams: { url: obj.url } 
      });
    }else{
      this.apiService.initializeVlcPlayer(obj,items);
    }
    

  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

}
