import { Injectable } from '@angular/core';
import { StatusBar, Style } from '@capacitor/status-bar';
import { ScreenOrientation } from '@capacitor/screen-orientation';
import { Platform } from '@ionic/angular';
import { NavigationBar } from '@hugotomazi/capacitor-navigation-bar';
import { BehaviorSubject } from 'rxjs';
// import 'shaka-player/dist/shaka-player.compiled';
declare var dashjs: any;
declare var Hls: any;
declare var shaka: any;


@Injectable({
  providedIn: 'root'
})
export class VideoPlayerService {
  private videoElement: HTMLVideoElement | null = null;
  private videoElementOver: HTMLElement | null = null;
  private closeButton: HTMLElement | null = null
  private hls: any;
  private player: any;
  private closeButtonTimeout: any;
  private shakaPlayer: any;
  isBuffering = new BehaviorSubject<boolean>(true);
  isPlaying = new BehaviorSubject<boolean>(false);
  private playerInstances = new Map<HTMLVideoElement, any>();
  constructor(private platform: Platform) {
    this.platform.pause.subscribe(() => this.onPause());
    this.platform.resume.subscribe(() => this.onResume());
  }

  videoList: any= []; // Your original array
  playlistContainer: any = document.getElementById("playlist-container");
  playlist: any = document.getElementById("playlist");
  closeside: any = document.getElementById("close-side");
  menubutton: any = document.getElementById("menu-button");

  initializePlayer(videoElement: HTMLVideoElement, url: string, streamType: string, playlist: any) {

    // let _videoContainer = document.createElement('div');
    // _videoContainer.style.position = 'absolute';
    // _videoContainer.style.left = '0';
    // _videoContainer.style.width = '100vw';
    // _videoContainer.style.height = '100vh';
    // _videoContainer.style.zIndex = (2).toString();

    // this.videoElement = document.createElement('video');
    // this.videoElement.controls = true;
    // this.videoElement.style.zIndex = (3).toString();
    // this.videoElement.style.width = '100vw';
    // this.videoElement.style.height = '100vh';
    
    // _videoContainer.appendChild(this.videoElement);
    this.videoList = playlist;
    console.log(playlist)
    
    this.videoElement = videoElement;
    this.configureStatusBar();
    this.renderPlaylist()
// alert(streamType)
    if (streamType === 'M3u8' || streamType === 'VLC' || streamType === 'YoutubeLive' || streamType == "Youtube") {
      this.initializeHlsPlayer(url);
    } else {
      this.initializeShakaPlayer(url);
      // this.initializeDashPlayer(url);
    }

    this.videoElement.addEventListener('pause', this.onPause.bind(this));
    this.videoElement.addEventListener('play', this.onPlay.bind(this));
    // this.videoElement.addEventListener('click', this.toggleCloseButton);


    this.videoElementOver = document.getElementById('video-overlay') as HTMLElement;
    this.closeButton = document.getElementById('close-button') as HTMLElement;
    // this.toggleCloseButton()
    this.closeButton.addEventListener('click', () => {
      this.videoElementOver!.style.display = 'none';
      this.playlistContainer!.style.display = 'none';
      this.menubutton!.style.display = 'block';
      this.cleanUpPlayer()  // Reset video playback
    }, false);
    
    this.closeside.addEventListener('click', () => {
      this.playlistContainer!.style.display = 'none';
      this.menubutton!.style.display = 'block';
      
    }, false);
    
    // this.videoElement.addEventListener('webkitendfullscreen', () => {
    this.menubutton.addEventListener('click', () => {
      this.playlistContainer!.style.display = 'block';
      this.menubutton!.style.display = 'none';
     
    }, false);
    
    // this.videoElement.addEventListener('webkitendfullscreen', () => {
      
    //     const videoElementOver = document.getElementById('video-overlay') as HTMLVideoElement;
    //     // alert('fullscreenchange')
    //     videoElementOver.style.display = "none";
    //     this.cleanUpPlayer()
      
      
    // }, false);
    
  }

  renderPlaylist() {
    this.playlist.innerHTML = '';
    this.videoList.forEach((item: any, index: any) => {
      const li = document.createElement('li');
      li.style.display = 'flex';
      li.style.alignItems = 'center';
      li.style.padding = '8px';
      li.style.cursor = 'pointer';
      li.style.borderBottom = '1px solid #444';
  
      // Thumbnail Image
      const img = document.createElement('img');
      img.src = item.banner || item.episoade_image; // fallback if thumbnail is missing
      img.alt = item.name || item.Episoade_Name;
      img.style.width = '60px';
      img.style.height = '40px';
      img.style.objectFit = 'cover';
      img.style.marginRight = '10px';
      img.style.borderRadius = '4px';
  
      // Title Text
      const title = document.createElement('span');
      title.textContent = item.name || item.Episoade_Name;
      title.style.color = 'white';
      title.style.fontSize = '16px';
  
      li.appendChild(img);
      li.appendChild(title);
  
      li.onclick = () => {
        // if()
        this.initializeHlsPlayer(item.url);
        this.playlistContainer!.style.display = 'none';
        this.menubutton!.style.display = 'block';
      };
  
      this.playlist.appendChild(li);
    });
    // this.playlistContainer.style.display = 'block';
  }

  
  // Function to show and hide the close button on tap
  toggleCloseButton() {
    this.closeButton!.style.display = 'block';
    clearTimeout(this.closeButtonTimeout);
    this.closeButtonTimeout = setTimeout(() => {
      this.closeButton!.style.display = 'none';
    }, 5000);
  }

  isPlayerInitialized(videoElement: HTMLVideoElement): boolean {
    return this.playerInstances.has(videoElement);
  }

  // initializePlayer(videoElement: HTMLVideoElement, url: string, type: string) {
  //   // Initialize player and store instance
  //   const playerInstance = /* initialize player with url and type */;
  //   this.playerInstances.set(videoElement, playerInstance);
  // }

  resetPlayer(videoElement: HTMLVideoElement) {
    const playerInstance = this.playerInstances.get(videoElement);
    if (playerInstance) {
      // Reset or stop the player instance
      playerInstance.stop();
      this.playerInstances.delete(videoElement);
    }
  }

  private initializeHlsPlayer(url: string) {
    if (this.hls) {
      this.hls.destroy();
    }

    this.hls = new Hls({
      enableWorker: true,  // Utilize worker threads for better performance
      lowLatencyMode: true,  // Enable low-latency mode
      maxBufferSize: 0,       // Let HLS.js manage buffer size dynamically
      maxBufferLength: 60,    // Buffer up to 60 seconds of video
      // maxBufferLength: 30, // Try increasing buffer size
      // maxMaxBufferLength: 600, // Maximum buffer size
    });
    // this.hls.loadSource('https://45.230.49.2:999/'+url);
    this.hls.loadSource(url);
    this.hls.attachMedia(this.videoElement!);

    if (Hls.isSupported()) {
      this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
        // this.tryAutoPlay();
        this.videoElement?.play()
      });
    } else if (this.videoElement!.canPlayType('application/vnd.apple.mpegurl')) {
      this.videoElement!.src = url;
    }
  }

  private initializeShakaPlayer(url: string) {
    if (this.shakaPlayer) {
      this.shakaPlayer.destroy();
    }

    this.shakaPlayer = new shaka.Player(this.videoElement!);

    // Listen for error events.
    this.shakaPlayer.addEventListener('error', (event: any) => {
      this.onShakaError(event);
    });

    this.shakaPlayer.load(url).then(() => {
      this.tryAutoPlay();
    }).catch((error: any) => {
      console.error('Error loading Shaka Player:', error);
    });
  }

  private onShakaError(event: any) {
    console.error('Shaka Player Error:', event);
    // Handle the error based on the severity and code.
    switch (event.detail.code) {
      case shaka.util.Error.Code.HTTP_ERROR:
        // Handle HTTP errors.
        console.error('HTTP Error:', event.detail);
        break;
      case shaka.util.Error.Code.DASH_NO_SEGMENT_INFO:
        // Handle DASH segment info errors.
        console.error('No Segment Info:', event.detail);
        break;
      default:
        // Handle other errors.
        console.error('Unhandled Shaka Error:', event.detail);
        break;
    }
  }

  private initializeDashPlayer(url: string) {
    // alert('jj')
    this.player = dashjs.MediaPlayer().create();
    this.player.initialize(this.videoElement, url, true);
  }

  private tryAutoPlay() {
    const promise = this.videoElement!.play();
    if (promise !== undefined) {
      promise.then(() => {
        this.isPlaying.next(true);
      }).catch(() => {
        this.videoElement!.muted = true;
        this.videoElement!.play();
        this.isPlaying.next(true);
      });
    }
  }

  cleanUpPlayer() {
    if (this.videoElement) {
      this.videoElement.pause();
      this.videoElement.src = '';
      this.videoElement.load();
    }

    if (this.hls) {
      this.hls.destroy();
    }

    if (this.player) {
      this.player.reset();
    }

    this.resetStatusBar();
    this.isPlaying.next(false);
  }

  private onPause() {
    if (this.videoElement) {
      this.videoElement.pause();
    }
    this.isBuffering.next(false);
    
    this.isPlaying.next(false);
  }

  private onResume() {
    if (this.videoElement) {
      this.videoElement.play();
      this.isPlaying.next(true);
    }
  }

  private onPlay() {
    this.isBuffering.next(true);
    this.isPlaying.next(true);
    setTimeout(() => {
      this.isBuffering.next(false);
    }, 1000);
  }

  private async configureStatusBar() {
    await ScreenOrientation.lock({ orientation: 'landscape' });
    StatusBar.setStyle({ style: Style.Light });
    StatusBar.setOverlaysWebView({ overlay: true });
    StatusBar.setBackgroundColor({ color: 'transparent' });
    StatusBar.hide();
    NavigationBar.setTransparency({ isTransparent: true });
    NavigationBar.hide();
  }

  private async resetStatusBar() {
    await ScreenOrientation.lock({ orientation: 'portrait' });
    StatusBar.setStyle({ style: Style.Dark });
    StatusBar.setOverlaysWebView({ overlay: false });
    StatusBar.setBackgroundColor({ color: '#000' });
    StatusBar.show();
    NavigationBar.setTransparency({ isTransparent: false });
    NavigationBar.show();
  }
}