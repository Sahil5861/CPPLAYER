import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Share } from '@capacitor/share';
import { Platform, NavController } from '@ionic/angular';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-events-list',
  templateUrl: './events-list.page.html',
  styleUrls: ['./events-list.page.scss'],
})
export class EventsListPage implements OnInit {
@ViewChild('video') myVideo?: ElementRef;
  seasonId: string | null = null;
  page: number = 1;
  records: number = 10;
  items: any[] = [];
  imageLoaded: Boolean[] = [];
  defaultImg: String = 'assets/default.png';
  selectedData: any;
  currentPage = 1;
  selected: string = 'season1';
  pageSize = 50;

  seasons: any[] = [];
  selectedSeason: any = null;
  episodes: any[] = [];
  seasonLoading: boolean = true;
  episodesLoading: boolean = false;

  constructor(
    private route: ActivatedRoute,
    public apiService: ApiService,
    public router: Router,
    public platform: Platform,
    private navCtrl: NavController
  ) {}

  ngOnInit() {
    this.route.paramMap.subscribe((params) => {
      this.seasonId = params.get('seasonId');
      console.log(this.seasonId);
    });

    const data = localStorage.getItem('selectedSportsData');
    if (data) {
      this.selectedData = JSON.parse(data);
      console.log(this.selectedData);
    }

    this.loadSeasons();
  }

  // loadItems(event?: any) {
  //   this.apiService
  //     .get(
  //       `getShowSeasonsEpisodes/${this.seasonId}?page=${this.page}&records=${this.records}`
  //     )
  //     .subscribe((res: any) => {
  //       console.log(res);
  //       if (Array.isArray(res) && res.length > 0) {
  //         this.items = this.items.concat(res); // append new records
  //         this.page++; // go to next page
  //       }

  //       if (event) {
  //         event.target.complete();
  //         if (res.length < this.records) {
  //           event.target.disabled = true; // disable infinite scroll if no more data
  //         }
  //       }

  //       setTimeout(() => {
  //         this.apiService.viewLoader = false;
  //       }, 800);
  //     });
  // }

  // loadMoreItems(event: any) {
  //   this.loadItems(event);
  // }

  loadSeasons() {
    this.seasonLoading = true;
    this.apiService.get(`getTouranamentSeasons/${this.seasonId}`).subscribe(
      (res: any) => {
        this.seasons = Array.isArray(res) ? res : [];
        this.seasonLoading = false;

        if (this.seasons.length > 0) {
          this.selectSeason(this.seasons[0]);
        }
      },
      () => {
        this.seasonLoading = false;
      }
    );
  }

  selectSeason(season: any) {
    this.selectedSeason = season;
    this.episodes = [];
    this.loadEpisodes(season.id);
  }

  loadEpisodes(seasonId: number) {
    this.episodesLoading = true;
    this.apiService.get(`getTouranamentSeasonsEvents/${seasonId}`).subscribe(
      (res: any) => {
        this.episodes = Array.isArray(res) ? res : [];
        this.episodesLoading = false;
      },
      () => {
        this.episodesLoading = false;
      }
    );
  }

  async share() {
    await Share.share(this.apiService.getShareOptions(this.selectedData.name));
  }

  ionViewWillLeave() {
    this.myVideo?.nativeElement.pause();
  }

  ionViewWillEnter() {
    this.myVideo?.nativeElement.play();
  }

  playVideo(obj: any) {
    // this.apiService.initializeVlcPlayer(obj, items);
    // console.log(obj);
    if (['youtube', 'youtubelive'].includes((obj.stream_type || '').toString().toLowerCase())) {
        this.router.navigate(['/player'], {
          queryParams: { url: obj.video_url },
        });
      } else {
        console.log('playvideowithurl');
        this.apiService.playVideoWithUrl(obj.video_url);
      }
  }

  goBack() {
    this.navCtrl.back();
  }

  // goTo(data: any){
  //   this.router.navigate(['/religious/episodes-list', data.id]);

  // }
}
