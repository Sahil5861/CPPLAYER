import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-subscription-payment',
  templateUrl: './subscription-payment.page.html',
  styleUrls: ['./subscription-payment.page.scss'],
})
export class SubscriptionPaymentPage implements OnInit {

  paymentMethodsList = [
    {
      id: '1',
      paymentMethodIcon: '../../../assets/images/paymentMethods/visa.png',
      paymentMethod: 'Visa Card',
    },
    {
      id: '2',
      paymentMethodIcon: '../../../assets/images/paymentMethods/masterCard.png',
      paymentMethod: 'Master Card',
    },
    {
      id: '3',
      paymentMethodIcon: '../../../assets/images/paymentMethods/paypal.png',
      paymentMethod: 'Paypal',
    },
    {
      id: '4',
      paymentMethodIcon: '../../../assets/images/paymentMethods/payU.png',
      paymentMethod: 'PayU Money',
    },
    {
      id: '5',
      paymentMethodIcon: '../../../assets/images/paymentMethods/stripe.png',
      paymentMethod: 'Stripe',
    }
  ];

  selectedPaymentMethodIndex:any;

  constructor(private router: Router, private navCtrl: NavController) { }

  ngOnInit() {
  }

  goBack() {
    this.navCtrl.back();
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

}
