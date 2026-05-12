import { Component, OnDestroy, OnInit } from '@angular/core';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { PluginListenerHandle } from '@capacitor/core';
import { ScreenOrientation } from '@capacitor/screen-orientation';
import { StatusBar } from '@capacitor/status-bar';
import { NavigationBar } from '@hugotomazi/capacitor-navigation-bar';
import { NavController, Platform } from '@ionic/angular';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../../api.service';
import { YouTubePlayer } from 'src/app/native-plugins/youtube-player.plugin';

@Component({
  selector: 'app-player',
  templateUrl: 'player.page.html',
  styleUrls: ['player.page.scss'],
})
export class PlayerPage implements OnInit, OnDestroy {
  rawVideoInput = '';
  videoId = '';
  videoTitle = '';
  videoUrl: SafeResourceUrl | undefined;
  nativeLaunchFailed = false;
  isNativeLaunchInProgress = false;

  private closedListener?: PluginListenerHandle;
  private hasOpenedNativePlayer = false;

  constructor(
    private navController: NavController,
    private platform: Platform,
    public apiService: ApiService,
    private route: ActivatedRoute,
    private sanitizer: DomSanitizer,
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe((params) => {
      this.rawVideoInput = (params['url'] || '').toString().trim();
      this.videoTitle = (params['title'] || params['name'] || '').toString().trim();
      this.videoId = this.extractYoutubeVideoId(this.rawVideoInput);
      this.nativeLaunchFailed = false;
      this.hasOpenedNativePlayer = false;

      if (!this.isNativePlatform()) {
        this.prepareWebFallbackUrl();
      }
    });
  }

  ngOnDestroy() {
    this.cleanupNativeListeners();
  }

  async ionViewDidEnter() {
    await this.hideSystemChrome();
    await this.lockLandscape();

    if (this.isNativePlatform()) {
      await this.openNativePlayer();
    }
  }

  ionViewWillLeave() {
    this.showSystemChrome();
    this.unlockPortrait();
  }

  get showWebFallback(): boolean {
    return !this.isNativePlatform() || this.nativeLaunchFailed;
  }

  get canRenderIframe(): boolean {
    return !!this.videoUrl;
  }

  get statusText(): string {
    if (this.nativeLaunchFailed && !this.canRenderIframe) {
      return 'Unable to open YouTube video.';
    }

    return this.isNativeLaunchInProgress
      ? 'Opening YouTube player...'
      : 'Preparing video...';
  }

  goBack() {
    this.navController.back();
  }

  private isNativePlatform(): boolean {
    return this.platform.is('android') || this.platform.is('ios');
  }

  private async openNativePlayer() {
    if (this.hasOpenedNativePlayer || this.isNativeLaunchInProgress) {
      return;
    }

    this.hasOpenedNativePlayer = true;

    if (!this.videoId) {
      this.nativeLaunchFailed = true;
      this.prepareWebFallbackUrl();
      return;
    }

    this.isNativeLaunchInProgress = true;

    try {
      await this.cleanupNativeListeners();

      this.closedListener = await YouTubePlayer.addListener('playerClosed', () => {
        setTimeout(() => {
          this.navController.back();
        }, 0);
      });

      await YouTubePlayer.open({
        videoId: this.videoId,
        title: this.videoTitle,
        appName: this.apiService.getAppName(),
        introDurationMs: 1800,
        outroDurationMs: 1200,
      });
    } catch (error) {
      console.error('Failed to open native YouTube player', error);
      this.nativeLaunchFailed = true;
      this.prepareWebFallbackUrl();
    } finally {
      this.isNativeLaunchInProgress = false;
    }
  }

  private async cleanupNativeListeners() {
    if (this.closedListener) {
      this.closedListener.remove();
      this.closedListener = undefined;
    }

    try {
      await YouTubePlayer.removeAllListeners();
    } catch (error) {
      // Ignore listener cleanup issues for unsupported platforms.
    }
  }

  private prepareWebFallbackUrl() {
    const identifier = encodeURIComponent(this.videoId || this.rawVideoInput);

    if (!identifier) {
      this.videoUrl = undefined;
      return;
    }

    this.videoUrl = this.sanitizer.bypassSecurityTrustResourceUrl(
      `https://www.youtube.com/embed/${identifier}?autoplay=1&playsinline=1&rel=0&controls=0&disablekb=1&fs=0&modestbranding=1&iv_load_policy=3&cc_load_policy=0&showinfo=0`
    );
  }

  private extractYoutubeVideoId(value: string): string {
    const cleanValue = (value || '').trim();

    if (!cleanValue) {
      return '';
    }

    const directIdMatch = cleanValue.match(/^[a-zA-Z0-9_-]{6,}$/);
    if (directIdMatch) {
      return cleanValue;
    }

    try {
      const normalizedUrl = cleanValue.startsWith('http')
        ? cleanValue
        : `https://${cleanValue}`;
      const parsedUrl = new URL(normalizedUrl);
      const host = parsedUrl.hostname.replace(/^www\./, '');

      if (host === 'youtu.be') {
        return parsedUrl.pathname.replace(/\//g, '').trim();
      }

      if (host.includes('youtube.com')) {
        const searchId = parsedUrl.searchParams.get('v');
        if (searchId) {
          return searchId.trim();
        }

        const pathParts = parsedUrl.pathname.split('/').filter(Boolean);
        const knownIdIndex = pathParts.findIndex((part) =>
          ['embed', 'shorts', 'live', 'watch'].includes(part)
        );

        if (knownIdIndex >= 0 && pathParts[knownIdIndex + 1]) {
          return pathParts[knownIdIndex + 1].trim();
        }

        if (pathParts.length) {
          return pathParts[pathParts.length - 1].trim();
        }
      }
    } catch (error) {
      return cleanValue;
    }

    return cleanValue;
  }

  private async hideSystemChrome() {
    try {
      await StatusBar.setOverlaysWebView({ overlay: true });
      NavigationBar.hide();
    } catch (error) {
      console.error('Unable to hide system chrome', error);
    }
  }

  private async showSystemChrome() {
    try {
      await StatusBar.setOverlaysWebView({ overlay: false });
      NavigationBar.show();
    } catch (error) {
      console.error('Unable to show system chrome', error);
    }
  }

  private async lockLandscape() {
    try {
      await ScreenOrientation.lock({ orientation: 'landscape' });
    } catch (error) {
      console.error('Unable to lock landscape orientation', error);
    }
  }

  private async unlockPortrait() {
    try {
      await ScreenOrientation.lock({ orientation: 'portrait' });
    } catch (error) {
      console.error('Unable to restore portrait orientation', error);
    }
  }
}
