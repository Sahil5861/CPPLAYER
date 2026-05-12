import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import { ScreenOrientation } from '@capacitor/screen-orientation';
import { Over18AccessService } from 'src/app/over18-access.service';

@Component({
  selector: 'app-home2',
  templateUrl: './home2.page.html',
  styleUrls: ['./home2.page.scss'],
})
export class Home2Page implements OnInit {

  bannersList: any = [];
  networks: any = [];
  tvChannels: any = [];
  tvChannelsPak: any = [];
  religionChannels: any = [];
  stageShowPak: any = [];
  laughterShows: any = [];
  sportsCategories: any = [];
  kids: any = [];
  domainData: any;
  showOver18Entry = false;
  fallbackImage = 'assets/images/logo.png';
  private readonly blockedImageHosts = new Set([
    'cdn.brandfetch.io',
    'imgs.search.brave.com',
    'shutterstock.com',
    'www.shutterstock.com',
  ]);

  /** 🔹 Skeleton flags */
  showBannerSkeleton = true;
  showNetworkSkeleton = true;
  showReligiousSkeleton = true;
  showTvSkeleton = true;
  showKidsSkeleton = true;
  showSportsSkeleton = true;
  showPakTvSkeleton = true;
  showStageSkeleton = true;

  /** 🔹 Image loading trackers */
  bannerLoaded: boolean[] = [];
  networkLoaded: boolean[] = [];
  religiousLoaded: boolean[] = [];
  tvLoaded: boolean[] = [];
  kidsLoaded: boolean[] = [];
  sportsLoaded: boolean[] = [];
  pakTvLoaded: boolean[] = [];
  stageLoaded: boolean[] = [];

  /** 🔹 Retry counters (max 3 retries per image) */
  private bannerRetries: number[] = [];
  private networkRetries: number[] = [];
  private religiousRetries: number[] = [];
  private tvRetries: number[] = [];
  private kidsRetries: number[] = [];
  private sportsRetries: number[] = [];
  private pakTvRetries: number[] = [];
  private stageRetries: number[] = [];

  constructor(
    private router: Router,
    public apiService: ApiService,
    private over18AccessService: Over18AccessService
  ) {}

  ngOnInit() {
    this.apiService.checkExpiryPlan();
    this.getData();
    this.domainData = JSON.parse(localStorage.getItem('domainData') || '{}');
    this.fallbackImage = this.apiService.getContentFallbackImage();
    this.over18AccessService.fetchVisibility().subscribe((isVisible: boolean) => {
      this.showOver18Entry = isVisible;
    });
  }

  getData() {
    // 🔹 Banner
    this.apiService.get('getCustomImageSlider').subscribe((res: any) => {
      this.bannersList = this.prepareImageItems(res, 'banner');
      this.bannerLoaded = new Array(this.bannersList.length).fill(false);
      this.bannerRetries = new Array(this.bannersList.length).fill(0);
      this.showBannerSkeleton = false;
    });

    // 🔹 TV
    this.apiService.get('getTvChannels?page=1&records=10').subscribe((res: any) => {
      this.tvChannels = this.prepareImageItems(res, 'logo');
      this.tvLoaded = new Array(this.tvChannels.length).fill(false);
      this.tvRetries = new Array(this.tvChannels.length).fill(0);
      this.showTvSkeleton = false;
    });

    // 🔹 TV Pak
    this.apiService.get('getTvChannelsPak?page=1&records=10').subscribe((res: any) => {
      this.tvChannelsPak = this.prepareImageItems(res, 'logo');
      this.pakTvLoaded = new Array(this.tvChannelsPak.length).fill(false);
      this.pakTvRetries = new Array(this.tvChannelsPak.length).fill(0);
      this.showPakTvSkeleton = false;
    });

    // 🔹 Religious
    this.apiService.get('getReligiousChannels?page=1&records=10').subscribe((res: any) => {
      this.religionChannels = this.prepareImageItems(res, 'logo');
      this.religiousLoaded = new Array(this.religionChannels.length).fill(false);
      this.religiousRetries = new Array(this.religionChannels.length).fill(0);
      this.showReligiousSkeleton = false;
    });

    // 🔹 Sports
    this.apiService.get('getsportCategories?page=1&records=10').subscribe((res: any) => {
      this.sportsCategories = this.prepareImageItems(res, 'thumbnail');
      this.sportsLoaded = new Array(this.sportsCategories.length).fill(false);
      this.sportsRetries = new Array(this.sportsCategories.length).fill(0);
      this.showSportsSkeleton = false;
    });

    // 🔹 Kids
    this.apiService.get('getKidsChannels?page=1&records=10').subscribe((res: any) => {
      this.kids = this.prepareImageItems(res, 'logo');
      this.kidsLoaded = new Array(this.kids.length).fill(false);
      this.kidsRetries = new Array(this.kids.length).fill(0);
      this.showKidsSkeleton = false;
    });

    // 🔹 Stage Shows
    this.apiService.get('getAllStageShowsPak?page=1&records=10').subscribe((res: any) => {
      this.stageShowPak = this.prepareImageItems(res, 'banner');
      this.stageLoaded = new Array(this.stageShowPak.length).fill(false);
      this.stageRetries = new Array(this.stageShowPak.length).fill(0);
      this.showStageSkeleton = false;
    });

    // 🔹 Networks
    this.apiService.post('getNetworks', { data_for: "content" }).subscribe((res: any) => {
      this.networks = this.prepareImageItems(res, 'logo');
      this.networkLoaded = new Array(this.networks.length).fill(false);
      this.networkRetries = new Array(this.networks.length).fill(0);
      this.showNetworkSkeleton = false;
    });
  }

  private prepareImageItems(items: any[], field: string) {
    return (items || []).map((item: any) => ({
      ...item,
      [field]: this.normalizeImageUrl(item?.[field]),
    }));
  }

  private normalizeImageUrl(url: any): string {
    const cleanUrl = typeof url === 'string' ? url.trim() : '';

    if (!cleanUrl) {
      return this.fallbackImage;
    }

    if (cleanUrl.startsWith('assets/') || cleanUrl.startsWith('data:image/')) {
      return cleanUrl;
    }

    try {
      const hostname = new URL(cleanUrl).hostname.toLowerCase();

      if (this.blockedImageHosts.has(hostname)) {
        return this.fallbackImage;
      }
    } catch {
      return this.fallbackImage;
    }

    return cleanUrl;
  }

  private appendCacheBuster(url: string): string {
    const cleanUrl = this.normalizeImageUrl(url);

    if (
      !cleanUrl ||
      cleanUrl === this.fallbackImage ||
      cleanUrl.startsWith('assets/') ||
      cleanUrl.startsWith('data:image/')
    ) {
      return cleanUrl || this.fallbackImage;
    }

    const [urlWithoutHash, hashFragment] = cleanUrl.split('#');
    const separator = urlWithoutHash.includes('?') ? '&' : '?';

    return `${urlWithoutHash}${separator}t=${Date.now()}${
      hashFragment ? `#${hashFragment}` : ''
    }`;
  }

  private handleRetry(
    index: number,
    item: any,
    field: string,
    retries: number[],
    loaded: boolean[]
  ) {
    const currentUrl = item?.[field];

    if (
      !currentUrl ||
      currentUrl === this.fallbackImage ||
      currentUrl.startsWith('assets/')
    ) {
      item[field] = this.fallbackImage;
      loaded[index] = true;
      return;
    }

    if (retries[index] < 3) {
      retries[index]++;
      setTimeout(() => {
        item[field] = this.appendCacheBuster(currentUrl);
      }, 1000 * retries[index]);
      return;
    }

    item[field] = this.fallbackImage;
    loaded[index] = true;
  }

  /** 🔹 Retry functions for each section (max 3 attempts) */
  retryBannerLoad(index: number, item: any) {
    this.handleRetry(index, item, 'banner', this.bannerRetries, this.bannerLoaded);
  }

  retryNetworkLoad(index: number, item: any) {
    this.handleRetry(index, item, 'logo', this.networkRetries, this.networkLoaded);
  }

  retryReligiousLoad(index: number, item: any) {
    this.handleRetry(index, item, 'logo', this.religiousRetries, this.religiousLoaded);
  }

  retryTvLoad(index: number, item: any) {
    this.handleRetry(index, item, 'logo', this.tvRetries, this.tvLoaded);
  }

  retryKidsLoad(index: number, item: any) {
    this.handleRetry(index, item, 'logo', this.kidsRetries, this.kidsLoaded);
  }

  retrySportsLoad(index: number, item: any) {
    this.handleRetry(index, item, 'thumbnail', this.sportsRetries, this.sportsLoaded);
  }

  retryPakTvLoad(index: number, item: any) {
    this.handleRetry(index, item, 'logo', this.pakTvRetries, this.pakTvLoaded);
  }

  retryStageLoad(index: number, item: any) {
    this.handleRetry(index, item, 'banner', this.stageRetries, this.stageLoaded);
  }

  ionViewWillEnter() {
    this.unlock();
  }

  async unlock() {
    await ScreenOrientation.lock({ orientation: 'portrait' });
  }

  openOver18() {
    this.over18AccessService.promptForPinAndNavigate();
  }

  gotoReligious(item: any) {
    localStorage.setItem('channelName', item.name);
    this.router.navigate(['/religious/show-list', item.id]);
  }

  gotoTvChannels(item: any) {
    localStorage.setItem('channelName', item.name);
    this.router.navigate(['/tv-channels/shows-list', item.id]);
  }

  gotoKids(item: any) {
    localStorage.setItem('channelName', item.name);
    this.router.navigate(['/kids/shows-list', item.id]);
  }

  gotoSports(item: any) {
    localStorage.setItem('channelName', item.title);
    this.router.navigate(['/sports/tournament-list', item.id]);
  }

  gotoTvChannelsPak(item: any) {
    localStorage.setItem('channelName', item.name);
    this.router.navigate(['/tv-channels-pak/shows-list', item.id]);
  }

  gotoNetwork(item: any) {
    localStorage.setItem('networkName', item.name);
    this.router.navigate(['/content-networks', item.id]);
  }

  playStage(item: any) {
    this.apiService.updateViewHistory({
      user_id: this.apiService.getUserDetail('id'),
      content_type: item.content_type,
      event_id: item.id,
      event_title: item.name || item.title,
      url: item.url || item.movie_url,
      category_id: ""
    });

    if ((item.source_type || '').toString().toLowerCase() === 'm3u8') {
      this.apiService.playVideoWithUrl(item.movie_url);
    } else {
      this.router.navigate(['/player'], {
        queryParams: { url: item.movie_url },
      });
    }
  }

  handleRefresh(event: any) {
    // Reset all loaders on refresh
    this.bannerLoaded = [];
    this.networkLoaded = [];
    this.religiousLoaded = [];
    this.tvLoaded = [];
    this.kidsLoaded = [];
    this.sportsLoaded = [];
    this.pakTvLoaded = [];
    this.stageLoaded = [];
    
    this.bannerRetries = [];
    this.networkRetries = [];
    this.religiousRetries = [];
    this.tvRetries = [];
    this.kidsRetries = [];
    this.sportsRetries = [];
    this.pakTvRetries = [];
    this.stageRetries = [];
    
    this.getData();
    setTimeout(() => event.target.complete(), 1500);
  }
}
