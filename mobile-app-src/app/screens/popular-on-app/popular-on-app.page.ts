import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
@Component({
  selector: 'app-popular-on-app',
  templateUrl: './popular-on-app.page.html',
  styleUrls: ['./popular-on-app.page.scss'],
})
export class PopularOnAppPage implements OnInit {
  anyParam: any = ''
  imageLoaded: any = []
  // popularOnAppList = [
  //   {
  //     id: '1',
  //     image: '../../../assets/images/popular/popular18.png',
  //   },
  //   {
  //     id: '2',
  //     image: '../../../assets/images/popular/popular1.png',
  //   },
  //   {
  //     id: '3',
  //     image: '../../../assets/images/popularWebSeries/series5.png',
  //   },
  //   {
  //     id: '4',
  //     image: '../../../assets/images/popularWebSeries/series4.png',
  //   },
  //   {
  //     id: '5',
  //     image: '../../../assets/images/popular/popular2.png',
  //   },
  //   {
  //     id: '6',
  //     image: '../../../assets/images/popular/popular3.png',
  //   },
  //   {
  //     id: '7',
  //     image: '../../../assets/images/popular/popular4.png',
  //   },
  //   {
  //     id: '8',
  //     image: '../../../assets/images/popular/popular5.png',
  //   },
  //   {
  //     id: '9',
  //     image: '../../../assets/images/popular/popular6.png',
  //   },
  //   {
  //     id: '10',
  //     image: '../../../assets/images/popular/popular7.png',
  //   },
  //   {
  //     id: '11',
  //     image: '../../../assets/images/popular/popular18.png',
  //   },
  //   {
  //     id: '12',
  //     image: '../../../assets/images/popular/popular19.png',
  //   },
  //   {
  //     id: '13',
  //     image: '../../../assets/images/popular/popular20.png',
  //   },
  //   {
  //     id: '14',
  //     image: '../../../assets/images/popular/popular21.png',
  //   },
  //   {
  //     id: '15',
  //     image: '../../../assets/images/popular/popular22.png',
  //   },
  //   {
  //     id: '16',
  //     image: '../../../assets/images/popular/popular8.png',
  //   },
  //   {
  //     id: '17',
  //     image: '../../../assets/images/popular/popular9.png',
  //   },
  //   {
  //     id: '18',
  //     image: '../../../assets/images/popular/popular10.png',
  //   },
  //   {
  //     id: '19',
  //     image: '../../../assets/images/popular/popular11.png',
  //   },
  //   {
  //     id: '20',
  //     image: '../../../assets/images/popular/popular12.png',
  //   },
  //   {
  //     id: '21',
  //     image: '../../../assets/images/popular/popular13.png',
  //   },
  //   {
  //     id: '22',
  //     image: '../../../assets/images/popular/popular14.png',
  //   },
  //   {
  //     id: '23',
  //     image: '../../../assets/images/popular/popular15.png',
  //   },
  //   {
  //     id: '24',
  //     image: '../../../assets/images/popular/popular17.png',
  //   },
  //   {
  //     id: '25',
  //     image: '../../../assets/images/popular/popular16.png',
  //   },
  // ];

    pageData: any = [];

    items: any[] = [];
    allItems: any[] = [];
    pageSize = 12;
    currentPage = 0;
    catName: any = ''

  constructor(private router: Router, public platform: Platform, private navCtrl: NavController,private route: ActivatedRoute,public apiService: ApiService) { }

  ngOnInit() {
   this.items = [];
    this.allItems = [];
    this.pageSize = 12;
    this.currentPage = 0;
    this.route.paramMap.subscribe((params: any) => {
      if(this.allItems.length == 0){
        this.anyParam = params.get('any');
        var url = 'getAllMovies';

        if(this.anyParam == 'See All'){
          this.catName = localStorage.getItem('catname')
          // getLiveTvReletedToGenre
          this.apiService.get('getLiveTvReletedToGenre/'+this.catName).subscribe((res:any)=>{
            console.log(res);
            if(typeof res == 'object'){
              this.allItems = res;
              this.loadMoreItems()
              
            }
            setTimeout(()=>{
              this.apiService.viewLoader = false;
            },800)
          },err=>{
            this.apiService.viewLoader = false;
          })
          // this.allItems = JSON.parse(localStorage.getItem('seealldata') || '');
          // this.loadMoreItems();
        }else{
          if(this.anyParam == 'Live TV'){
            url = 'getAllLiveTV';
          }else if(this.anyParam == 'Web Series'){
            url = 'getAllWebSeries';
          }else if(this.anyParam == 'Network Content'){
            let id = localStorage.getItem('networkId');
            url = 'getAllContentsOfNetwork/'+id;
          }
          this.apiService.viewLoader = true;
          this.apiService.get(url).subscribe((res:any)=>{
            console.log(res);
            if(typeof res == 'object'){
              this.allItems = res;
              this.loadMoreItems()
              
            }
            setTimeout(()=>{
              this.apiService.viewLoader = false;
            },800)
          },err=>{
            this.apiService.viewLoader = false;
          })
        }
        
      }
    });
  }
  loadMoreItems(event?: any) {
    const nextPageItems = this.allItems.slice(
      this.currentPage * this.pageSize,
      (this.currentPage + 1) * this.pageSize
    );
    this.items = this.items.concat(nextPageItems);
    this.currentPage++;

    if (event) {
      event.target.complete();
    }
    // alert(this.currentPage)
    if (this.items.length >= this.allItems.length && this.currentPage > 1) {
      event.target.disabled = true;
    }
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(item: any) {
    if(item.content_type == 3){
      // this.router.navigateByUrl(screen);
      // localStorage.setItem('currentObject',JSON.stringify(item))
      // this.router.navigateByUrl('video-player')
      this.apiService.initializeVlcPlayer(item,this.items);
    }else if(item.content_type == 1){
      // localStorage.setItem('currentObject',JSON.stringify(item))
      this.router.navigateByUrl('movie-detail/'+item.id+'/movie');
    
    }else if(item.content_type == 2){
      // localStorage.setItem('currentObject',JSON.stringify(item))
      this.router.navigateByUrl('episodes/'+item.id);
    }
  }

}
