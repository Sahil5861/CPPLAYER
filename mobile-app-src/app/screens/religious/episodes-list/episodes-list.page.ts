import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-episodes-list',
  templateUrl: './episodes-list.page.html',
  styleUrls: ['./episodes-list.page.scss'],
})
export class EpisodesListPage implements OnInit {
  episodeId: string | null = null;
  page: number = 1;
  records: number = 10;
  items: any[] = [];
  imageLoaded:Boolean[] = [];
  defaultImg:String = 'assets/default.png'
  show: any
  constructor(private route: ActivatedRoute, public apiService: ApiService, public router:Router) {}

  ngOnInit() {
    this.show = localStorage.getItem('show-religious') || "";
    this.route.paramMap.subscribe((params) => {
      this.episodeId = params.get('episodeId');
      console.log(this.episodeId);
    });

    this.loadItems();
  }

  loadItems(event?: any) {
    this.apiService
      .get(`getReligiousShowsEpisodes/${this.episodeId}?page=${this.page}&records=${this.records}`)
      .subscribe((res: any) => {
        console.log(res);
        if (Array.isArray(res) && res.length > 0) {
          this.items = this.items.concat(res); // append new records
          this.page++; // go to next page
        }

        if (event) {
          event.target.complete();
          if (res.length < this.records) {
            event.target.disabled = true; // disable infinite scroll if no more data
          }
        }

        setTimeout(() => {
          this.apiService.viewLoader = false;
        }, 800);
      });
  }

  loadMoreItems(event: any) {
    this.loadItems(event);
  }

  // goTo(data: any){
  //   this.router.navigate(['/religious/episodes-list', data.id]);

  // }

  play(item:any){
    this.apiService.updateViewHistory({
        "user_id" : this.apiService.getUserDetail('id'),
        "content_type" : item.content_type,
        "event_id" : item.id,
        "event_title" : item.name || item.title,
        "url" : item.url || item.movie_url,
        "category_id" : localStorage.getItem('categoryId')
    })
    if(item.source === "youtube"){
      console.log('youtube');
      // this.router.navigate(['/player'])
      this.router.navigate(['/player'], { 
        queryParams: { url: item.url } 
      });
    }else{
      console.log('m3u8');
      this.apiService.playVideoWithUrl(item.url);
    }
  }
}
