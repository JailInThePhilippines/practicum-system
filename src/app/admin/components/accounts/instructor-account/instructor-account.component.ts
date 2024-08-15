import { Component } from '@angular/core';
import { AuthService } from '../../../../auth.service';
import { DataService } from '../../../../data.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-instructor-account',
  templateUrl: './instructor-account.component.html',
  styleUrls: ['./instructor-account.component.css'],
})
export class InstructorAccountComponent {
  formData: any = {
    block_handled: [],
    program_handled: [],
    year_handled: []
  }; // Object to hold form data
  accountType: string = 'instructor';

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private snackBar: MatSnackBar
  ) {}

  onSubmit() {
    // Get admin_id using AuthService
    const admin_id = this.authService.getCurrentAdminId();

    // Add admin_id to formData
    this.formData.admin_id = admin_id;

    // Convert arrays to comma-separated strings
    this.formData.block_handled = this.formData.block_handled.join(',');
    this.formData.program_handled = this.formData.program_handled.join(',');
    this.formData.year_handled = this.formData.year_handled.join(',');

    // Determine which method to call based on account type
    if (this.accountType === 'instructor') {
      this.dataService.createInstructorAccount(this.formData).subscribe(
        (response) => {
          console.log('Instructor Account Response:', response);
          this.handleSuccess();
        },
        (error) => {
          console.error('Error creating Instructor Account:', error);
          this.handleError();
        }
      );
    } else if (this.accountType === 'student') {
      this.dataService.createStudentAccount(this.formData).subscribe(
        (response) => {
          console.log('Student Account Response:', response);
          this.handleSuccess();
        },
        (error) => {
          console.error('Error creating Student Account:', error);
          this.handleError();
        }
      );
    } else {
      this.dataService.createEmployerAccount(this.formData).subscribe(
        (response) => {
          console.log('Employer Account Response:', response);
          this.handleSuccess();
        },
        (error) => {
          console.error('Error creating Employer Account:', error);
          this.handleError();
        }
      );
    }
  }

  handleSuccess() {
    this.snackBar.open('Account Created Successfully', 'Close', {
      duration: 3000,
      panelClass: ['custom-snackbar'],
    });
    this.resetForm();
  }

  handleError() {
    this.snackBar.open('Account Creation Unsuccessful', 'Close', {
      duration: 3000,
      panelClass: ['custom-snackbar'],
    });
  }

  resetForm() {
    // Reset form data after submission
    this.formData = {
      block_handled: [],
      program_handled: [],
      year_handled: []
    };
  }
}