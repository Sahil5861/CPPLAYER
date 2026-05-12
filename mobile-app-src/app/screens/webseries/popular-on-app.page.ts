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

  /** 🔹 Header */
  networkName: string = 'Web Series';

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
  page: number = 1;
  records: number = 50;
  lastPage: boolean = false;
  loading: boolean = false;
  pendingReset = false;

  showSkeleton = false;

  showNetworkSkeleton = false;
  showGenreSkeleton = false;

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
      .post('getNetworks', { data_for: 'webseries' })
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
    this.networkName = network.name || 'Web Series';

    this.selectedGenre = '';
    this.page = 1;
    this.lastPage = false;
    this.items = [];
    this.sliders = [];
    this.imageLoaded = [];

    this.loadGenres();
    this.loadContents(true);
  }

  /** 🔹 Load genres */
  loadGenres() {
    this.showGenreSkeleton = true;

    this.apiService
      .post('getGenreByContentNetwork', {
        data_for: 'webseries',
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
      data_for: 'webseries',
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

          }, Math.max(minSkeletonTime - elapsed, 0));
        },
        () => {
          this.loading = false;
          this.showSkeleton = false;
          this.flushPendingReset();
        }
      );
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
    // aapka existing logic yahan rahega
    console.log('Clicked item:', item);
  }

  /** 🔹 Back */
  goBack() {
    this.navCtrl.back();
  }

  /** 🔹 Unified navigation (same as content-network & webseries) */
  goTo(item: any) {

    // Web Series → Episodes
    if (item.content_type !== 1) {
      localStorage.setItem('categoryId', item.id);
      this.router.navigate(['/episodes', item.id]);
      return;
    }
  }
}
