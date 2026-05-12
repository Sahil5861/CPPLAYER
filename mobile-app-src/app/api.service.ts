import { Injectable } from '@angular/core';
import { Location } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, EMPTY, Observable, of, throwError } from 'rxjs';
import { catchError, mergeMap, tap } from 'rxjs/operators';
import { ToastController, Platform } from '@ionic/angular';
import { ScreenOrientation } from '@capacitor/screen-orientation';
import {
  CanActivate,
  Router,
  RouterStateSnapshot,
  ActivatedRouteSnapshot,
} from '@angular/router';
import {
  AlertController,
  LoadingController,
  NavController,
} from '@ionic/angular';
import { environment } from '../environments/environment';
import { Device } from '@awesome-cordova-plugins/device/ngx';
import { CapacitorVideoPlayer } from 'capacitor-video-player';
import { VLCPlayer } from 'capacitor-vlc-plugin';

import { VideoPlayerService } from './video-player.service';
import { io } from 'socket.io-client';

type LiveStreamPlayerMode = 'video-player' | 'vlc';
type DomainContentData = {
  app_name?: string;
  logo?: string;
  theme_color?: string;
  domain?: string;
  [key: string]: any;
};

type AppBranding = {
  appName: string;
  logo: string;
  customLogo: string;
  themeColor: string;
  domain: string;
  raw: DomainContentData | null;
};

@Injectable({
  providedIn: 'root',
})
export class ApiService {
  readonly defaultAppName = 'Get Play Box';
  readonly defaultAppLogo = 'assets/mobi_img/get-play-box-logo.png';
  readonly defaultContentFallbackImage = 'assets/images/get-play-box-fallback.png';
  readonly preferredDomains = [
    'dash.getplaybox.com',
    'dashboard.cpplayers.com',
    'testing.cpplayers.com',
  ];
  readonly defaultDomain = 'dash.getplaybox.com';

  videoPlayer: any;
  socket: any;

  handlerPlay: any;
  handlerPause: any;
  handlerEnded: any;
  handlerReady: any;
  handlerExit: any;

  userData: any;
  errMessage: any = 'something went wrong.';
  domain = this.resolveDomain();
  viewLoader: any = false;
  // apiUrl="http://localhost/thepartyonus/backend/api/"
  // apiUrl = 'https://dashboard.cpplayers.com/api/v3/';
  apiUrl = this.buildApiUrl();

  // baseUrl="https://dev.mela.tv/public/"

  settings: any = { channels: [], enableAll: 1 };
  commonErrorMsg: any = 'Something went wrong.';
  noInternetMsg: any = "Can't connect right now. Try again.";
  userOrders: any = [];

  public country: any = '';
  code_calling: any = '';
  eventData: any;
  termPageData: any = {};
  price: any = 0;
  cat: any;
  city: any;
  randomOtp: any;

  app_version_code: any = 4;
  referContent: any;
  currentObject: any = {};

  lastVideoId: any = '';
  lastGetTime: any;

  liveSyncInterval: any;
  deviceId: any;
  activeNativePlayer: LiveStreamPlayerMode | null = null;
  isAuthRedirecting = false;
  branding: AppBranding = {
    appName: this.defaultAppName,
    logo: this.defaultAppLogo,
    customLogo: '',
    themeColor: '#5451f5',
    domain: this.resolveDomain(),
    raw: null,
  };

  constructor(
    private device: Device,
    public videoPlayerService: VideoPlayerService,
    public platform: Platform,
    public navController: NavController,
    public location: Location,
    public loadingController: LoadingController,
    private router: Router,
    public alertController: AlertController,
    private http: HttpClient,
    public toastController: ToastController
  ) {
    const eventDataString = localStorage.getItem('eventData');
    this.setDomain(localStorage.getItem('domain'));
    this.platform.pause.subscribe(() => this.onPause());
    this.platform.resume.subscribe(() => this.onResume());
    this.loadDomainBrandingFromStorage();
    if (eventDataString !== null) {
      this.eventData = JSON.parse(eventDataString);
    }
    if (this.platform.is('ios')) {
      this.getSettings();
    }
    this.videoPlayer = CapacitorVideoPlayer;

    this.addListenersToPlayerPlugin();

    // this._initializeVLCPlayer();

    // this.socket = io('https://78.46.212.202:3000', {
    //   reconnection: true,
    //   reconnectionAttempts: Infinity,
    //   reconnectionDelay: 1000,
    //   reconnectionDelayMax: 5000,
    //   timeout: 20000,
    //   transports: ['websocket'], // Force WebSocket transport
    //   rejectUnauthorized: false, // Temporarily bypass SSL verification
    // });

    // Listen for video URL response
    // this.socket.on('videoUrl', (data: any) => {
    //   this.hideLoading();
    //   console.log(data);
    //   if (
    //     data &&
    //     data.youtubeId &&
    //     data.videoUrl &&
    //     this.lastVideoId == data.youtubeId &&
    //     this.lastGetTime > Date.now() / 1000 - 10
    //   ) {
    //     console.log('Received from server:', data);
    //     this.playVideoWithUrl(data.videoUrl);
    //   }
    // });

    // Handle socket errors
    // this.socket.on('error', (data: any) => {
    //   console.error('Socket error:', data);
    //   if (
    //     data &&
    //     data.youtubeId &&
    //     data.error &&
    //     this.lastVideoId == data.youtubeId &&
    //     this.lastGetTime > Date.now() / 1000 - 10
    //   ) {
    //     this.hideLoading();
    //     this.showPopup('Error', 'Something went wrong. Try again later.');
    //   }
    // });

    // var self = this;
    // $.get( "https://ipapi.co/json", function( data ) {
    //   console.log(data);
    //   self.country = data.country;
    //   self.code_calling = data.country_calling_code;
    //   // $( ".result" ).html( data );
    //   // alert( "Load was performed." );
    // });
    // localStorage.removeItem('cartItems')
    // console.log(localStorage.getItem('cartItems'))

    // let data = {
    //   user_id:"0", name:"User", email:"user@partyonus.com", phone:"+91 xxxxx xxxxx",
    // }
    // localStorage.setItem('userDetail',JSON.stringify(data));
    // this.initializeVlcPlayer('https://live.mobifreetv.com/fx-push/Ptc-Punjabi/playlist.m3u8');

    this.logDeviceId();
  }

  private sanitizeDomain(value: any): string {
    const cleanedValue = (value || '').toString().trim().toLowerCase();

    if (!cleanedValue) {
      return '';
    }

    return cleanedValue
      .replace(/^https?:\/\//, '')
      .replace(/^www\./, '')
      .replace(/\/.*$/, '');
  }

  resolveDomain(preferred?: any): string {
    const explicitDomain = this.sanitizeDomain(preferred);
    if (explicitDomain) {
      return explicitDomain;
    }

    const storedDomain = this.sanitizeDomain(localStorage.getItem('domain'));
    if (storedDomain) {
      return storedDomain;
    }

    return this.defaultDomain;
  }

  buildApiUrl(preferred?: any): string {
    return `https://${this.resolveDomain(preferred)}/api/v3/`;
  }

  setDomain(preferred?: any): string {
    const resolvedDomain = this.resolveDomain(preferred);
    this.domain = resolvedDomain;
    this.apiUrl = this.buildApiUrl(resolvedDomain);
    localStorage.setItem('domain', resolvedDomain);

    if (this.branding) {
      this.branding.domain = resolvedDomain;
    }

    return resolvedDomain;
  }

  private refreshApiUrl(preferred?: any): string {
    const resolvedDomain = this.resolveDomain(preferred);
    this.domain = resolvedDomain;
    this.apiUrl = this.buildApiUrl(resolvedDomain);
    return this.apiUrl;
  }

  private normalizeDomainBranding(domainContent: DomainContentData | null): AppBranding {
    const currentDomain = this.resolveDomain();
    const appName =
      typeof domainContent?.app_name === 'string' && domainContent.app_name.trim()
        ? domainContent.app_name.trim()
        : this.defaultAppName;
    const customLogo =
      typeof domainContent?.logo === 'string' && domainContent.logo.trim()
        ? domainContent.logo.trim()
        : '';
    const themeColor =
      typeof domainContent?.theme_color === 'string' && domainContent.theme_color.trim()
        ? domainContent.theme_color.trim()
        : '#5451f5';
    const domain =
      typeof domainContent?.domain === 'string' && domainContent.domain.trim()
        ? this.sanitizeDomain(domainContent.domain) || currentDomain
        : currentDomain;

    return {
      appName,
      logo: customLogo || this.defaultAppLogo,
      customLogo,
      themeColor,
      domain,
      raw: domainContent,
    };
  }

  private applyDomainBranding(domainContent: DomainContentData | null) {
    this.branding = this.normalizeDomainBranding(domainContent);

    if (typeof document !== 'undefined') {
      document.title = this.branding.appName;
    }
  }

  private getStoredDomainContent(): DomainContentData | null {
    const parseJson = (value: string | null) => {
      if (!value) {
        return null;
      }

      try {
        return JSON.parse(value);
      } catch (error) {
        return null;
      }
    };

    const domainData = parseJson(localStorage.getItem('domainData'));
    if (domainData && typeof domainData === 'object') {
      return domainData;
    }

    const userData = parseJson(localStorage.getItem('userData'));
    const domainContent = userData?.domain_content;

    return domainContent && typeof domainContent === 'object'
      ? domainContent
      : null;
  }

  loadDomainBrandingFromStorage(): AppBranding {
    const storedDomainContent = this.getStoredDomainContent();

    if (!storedDomainContent) {
      this.applyDomainBranding(null);
      return this.branding;
    }

    this.applyDomainBranding(storedDomainContent);

    return this.branding;
  }

  setDomainBranding(domainContent: DomainContentData | null): AppBranding {
    if (domainContent && typeof domainContent === 'object') {
      localStorage.setItem('domainData', JSON.stringify(domainContent));
      this.applyDomainBranding(domainContent);
      return this.branding;
    }

    return this.loadDomainBrandingFromStorage();
  }

  clearDomainBranding() {
    this.applyDomainBranding(null);
  }

  getAppName(): string {
    return this.branding.appName || this.defaultAppName;
  }

  getAppLogo(): string {
    return (
      this.branding.customLogo ||
      this.getStoredDomainContent()?.logo ||
      this.branding.logo ||
      this.defaultAppLogo
    );
  }

  getContentFallbackImage(): string {
    return (
      this.branding.customLogo ||
      this.getStoredDomainContent()?.logo ||
      this.defaultContentFallbackImage
    );
  }

  getImageUrl(url: any, fallbackType: 'content' | 'app' = 'content'): string {
    const cleanUrl = typeof url === 'string' ? url.trim() : '';

    if (cleanUrl) {
      return cleanUrl;
    }

    return fallbackType === 'app'
      ? this.getAppLogo()
      : this.getContentFallbackImage();
  }

  getDisplayTitle(item: any): string {
    const candidates = [
      item?.channel_name,
      item?.name,
      item?.title,
      item?.event_title,
      item?.channel_title,
      item?.show_name,
    ];

    const match = candidates.find(
      (value) => typeof value === 'string' && value.trim()
    );

    return match ? match.trim() : '';
  }

  handleImageErrorEvent(event: any, fallbackType: 'content' | 'app' = 'content') {
    this.handleImageErrorTarget(event?.target, fallbackType);
  }

  clearImageFallbackState(target: any) {
    if (!target || typeof target.removeAttribute !== 'function') {
      return;
    }

    target.removeAttribute('data-fallback-stage');
  }

  handleImageErrorTarget(target: any, fallbackType: 'content' | 'app' = 'content') {
    if (!target) {
      return;
    }

    const primaryFallback =
      fallbackType === 'app' ? this.getAppLogo() : this.getContentFallbackImage();
    const finalFallback =
      fallbackType === 'app'
        ? this.defaultAppLogo
        : this.defaultContentFallbackImage;
    const currentSrc =
      (
        target.getAttribute?.('src') ||
        target.src ||
        target.currentSrc ||
        ''
      ).toString().trim();
    const stage = target.getAttribute?.('data-fallback-stage') || '';

    if (stage !== 'primary' && currentSrc !== primaryFallback) {
      this.setImageSource(target, primaryFallback);
      target.setAttribute?.('data-fallback-stage', 'primary');
      return;
    }

    if (currentSrc !== finalFallback) {
      this.setImageSource(target, finalFallback);
      target.setAttribute?.('data-fallback-stage', 'final');
    }
  }

  private setImageSource(target: any, source: string) {
    if (!target || !source) {
      return;
    }

    try {
      target.src = source;
    } catch (error) {
      console.error('Unable to set image source', error);
    }

    if (typeof target.setAttribute === 'function') {
      target.setAttribute('src', source);
    }
  }

  getShareOptions(contentName?: string, customText?: string) {
    const appName = this.getAppName();
    const title = appName;
    const text =
      customText ||
      (contentName
        ? `Watch ${contentName} on ${appName} with your friends.`
        : `Discover ${appName}. Share movies, live TV, and more with friends.`);

    return {
      title,
      text,
      url: this.platform.is('ios')
        ? 'https://apps.apple.com/app/ekom-flix/id6628923442'
        : 'https://play.google.com/store/apps/details?id=com.getplaybox.cti',
      dialogTitle: 'Share with buddies',
    };
  }

  //  async _initializeVLCPlayer() {
  //   try {
  //     await VLCPlayer.initialize();
  //     console.log('VLC Player initialized');
  //   } catch (error) {
  //     console.error('Error initializing VLC Player:', error);
  //   }
  // }

  async unlockScreenOrientation() {
    await ScreenOrientation.lock({ orientation: 'portrait' });
  }

  async logDeviceId() {
    // this.deviceId = this.device.uuid;
    this.deviceId = '146511585cda5595';
    console.log('deviceId ' + this.deviceId);
  }

  getLiveTvGenreList(): Observable<any> {
    this.refreshApiUrl();
    return this.withAuthHandling(
      this.http.get(`${this.apiUrl}getLiveTvGenreList`)
    );
  }

  getAllLiveTV(
    genre: string,
    languageId: number | string,
    page: number,
    records: number = 25
  ): Observable<any> {
    this.refreshApiUrl();
    return this.withAuthHandling(
      this.http.post(
        `${this.apiUrl}getAllLiveTV?page=${page}&records=${records}`,
        {
          genere: genre,
          genre,
          languageId,
          language_id: languageId,
        }
      )
    );
  }

  async addListenersToPlayerPlugin(): Promise<void> {
    this.handlerPlay = await this.videoPlayer.addListener(
      'jeepCapVideoPlayerPlay',
      async (data: any) => {
        const fromPlayerId = data.fromPlayerId;

        // Start a sync interval to periodically check and stay at the live edge
        this.startLiveSync();

        console.log(`<<<< onPlay in ViewerVideo ${fromPlayerId}`);
      },
      false
    );

    this.handlerPause = await this.videoPlayer.addListener(
      'jeepCapVideoPlayerPause',
      (data: any) => {
        const fromPlayerId = data.fromPlayerId;
        const currentTime = data.currentTime;
        console.log(
          `<<<< onPause in ViewerVideo ${fromPlayerId} ct: ${currentTime}`
        );

        // Stop the live sync interval when paused
        this.stopLiveSync();
      },
      false
    );

    this.handlerEnded = await this.videoPlayer.addListener(
      'jeepCapVideoPlayerEnded',
      (data: any) => {
        const fromPlayerId = data.fromPlayerId;
        const currentTime = data.currentTime;
        console.log(
          `<<<< onEnded in ViewerVideo ${fromPlayerId} ct: ${currentTime}`
        );

        // Stop the live sync interval when playback ends
        this.stopLiveSync();
        this.activeNativePlayer = null;
        this.unlockScreenOrientation();
      },
      false
    );

    this.handlerExit = await this.videoPlayer.addListener(
      'jeepCapVideoPlayerExit',
      (data: any) => {
        const dismiss = data.dismiss;
        console.log(`<<<< onExit in ViewerVideo ${dismiss}`);
        this.stopLiveSync();
        this.activeNativePlayer = null;
        this.unlockScreenOrientation();
      },
      false
    );

    this.handlerReady = await this.videoPlayer.addListener(
      'jeepCapVideoPlayerReady',
      async (data: any) => {
        const fromPlayerId = data.fromPlayerId;

        // Start the sync when the player is ready
        this.startLiveSync();

        console.log(`<<<< onReady in ViewerVideo ${fromPlayerId}`);
      },
      false
    );

    return;
  }

  // {"id":1,"admin_id":4,"domain":"coretechinfo.com","content":"India","logo":"https://coretechinfo.com/frontend/assets/images/logo.png","app_name":"CTI","theme_color":"#5451f5","live_channels":[""],"movies":1,"webseries":1,"tvshow":1,"tvshow_pak":1,"kids_show":1,"religious":1,"sports":1,"stage_shows":1,"laughter_shows":1,"created_at":"2025-08-16T19:52:36.000000Z","updated_at":"2025-08-20T05:41:53.000000Z"}

  showCat(item: string): boolean {
    const data = this.getStoredDomainContent();
    return !!data && data[item] === 1;
  }

  private isInvalidAuthResponse(response: any): boolean {
    const message = (response?.msg || response?.message || '')
      .toString()
      .toLowerCase();
    const hasAuthMessage =
      message.includes('invalid authentication') ||
      message.includes('please login again') ||
      message.includes('auth key not found');
    const hasFailureStatus =
      response?.status === false ||
      response?.status === 0 ||
      response?.status === 'false';

    return (
      hasAuthMessage ||
      ((response?.login === true || response?.logout === true) && hasFailureStatus)
    );
  }

  private handleAuthRedirect(message?: string) {
    if (this.isAuthRedirecting) {
      return;
    }

    this.isAuthRedirecting = true;
    this.hideLoading();
    this.stopLiveSync();
    this.activeNativePlayer = null;

    const redirectMessage =
      (message || '').toString().trim() ||
      'Invalid authentication. Please login again';

    this.alertController
      .create({
        header: 'Session Expired',
        message: redirectMessage,
        backdropDismiss: false,
        buttons: [
          {
            text: 'OK',
            handler: () => {
              this.clearDomainBranding();
              localStorage.clear();
              this.router.navigateByUrl('/auth/login', { replaceUrl: true });
              this.isAuthRedirecting = false;
            },
          },
        ],
      })
      .then((alert) => alert.present())
      .catch(() => {
        this.clearDomainBranding();
        localStorage.clear();
        this.router.navigateByUrl('/auth/login', { replaceUrl: true });
        this.isAuthRedirecting = false;
      });
  }

  private withAuthHandling<T>(request$: Observable<T>): Observable<T> {
    return request$.pipe(
      mergeMap((response: any) => {
        if (this.isInvalidAuthResponse(response)) {
          this.handleAuthRedirect(response?.msg || response?.message);
          return EMPTY;
        }

        return of(response as T);
      }),
      catchError((error: any) => {
        if (this.isInvalidAuthResponse(error?.error) || error?.status === 401) {
          this.handleAuthRedirect(
            error?.error?.msg ||
              error?.error?.message ||
              'Invalid authentication. Please login again'
          );
          return EMPTY;
        }

        return throwError(() => error);
      })
    );
  }


  // Periodically sync to the live edge
  startLiveSync() {
    if (this.activeNativePlayer !== 'video-player') {
      return;
    }

    this.stopLiveSync();
    this.liveSyncInterval = setInterval(async () => {
      try {
        const currentTime = await this.videoPlayer.getCurrentTime();
        const liveThreshold = 10; // Define a threshold for live edge proximity in seconds

        // Always seek to the live edge directly
        await this.videoPlayer.setCurrentTime(
          (await this.videoPlayer.getDuration()) + 3600
        );
        console.log('Seeking to the live edge of the stream');
      } catch (error) {
        console.error('Error during live sync:', error);
      }
    }, 10000); // Sync every 10 seconds, adjust as needed
  }

  // Stop the live sync interval
  stopLiveSync() {
    if (this.liveSyncInterval) {
      clearInterval(this.liveSyncInterval);
      this.liveSyncInterval = null;
    }
  }

  private getConfiguredLiveStreamPlayer(): LiveStreamPlayerMode {
    const override = localStorage.getItem('live_stream_player');

    if (override === 'vlc' || override === 'video-player') {
      return override;
    }

    return environment.player.liveStreamPlayer === 'vlc'
      ? 'vlc'
      : 'video-player';
  }

  private canUseVlcPlayer(): boolean {
    return (
      this.getConfiguredLiveStreamPlayer() === 'vlc' &&
      (this.platform.is('android') || this.platform.is('ios'))
    );
  }

  private isYoutubeStream(streamType: any): boolean {
    const normalizedType = (streamType || '').toString().toLowerCase();
    return normalizedType === 'youtube' || normalizedType === 'youtubelive';
  }

  private getPlaybackUrl(obj: any): string {
    return (
      obj?.url ||
      obj?.movie_url ||
      obj?.video_url ||
      obj?.channel_link ||
      ''
    )
      .toString()
      .trim();
  }

  private getStreamType(obj: any): string {
    if (!obj) {
      return '';
    }

    return (
      obj?.stream_type ||
      obj?.source_type ||
      obj?.source ||
      obj?.type ||
      ''
    )
      .toString()
      .trim();
  }

  private shouldUseVlcForObject(obj: any): boolean {
    if (!this.canUseVlcPlayer()) {
      return false;
    }

    const streamType = this.getStreamType(obj);

    if (this.isYoutubeStream(streamType)) {
      return false;
    }

    return String(obj?.content_type) === '3' || streamType.toLowerCase() === 'vlc';
  }

  private async openYoutubePlayer(url: string) {
    if (!url) {
      return;
    }

    await this.router.navigate(['/player'], {
      queryParams: { url },
    });
  }

  private async playWithConfiguredLivePlayer(
    url: string,
    options: { title?: string; isLive?: boolean } = {}
  ) {
    const cleanUrl = (url || '').trim();

    if (!cleanUrl) {
      return;
    }

    if (options.isLive === true && this.canUseVlcPlayer()) {
      await this.playWithVlc(cleanUrl, options);
      return;
    }

    this.playDirect(cleanUrl);
  }

  private async playWithVlc(
    url: string,
    options: { title?: string; isLive?: boolean } = {}
  ) {
    try {
      this.stopLiveSync();
      this.activeNativePlayer = 'vlc';
      this.hideLoading();

      await VLCPlayer.playStream({
        videoUrl: url,
        isLive: options.isLive ?? true,
        title: options.title || '',
      });
    } catch (error) {
      console.error('Error opening VLC player', error);
      this.activeNativePlayer = null;
      this.playDirect(url);
    }
  }

  async initializeVlcPlayer(obj: any, playlist: any = []) {
    console.log(obj);

    const url = this.getPlaybackUrl(obj);
    const streamType = this.getStreamType(obj);

    if (!url) {
      this.showPopup('Error', 'Stream URL not found.');
      return;
    }

    if (this.isYoutubeStream(streamType)) {
      await this.openYoutubePlayer(url);
      return;
    }

    if (this.shouldUseVlcForObject(obj)) {
      await this.playWithConfiguredLivePlayer(url, {
        isLive: true,
        title: obj?.name || obj?.title || '',
      });
      return;
    }

    this.play(
      {
        ...obj,
        url,
        stream_type: obj?.stream_type || streamType,
      },
      playlist
    );
  }

  private getDomainFromUrl(url: string): string {
    try {
      return new URL(url).hostname;
    } catch {
      return '';
    }
  }

  async playVideoWithUrl(
    videoInput: any,
    options: { title?: string; isLive?: boolean } = {}
  ) {
    const sourceObject =
      typeof videoInput === 'string' ? null : videoInput || null;
    const cleanUrl = sourceObject
      ? this.getPlaybackUrl(sourceObject)
      : (videoInput || '').toString().trim();
    const streamType = sourceObject ? this.getStreamType(sourceObject) : '';
    const resolvedOptions = {
      title:
        options.title ||
        sourceObject?.channel_name ||
        sourceObject?.name ||
        sourceObject?.title ||
        '',
      isLive:
        typeof options.isLive === 'boolean'
          ? options.isLive
          : sourceObject
            ? String(sourceObject?.content_type) === '3' ||
              streamType.toLowerCase() === 'vlc'
            : false,
    };

    if (!cleanUrl) {
      this.showPopup('Error', 'Stream URL not found.');
      return;
    }

    if (sourceObject && this.isYoutubeStream(streamType)) {
      await this.openYoutubePlayer(cleanUrl);
      return;
    }

    const videoDomain = this.getDomainFromUrl(cleanUrl);

    this.get('getCDNSettings').subscribe((cdnRes: any) => {

      // ❌ CDN disabled or status false
      if (!cdnRes?.status || !cdnRes?.emabled) {
        this.playWithConfiguredLivePlayer(cleanUrl, resolvedOptions);
        return;
      }

      // ✅ domain match check
      const domainMatched = cdnRes.domains?.some(
        (d: any) => d.domain_name === videoDomain
      );

      if (!domainMatched) {
        // ❌ domain not matched → no token
        this.playWithConfiguredLivePlayer(cleanUrl, resolvedOptions);
        return;
      }

      // ✅ domain matched → generate token
      this.generateTokenAndPlay(cleanUrl, resolvedOptions);
    });
  }

  private generateTokenAndPlay(
    url: string,
    options: { title?: string; isLive?: boolean } = {}
  ) {
    const payload = {
      url,
      token_expiry_seconds: 5
    };

    this.post('generateSecureToken', payload).subscribe((res: any) => {
      if (res?.url) {
        this.playWithConfiguredLivePlayer(res.url, options);
      } else {
        // fallback
        this.playWithConfiguredLivePlayer(url, options);
      }
    });
  }

  private playDirect(url: string) {
    const obj = {
      url,
      stream_type: 'M3u8',
      content_type: '3',
    };

    this.activeNativePlayer = this.platform.is('android')
      ? 'video-player'
      : null;
    this.hideLoading();
    this.play(obj, []);
  }



  // async playVideoWithUrl(videoUrl: string) {
  //   const obj = {
  //     url: videoUrl.trim(),
  //     stream_type: 'M3u8',
  //     content_type: '3',
  //   };
  //   this.hideLoading();
  //   this.play(obj, []);
  // }

  // async onPause() {
  //   try {
  //     await VLCPlayer.pause();
  //   } catch (error) {
  //     console.error('Error pausing video:', error);
  //   }
  // }

  async onPause() {
    if (this.activeNativePlayer !== 'video-player') {
      return;
    }

    try {
      const isPlaying = await this.videoPlayer.isPlaying({
        playerId: '_fullscreen',
      });

      if (isPlaying) {
        await this.videoPlayer.pause({ playerId: '_fullscreen' });
      }
    } catch (error) {
      console.log('Error pausing native video player', error);
    }
  }

  async onResume() {
    if (this.activeNativePlayer !== 'video-player') {
      return;
    }

    try {
      // Attempt to check if the video player is playing
      const isPlaying = await this.videoPlayer.isPlaying({
        playerId: '_fullscreen',
      });

      // If the player is not playing (i.e., it's paused), resume playback
      if (!isPlaying) {
        const result = await this.videoPlayer.play({ playerId: '_fullscreen' });
        console.log('Video resumed:', result); // Log the result for debugging purposes
      }
    } catch (error) {
      // If the player is not open or any error occurs
      console.log(
        'Error: The video player might not be initialized or open',
        error
      );

      // Optionally, handle the case where the player is not open by reinitializing it
      // await this.videoPlayer.initPlayer({ /* player initialization options here */ });

      // Then, you can choose to play the video after initialization
      // await this.videoPlayer.play({ playerId: '_fullscreen' });
    }
  }

  // async onResume() {
  //   try {
  //     const { isPlaying } = await VLCPlayer.isPlaying();
  //     if (!isPlaying) {
  //       await VLCPlayer.resume();  // Use the new resume method
  //     } else {
  //       console.log('Player is already playing.');
  //     }
  //   } catch (error) {
  //     console.error('Error resuming video:', error);
  //   }
  // }

  async play(obj: any, playlist: any) {
    console.log(obj);

    const streamType = this.getStreamType(obj);

    if (this.platform.is('ios')) {
      this.activeNativePlayer = null;
      const videoElement = document.getElementById(
        '__fullscreen'
      ) as HTMLVideoElement;
      const videoElementOver = document.getElementById(
        'video-overlay'
      ) as HTMLVideoElement;
      videoElementOver.style.display = 'block';

      if (videoElement instanceof HTMLVideoElement) {
        if (this.videoPlayerService.isPlayerInitialized(videoElement)) {
          // If already initialized, stop or reset the player
          this.videoPlayerService.resetPlayer(videoElement);
        }
        this.videoPlayerService.initializePlayer(
          videoElement,
          obj.url,
          streamType,
          playlist
        );
      } else {
        console.error('Video element not found or is not a video element');
        // Handle the error appropriately
      }
      // } else {
      // if(obj.stream_type == 'VLC'){
      //   try {
      //     await VLCPlayer.playStream({
      //       videoUrl: obj.url,
      //       isLive: true
      //     });
      //   }catch (error) {
      //     console.error('Error initializing or playing video', error);
      //     // Handle the error appropriately
      //   }
    } else {
      try {
        this.activeNativePlayer = 'video-player';
        await this.videoPlayer.initPlayer({
          mode: 'fullscreen',
          url: obj.url,
          playlist: playlist,
          playerId: '_fullscreen',
          componentTag: 'my-page',
          pipEnabled: false,
          displayMode: 'landscape',
          chromecast: false,
          options: ['--network-caching=1000', '--live-caching=3000'],
          time: 'latest',
        });

        this.videoPlayer.play();
      } catch (error) {
        console.error('Error initializing or playing video', error);
        // Handle the error appropriately
      }
    }

    // }
  }

  getUserDetail(key: any = '') {
    // let userDetail = JSON.parse(localStorage.getItem('userData'))
    let userDetail: any;
    const userDataString = localStorage.getItem('userData');
    if (userDataString !== null) {
      userDetail = JSON.parse(userDataString);
    }
    if (key != '') {
      return userDetail[key];
    } else {
      return userDetail;
    }
  }

  diff_years(dt2: any, dt1: any) {
    console.log(dt2);
    console.log(dt1);
    var diff = (Number(dt2) - Number(dt1)) / 1000;
    diff /= 60 * 60 * 24;
    return Math.abs(Math.round(diff / 365.25));
  }

  isLogin() {
    if (localStorage.getItem('isLogin')) {
      return true;
    } else {
      return false;
    }
  }

  formateDate(previous: any) {
    var msPerMinute = 60 * 1000;
    var msPerHour = msPerMinute * 60;
    var msPerDay = msPerHour * 24;
    var msPerMonth = msPerDay * 30;
    var msPerYear = msPerDay * 365;

    var elapsed = Date.now() - previous * 1000;

    if (elapsed < msPerMinute) {
      return Math.round(elapsed / 1000) + ' seconds ago';
    } else if (elapsed < msPerHour) {
      return Math.round(elapsed / msPerMinute) + ' minutes ago';
    } else if (elapsed < msPerDay) {
      return Math.round(elapsed / msPerHour) + ' hours ago';
    } else if (elapsed < msPerMonth) {
      return Math.round(elapsed / msPerDay) + ' days ago';
    } else if (elapsed < msPerYear) {
      return Math.round(elapsed / msPerMonth) + ' months ago';
    } else {
      return Math.round(elapsed / msPerYear) + ' years ago';
    }
  }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): boolean {
    // const isLoggedIn = localStorage.getItem('isLogin'); // ... your login logic here
    const firstTime = localStorage.getItem('firstTime'); // ... your login logic here
    let url: any = route.url;
    // console.log(url[0].path)
    if (!firstTime) {
      return true;
    } else {
      console.log('j');
      this.router.navigate(['/auth/login']);
      return false;
    }
  }
  getAppVersion() {
    this.get('getAppVersion').subscribe(
      (response) => {
        let res = response.result;
        if (Number(res.app_version_code) > Number(this.app_version_code)) {
          if (res.ForceUpdate) {
            this.forceUpdatePopup(res.UpdateMessage);
          } else {
            if (localStorage.getItem('UpdationDate')) {
              if (
                Number(localStorage.getItem('UpdationDate')) <
                Number(res.UpdationDate)
              ) {
                this.normalUpdate(res.UpdateMessage, res.UpdationDate);
              }
            } else {
              this.normalUpdate(res.UpdateMessage, res.UpdationDate);
            }
          }
        }
      },
      (err) => {
        console.log(err);
      }
    );
  }

  async normalUpdate(UpdateMessage: any, UpdationDate: any) {
    const alert = await this.alertController.create({
      header: 'New Update!',
      message: UpdateMessage,
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: (blah) => {
            localStorage.setItem('UpdationDate', UpdationDate);
            console.log('Confirm Cancel: blah');
            // App['exitApp']();
          },
        },
        {
          text: 'Update',
          handler: () => {
            if (this.platform.is('ios')) {
              window.open(
                'https://apps.apple.com/us/app/api-savwipl/id1593005716',
                '_system',
                'location=yes'
              );
            } else {
              window.open(
                'https://play.google.com/store/apps/details?id=com.castkom.api2021',
                '_system',
                'location=yes'
              );
            }

            // App['exitApp']();
            // App['exitApp']();
          },
        },
      ],
    });

    await alert.present();
  }
  async checkExpiryPlan() {
    this.get('checkExpiryPlan').subscribe(res=>{
      if(res.plan_will_expire){
        this.showExpiryPopup(res.message)
      }
      if(res.plan_expired){
        setTimeout(()=>{
          this.showExpiryPopup(res.message);
        },1200)
        setTimeout(()=>{
          window.location.reload();
        },700)
        localStorage.clear()
        this.router.navigateByUrl('auth/login')
      }
    },err=>{

    })
    
  }

  async showExpiryPopup(message: any){
    const alert = await this.alertController.create({
      header: 'Alert!',
      message: message,
      buttons: [
        {
          text: 'OK',
          role: 'cancel',
          cssClass: '',
          handler: (blah) => {
            
          },
        
        },
      ],
    });

    await alert.present();
  }
  async showPlanExpiredPopup(message: any){
    const alert = await this.alertController.create({
      header: 'Alert!',
      message: message,
      buttons: [
        {
          text: 'OK',
          role: 'cancel',
          cssClass: '',
          handler: (blah) => {
            setTimeout(()=>{
              window.location.reload();
            },700)
            localStorage.clear()
            this.router.navigateByUrl('auth/login')
          },
        
        },
      ],
    });

    await alert.present();
  }

  back() {
    this.location.back();
  }

  async forceUpdatePopup(UpdateMessage: any) {
    const alert = await this.alertController.create({
      header: 'New Update!',
      message: UpdateMessage,
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
          cssClass: 'secondary',
          handler: (blah) => {
            console.log('Confirm Cancel: blah');
            // App['exitApp']();
          },
        },
        {
          text: 'Update',
          handler: () => {
            if (this.platform.is('ios')) {
              window.open(
                'https://apps.apple.com/us/app/api-savwipl/id1593005716',
                '_system',
                'location=yes'
              );
            } else {
              window.open(
                'https://play.google.com/store/apps/details?id=com.castkom.api2021',
                '_system',
                'location=yes'
              );
            }
            // App['exitApp']();
          },
        },
      ],
    });

    await alert.present();
  }

  getSettings() {
    this.get('getSettings').subscribe((res: any) => {
      console.log(res);
      // alert(res.enableAll)
      this.settings = res;
      // this.settings.channels.push(149)
    });
  }

  updateViewHistory(data: any) {
    this.post('updateUserHistory',data).subscribe((res: any) => {
      console.log(res);
      if(res.logout && res.planExpired){
        this.showPlanExpiredPopup(res.message)
      }
      // alert(res.enableAll)
      // this.settings = res;
      // this.settings.channels.push(149)
    },err=>{
      console.log(err)
    });
  }

  logout(): Observable<any> {
    this.refreshApiUrl();
    return this.withAuthHandling(
      this.http.get<any>(this.apiUrl + 'user/signout').pipe(
        tap((_) => this.log('logout')),
        catchError(this.handleError('logout', []))
      )
    );
  }

  post(url: any, data: any, domainOverride?: any): Observable<any> {
    this.refreshApiUrl(domainOverride);
    return this.withAuthHandling(
      this.http.post<any>(this.apiUrl + '' + url, data)
    );
  }

  get(url: any, domainOverride?: any): Observable<any> {
    this.refreshApiUrl(domainOverride);
    return this.withAuthHandling(
      this.http.get<any>(this.apiUrl + '' + url)
    );
  }

  pauseVideo() {
    document.querySelectorAll('video').forEach((vid) => (vid.muted = true));
  }

  userPic(image: any) {
    image.src = 'assets/images/user-dummy-pic.png';
  }

  pagePic(image: any) {
    image.src = 'assets/images/paceholder.jpeg';
  }

  catImage(image: any) {
    image.src = 'assets/images/store.png';
  }

  async presentToast(msg: any) {
    const toast = await this.toastController.create({
      message: msg,
      duration: 4000,
      position: 'bottom',
      buttons: ['OK'],
    });
    toast.present();
  }

  getWatchListMovies() {
    if (localStorage.getItem('movieWatchlist')) {
      return JSON.parse(localStorage.getItem('movieWatchlist') || '');
    } else {
      return [];
    }
  }

  addToMovieWatchList(MovieId: any) {
    // alert(MovieId)
    var data: any = [];
    if (localStorage.getItem('movieWatchlist')) {
      // console.log(localStorage.getItem('movieWatchlist'))
      // alert('lll'+MovieId)
      var data = JSON.parse(localStorage.getItem('movieWatchlist') || '');
      data.push(MovieId);
      localStorage.setItem('movieWatchlist', JSON.stringify(data));
      // console.log(localStorage.getItem('movieWatchlist'))
    } else {
      // alert(MovieId)
      data.push(MovieId);
      localStorage.setItem('movieWatchlist', JSON.stringify(data));
    }
    console.log(data);
  }

  deleteMovieFromWatchlist(MovieId: any) {
    if (localStorage.getItem('movieWatchlist')) {
      var data = JSON.parse(localStorage.getItem('movieWatchlist') || '');
      let index = data.indexOf(MovieId);
      data.splice(index, 1);
      localStorage.setItem('movieWatchlist', JSON.stringify(data));
    }
  }

  checkMovieFromWatchlist(MovieId: any) {
    // console.log(localStorage.getItem('movieWatchlist'))
    if (localStorage.getItem('movieWatchlist')) {
      var data = JSON.parse(localStorage.getItem('movieWatchlist') || '');
      // console.log(data)
      let index = data.indexOf(MovieId);
      if (index > -1) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  getWatchListWebSeries() {
    if (localStorage.getItem('webSeriesWatchlist')) {
      return JSON.parse(localStorage.getItem('webSeriesWatchlist') || '');
    } else {
      return [];
    }
  }
  addToWebSeriesWatchList(WebSeriesId: any) {
    // alert(WebSeriesId)
    var data: any = [];
    if (localStorage.getItem('webSeriesWatchlist')) {
      // console.log(localStorage.getItem('webSeriesWatchlist'))
      // alert('lll'+WebSeriesId)
      var data = JSON.parse(localStorage.getItem('webSeriesWatchlist') || '');
      data.push(WebSeriesId);
      localStorage.setItem('webSeriesWatchlist', JSON.stringify(data));
      // console.log(localStorage.getItem('webSeriesWatchlist'))
    } else {
      // alert(WebSeriesId)
      data.push(WebSeriesId);
      localStorage.setItem('webSeriesWatchlist', JSON.stringify(data));
    }
    console.log(data);
  }

  deleteWebSeriesFromWatchlist(WebSeriesId: any) {
    if (localStorage.getItem('webSeriesWatchlist')) {
      var data = JSON.parse(localStorage.getItem('webSeriesWatchlist') || '');
      let index = data.indexOf(WebSeriesId);
      data.splice(index, 1);
      localStorage.setItem('webSeriesWatchlist', JSON.stringify(data));
    }
  }

  checkWebSeriesFromWatchlist(WebSeriesId: any) {
    // console.log(localStorage.getItem('webSeriesWatchlist'))
    if (localStorage.getItem('webSeriesWatchlist')) {
      var data = JSON.parse(localStorage.getItem('webSeriesWatchlist') || '');
      // console.log(data)
      let index = data.indexOf(WebSeriesId);
      if (index > -1) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  addToTvShowsWatchList(showId: any) {
    // alert(WebSeriesId)
    var data: any = [];
    if (localStorage.getItem('tvShowsWatchlist')) {
      // console.log(localStorage.getItem('tvShowsWatchlist'))
      // alert('lll'+WebSeriesId)
      var data = JSON.parse(localStorage.getItem('tvShowsWatchlist') || '');
      data.push(showId);
      localStorage.setItem('tvShowsWatchlist', JSON.stringify(data));
      // console.log(localStorage.getItem('tvShowsWatchlist'))
    } else {
      // alert(WebSeriesId)
      data.push(showId);
      localStorage.setItem('tvShowsWatchlist', JSON.stringify(data));
    }
    console.log(data);
  }

  deleteTvShowsFromWatchlist(showId: any) {
    if (localStorage.getItem('tvShowsWatchlist')) {
      var data = JSON.parse(localStorage.getItem('tvShowsWatchlist') || '');
      let index = data.indexOf(showId);
      data.splice(index, 1);
      localStorage.setItem('tvShowsWatchlist', JSON.stringify(data));
    }
  }

  checkTvShowsFromWatchlist(showId: any) {
    // console.log(localStorage.getItem('tvShowsWatchlist'))
    if (localStorage.getItem('tvShowsWatchlist')) {
      var data = JSON.parse(localStorage.getItem('tvShowsWatchlist') || '');
      // console.log(data)
      let index = data.indexOf(showId);
      if (index > -1) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  addToKidsShowsWatchList(showId: any) {
    // alert(WebSeriesId)
    var data: any = [];
    if (localStorage.getItem('kidsShowsWatchlist')) {
      // console.log(localStorage.getItem('kidsShowsWatchlist'))
      // alert('lll'+WebSeriesId)
      var data = JSON.parse(localStorage.getItem('kidsShowsWatchlist') || '');
      data.push(showId);
      localStorage.setItem('kidsShowsWatchlist', JSON.stringify(data));
      // console.log(localStorage.getItem('kidsShowsWatchlist'))
    } else {
      // alert(WebSeriesId)
      data.push(showId);
      localStorage.setItem('kidsShowsWatchlist', JSON.stringify(data));
    }
    console.log(data);
  }

  deleteKidsShowsFromWatchlist(showId: any) {
    if (localStorage.getItem('kidsShowsWatchlist')) {
      var data = JSON.parse(localStorage.getItem('kidsShowsWatchlist') || '');
      let index = data.indexOf(showId);
      data.splice(index, 1);
      localStorage.setItem('kidsShowsWatchlist', JSON.stringify(data));
    }
  }

  checkKidsShowsFromWatchlist(showId: any) {
    // console.log(localStorage.getItem('kidsShowsWatchlist'))
    if (localStorage.getItem('kidsShowsWatchlist')) {
      var data = JSON.parse(localStorage.getItem('kidsShowsWatchlist') || '');
      // console.log(data)
      let index = data.indexOf(showId);
      if (index > -1) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }


  addToSportsShowsWatchList(showId: any) {
    // alert(WebSeriesId)
    var data: any = [];
    if (localStorage.getItem('sportsShowsWatchlist')) {
      // console.log(localStorage.getItem('sportsShowsWatchlist'))
      // alert('lll'+WebSeriesId)
      var data = JSON.parse(localStorage.getItem('sportsShowsWatchlist') || '');
      data.push(showId);
      localStorage.setItem('sportsShowsWatchlist', JSON.stringify(data));
      // console.log(localStorage.getItem('sportsShowsWatchlist'))
    } else {
      // alert(WebSeriesId)
      data.push(showId);
      localStorage.setItem('sportsShowsWatchlist', JSON.stringify(data));
    }
    console.log(data);
  }

  deleteSportsShowsFromWatchlist(showId: any) {
    if (localStorage.getItem('sportsShowsWatchlist')) {
      var data = JSON.parse(localStorage.getItem('sportsShowsWatchlist') || '');
      let index = data.indexOf(showId);
      data.splice(index, 1);
      localStorage.setItem('sportsShowsWatchlist', JSON.stringify(data));
    }
  }

  checkSportsShowsFromWatchlist(showId: any) {
    // console.log(localStorage.getItem('sportsShowsWatchlist'))
    if (localStorage.getItem('sportsShowsWatchlist')) {
      var data = JSON.parse(localStorage.getItem('sportsShowsWatchlist') || '');
      // console.log(data)
      let index = data.indexOf(showId);
      if (index > -1) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }


  addToTvShowsPakWatchList(showId: any) {
    // alert(WebSeriesId)
    var data: any = [];
    if (localStorage.getItem('tvShowsPakWatchlist')) {
      // console.log(localStorage.getItem('tvShowsPakWatchlist'))
      // alert('lll'+WebSeriesId)
      var data = JSON.parse(localStorage.getItem('tvShowsPakWatchlist') || '');
      data.push(showId);
      localStorage.setItem('tvShowsPakWatchlist', JSON.stringify(data));
      // console.log(localStorage.getItem('tvShowsPakWatchlist'))
    } else {
      // alert(WebSeriesId)
      data.push(showId);
      localStorage.setItem('tvShowsPakWatchlist', JSON.stringify(data));
    }
    console.log(data);
  }

  deleteTvShowsPakFromWatchlist(showId: any) {
    if (localStorage.getItem('tvShowsPakWatchlist')) {
      var data = JSON.parse(localStorage.getItem('tvShowsPakWatchlist') || '');
      let index = data.indexOf(showId);
      data.splice(index, 1);
      localStorage.setItem('tvShowsPakWatchlist', JSON.stringify(data));
    }
  }

  checkTvShowsPakFromWatchlist(showId: any) {
    // console.log(localStorage.getItem('tvShowsPakWatchlist'))
    if (localStorage.getItem('tvShowsPakWatchlist')) {
      var data = JSON.parse(localStorage.getItem('tvShowsPakWatchlist') || '');
      // console.log(data)
      let index = data.indexOf(showId);
      if (index > -1) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  async confirmAlert(title: any, msg: any) {
    const alert = await this.alertController.create({
      header: 'Agent Registration!!',
      subHeader: title,
      message: msg,
      buttons: ['OK'],
    });

    await alert.present();
  }

  async showPopup(title: any, msg: any) {
    const alert = await this.alertController.create({
      header: title,
      message: msg,
      buttons: ['OK'],
    });

    await alert.present();
  }

  public showLoading() {
    this.loadingController
      .create({
        message: '',
        cssClass: 'custom',
        spinner: 'dots',
      })
      .then((res: any) => {
        res.present();

        res.onDidDismiss().then((dis: any) => {
          console.log('Loading dismissed! after 2 Seconds');
        });
      });
  }

  public hideLoading() {
    setTimeout(() => {
      this.loadingController.dismiss();
    }, 1000);
  }

  private handleError<T>(operation: any = 'operation', result?: T) {
    return (error: any): Observable<T> => {
      // TODO: send the error to remote logging infrastructure
      console.error(error); // log to console instead

      // TODO: better job of transforming error for user consumption
      this.log(`${operation} failed: ${error.message}`);

      // Let the app keep running by returning an empty result.
      return of(result as T);
    };
  }

  /** Log a HeroService message with the MessageService */
  private log(message: string) {
    console.log(message);
  }
}
