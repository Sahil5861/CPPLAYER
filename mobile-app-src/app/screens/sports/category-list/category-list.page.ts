import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../../api.service';

@Component({
  selector: 'app-category-list',
  templateUrl: './category-list.page.html',
  styleUrls: ['./category-list.page.scss'],
})
export class CategoryListPage implements OnInit {

  /** 🔹 Header */
  networkName: string = 'Sports';

  /** 🔹 Networks */
  networks: any[] = [];
  networkId: any = null;

  /** 🔹 Genres */
  genres: any[] = [];
  selectedGenre: string = '';

  /** 🔹 Sliders */
  sliders: any[] = [];
  sliderLoaded: boolean[] = [];

  /** 🔹 Grid Items */
  items: any[] = [];
  imageLoaded: boolean[] = [];

  /** 🔹 Pagination */
  page: number = 1;
  records: number = 30;
  lastPage: boolean = false;
  loading: boolean = false;
  pendingReset = false;

  showSkeleton = false;
  showNetworkSkeleton = false;
  showGenreSkeleton = false;

  /** 🔹 Retry counters (max 3 retries per image) */
  private imageRetries: number[] = [];
  private sliderRetries: number[] = [];

  constructor(
    private router: Router,
    public platform: Platform,
    private navCtrl: NavController,
    private route: ActivatedRoute,
    public apiService: ApiService
  ) {}

  ngOnInit() {
    this.getNetworks();
  }

  /** 🔹 Networks for header segment */
  getNetworks() {
    this.showNetworkSkeleton = true;

    this.apiService
      .post('getNetworks', { data_for: 'sports' })
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

  /** 🔹 On network click */
  selectNetwork(network: any) {
    if (!network || this.networkId === network.id) return;

    this.networkId = network.id;
    this.networkName = network.name || 'Kids';

    this.selectedGenre = '';
    this.page = 1;
    this.lastPage = false;
    this.items = [];
    this.sliders = [];
    this.imageLoaded = [];
    this.sliderLoaded = [];
    this.imageRetries = [];
    this.sliderRetries = [];

    this.loadGenres();
    this.loadContents(true);
  }

  /** 🔹 Load genres */
  loadGenres() {
    this.showGenreSkeleton = true;

    this.apiService
      .post('getGenreByContentNetwork', {
        data_for: 'sports',
        network_id: this.networkId
      })
      .subscribe((res: any) => {

        this.genres = res?.genres || [];
        this.showGenreSkeleton = false;

      }, () => {
        this.showGenreSkeleton = false;
      });
  }

  /** 🔹 Genre click (HTML mapped) */
  onGenreChange(genre: any) {
    this.selectedGenre = genre || '';
    this.loadContents(true);
  }

  private resetContentState() {
    this.page = 1;
    this.items = [];
    this.imageLoaded = [];
    this.imageRetries = [];
    this.lastPage = false;
  }

  private flushPendingReset() {
    if (!this.pendingReset) {
      return;
    }

    this.pendingReset = false;
    this.loadContents(true);
  }

  /** 🔹 Load contents */
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
      data_for: 'sports',
      genre: this.selectedGenre || ''
    };

    const startTime = Date.now();
    const minSkeletonTime = 400;

    this.apiService
      .post(
        `getAllContentsOfNetworkNew?page=${this.page}&records=${this.records}`,
        payload
      )
      .subscribe(
        (res: any) => {

          const elapsed = Date.now() - startTime;

          setTimeout(() => {
            if (this.pendingReset) {
              this.loading = false;
              this.showSkeleton = false;
              this.flushPendingReset();
              return;
            }

            // sliders only on first load / genre change
            if (reset && res?.content_sliders?.length) {
              this.sliders = res.content_sliders;
              this.sliderLoaded = new Array(this.sliders.length).fill(false);
              this.sliderRetries = new Array(this.sliders.length).fill(0);
            }

            if (res?.data?.length) {
              this.items.push(...res.data);
              res.data.forEach(() => {
                this.imageLoaded.push(false);
                this.imageRetries.push(0);
              });
              this.page++;
              this.lastPage = res.data.length < this.records;
            } else {
              this.lastPage = true;
            }

            this.loading = false;
            this.showSkeleton = false;
            this.flushPendingReset();

          }, Math.max(minSkeletonTime - elapsed, 0));
        },
        () => {
          this.loading = false;
          this.showSkeleton = false;
          this.flushPendingReset();
        }
      );
  }

  /** 🔹 Retry slider image load */
  retrySliderLoad(index: number, item: any) {
    if (this.sliderRetries[index] < 3) {
      this.sliderRetries[index]++;
      setTimeout(() => {
        // Force reload by adding timestamp
        const originalUrl = item.banner.split('?')[0];
        item.banner = originalUrl + '?t=' + Date.now();
      }, 1000 * this.sliderRetries[index]);
    } else {
      // After 3 retries, hide spinner
      this.sliderLoaded[index] = true;
    }
  }

  /** 🔹 Retry grid image load */
  retryImageLoad(index: number, item: any) {
    if (this.imageRetries[index] < 3) {
      this.imageRetries[index]++;
      setTimeout(() => {
        // Try thumbnail first, fallback to banner
        const imageUrl = item.thumbnail || item.banner;
        const originalUrl = imageUrl.split('?')[0];
        
        if (item.thumbnail) {
          item.thumbnail = originalUrl + '?t=' + Date.now();
        } else {
          item.banner = originalUrl + '?t=' + Date.now();
        }
      }, 1000 * this.imageRetries[index]);
    } else {
      // After 3 retries, hide spinner
      this.imageLoaded[index] = true;
    }
  }

  /** 🔹 Infinite scroll (HTML mapped) */
  loadMoreItems(event: any) {
    if (this.loading || this.lastPage) {
      event.target.complete();
      return;
    }

    this.loadContents(false);
    event.target.complete();
  }

  /** 🔹 Item click */
  checktype(item: any) {
    console.log('Clicked item:', item);
  }

  /** 🔹 Back */
  goBack() {
    this.navCtrl.back();
  }

  /** 🔹 Unified navigation */
  goTo(data: any){
    localStorage.setItem('channelName',data.title)
    this.router.navigate(['/sports/tournament-list', data.id]);

  }
}
