import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavController, Platform } from '@ionic/angular';

@Component({
  selector: 'app-web-serice-detail',
  templateUrl: './web-serice-detail.page.html',
  styleUrls: ['./web-serice-detail.page.scss'],
})
export class WebSericeDetailPage implements OnInit {

  rating = 5;
  inBookmark = false;

  starCastList = [
    {
      id: '1',
      starCastImage: '../../../assets/images/starCast/starCast1.png',
      starCastName: 'Lee Jung jae',
    },
    {
      id: '2',
      starCastImage: '../../../assets/images/starCast/starCast2.png',
      starCastName: 'Park Hae soo',
    },
    {
      id: '3',
      starCastImage: '../../../assets/images/starCast/starCast3.png',
      starCastName: 'Wi Ha jun',
    },
    {
      id: '4',
      starCastImage: '../../../assets/images/starCast/starCast4.png',
      starCastName: 'Jung Ho yeon',
    }
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

  constructor(private router: Router, public platform: Platform, private navCtrl: NavController) { }

  ngOnInit() {
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }
}
