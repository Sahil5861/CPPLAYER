import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-shows-list',
  templateUrl: './shows-list.page.html',
  styleUrls: ['./shows-list.page.scss'],
})
export class ShowsListPage implements OnInit {
  channelId: string | null = null;
  page: number = 1;
  records: number = 10;
  items: any[] = [];
  imageLoaded: Boolean[] = [];
  defaultImg: String = 'assets/default.png';
  channelName: any;
  constructor(
    private route: ActivatedRoute,
    public apiService: ApiService,
    public router: Router
  ) {}

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
      .get(
        `getKidsShows/${this.channelId}?page=${this.page}&records=${this.records}`
      )
      .subscribe((res: any) => {
        console.log(res);
        if (Array.isArray(res) && res.length > 0) {
          this.items = this.items.concat(res); 
          this.page++;
        }

        if (event) {
          event.target.complete();
          if (res.length < this.records) {
            event.target.disabled = true; 
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

  goTo(data: any) {
    localStorage.setItem("selectedKidsData", JSON.stringify(data));
    localStorage.setItem('categoryId',data.id)
    this.router.navigate(['/kids/episodes-list', data.id]);
  }
}
