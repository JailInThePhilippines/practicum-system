import { Component } from '@angular/core';
import { DataService } from '../../data.service';
import { Router } from '@angular/router';
import { AuthService } from '../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-admin-login',
  templateUrl: './admin-login.component.html',
  styleUrl: './admin-login.component.css'
})
export class AdminLoginComponent {
  email: string = '';
  password: string = '';
  message: string = '';

  constructor(
    private dataService: DataService,
    private router: Router,
    private authService: AuthService,
    private snackBar: MatSnackBar
  ) {}

  onSubmit() {
    this.dataService.adminLogin(this.email, this.password).subscribe(
      (response) => {
        // Check if the response indicates a successful login
        if (response.success) {
          console.log('Login successful:', response);
          this.snackBar.open('Login Successful', '', {
            duration: 3000,
            panelClass: ['custom-snackbar']
          });
          // Call AuthService login method and pass the JWT token and user role
          this.authService.login(this.email, this.password, 'admin').subscribe(
            () => {
              // Redirect to the instructor dashboard or any other page
              this.router.navigate(['/admin', 'dashboard']);
              // Clear any previous error messages
              this.message = '';
            },
            (error) => {
              // Handle error during AuthService login
              console.error('AuthService login error:', error);
              this.snackBar.open('An error occurred during login, please try again later.', 'Close', {
                duration: 3000,
                panelClass: ['custom-snackbar']
              });
            }
          );
        } else {
          // Handle unsuccessful login (invalid credentials)
          console.error('Invalid email or password');
          this.snackBar.open('Invalid email or password', 'Close', {
            duration: 3000,
            panelClass: ['custom-snackbar']
          });
        }
      },
      (error) => {
        // Handle login error (HTTP error, server unreachable, etc.)
        console.error('Login error:', error);
        this.snackBar.open('An error ocurred, please try again later.', 'Close', {
          duration: 3000,
          panelClass: ['custom-snackbar']
        });
      }
    );
  }
}
