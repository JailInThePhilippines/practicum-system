import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-employer-side-navbar',
  templateUrl: './employer-side-navbar.component.html',
  styleUrl: './employer-side-navbar.component.css'
})
export class EmployerSideNavbarComponent implements OnInit {
  employerName: string = '';

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private router: Router
  ) {}

  isActive(route: string): boolean {
    return this.router.url.includes(route);
  }

  ngOnInit(): void {
    this.fetchEmployerName();
  }

  fetchEmployerName(): void {
    const employerId = this.authService.getCurrentEmployerId();
    if (employerId) {
      this.dataService.getEmployerName(employerId).subscribe(
        (response) => {
          if (response && response.employer_name) {
            this.employerName = response.employer_name;
          }
        },
        (error) => {
          console.error('Error fetching employer name:', error);
        }
      );
    }
  }  

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/employer-login']);
  }

}
