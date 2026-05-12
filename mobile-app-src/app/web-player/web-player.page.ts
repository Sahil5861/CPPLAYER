import { Component, OnInit, ElementRef, ViewChild } from '@angular/core';
import { CapacitorVideoPlayer } from 'capacitor-video-player';
import { Platform } from '@ionic/angular';

@Component({
  selector: 'app-video-player',
  template: `
    <ion-content>
      <div #videoContainer id="videoContainer"></div>
      <ion-button (click)="playPauseVideo()">{{ isPlaying ? 'Pause' : 'Play' }}</ion-button>
      <ion-range [(ngModel)]="currentTime" (ionChange)="seekTo()" min="0" [max]="duration"></ion-range>
      <ion-range [(ngModel)]="volume" (ionChange)="setVolume()" min="0" max="1" step="0.1"></ion-range>
    </ion-content>
  `,
  styles: [`
    #videoContainer {
      width: 100%;
      height: 100%;
      background-color: #000;
    }
  `]
})
export class WebPlayerPage implements OnInit {
  @ViewChild('videoContainer', { static: true }) videoContainer!: ElementRef;

  private videoPlayer: any;
  private playerId = 'videoContainer';
  public currentTime = 0;
  public duration = 0;
  public volume = 1;
  public isPlaying = false;

  constructor(private platform: Platform) {}

  async ngOnInit() {
    this.videoPlayer = CapacitorVideoPlayer;
    await this.initializePlayer();
  }

  async initializePlayer() {
    const options: any = {
      mode: this.platform.is('mobile') ? 'fullscreen' : 'embedded',
      url: 'https://live.mobifreetv.com/fx-push/DKB/playlist.m3u8',
      playerId: this.playerId,
      componentTag: 'app-video-player',
      pipEnabled: false,
      displayMode: "landscape",
      chromecast: false
    };

    if (this.platform.is('desktop')) {
      options['width'] = this.videoContainer.nativeElement.offsetWidth;
      options['height'] = this.videoContainer.nativeElement.offsetHeight;
    }

    const result = await this.videoPlayer.initPlayer(options);

    if (result.result) {
      this.addListeners();
      await this.updateDuration();
    } else {
      console.error('Failed to initialize video player:', result.message);
    }
  }

  addListeners() {
    this.videoPlayer.addListener('jeepCapVideoPlayerReady', () => {
      console.log('Video player ready');
    });

    this.videoPlayer.addListener('jeepCapVideoPlayerPlay', () => {
      console.log('Video is playing');
      this.isPlaying = true;
    });

    this.videoPlayer.addListener('jeepCapVideoPlayerPause', () => {
      console.log('Video is paused');
      this.isPlaying = false;
    });

    this.videoPlayer.addListener('jeepCapVideoPlayerEnded', () => {
      console.log('Video has ended');
      this.isPlaying = false;
    });
  }

  async playPauseVideo() {
    if (this.isPlaying) {
      await this.videoPlayer.pause({ playerId: this.playerId });
    } else {
      await this.videoPlayer.play({ playerId: this.playerId });
    }
  }

  async seekTo() {
    await this.videoPlayer.setCurrentTime({ playerId: this.playerId, seektime: this.currentTime });
  }

  async setVolume() {
    await this.videoPlayer.setVolume({ playerId: this.playerId, volume: this.volume });
  }

  async updateTime() {
    const result = await this.videoPlayer.getCurrentTime({ playerId: this.playerId });
    if (result.result) {
      this.currentTime = result.value;
    }
  }

  async updateDuration() {
    const result = await this.videoPlayer.getDuration({ playerId: this.playerId });
    if (result.result) {
      this.duration = result.value;
    }
  }

  ngOnDestroy() {
    this.videoPlayer.stopAllPlayers();
  }
}