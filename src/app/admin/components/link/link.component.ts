import { Component } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-link',
  templateUrl: './link.component.html',
  styleUrls: ['./link.component.css'],
})
export class LinkComponent {
  formData: any = {}; // Object to hold form data

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar
  ) {}

  submitForm(): void {
    // Fetch the admin ID from AuthService
    this.formData.admin_id = this.authService.getCurrentAdminId();

    // Check if studentId and employerId are provided
    if (this.formData.student_id && this.formData.employer_id) {
      // Call linkStudentAndEmployer method in DataService
      this.dataService.linkStudentAndEmployer(this.formData).subscribe(
        (response) => {
          console.log('Linking successful:', response);
          this.snackBar.open('Linking Successful', 'Close', {
            duration: 3000,
            panelClass: ['custom-snackbar']
          });

          this.formData.student_id = '';
          this.formData.employer_id = '';
        },
        (error) => {
          console.error('Error linking student and employer:', error);
          this.snackBar.open('Error linking student and employer', 'Close', {
            duration: 3000,
            panelClass: ['custom-snackbar']
          });
        }
      );
    } else {
      console.error('Student ID and Employer ID are required.');
      this.snackBar.open('Student ID and Employer ID are required.', 'Close', {
        duration: 3000,
        panelClass: ['custom-snackbar']
      });
    }
  }
}