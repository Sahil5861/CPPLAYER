import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';

@Component({
  selector: 'app-popular-on-app',
  templateUrl: './popular-on-app.page.html',
  styleUrls: ['./popular-on-app.page.scss'],
})
export class PopularOnAppPage implements OnInit {

  /** 🔹 Networks */
  networks: any[] = [];
  networkId: any = null;

  /** 🔹 Genres */
  genres: any[] = [];
  selectedGenre: string = '';

  /** 🔹 Sliders */
  sliders: any[] = [];

  /** 🔹 Grid Items */
  items: any[] = [];
  imageLoaded: boolean[] = [];

  /** 🔹 Pagination */
  page = 1;
  records = 30;
  lastPage = false;
  loading = false;
  pendingReset = false;

  /** 🔹 Skeleton flags */
  showSkeleton = false;
  showNetworkSkeleton = false;
  showGenreSkeleton = false;

  constructor(
    private router: Router,
    public platform: Platform,
    private navCtrl: NavController,
    public apiService: ApiService
  ) {}

  ngOnInit() {
    this.getNetworks();
  }

  // ================= NETWORKS =================
  getNetworks() {
    this.showNetworkSkeleton = true;

    this.apiService
      .post('getNetworks', { data_for: 'movies' })
      .subscribe((res: any) => {
        this.networks = res || [];
        this.showNetworkSkeleton = false;

        if (this.networks.length) {
          this.selectNetwork(this.networks[0]);
        }
      }, () => {
        this.showNetworkSkeleton = false;
      });
  }

  selectNetwork(network: any) {
    if (!network || this.networkId === network.id) return;

    this.networkId = network.id;

    this.selectedGenre = '';
    this.page = 1;
    this.lastPage = false;
    this.items = [];
    this.imageLoaded = [];
    this.sliders = [];

    this.loadGenres();
    this.loadContents(true);
  }

  // ================= GENRES =================
  loadGenres() {
    this.showGenreSkeleton = true;

    this.apiService
      .post('getGenreByContentNetwork', {
        data_for: 'movies',
        network_id: this.networkId
      })
      .subscribe((res: any) => {
        this.genres = res?.genres || [];
        this.showGenreSkeleton = false;
      }, () => {
        this.showGenreSkeleton = false;
      });
  }

  onGenreChange(genre: any) {
    this.selectedGenre = genre || '';
    this.loadContents(true);
  }

  private resetContentState() {
    this.page = 1;
    this.items = [];
    this.imageLoaded = [];
    this.lastPage = false;
  }

  private flushPendingReset() {
    if (!this.pendingReset) {
      return;
    }

    this.pendingReset = false;
    this.loadContents(true);
  }

  // ================= CONTENT =================
  loadContents(reset: boolean) {
    if (reset) {
      this.resetContentState();
    } else if (this.lastPage) {
      return;
    }

    if (this.loading) {
      if (reset) {
        this.pendingReset = true;
      }
      return;
    }

    this.pendingReset = false;
    this.loading = true;
    this.showSkeleton = true;

    const payload: any = {
      network_id: this.networkId,
      data_for: 'movies',
      genre: this.selectedGenre || ''
    };

    const start = Date.now();
    const minTime = 400;

    this.apiService
      .post(
        `getAllContentsOfNetworkNew?page=${this.page}&records=${this.records}`,
        payload
      )
      .subscribe((res: any) => {

        const elapsed = Date.now() - start;

        setTimeout(() => {
          if (this.pendingReset) {
            this.loading = false;
            this.showSkeleton = false;
            this.flushPendingReset();
            return;
          }

          if (reset) {
            this.sliders = res?.content_sliders || [];
          }

          if (res?.data?.length) {
            this.items.push(...res.data);
            res.data.forEach(() => this.imageLoaded.push(false));
            this.page++;
            this.lastPage = res.data.length < this.records;
          } else {
            this.lastPage = true;
          }

          this.loading = false;
          this.showSkeleton = false;
          this.flushPendingReset();

        }, Math.max(minTime - elapsed, 0));
      }, () => {
        this.loading = false;
        this.showSkeleton = false;
        this.flushPendingReset();
      });
  }

  // ================= LOAD MORE =================
  loadMoreItems(event: any) {
    if (this.loading || this.lastPage) {
      event.target.complete();
      return;
    }

    this.loadContents(false);

    setTimeout(() => {
      event.target.complete();
    }, 300);
  }

  // ================= PLAY =================
  goTo(item: any) {
    this.apiService.updateViewHistory({
      user_id: this.apiService.getUserDetail('id'),
      content_type: item.content_type,
      event_id: item.id,
      event_title: item.name || item.title,
      url: item.movie_url,
      category_id: ''
    });

    if (['youtube', 'youtubelive'].includes((item.source_type || '').toString().toLowerCase())) {
      this.router.navigate(['/player'], {
        queryParams: { url: item.movie_url }
      });
    } else {
      this.apiService.playVideoWithUrl(item.movie_url);
    }
  }

  goBack() {
    this.navCtrl.back();
  }
}
