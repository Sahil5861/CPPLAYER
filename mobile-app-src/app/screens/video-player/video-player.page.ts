import { Component, AfterViewInit, ElementRef, ViewChild, ChangeDetectorRef, OnDestroy } from '@angular/core';
import { StatusBar, Style } from '@capacitor/status-bar';
import { ScreenOrientation } from '@capacitor/screen-orientation';
import { Platform, NavController } from '@ionic/angular';
import { ApiService } from '../../api.service';
import { NavigationBar } from '@hugotomazi/capacitor-navigation-bar';
import { ActivatedRoute, Router } from '@angular/router';
declare var dashjs: any;
declare var Hls: any;

@Component({
  selector: 'app-video-player',
  templateUrl: './video-player.page.html',
  styleUrls: ['./video-player.page.scss'],
})
export class VideoPlayerPage implements AfterViewInit, OnDestroy {
  isBuffering = true;
  currentObject: any;
  player: any;
  hls: any;
  load = false;
  private intervalId: any;

  @ViewChild('video', { read: ElementRef, static: false }) videoChild!: ElementRef<HTMLVideoElement>;

  constructor(
    public apiService: ApiService,
    private platform: Platform,
    private cdr: ChangeDetectorRef,
    private navCntrl: NavController,
    private router: Router
  ) {
    this.platform.pause.subscribe(() => this.onPause());
    this.platform.resume.subscribe(() => this.onResume());
  }

  ngOnInit() {
    this.currentObject = {};
    this.intervalId = setInterval(() => {
      StatusBar.hide();
      NavigationBar.hide();
    }, 5000);

    const storedObject = localStorage.getItem('currentObject');
    if (storedObject) {
      this.currentObject = JSON.parse(storedObject);
    }
  }

  ngAfterViewInit() {
    setTimeout(() => {
      this.apiService.viewLoader = false;
      this.cdr.detectChanges();
      if (this.videoChild?.nativeElement) {
        this.initializePlayer();
      }
    });
  }

  ionViewDidEnter() {
    this.lockScreenOrientation();
    this.configureStatusBar();
    this.cdr.detectChanges();
  }

  ionViewWillLeave() {
    this.cleanUpPlayer();
    this.unlockScreenOrientation();
    this.resetStatusBar();
  }

  ngOnDestroy() {
    // this.cleanUpPlayer();
    clearInterval(this.intervalId);
  }

  private async lockScreenOrientation() {
    await ScreenOrientation.lock({ orientation: 'landscape' });
  }

  private async unlockScreenOrientation() {
    await ScreenOrientation.lock({ orientation: 'portrait' });
  }

  private configureStatusBar() {
    StatusBar.setStyle({ style: Style.Light });
    StatusBar.setOverlaysWebView({ overlay: true });
    StatusBar.setBackgroundColor({ color: 'transparent' });
    StatusBar.hide();
    NavigationBar.setTransparency({ isTransparent: true });
    NavigationBar.hide();
  }

  private resetStatusBar() {
    StatusBar.setStyle({ style: Style.Dark });
    StatusBar.setOverlaysWebView({ overlay: false });
    StatusBar.setBackgroundColor({ color: '#000' });
    StatusBar.show();
    NavigationBar.setTransparency({ isTransparent: false });
    NavigationBar.show();
  }

  private initializePlayer() {
    const url: string = this.currentObject.url;
    const streamType: string = this.currentObject.stream_type || this.currentObject.type;

    if (streamType === 'M3u8' || streamType === 'VLC') {
      this.initializeHlsPlayer(url);
    } else {
      this.initializeDashPlayer(url);
    }

    this.videoChild.nativeElement.addEventListener('pause', this.onPause.bind(this));
    this.videoChild.nativeElement.addEventListener('play', this.onPlay.bind(this));
  }

  private initializeHlsPlayer(url: string) {
    if (this.hls) {
      this.hls.destroy();
    }

    this.hls = new Hls();
    this.hls.loadSource(url);
    this.hls.attachMedia(this.videoChild.nativeElement);

    if (Hls.isSupported()) {
      this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
        this.tryAutoPlay();
      });
    } else if (this.videoChild.nativeElement.canPlayType('application/vnd.apple.mpegurl')) {
      this.videoChild.nativeElement.src = url;
    }
  }

  private initializeDashPlayer(url: string) {
    this.player = dashjs.MediaPlayer().create();
    this.player.initialize(this.videoChild.nativeElement, url, true);
  }

  private tryAutoPlay() {
    const promise = this.videoChild.nativeElement.play();
    if (promise !== undefined) {
      promise.then(() => {
        // Autoplay started
      }).catch(() => {
        this.videoChild.nativeElement.muted = true;
        this.videoChild.nativeElement.play();
        setTimeout(() => {
          this.apiService.viewLoader = false;
        }, 800);
      });
    }
  }

  private cleanUpPlayer() {
    this.videoChild.nativeElement.pause();
    this.videoChild.nativeElement.src = '';
    this.videoChild.nativeElement.load();

    if (this.hls) {
      this.hls.destroy();
    }

    if (this.player) {
      this.player.reset();
    }

    localStorage.removeItem('currentObject');
    this.currentObject = {};
    this.cdr.detectChanges();
    setTimeout(()=>{
      // alert(this.router.url)
      if(this.router.url == '/video-player'){
        this.navCntrl.pop()
      }
    },800)
    
  }

  private onPause() {
    this.isBuffering = false;
  }

  private onResume() {
    this.videoChild.nativeElement.play();
  }

  private onPlay() {
    this.isBuffering = true;
    setTimeout(() => {
      this.load = true;
    }, 1000);
  }
}
