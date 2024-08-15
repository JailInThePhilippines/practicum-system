import { Component } from '@angular/core';
import { DataService } from '../../data.service';
import { Router } from '@angular/router';
import { AuthService } from '../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-student-login',
  templateUrl: './student-login.component.html',
  styleUrls: ['./student-login.component.css']
})
export class StudentLoginComponent {
  email: string = ''; // Change username to email
  password: string = '';
  message: string = '';

  constructor(
    private dataService: DataService,
    private router: Router,
    private authService: AuthService,
    private snackBar: MatSnackBar
  ) {}

  onSubmit() {
    this.dataService.studentLogin(this.email, this.password) // Change username to email
      .subscribe(
        response => {
          if (response.success) {
            console.log('Login successful:', response);
            this.snackBar.open('Login Successful', '', {
              duration: 3000,
              panelClass: ['custom-snackbar']
            });
            // Call login method of AuthService and pass the JWT token and user role
            this.authService.login(this.email, this.password, 'student').subscribe( // Change username to email
              () => {
                // Redirect to the dashboard
                this.router.navigate(['/student', 'dashboard']);
              },
              error => {
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
        error => {
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