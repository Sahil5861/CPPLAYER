import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-subscription-done',
  templateUrl: './subscription-done.page.html',
  styleUrls: ['./subscription-done.page.scss'],
})
export class SubscriptionDonePage implements OnInit {

  constructor(private router: Router) { }

  ngOnInit() {
  }

  goTo(screen: any) {
    this.router.navigateByUrl(screen);
  }

}
