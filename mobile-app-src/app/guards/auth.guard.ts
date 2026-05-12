// src/app/guards/auth.guard.ts

import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(
   
    private router: Router
  ) {}

  canActivate(): boolean {
    // AuthService se check karein ki user logged in hai ya nahi
    if (!localStorage.getItem('isLogin')) {
      // Agar logged in hai, to route access karne dein
      return true;
    } else {
      // Agar logged in nahi hai, to login page par redirect kar dein
      console.log('Access denied. Redirecting to login page.');
      this.router.navigate(['/bottom-tab-bar']);
      // Aur route access rok dein
      return false;
    }
  }
}
