import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-settings',
  templateUrl: './settings.page.html',
  styleUrls: ['./settings.page.scss'],
})
export class SettingsPage implements OnInit {

  languagesList = ['English (USA)', 'Hindi'];

  downloadOptionsList = ['WiFi Only', 'Mobile Data and WiFi'];

  videoQualitiesList = ['HD (High Definition) 720p', 'Full HD 1080p'];

  selectedPreferedLanguage = this.languagesList[0];
  selectedDownloadOption = this.downloadOptionsList[0];
  selectedVideoQuality = this.videoQualitiesList[0];

  constructor(private navCtrl: NavController) { }

  ngOnInit() {
  }

  goBack() {
    this.navCtrl.back();
  }

}
