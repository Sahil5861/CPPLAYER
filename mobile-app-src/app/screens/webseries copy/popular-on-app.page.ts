import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';
import { ApiService } from '../../api.service';
@Component({
  selector: 'app-popular-on-app',
  templateUrl: './popular-on-app.page.html',
  styleUrls: ['./popular-on-app.page.scss'],
})
export class PopularOnAppPage implements OnInit {
  anyParam: any = ''
  // popularOnAppList = [
  //   {
  //     id: '1',
  //     image: '../../../assets/images/popular/popular18.png',
  //   },
  //   {
  //     id: '2',
  //     image: '../../../assets/images/popular/popular1.png',
  //   },
  //   {
  //     id: '3',
  //     image: '../../../assets/images/popularWebSeries/series5.png',
  //   },
  //   {
  //     id: '4',
  //     image: '../../../assets/images/popularWebSeries/series4.png',
  //   },
  //   {
  //     id: '5',
  //     image: '../../../assets/images/popular/popular2.png',
  //   },
  //   {
  //     id: '6',
  //     image: '../../../assets/images/popular/popular3.png',
  //   },
  //   {
  //     id: '7',
  //     image: '../../../assets/images/popular/popular4.png',
  //   },
  //   {
  //     id: '8',
  //     image: '../../../assets/images/popular/popular5.png',
  //   },
  //   {
  //     id: '9',
  //     image: '../../../assets/images/popular/popular6.png',
  //   },
  //   {
  //     id: '10',
  //     image: '../../../assets/images/popular/popular7.png',
  //   },
  //   {
  //     id: '11',
  //     image: '../../../assets/images/popular/popular18.png',
  //   },
  //   {
  //     id: '12',
  //     image: '../../../assets/images/popular/popular19.png',
  //   },
  //   {
  //     id: '13',
  //     image: '../../../assets/images/popular/popular20.png',
  //   },
  //   {
  //     id: '14',
  //     image: '../../../assets/images/popular/popular21.png',
  //   },
  //   {
  //     id: '15',
  //     image: '../../../assets/images/popular/popular22.png',
  //   },
  //   {
  //     id: '16',
  //     image: '../../../assets/images/popular/popular8.png',
  //   },
  //   {
  //     id: '17',
  //     image: '../../../assets/images/popular/popular9.png',
  //   },
  //   {
  //     id: '18',
  //     image: '../../../assets/images/popular/popular10.png',
  //   },
  //   {
  //     id: '19',
  //     image: '../../../assets/images/popular/popular11.png',
  //   },
  //   {
  //     id: '20',
  //     image: '../../../assets/images/popular/popular12.png',
  //   },
  //   {
  //     id: '21',
  //     image: '../../../assets/images/popular/popular13.png',
  //   },
  //   {
  //     id: '22',
  //     image: '../../../assets/images/popular/popular14.png',
  //   },
  //   {
  //     id: '23',
  //     image: '../../../assets/images/popular/popular15.png',
  //   },
  //   {
  //     id: '24',
  //     image: '../../../assets/images/popular/popular17.png',
  //   },
  //   {
  //     id: '25',
  //     image: '../../../assets/images/popular/popular16.png',
  //   },
  // ];

    pageData: any = [];

  constructor(private router: Router, public platform: Platform, private navCtrl: NavController,private route: ActivatedRoute,public apiService: ApiService) { }

  ngOnInit() {
    this.route.paramMap.subscribe((params: any) => {
      this.anyParam = params.get('any');
      var url = 'getAllMovies';
      if(this.anyParam == 'Live TV'){
        url = 'getAllLiveTV';
      }else if(this.anyParam == 'Web Series'){
        url = 'getAllWebSeries';
      }else if(this.anyParam == 'Network Content'){
        let id = localStorage.getItem('networkId');
        url = 'getAllContentsOfNetwork/'+id;
      }
      this.apiService.get(url).subscribe((res:any)=>{
        console.log(res);
        if(typeof res == 'object'){
          this.pageData = res;
        }
      })
    });
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(item: any) {
    // this.router.navigateByUrl('movie-detail/'+item.id+'/Web Series');
  }

}
