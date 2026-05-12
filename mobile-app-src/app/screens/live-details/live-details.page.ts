import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-live-details',
  templateUrl: './live-details.page.html',
  styleUrls: ['./live-details.page.scss'],
})
export class LiveDetailsPage implements OnInit {

  constructor(public apiService: ApiService) { }

  ngOnInit() {
  }

  moreLikeMoviesList = [
    {
        id: '1',
        movieImage: '../../../assets/images/popular/popular16.png',
    },
    {
        id: '2',
        movieImage: '../../../assets/images/popular/popular10.png',
    },
    {
        id: '3',
        movieImage: '../../../assets/images/popular/popular11.png',
    },
    {
        id: '4',
        movieImage: '../../../assets/images/popular/popular13.png',
    },
    {
        id: '5',
        movieImage: '../../../assets/images/popular/popular14.png',
    },
    {
        id: '6',
        movieImage: '../../../assets/images/popular/popular15.png',
    },
    {
        id: '7',
        movieImage: '../../../assets/images/popularMovies/movie6.png',
    },
];
}
