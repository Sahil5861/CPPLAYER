import { Component, OnDestroy, OnInit } from '@angular/core';
import { NavigationStart, Router } from '@angular/router';
import { filter, Subscription } from 'rxjs';

import { ApiService } from 'src/app/api.service';
import { Over18AccessService } from 'src/app/over18-access.service';

@Component({
  selector: 'app-over18',
  templateUrl: './over18.page.html',
  styleUrls: ['./over18.page.scss'],
})
export class Over18Page implements OnInit, OnDestroy {
  items: any[] = [];
  imageLoaded: boolean[] = [];
  currentPage = 1;
  pageSize = 24;
  isLoading = false;
  hasMore = true;

  private pin = '';
  private nextUrl = '';
  private routeSub?: Subscription;

  constructor(
    private router: Router,
    public apiService: ApiService,
    private over18AccessService: Over18AccessService
  ) {}

  ngOnInit() {
    this.routeSub = this.router.events
      .pipe(filter((event) => event instanceof NavigationStart))
      .subscribe((event) => {
        this.nextUrl = (event as NavigationStart).url || '';
      });
  }

  ionViewWillEnter() {
    this.pin = this.over18AccessService.getSessionPin();

    if (!this.pin) {
      this.router.navigateByUrl('/bottom-tab-bar/home2');
      return;
    }

    if (!this.items.length) {
      this.loadItems(true);
    }
  }

  ionViewWillLeave() {
    if (!this.keepSessionForRoute(this.nextUrl)) {
      this.over18AccessService.clearSession();
    }
  }

  ngOnDestroy() {
    this.routeSub?.unsubscribe();
  }

  private keepSessionForRoute(url: string): boolean {
    const cleanUrl = (url || '').split('?')[0];
    return cleanUrl === '/player' || cleanUrl === '/video-player';
  }

  goBack() {
    this.over18AccessService.clearSession();
    this.router.navigateByUrl('/bottom-tab-bar/home2');
  }

  loadItems(reset = false, event?: any) {
    if (!this.pin || this.isLoading) {
      event?.target?.complete();
      return;
    }

    if (reset) {
      this.currentPage = 1;
      this.hasMore = true;
      this.items = [];
      this.imageLoaded = [];
    }

    if (!this.hasMore) {
      event?.target?.complete();
      if (event?.target) {
        event.target.disabled = true;
      }
      return;
    }

    this.isLoading = true;
    this.apiService
      .post(`getAllAbove18Movies?page=${this.currentPage}&records=${this.pageSize}`, {
        pin: this.pin,
        genre: '',
      })
      .subscribe({
        next: (res: any) => {
          const data = Array.isArray(res) ? res : [];
          if (data.length) {
            this.items = this.items.concat(data);
            this.imageLoaded = new Array(this.items.length).fill(false);
            this.currentPage++;
          }

          if (!data.length || data.length < this.pageSize) {
            this.hasMore = false;
          }
        },
        error: () => {
          this.hasMore = false;
        },
        complete: () => {
          this.isLoading = false;
          if (event?.target) {
            event.target.complete();
            if (!this.hasMore) {
              event.target.disabled = true;
            }
          }
        },
      });
  }

  handleRefresh(event: any) {
    this.loadItems(true, event);
  }

  loadMore(event: any) {
    this.loadItems(false, event);
  }

  playItem(item: any) {
    if (['youtube', 'youtubelive'].includes((item?.source_type || '').toString().toLowerCase())) {
      this.router.navigate(['/player'], {
        queryParams: { url: item?.movie_url || '' },
      });
      return;
    }

    this.apiService.playVideoWithUrl(item?.movie_url);
  }
}
