import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges } from '@angular/core';
import { ApiService } from 'src/app/api.service';

type MediaCardVariant = 'banner' | 'poster' | 'square' | 'thumb';
type MediaCardFit = 'cover' | 'contain';
type MediaFallbackType = 'content' | 'app';

@Component({
  selector: 'app-media-card',
  templateUrl: './media-card.component.html',
  styleUrls: ['./media-card.component.scss'],
})
export class MediaCardComponent implements OnChanges {
  @Input() src: any = '';
  @Input() alt = '';
  @Input() variant: MediaCardVariant = 'poster';
  @Input() fit: MediaCardFit = 'cover';
  @Input() padding = '0px';
  @Input() background = 'var(--media-card-surface)';
  @Input() fallbackType: MediaFallbackType = 'content';
  @Input() referrerPolicy: HTMLImageElement['referrerPolicy'] = 'no-referrer';
  @Input() loading: HTMLImageElement['loading'] = 'lazy';
  @Input() spinner = true;

  @Output() cardClick = new EventEmitter<void>();
  @Output() imageLoad = new EventEmitter<Event>();
  @Output() imageError = new EventEmitter<Event>();

  resolvedSrc = '';
  isLoaded = false;

  constructor(public apiService: ApiService) {}

  ngOnChanges(changes: SimpleChanges) {
    if ('src' in changes || 'fallbackType' in changes) {
      this.resolvedSrc = this.apiService.getImageUrl(this.src, this.fallbackType);
      this.isLoaded = false;
    }
  }

  handleClick() {
    this.cardClick.emit();
  }

  handleLoad(event: Event) {
    this.isLoaded = true;
    this.apiService.clearImageFallbackState(event.target);
    this.imageLoad.emit(event);
  }

  handleError(event: Event) {
    this.isLoaded = false;
    this.imageError.emit(event);
    this.apiService.handleImageErrorEvent(event, this.fallbackType);
  }
}
