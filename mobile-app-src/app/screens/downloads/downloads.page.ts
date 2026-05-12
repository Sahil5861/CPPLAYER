import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-downloads',
  templateUrl: './downloads.page.html',
  styleUrls: ['./downloads.page.scss'],
})
export class DownloadsPage implements OnInit {

  downloadsList = [
    {
      id: '1',
      image: '../../../assets/images/continueWatching/continueWatching1.png',
      watchingPercentage: 100,
      movieOrWebSeriesName: 'Squid Game',
      watchingTime: '02:30 h',
      episodes: 9,
      memoryTaken: '1.5GB',
      category: 'Comedy',
      languages: 'English - Hindi',
    },
    {
      id: '2',
      image: '../../../assets/images/popularWebSeries/series2.png',
      watchingPercentage: 100,
      movieOrWebSeriesName: 'Fate The Winx Saga',
      watchingTime: '03:20 h',
      episodes: 12,
      memoryTaken: '2.2GB',
      category: 'Comedy',
      languages: 'English - Hindi',
    },
    {
      id: '3',
      image: '../../../assets/images/continueWatching/continueWatching3.png',
      watchingPercentage: 100,
      movieOrWebSeriesName: 'Lucifer',
      watchingTime: '02:30 h',
      episodes: 6,
      memoryTaken: '1.5GB',
      category: 'Comedy',
      languages: 'English - Hindi',
    },
    {
      id: '4',
      image: '../../../assets/images/popularMovies/movie1.png',
      watchingPercentage: 100,
      movieOrWebSeriesName: 'Red Notice',
      watchingTime: '01:27 h',
      episodes: 4,
      memoryTaken: '1.0GB',
      category: 'Comedy',
      languages: 'English - Hindi',
    },
  ];

  constructor(private navCtrl: NavController) { }

  ngOnInit() {
  }

  goBack() {
    this.navCtrl.back()
  }

}
