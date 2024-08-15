import { Component } from '@angular/core';
import { DataService } from '../../data.service';
import { Router } from '@angular/router';
import { AuthService } from '../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-employer-login',
  templateUrl: './employer-login.component.html',
  styleUrls: ['./employer-login.component.css']
})
export class EmployerLoginComponent {
  email: string = ''; // Change from username to email
  password: string = '';
  message: string = '';

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private router: Router,
    private snackBar: MatSnackBar
  ) {}

  onSubmit() {
    this.dataService.employerLogin(this.email, this.password).subscribe(
      (response) => {
        if (response.success) {
          console.log('Login successful:', response);
          this.snackBar.open('Login Successful', '', {
            duration: 3000,
            panelClass: ['custom-snackbar']
          });
          // Call AuthService login method and pass the JWT token and user role
          this.authService.login(this.email, this.password, 'employer').subscribe(
            () => {
              // Redirect to the employer dashboard
              this.router.navigate(['/employer', 'dashboard']);
              this.message = '';
            },
            (error) => {
              console.error('AuthService login error:', error);
              this.snackBar.open('An error occurred during login, please try again later.', 'Close', {
                duration: 3000,
                panelClass: ['custom-snackbar']
              });
            }
          );
        } else {
          console.error('Invalid email or password');
          this.snackBar.open('Invalid email or password', 'Close', {
            duration: 3000,
            panelClass: ['custom-snackbar']
          });
        }
      },
      (error) => {
        console.error('Login error:', error);
        this.snackBar.open('An error ocurred, please try again later.', 'Close', {
          duration: 3000,
          panelClass: ['custom-snackbar']
        });
      }
    );
  }

}