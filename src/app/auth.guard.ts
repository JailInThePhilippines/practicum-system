import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { AuthService } from './auth.service';
import { Observable } from 'rxjs';
import { map, switchMap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  private currentUrl: string = '/landing';

  constructor(private authService: AuthService, private router: Router) {
    this.setDefaultUrl();
  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {
    const expectedRole = route.data['expectedRole'];

    return this.authService.checkAuthenticationStatus().pipe(
      switchMap(() => this.authService.isAuthenticated()),
      map(isAuthenticated => {
        if (isAuthenticated) {
          if (this.authService.getUserRole() === expectedRole) {
            if (this.isValidRoute(state.url)) {
              this.currentUrl = state.url;
              return true;
            } else {
              this.router.navigate([this.currentUrl]);
              return false;
            }
          } else {
            this.redirectToLogin(state.url);
            return false;
          }
        } else {
          this.redirectToLogin(state.url);
          return false;
        }
      })
    );
  }

  private setDefaultUrl() {
    const role = this.authService.getUserRole();
    switch (role) {
      case 'student':
        this.currentUrl = '/student/dashboard';
        break;
      case 'instructor':
        this.currentUrl = '/instructor/dashboard';
        break;
      case 'employer':
        this.currentUrl = '/employer/dashboard';
        break;
      case 'admin':
        this.currentUrl = '/admin/dashboard';
        break;
      default:
        this.currentUrl = '/landing';
        break;
    }
  }

  private isValidRoute(url: string): boolean {
    const validRoutes = [
      '/student/dashboard',
      '/student/profile',
      '/student/weekly-accomplishments',
      '/student/portfolio',
      '/student/proof-of-evidences',
      '/student/dtr',
      '/student/requirements',
      '/student/feedback',
      '/student/certificate-of-completion',
      '/student/final-report',
      '/student/exit-poll',
      '/employer/dashboard',
      '/employer/ojt-dtr',
      '/employer/work-accomplishments',
      '/employer/feedback',
      '/employer/certificate-of-completion',
      '/instructor/class',
      '/instructor/dashboard',
      '/instructor/signed-documents',
      '/instructor/endorsement-letter',
      '/instructor/proof-of-evidences',
      '/instructor/poe-acquaintance',
      '/instructor/poe-seminars',
      '/instructor/poe-foundation-week',
      '/instructor/poe-sportsfest',
      '/instructor/parents-consent',
      '/instructor/acceptance-letter',
      '/instructor/moa',
      '/instructor/vaccination-card',
      '/instructor/barangay-clearance',
      '/instructor/medical-certificate',
      '/instructor/resume',
      '/instructor/work-accomplishments',
      '/instructor/weekly-accomplishments',
      '/instructor/dtr',
      '/instructor/employer-feedback',
      '/instructor/certificate-of-completion',
      '/instructor/requirement-checking',
      '/instructor/announcement',
      '/admin/dashboard',
      '/admin/accounts',
      '/admin/link-accounts'
    ];

    return validRoutes.includes(url);
  }

  private redirectToLogin(url: string) {
    if (url.startsWith('/student')) {
      this.router.navigate(['/student-login'], { queryParams: { returnUrl: url } });
    } else if (url.startsWith('/employer')) {
      this.router.navigate(['/employer-login'], { queryParams: { returnUrl: url } });
    } else if (url.startsWith('/instructor')) {
      this.router.navigate(['/instructor-login'], { queryParams: { returnUrl: url } });
    } else {
      this.router.navigate(['/landing-page'], { queryParams: { returnUrl: url } });
    }
  }
}