import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';

@Component({
  selector: 'app-admin-dashboard',
  templateUrl: './admin-dashboard.component.html',
  styleUrl: './admin-dashboard.component.css',
})
export class AdminDashboardComponent implements OnInit {
  studentCount: number = 0;
  employerCount: number = 0;
  instructorCount: number = 0;
  linkedAccountsCount: number = 0;
  students: any[] = [];
  instructors: any[] = [];
  employers: any[] = [];
  linkedAccounts: any[] = [];

  showStudentContent: boolean = false;
  showInstructorContent: boolean = false;
  showEmployerContent: boolean = false;
  showLinkedAccountsContent: boolean = false;

  constructor(
    private dataService: DataService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.fetchData();
  }

  fetchData(): void {
    this.getStudentCount();
    this.getEmployerCount();
    this.getInstructorCount();
    this.getLinkedAccountsCount();
    this.getStudentsForAdmin();
    this.getInstructorsForAdmin();
    this.getEmployersForAdmin();
    this.getLinkedAccountsForAdmin();
  }

  getStudentsForAdmin(): void {
    this.dataService.getStudentsForAdmin().subscribe(
      (response) => {
        if (response && response.length > 0) {
          this.students = response;
        } else {
          console.error('Error: No students found');
        }
      },
      (error) => {
        console.error('Error fetching students:', error);
      }
    );
  }

  getInstructorsForAdmin(): void {
    this.dataService.getInstructorsForAdmin().subscribe(
      (response) => {
        if (response && response.length > 0) {
          this.instructors = response;
        } else {
          console.error('Error: No instructors found');
        }
      },
      (error) => {
        console.error('Error fetching instructors:', error);
      }
    );
  }

  getEmployersForAdmin(): void {
    this.dataService.getEmployersForAdmin().subscribe(
      (response) => {
        if (response && response.length > 0) {
          this.employers = response;
        } else {
          console.error('Error: No employers found');
        }
      },
      (error) => {
        console.error('Error fetching employers:', error);
      }
    );
  }

  getLinkedAccountsForAdmin(): void {
    this.dataService.getLinkedAccountsForAdmin().subscribe(
      (response) => {
        if (response && response.length > 0) {
          this.linkedAccounts = response;
        } else {
          console.error('Error: No linked accounts found');
        }
      },
      (error) => {
        console.error('Error fetching linked accounts:', error);
      }
    );
  }

  getEmployerCount(): void {
    this.dataService.getEmployerCount().subscribe(
      (response) => {
        this.employerCount = response?.count || 0;
      },
      (error) => {
        console.error('Error fetching employer count:', error);
      }
    );
  }

  getStudentCount(): void {
    this.dataService.getStudentCount().subscribe(
      (response) => {
        this.studentCount = response?.count || 0;
      },
      (error) => {
        console.error('Error fetching student count:', error);
      }
    );
  }

  getInstructorCount(): void {
    this.dataService.getInstructorCount().subscribe(
      (response) => {
        this.instructorCount = response?.count || 0;
      },
      (error) => {
        console.error('Error fetching instructor count:', error);
      }
    );
  }

  getLinkedAccountsCount(): void {
    this.dataService.getLinkedAccountsCount().subscribe(
      (response) => {
        this.linkedAccountsCount = response?.count || 0;
      },
      (error) => {
        console.error('Error fetching linked accounts count:', error);
      }
    );
  }

  deleteStudent(student: any): void {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        this.dataService.deleteStudent(student.user_id).subscribe(
          () => {
            this.students = this.students.filter(
              (s) => s.user_id !== student.user_id
            );
            this.snackBar.open('Student Deleted Successfully', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          },
          (error) => {
            console.error('Error deleting student:', error);
            this.snackBar.open('Student Deletion Unsuccessful', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          }
        );
      }
    });
  }

  deleteInstructor(instructor: any): void {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        // Proceed with deletion
        this.dataService.deleteInstructor(instructor.instructor_id).subscribe(
          () => {
            // Remove the deleted student from the students array
            this.instructors = this.instructors.filter(
              (i) => i.instructor_id !== instructor.instructor_id
            );
            this.snackBar.open('Instructor Deleted Successfully', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          },
          (error) => {
            console.error('Error deleting instructor:', error);
            this.snackBar.open('Instructor Deletion Unsuccessful', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          }
        );
      }
    });
  }

  deleteEmployer(employer: any): void {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        // Proceed with deletion
        this.dataService.deleteEmployer(employer.employer_id).subscribe(
          () => {
            // Remove the deleted student from the students array
            this.employers = this.employers.filter(
              (e) => e.employer_id !== employer.employer_id
            );
            this.snackBar.open('Employer Deleted Successfully', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          },
          (error) => {
            console.error('Error deleting employer:', error);
            this.snackBar.open('Employer Deletion Unsuccessful', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          }
        );
      }
    });
  }

  deleteLinkedAccount(linkedAccounts: any): void {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        // Proceed with deletion
        this.dataService.deleteLinkedAccount(linkedAccounts.employer_id).subscribe(
          () => {
            // Remove the deleted student from the students array
            this.linkedAccounts = this.linkedAccounts.filter(
              (li) => li.employer_id !== linkedAccounts.employer_id,
            );
            this.snackBar.open('Unlinking Successful', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          },
          (error) => {
            console.error('Error unlinking:', error);
            this.snackBar.open('Unlinking Unsuccessful', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          }
        );
      }
    });
  }

  toggleStudentContent(): void {
    this.showStudentContent = !this.showStudentContent;
  }

  toggleInstructorContent(): void {
    this.showInstructorContent = !this.showInstructorContent;
  }

  toggleEmployerContent(): void {
    this.showEmployerContent = !this.showEmployerContent;
  }

  toggleLinkedAccountsContent(): void {
    this.showLinkedAccountsContent = !this.showLinkedAccountsContent;
  }
}
