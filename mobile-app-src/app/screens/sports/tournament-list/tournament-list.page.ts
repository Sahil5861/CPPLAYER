import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-tournament-list',
  templateUrl: './tournament-list.page.html',
  styleUrls: ['./tournament-list.page.scss'],
})
export class TournamentListPage implements OnInit {
categoryId: string | null = null;
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
      this.categoryId = params.get('categoryId');
      console.log(this.categoryId);
    });

    this.loadItems();
  }

  loadItems(event?: any) {
    this.apiService
      .get(
        `getsportTournament/${this.categoryId}?page=${this.page}&records=${this.records}`
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
    localStorage.setItem("selectedSportsData", JSON.stringify(data));
    localStorage.setItem('categoryId',data.id)
    this.router.navigate(['/sports/events-list', data.id]);
  }
}
