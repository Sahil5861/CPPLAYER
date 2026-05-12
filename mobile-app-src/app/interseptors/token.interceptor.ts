import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpResponse,
  HttpErrorResponse,HttpHeaders
} from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import {
  Router
} from '@angular/router';
import { ToastController } from '@ionic/angular';
import { Injectable } from '@angular/core';


@Injectable()
export class TokenInterceptor implements HttpInterceptor {
	constructor(private router: Router,
  	public toastController: ToastController) {}

  	intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {

		  const token = 'vLQTuPZUxktl5mVW';

		  const authReq = request.clone({
		    headers: new HttpHeaders({
		      'Content-Type':  'application/json',
		      'auth-key': localStorage.getItem('auth_key') || '',
			  "domain": localStorage.getItem('domain') || 'dash.getplaybox.com',
		    })
		  });

		  console.log('Intercepted HTTP call', authReq);

		  return next.handle(authReq);

		  
	}

	async presentToast(msg: any) {
	  const toast = await this.toastController.create({
	    message: msg,
	    duration: 2000,
	    position: 'top'
	  });
	  toast.present();
	}
}
