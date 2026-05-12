import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-show-list',
  templateUrl: './show-list.page.html',
  styleUrls: ['./show-list.page.scss'],
})
export class ShowListPage implements OnInit {
  channelId: string | null = null;
  page: number = 1;
  records: number = 10;
  items: any[] = [];
  imageLoaded:Boolean[] = [];
  defaultImg:String = 'assets/default.png'
  channelName: any;
  constructor(private route: ActivatedRoute, public apiService: ApiService, public router:Router) {}

  ngOnInit() {
    this.channelName = localStorage.getItem('channelName')
    this.route.paramMap.subscribe((params) => {
      this.channelId = params.get('channelId');
      console.log(this.channelId);
    });

    this.loadItems();
  }

  loadItems(event?: any) {
    this.apiService
      .get(`getReligiousShows/${this.channelId}?page=${this.page}&records=${this.records}`)
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

  goTo(data: any){
    localStorage.setItem('show-religious',data.title)
    localStorage.setItem('categoryId',data.id)
    this.router.navigate(['/religious/episodes-list', data.id]);

  }
}
