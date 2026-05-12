import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-content-networks',
  templateUrl: './content-networks.page.html',
  styleUrls: ['./content-networks.page.scss'],
})
export class ContentNetworksPage implements OnInit {

  networkId: any;
  networkName: any;

  /** Genres */
  genres: string[] = [];
  selectedGenre: string = '';

  /** Slider */
  sliders: any[] = [];

  /** Grid items */
  items: any[] = [];
  imageLoaded: boolean[] = [];

  /** Pagination */
  page = 1;
  records = 30;
  lastPage = false;
  loading = false;
  pendingReset = false;

  /** Skeleton flags */
  showSkeleton = false;
  showGenreSkeleton = false;

  constructor(
    private route: ActivatedRoute,
    public apiService: ApiService,
    private router: Router
  ) {}

  ngOnInit() {
    this.networkName = localStorage.getItem('networkName');
    this.networkId = this.route.snapshot.paramMap.get('networkId');

    this.loadGenres();
    this.loadContents(true);
  }

  // ================= GENRES =================
  loadGenres() {
    this.showGenreSkeleton = true;

    this.apiService
      .post('getGenreByContentNetwork', {
        data_for: '',
        network_id: this.networkId,
      })
      .subscribe((res: any) => {
        this.genres = res?.genres || [];
        this.showGenreSkeleton = false;
      }, () => {
        this.showGenreSkeleton = false;
      });
  }

  onGenreChange(genre: string) {
    if (this.selectedGenre === genre) return;

    this.selectedGenre = genre;
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

    const payload = {
      genre: this.selectedGenre || '',
      network_id: this.networkId,
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
  checktype(content: any) {
    if (content.content_type === 1) {
      if (
        ['youtube', 'youtubelive'].includes(
          (content.source_type || '').toString().toLowerCase()
        )
      ) {
        this.router.navigate(['/player'], {
          queryParams: { url: content.movie_url },
        });
      } else {
        this.apiService.playVideoWithUrl(content.movie_url);
      }
    } else {
      this.router.navigate(['/tv-channels/episodes-list/', content.id]);
    }
  }
}
