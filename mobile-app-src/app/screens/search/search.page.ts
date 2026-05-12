import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
@Component({
  selector: 'app-search',
  templateUrl: './search.page.html',
  styleUrls: ['./search.page.scss'],
})
export class SearchPage implements OnInit {

  searchCategoryList: any = [];
  searchText: any = ''
  
 
  searchList: any = [];
  imageLoaded: any = [];

  page: number = 1;
  records: number = 30;
  hasMore: boolean = true;

  reqSub:any;
  constructor(private router: Router,public apiService: ApiService) { }

  ngOnInit() {
    // this.apiService.get('getSearchCategoryList').subscribe((res:any)=>{
    //   console.log(res);
    //   if(typeof res == 'object'){
    //     this.searchCategoryList = res;
    //   }
    // })

    
  }

  private getSearchItems(res: any): any[] {
    if (Array.isArray(res)) {
      return res;
    }

    return (
      res?.data ||
      res?.results ||
      res?.channels ||
      res?.items ||
      []
    );
  }

  getItemImage(item: any): string {
    return this.apiService.getImageUrl(
      item?.banner ||
      item?.poster ||
      item?.thumbnail ||
      item?.channel_logo ||
      item?.logo ||
      item?.image ||
      item?.movie_image ||
      ''
    );
  }

  getItemVariant(item: any): 'poster' | 'square' {
    return 'poster';
  }

  getItemFit(item: any): 'cover' | 'contain' {
    return this.getItemVariant(item) === 'square' ? 'contain' : 'cover';
  }

  getItemPadding(item: any): string {
    return this.getItemVariant(item) === 'square' ? '10px' : '0px';
  }

  getItemBackground(item: any): string {
    return this.getItemVariant(item) === 'square'
      ? '#ffffff'
      : 'var(--media-card-surface)';
  }

  search(reset: boolean = true) {
    if(this.reqSub){
      this.reqSub.unsubscribe()
    }
    if (this.searchText.length > 2) {
      if (reset) {
        this.page = 1;
        this.searchList = [];
        this.imageLoaded = [];
        this.hasMore = true;
      }

      this.reqSub = this.apiService.post(`getSearchCategoryList?page=${this.page}&records=${this.records}`, {
        keywords: this.searchText
      }).subscribe((res: any) => {
        console.log(res);
        const nextItems = this.getSearchItems(res);

        if (nextItems.length > 0) {
          this.searchList = [...this.searchList, ...nextItems];
          nextItems.forEach(() => this.imageLoaded.push(false));
          this.hasMore = nextItems.length >= this.records;
        } else {
          this.hasMore = false;
        }
      });
    }else{
      this.page = 1;
      this.searchList = [];
      this.imageLoaded = [];
      this.hasMore = true;
    }
    
  }

  goToDetail(item: any) {
    // this.router.navigateByUrl(item);
    if(item.content_type == 3){
      // this.router.navigateByUrl(screen);
      this.apiService.playVideoWithUrl(item);
      // localStorage.setItem('currentObject',JSON.stringify(item))
      // this.router.navigateByUrl('video-player')
    }else if(item.content_type == 1){
      if (['youtube', 'youtubelive'].includes((item.source_type || '').toString().toLowerCase())) {
        this.router.navigate(['/player'], {
            queryParams: { url: item.movie_url },
          });
      }else{
        this.apiService.playVideoWithUrl(item.movie_url)
      }
    }
    else if(item.content_type == 2){
      // web-series
      // localStorage.setItem('channelName',item.name)
      this.router.navigateByUrl('episodes/'+item.id);
    }
    else if(item.content_type == 4){
      // tv-show
      localStorage.setItem('channelName',item.name)
      this.router.navigate(['/tv-channels/shows-list', item.id]);
    }
    else if(item.content_type == 5){
      // tv show pak
      localStorage.setItem('channelName',item.name)
      this.router.navigate(['/tv-channels-pak/shows-list', item.id]);
    }
    else if(item.content_type == 6){
      // kids
      localStorage.setItem('channelName',item.name)
      this.router.navigate(['/kids/shows-list', item.id]);
    }
    else if(item.content_type == 7){
      // religius
      localStorage.setItem('channelName',item.name)
      this.router.navigate(['/religious/show-list', item.id]);
    }
    else if(item.content_type == 8){
      // sports
      localStorage.setItem('channelName',item.title)
      this.router.navigate(['/sports/tournament-list', item.id]);
    }
    else if(item.content_type == 9){
      // stage-show-pak
      if (['youtube', 'youtubelive'].includes((item.source_type || '').toString().toLowerCase())) {
        this.router.navigate(['/player'], {
            queryParams: { url: item.movie_url },
          });
      }else{
        this.apiService.playVideoWithUrl(item.movie_url)
      }
    }
    else if(item.content_type == 10){
      // laghter
      
      // localStorage.setItem('channelName',item.name)
      // this.router.navigate(['/tv-channels-pak/shows-list', item.id]);
    }
  }


  loadMore(event: any) {
    this.page++;
    this.apiService.post(`getSearchCategoryList?page=${this.page}&records=${this.records}`, {
      keywords: this.searchText
    }).subscribe((res: any) => {
      const nextItems = this.getSearchItems(res);

      if (nextItems.length > 0) {
        this.searchList = [...this.searchList, ...nextItems];
        nextItems.forEach(() => this.imageLoaded.push(false));
        this.hasMore = nextItems.length >= this.records;
      } else {
        this.hasMore = false;
        event.target.disabled = true; // infinite scroll stop
      }

      event.target.complete();
    });
  }


}
