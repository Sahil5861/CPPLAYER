import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NavController, ToastController } from '@ionic/angular';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrls: ['./notifications.page.scss'],
})
export class NotificationsPage implements OnInit {

  notificationList: any = [
    {
      id: '1',
      title: 'Upgrade premium now',
      description: 'Lorem ipsum dolor sit amet, consectetur Ipsun adipiscing elit. Ipsum, placerat nunc.',
      time: 'Today',
    },
    {
      id: '2',
      title: 'You haven’t watched The Crown.',
      description: 'Lorem ipsum dolor sit amet, consectetur Ipsun adipiscing elit. Ipsum, placerat nunc.',
      time: 'Today',
    },
    {
      id: '3',
      title: 'Watch now new trending movies',
      description: 'Lorem ipsum dolor sit amet, consectetur Ipsun adipiscing elit. Ipsum, placerat nunc.',
      time: 'Today',
    }
  ];
  isToastOpen = false;

  constructor(private router: Router, private navCtrl: NavController, private changeDetector: ChangeDetectorRef, private tostCtrl: ToastController) { }

  ngOnInit() {
  }

  removeNotification(id: any) {
    const copyList = this.notificationList;
    const newList = copyList.filter((item: any) => item.id !== id);
    this.notificationList = newList;
    this.isToastOpen = true;
    this.tostCtrl.getTop();
    this.changeDetector.detectChanges();
  }

  goBack() {
    this.navCtrl.back()
  }

}
