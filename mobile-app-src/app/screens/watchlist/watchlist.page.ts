import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { IonItemSliding, NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';

@Component({
  selector: 'app-watchlist',
  templateUrl: './watchlist.page.html',
  styleUrls: ['./watchlist.page.scss'],
})
export class WatchlistPage implements OnInit {
  selectedTabValue = 'Web Series';
  isToastOpen = false;

  tabs = [
    'Web Series',
    'TV Shows',
    'Kids Shows',
    'Sports Shows',
    'TV Shows (Pak)',
  ];

  movieWatchlist: any = [];

  tvShowsWatchlist: any = [];
  tvShowsPakWatchlist: any = [];
  kidsShowsWatchlist: any = [];
  sportsShowsWatchlist: any = [];

  webseriesWatchlist: any = [];
  imageLoaded: any = [];

  constructor(
    private router: Router,
    public platform: Platform,
    private navCtrl: NavController,
    public apiService: ApiService
  ) {}

  ngOnInit() {
    let webseriesIds = localStorage.getItem('webSeriesWatchlist') || '';
    if (webseriesIds) {
      let id = JSON.parse(webseriesIds);
      let commaSeperataId = id.join(',');
      this.apiService
        .post('getWatchList', {
          module: 'web_series',
          content_ids: commaSeperataId,
        })
        .subscribe((res: any) => {
          console.log(res);
          if (typeof res == 'object') {
            this.webseriesWatchlist = res.data;
          }
        });
    }

    let tvShowIds = localStorage.getItem('tvShowsWatchlist') || '';
    if (tvShowIds) {
      let id = JSON.parse(tvShowIds);
      let commaSeperataId = id.join(',');
      this.apiService
        .post('getWatchList', {
          module: 'tv_shows',
          content_ids: commaSeperataId,
        })
        .subscribe((res: any) => {
          console.log(res);
          if (typeof res == 'object') {
            this.tvShowsWatchlist = res.data;

            console.log(this.tvShowsWatchlist);
          }
        });
    }

    let kidsShowIds = localStorage.getItem('kidsShowsWatchlist') || '';
    if (kidsShowIds) {
      let kidId = JSON.parse(kidsShowIds);
      let commaSeperataKidId = kidId.join(',');
      this.apiService
        .post('getWatchList', {
          module: 'kids_shows',
          content_ids: commaSeperataKidId,
        })
        .subscribe((res: any) => {
          console.log(res);
          if (typeof res == 'object') {
            this.kidsShowsWatchlist = res.data;

            console.log(this.kidsShowsWatchlist);
          }
        });
    }

    let SportsShowIds = localStorage.getItem('sportsShowsWatchlist') || '';
    if (SportsShowIds) {
      let SportsId = JSON.parse(SportsShowIds);
      let commaSeperataSportsId = SportsId.join(',');
      this.apiService
        .post('getWatchList', {
          module: 'sports_tournaments',
          content_ids: commaSeperataSportsId,
        })
        .subscribe((res: any) => {
          console.log(res);
          if (typeof res == 'object') {
            this.sportsShowsWatchlist = res.data;

            console.log(this.sportsShowsWatchlist);
          }
        });
    }

    let tvShowsPakShowIds = localStorage.getItem('tvShowsPakWatchlist') || '';
    if (tvShowsPakShowIds) {
      let TvPakId = JSON.parse(tvShowsPakShowIds);
      let commaSeperataTvPakId = TvPakId.join(',');
      this.apiService
        .post('getWatchList', {
          module: 'tv_shows_pak',
          content_ids: commaSeperataTvPakId,
        })
        .subscribe((res: any) => {
          console.log(res);
          if (typeof res == 'object') {
            this.tvShowsPakWatchlist = res.data;

            console.log(this.tvShowsPakWatchlist);
          }
        });
    }
  }

  onDelete(id: string, slidingEl: IonItemSliding) {
    slidingEl.close();
    this.movieWatchlist = this.movieWatchlist.filter(
      (item: any) => item.id !== id
    );
    this.isToastOpen = true;
  }

  onDeleteTvShow(id: string, slidingEl: IonItemSliding) {
    slidingEl.close();
    this.tvShowsWatchlist = this.tvShowsWatchlist.filter(
      (item: any) => item.id !== id
    );
    this.isToastOpen = true;
  }

  onDeleteSeries(id: string, slidingEl: IonItemSliding) {
    slidingEl.close();
    this.webseriesWatchlist = this.webseriesWatchlist.filter(
      (item: any) => item.id !== id
    );
    this.isToastOpen = true;
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(item: any) {
    // if (item.content_type == 2) {

      this.router.navigateByUrl('episodes/' + item.id);
    // } else {
      // this.router.navigateByUrl('movie-detail/'+item.id+'/movie');
    // }
  }
  onFilterUpdate(event: any) {
    this.selectedTabValue = event.detail.value;
  }

  goToTvShows(item: any) {
    localStorage.setItem('selectedTvData', JSON.stringify(item));
    this.router.navigateByUrl('/tv-channels/episodes-list/' + item.id);
  }

  goToKidsShows(item: any) {
    localStorage.setItem('selectedKidsData', JSON.stringify(item));
    this.router.navigateByUrl('/kids/episodes-list/' + item.id);
  }
  goToSportsShows(data: any) {
    localStorage.setItem('selectedSportsData', JSON.stringify(data));
    this.router.navigate(['/sports/events-list', data.id]);
  }
  goToTvShowsPak(data: any) {
    localStorage.setItem('selectedTvPakData', JSON.stringify(data));
    this.router.navigate(['/tv-channels-pak/episodes-list', data.id]);
  }
}
