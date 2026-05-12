// video-player.component.ts
import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { VideoPlayerService } from './video-player.service';

@Component({
  selector: 'app-video-player',
  template: `
    <div *ngIf="videoPlayerService.isPlaying | async" class="video-overlay">
      <video #videoElement></video>
      <div *ngIf="videoPlayerService.isBuffering | async" class="buffering-indicator">
        Buffering...
      </div>
      <button (click)="closePlayer()">Close</button>
    </div>
  `,
  styles: [`
    .video-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: black;
      z-index: 1000;
    }
    video {
      width: 100%;
      height: 100%;
    }
    .buffering-indicator {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
    }
  `]
})
export class VideoPlayerComponent implements OnInit {
  @ViewChild('videoElement', { static: false }) videoElement!: ElementRef<HTMLVideoElement>;

  constructor(public videoPlayerService: VideoPlayerService) {}

  ngOnInit() {}

  playVideo(url: string, streamType: string) {
    this.videoPlayerService.initializePlayer(this.videoElement.nativeElement, url, streamType);
  }

  closePlayer() {
    this.videoPlayerService.cleanUpPlayer();
  }
}