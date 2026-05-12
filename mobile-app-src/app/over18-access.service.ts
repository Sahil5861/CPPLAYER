import { Injectable } from '@angular/core';
import { AlertController } from '@ionic/angular';
import { firstValueFrom, of } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { Router } from '@angular/router';

import { ApiService } from './api.service';

@Injectable({
  providedIn: 'root',
})
export class Over18AccessService {
  private isPinValidationRunning = false;
  private sessionPin = '';

  constructor(
    private apiService: ApiService,
    private alertCtrl: AlertController,
    private router: Router
  ) {}

  fetchVisibility() {
    return this.apiService.get('showabove18').pipe(
      map((res: any) => !!res?.status),
      catchError(() => of(false))
    );
  }

  getSessionPin(): string {
    return this.sessionPin;
  }

  clearSession() {
    this.sessionPin = '';
  }

  async promptForPinAndNavigate() {
    if (this.isPinValidationRunning) {
      return;
    }

    const alert = await this.alertCtrl.create({
      header: 'Enter PIN',
      inputs: [
        {
          name: 'pin',
          type: 'password',
          placeholder: 'Enter your 18+ PIN',
        },
      ],
      buttons: [
        {
          text: 'Cancel',
          role: 'cancel',
        },
        {
          text: 'Submit',
          handler: (data) => {
            const pin = `${data?.pin || ''}`.trim();

            if (!pin) {
              return false;
            }

            setTimeout(() => {
              this.validatePinAndNavigate(pin);
            }, 0);

            return true;
          },
        },
      ],
    });

    await alert.present();
  }

  private async validatePinAndNavigate(pin: string) {
    const normalizedPin = `${pin || ''}`.trim();

    if (!normalizedPin || this.isPinValidationRunning) {
      return;
    }

    this.isPinValidationRunning = true;

    try {
      const response = await firstValueFrom(
        this.apiService
          .post('getAllAbove18Movies?page=1&records=1', {
            pin: normalizedPin,
            genre: '',
          })
          .pipe(catchError(() => of(null))),
        { defaultValue: null }
      );

      if (Array.isArray(response)) {
        this.sessionPin = normalizedPin;
        this.isPinValidationRunning = false;
        this.router.navigateByUrl('/over18');
        return;
      }

      this.isPinValidationRunning = false;

      if (response !== null) {
        const invalidAlert = await this.alertCtrl.create({
          header: 'Invalid PIN',
          message: 'Please enter a valid 18+ PIN.',
          buttons: ['OK'],
        });
        await invalidAlert.present();
      }
    } catch (error) {
      this.isPinValidationRunning = false;
    }
  }
}
