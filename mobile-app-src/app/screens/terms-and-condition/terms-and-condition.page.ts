import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { Router, ActivatedRoute } from '@angular/router';
@Component({
  selector: 'app-terms-and-condition',
  templateUrl: './terms-and-condition.page.html',
  styleUrls: ['./terms-and-condition.page.scss'],
})
export class TermsAndConditionPage implements OnInit {

  termsOfUseList = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit purl Purus justo, lectus consectetur amet aliquet fermentum elit Odio amet habitant.',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fer mentum nunc aliquam nullam ultrices. Viverra mi rhoncusnec diam consequat feugiat. Nisi, et vulputate augue faucibus magna tristique.'
  ];

  companyPoliciesList = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit purl Purus justo, lectus consectetur amet aliquet fermentum elit Odio amet habitant.',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fer mentum nunc aliquam nullam ultrices. Viverra mi rhoncusnec diam consequat feugiat. Nisi, et vulputate augue faucibus magna tristique.'
  ];

  constructor(private navCtrl: NavController,private route: ActivatedRoute) { }
  typeParam: any = 1
  ngOnInit() {
    this.route.paramMap.subscribe((params: any) => {
      this.typeParam = params.get('any');
      
    });
  }

  
  goBack() {
    this.navCtrl.back()
  }

}
