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
  
    pageData: any = [];

    items: any[] = [];
    allItems: any[] = [];
    pageSize = 12;
    currentPage = 0;
    catName: any = ''

  constructor(private router: Router, public platform: Platform, private navCtrl: NavController,private route: ActivatedRoute,public apiService: ApiService) { }

  ngOnInit() {
  //  alert('ll')
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
      // this.router.navigateByUrl('movie-detail/'+item.id+'/movie');
      this.apiService.updateViewHistory({
          "user_id" : this.apiService.getUserDetail('id'),
          "content_type" : item.content_type,
          "event_id" : item.id,
          "event_title" : item.name || item.title,
          "url" : item.url || item.movie_url,
          "category_id" : ""
      })
      if (['youtube', 'youtubelive'].includes((item.source_type || '').toString().toLowerCase())) {
        this.router.navigate(['/player'], {
            queryParams: { url: item.movie_url },
          });
      }else{
        this.apiService.playVideoWithUrl(item.movie_url)
      }
    
    }else if(item.content_type == 4){
      // localStorage.setItem('currentObject',JSON.stringify(item))
      this.router.navigateByUrl('/tv-channels/episodes-list/'+item.id);
    }
  }

}
