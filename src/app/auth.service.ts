import { Injectable, Inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Observable, of, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private authTokenKey = 'authToken';
  private userRoleKey = 'userRole';
  private userIdKey = 'userId';
  private employerIdKey = 'employerId';
  private instructorIdKey = 'instructorId';
  private adminIdKey = 'adminId';
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);
  private userId: number | null = null;
  private employerId: number | null = null;
  private instructorId: number | null = null;
  private adminId: number | null = null;

  constructor(
    @Inject(PLATFORM_ID) private platformId: Object,
    private http: HttpClient
  ) {
    if (isPlatformBrowser(this.platformId)) {
      this.checkAuthenticationStatus();
    }
  }

  checkAuthenticationStatus(): Observable<boolean> {
    return new Observable(observer => {
      const authToken = localStorage.getItem(this.authTokenKey);
      const userRole = localStorage.getItem(this.userRoleKey);
      const userId = localStorage.getItem(this.userIdKey);
      const employerId = localStorage.getItem(this.employerIdKey);
      const instructorId = localStorage.getItem(this.instructorIdKey);
      const adminId = localStorage.getItem(this.adminIdKey);

      if (authToken && userRole && userId && employerId && instructorId && adminId) {
        this.userId = parseInt(userId, 10);
        this.employerId = parseInt(employerId, 10);
        this.instructorId = parseInt(instructorId, 10);
        this.adminId = parseInt(adminId, 10);
        this.isAuthenticatedSubject.next(true);
      } else {
        this.isAuthenticatedSubject.next(false);
      }
      observer.next(true);
      observer.complete();
    });
  }

  login(email: string, password: string, role: string): Observable<any> {
    let loginEndpoint: string;
  
    switch (role) {
      case 'student':
        loginEndpoint = 'http://localhost/PractiEase/api/student_login';
        break;
      case 'employer':
        loginEndpoint = 'http://localhost/PractiEase/api/employer_login';
        break;
      case 'instructor':
        loginEndpoint = 'http://localhost/PractiEase/api/instructor_login';
        break;
      case 'admin':
        loginEndpoint = 'http://localhost/PractiEase/api/admin_login';
        break;
      default:
        console.error('Invalid role:', role);
        return of({ error: 'Invalid role' });
    }
  
    return this.http.post<any>(loginEndpoint, { email, password }).pipe(
      tap(response => {
        this.userId = response.user_id;
        this.employerId = response.employer_id;
        this.instructorId = response.instructor_id;
        this.adminId = response.admin_id;
        localStorage.setItem(this.userRoleKey, role);
        localStorage.setItem(this.authTokenKey, response.auth_token);
        localStorage.setItem(this.userIdKey, String(response.user_id));
        localStorage.setItem(this.employerIdKey, String(response.employer_id));
        localStorage.setItem(this.instructorIdKey, String(response.instructor_id));
        localStorage.setItem(this.adminIdKey, String(response.admin_id));
        this.isAuthenticatedSubject.next(true);
      })
    );
  }
  
  logout() {
    this.isAuthenticatedSubject.next(false);
    this.userId = null;
    this.employerId = null;
    this.instructorId = null;
    this.adminId = null;
    if (isPlatformBrowser(this.platformId)) {
      localStorage.removeItem(this.authTokenKey);
      localStorage.removeItem(this.userRoleKey);
      localStorage.removeItem(this.userIdKey);
      localStorage.removeItem(this.employerIdKey);
      localStorage.removeItem(this.adminIdKey);
    }
  }

  isAuthenticated(): Observable<boolean> {
    return this.isAuthenticatedSubject.asObservable();
  }

  getCurrentUserId(): number | null {
    return this.userId;
  }

  getCurrentEmployerId(): number | null {
    return this.employerId;
  }

  getCurrentInstructorId(): number | null {
    return this.instructorId;
  }

  getCurrentAdminId(): number | null {
    return this.adminId;
  }

  getAuthToken(): string | null {
    return isPlatformBrowser(this.platformId) ? localStorage.getItem(this.authTokenKey) : null;
  }

  getUserRole(): string | null {
    return isPlatformBrowser(this.platformId) ? localStorage.getItem(this.userRoleKey) : null;
  }
}