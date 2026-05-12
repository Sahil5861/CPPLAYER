import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
import { Share } from '@capacitor/share';
@Component({
  selector: 'app-movie-detail',
  templateUrl: './movie-detail.page.html',
  styleUrls: ['./movie-detail.page.scss'],
})
export class MovieDetailPage implements OnInit {
  typeParam: any = '';
  itemId: any;
  details: any = {};
  videos: any = [];
  seasons: any = [];
  rating = 5;
  inBookmark = false;

  clipsList = [
    {
      id: '1',
      clipImage: '../../../assets/images/clips/clip1.png',
      clipViewTime: '1:50min',
      trailerOrTeaser: 'Trailer',
    },
    {
      id: '2',
      clipImage: '../../../assets/images/clips/clip2.png',
      clipViewTime: '30sec',
      trailerOrTeaser: 'Teaser',
    },
    {
      id: '3',
      clipImage: '../../../assets/images/clips/clip3.png',
      clipViewTime: '50sec',
      trailerOrTeaser: 'Teaser',
      trailerOrTeaserNumber: 2,
    },
    {
      id: '4',
      clipImage: '../../../assets/images/clips/clip4.png',
      clipViewTime: '50sec',
      trailerOrTeaser: 'Teaser',
      trailerOrTeaserNumber: 3,
    },
  ];

  reviewsList = [
    {
      id: '1',
      userImage: '../../../assets/images/users/user1.png',
      userName: 'Jane Cooper',
      reviewDate: '24 May, 2020',
      rating: 4.0,
      review: 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit ullamco Exercitation veniam consequat sunt nostrud amet.'
    },
    {
      id: '2',
      userImage: '../../../assets/images/users/user2.png',
      userName: 'Leslie Alexander',
      reviewDate: '17 Oct, 2020',
      rating: 4.0,
      review: 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit ullamco Exercitation veniam consequat sunt nostrud amet.'
    },
    {
      id: '3',
      userImage: '../../../assets/images/users/user3.png',
      userName: 'Brooklyn Simmons',
      reviewDate: '22 Oct, 2020',
      rating: 3.0,
      review: 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit ullamco Exercitation veniam consequat sunt nostrud amet.'
    },
    {
      id: '4',
      userImage: '../../../assets/images/users/user4.png',
      userName: 'Guy Hawkins',
      reviewDate: '8 Sep, 2020',
      rating: 4.0,
      review: 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit ullamco Exercitation veniam consequat sunt nostrud amet.'
    },
    {
      id: '5',
      userImage: '../../../assets/images/users/user5.png',
      userName: 'Jenny Wilson',
      reviewDate: '24 May, 2020',
      rating: 4.0,
      review: 'Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat duis enim velit mollit ullamco Exercitation veniam consequat sunt nostrud amet.'
    },
  ];

  constructor(private router: Router, public platform: Platform, private navCtrl: NavController,private route: ActivatedRoute, public apiService: ApiService) { }

  ngOnInit() {
    this.route.paramMap.subscribe((params: any) => {
      this.typeParam = params.get('type');
      this.itemId = params.get('id');
      if(this.typeParam == 'Web Series'){
        this.webSeries();
      }else {
        this.movies();
      }
    });
  }

  async share(){
    await Share.share(this.apiService.getShareOptions(this.details.name));
  }

  webSeries(){
    this.apiService.viewLoader = true;
      this.apiService.get('getWebSeriesDetails/'+this.itemId).subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.details = res;
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })

      this.apiService.get('getEpisodes/'+this.itemId+'/0').subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.videos = res;
          res.forEach((ele:any)=>{
            if(this.seasons.indexOf(ele.season_id) < 0){
              this.seasons.push(ele.season_id);
            }
          })
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })
  }

  movies(){
    this.apiService.viewLoader = true;
      this.apiService.get('getMovieDetails/'+this.itemId).subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.details = res;
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })

      this.apiService.get('getMoviePlayLinks/'+this.itemId+'/0').subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.videos = res;
          
          setTimeout(()=>{
            this.apiService.viewLoader = false;
          },500)
        }
      })
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(item: any) {
    this.apiService.initializeVlcPlayer(item);
    // console.log(item)
    // localStorage.setItem('currentObject',JSON.stringify(item))
    // this.router.navigateByUrl('video-player')
    // this.router.navigateByUrl(screen);
  }
}
