import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-admin-side-nav',
  templateUrl: './admin-side-nav.component.html',
  styleUrl: './admin-side-nav.component.css',
})
export class AdminSideNavComponent {
  list = [
    {
      number: '1',
      name: 'Dashboard',
      icon: 'fa fa-home',
    },
    {
      number: '2',
      name: 'Accounts',
      icon: 'fa-solid fa-user',
    },
    {
      number: '3',
      name: 'Link-Accounts',
      icon: 'fa-solid fa-link',
    },
  ];

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private router: Router
  ) {}

  isActive(route: string): boolean {
    return this.router.url.includes(route);
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/landing']);
  }

}
