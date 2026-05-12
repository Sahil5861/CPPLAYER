import { Component, Input, OnInit } from '@angular/core';
import { ModalController } from '@ionic/angular';
import { ApiService } from '../../api.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-above18-modal',
  template: `
    <ion-header>
      <ion-toolbar>
        <ion-title>18+ Movies</ion-title>
        <ion-buttons slot="end">
          <ion-button (click)="close()">Close</ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content [fullscreen]="true">
    <div style="height: 100%; background: #000;">
        <ion-grid [fixed]="true" style="--ion-grid-columns: 3;">
        <ion-row>
            <ion-col
            size="1"
            *ngFor="let item of items; let index = index"
            style="padding: 5px;"
            >
            <div class="icon-image-container-list">
                <ion-img
                (click)="goTo(item)"
                [src]="item.poster"
                class="imageOverflow icon-image"
                (ionImgDidLoad)="imageLoaded[index] = true"
                ></ion-img>
                <ion-spinner
                *ngIf="!imageLoaded[index]"
                name="crescent"
                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"
                ></ion-spinner>
            </div>
            </ion-col>
        </ion-row>
        </ion-grid>
    </div>    

      <ion-infinite-scroll (ionInfinite)="loadMoreItems($event)">
        <ion-infinite-scroll-content
          loadingSpinner="bubbles"
          loadingText="Loading more data..."
        ></ion-infinite-scroll-content>
      </ion-infinite-scroll>
    </ion-content>
  `,
})
export class Above18ModalComponent implements OnInit {
  @Input() pin!: string; // parent se PIN pass karenge
  items: any[] = [];
  imageLoaded: boolean[] = [];
  currentPage: number = 1;
  pageSize: number = 10; // ek baar me 10 records load

  constructor(
    private modalCtrl: ModalController,
    private apiService: ApiService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadMoreItems(); // first page load
  }

  close() {
    localStorage.removeItem('over18Open')
    this.modalCtrl.dismiss();
  }

  loadMoreItems(event?: any) {
    //   .get(`getAllAbove18Movies/${this.pin}?page=${this.currentPage}&records=${this.pageSize}`)
    this.apiService
    .post(`getAllAbove18Movies?page=${this.currentPage}&records=${this.pageSize}`,{
          "pin" : this.pin,
          "genre" : ""
      })
      .subscribe((res: any) => {
        if (Array.isArray(res) && res.length > 0) {
          this.items = this.items.concat(res);
          this.currentPage++;
        }

        if (event) {
          event.target.complete();
          if (!res || res.length < this.pageSize) {
            event.target.disabled = true; // no more data
          }
        }

        setTimeout(() => {
          this.apiService.viewLoader = false;
        }, 800);
      });
  }

  goTo(item: any) {
    console.log('Clicked Movie:', item);
    setTimeout(()=>{
        this.modalCtrl.dismiss()
        localStorage.setItem('over18Open',this.pin);
    },500)
    if (['youtube', 'youtubelive'].includes((item.source_type || '').toString().toLowerCase())) {
      
      this.router.navigate(['/player'], {
        queryParams: { url: item.movie_url },
      });
    } else {
      this.apiService.playVideoWithUrl(item.movie_url);
    }
  }
}
