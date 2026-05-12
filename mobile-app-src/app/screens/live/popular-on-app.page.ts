import { Component, OnInit, ViewChild } from '@angular/core';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
import { IonInfiniteScroll } from '@ionic/angular';

@Component({
  selector: 'app-popular-on-app',
  templateUrl: './popular-on-app.page.html',
  styleUrls: ['./popular-on-app.page.scss'],
})
export class PopularOnAppPage implements OnInit {

  /** Languages */
  languages: any[] = [];
  selectedLanguage: any = null;

  /** Sliders (language based) */
  sliders: any[] = [];

  /** Genres */
  genres: any[] = [];
  selectedGenre: string = 'All';

  /** Channels */
  channels: any[] = [];
  page = 1;
  records = 24;
  loading = false;
  lastPage = false;
  pendingReset = false;

  showSkeleton = false;

  imageLoaded: boolean[] = [];

  @ViewChild(IonInfiniteScroll) infiniteScroll!: IonInfiniteScroll;

  constructor(
    public apiService: ApiService,
    public platform: Platform,
    private navCtrl: NavController
  ) {}

  ngOnInit() {
    this.loadLanguages();
    this.loadGenres();
  }

  // ===============================
  // 🔹 LANGUAGES (SEPARATE)
  // ===============================
  loadLanguages() {
    this.apiService
      .get('getAllLanguages')
      .subscribe((res: any) => {

        this.languages = res?.languages || [];

        if (this.languages.length > 0) {
          this.onLanguageChange(this.languages[0]); // default select
        }
      });
  }

  onLanguageChange(lang: any) {
    if (this.selectedLanguage === lang) return;

    this.selectedLanguage = lang;
    this.sliders = lang.slider || [];

    // ✅ RESET GENRE
    this.selectedGenre = 'All';

    this.loadChannels(true);
  }

  doRefresh(event: any) {
    this.selectedGenre = 'All';
    this.loadChannels(true);

    setTimeout(() => {
      event.target.complete();
    }, 500);
  }



  // ===============================
  // 🔹 GENRES (SEPARATE)
  // ===============================
  loadGenres() {
    this.apiService
      .get('getLiveTvGenreList')
      .subscribe((res: any) => {
        this.genres = res?.data || [];
      });
  }

  onGenreChange(genre: string) {
    if (this.selectedGenre === genre) return;

    this.selectedGenre = genre;
    this.loadChannels(true);
  }


  // ===============================
  // 🔹 CHANNELS (PAGINATED)
  // ===============================
  resetChannels() {
    this.page = 1;
    this.channels = [];
    this.lastPage = false;
    this.imageLoaded = [];
  }

  private flushPendingReset() {
    if (!this.pendingReset) {
      return;
    }

    this.pendingReset = false;
    this.loadChannels(true);
  }

  loadChannels(reset: boolean = false) {
    if (reset) {
      this.resetChannels();
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

    const startTime = Date.now();

    this.apiService
      .getAllLiveTV(
        this.selectedGenre === 'All' ? '' : this.selectedGenre,
        this.selectedLanguage?.id || '',
        this.page,
        this.records
      )
      .subscribe((res: any) => {

        const elapsed = Date.now() - startTime;
        const minTime = 400;
        const nextItems = Array.isArray(res)
          ? res
          : res?.data || res?.channels || [];

        setTimeout(() => {
          if (this.pendingReset) {
            this.loading = false;
            this.showSkeleton = false;
            this.flushPendingReset();
            return;
          }

          if (this.page === 1 && res?.content_sliders?.length) {
            this.sliders = res.content_sliders;
          }

          if (nextItems.length > 0) {
            this.channels.push(...nextItems);
            nextItems.forEach(() => this.imageLoaded.push(false));
            this.page++;
            this.lastPage = nextItems.length < this.records;
          } else {
            this.lastPage = true;
          }

          this.loading = false;
          this.showSkeleton = false;
          this.flushPendingReset();

        }, Math.max(minTime - elapsed, 0));
      }, (error) => {
        console.error('Error loading live channels', error);
        this.loading = false;
        this.showSkeleton = false;
        this.flushPendingReset();
      });
  }


  loadMore(event: any) {
    if (this.loading || this.lastPage) {
      event.target.complete();
      return;
    }

    this.loadChannels(false);

    setTimeout(() => {
      event.target.complete();
    }, 300);
  }


  // ===============================
  // 🔹 PLAY LIVE CHANNEL
  // ===============================
  goTo(item: any) {
    this.apiService.updateViewHistory({
      user_id: this.apiService.getUserDetail('id'),
      content_type: item.content_type,
      event_id: item.id,
      event_title: item.channel_name,
      url: item.channel_link,
      category_id: ''
    });

    this.apiService.playVideoWithUrl(item, {
      isLive: true,
      title: item.channel_name || item.name || item.title || '',
    });
  }

  goBack() {
    this.navCtrl.back();
  }
}
