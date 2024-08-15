import { Component, OnInit } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { ViewSubmissionsForEmployerDialogComponent } from '../view-submissions-for-employer-dialog/view-submissions-for-employer-dialog.component';

@Component({
  selector: 'app-employer-dashboard',
  templateUrl: './employer-dashboard.component.html',
  styleUrls: ['./employer-dashboard.component.css'],
})
export class EmployerDashboardComponent implements OnInit {
  displayedColumns: string[] = ['student_name', 'program_year_block', 'school_id', 'actions'];
  dataSource = new MatTableDataSource<any>();

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    public dialog: MatDialog
  ) {}

  ngOnInit(): void {
    const employerId = this.authService.getCurrentEmployerId();
    if (employerId) {
      this.fetchAssociatedStudents(employerId);
    }
  }

  fetchAssociatedStudents(employerId: number): void {
    this.dataService.getAssociatedStudents(employerId).subscribe(
      (students) => {
        if (Array.isArray(students)) {
          students.forEach((student) => {
            this.dataService
              .getStudentProfilePicture(employerId, student.user_id)
              .subscribe(
                (result) => {
                  student.profile_picture = result.success
                    ? result.image_path
                    : 'assets/default-profile-picture.png';
                },
                (error) => {
                  console.error('Error fetching profile picture:', error);
                  student.profile_picture = 'assets/default-profile-picture.png';
                }
              );
          });
          this.dataSource.data = students.sort((a, b) =>
            a.student_name.localeCompare(b.student_name)
          );
        } else {
          console.error('Error: Data returned is not an array.');
        }
      },
      (error) => {
        console.error('Error fetching associated students:', error);
      }
    );
  }

  getProfilePictureUrl(student: any): string {
    return student.profile_picture
      ? 'http://localhost/PractiEase/api/' + student.profile_picture
      : 'assets/default-profile-picture.png';
  }

  openDialog(student: any): void {
    const employerId = this.authService.getCurrentEmployerId();
    const screenWidth = window.innerWidth;
    let dialogPosition = { left: '15%' };
  
    if (screenWidth === 1366) {
      dialogPosition = { left: '17%' }; // Adjust the value as needed
    }
  
    const dialogRef = this.dialog.open(ViewSubmissionsForEmployerDialogComponent, {
      width: '90%',
      data: { student, employerId },
      position: dialogPosition
    });
  
    dialogRef.afterClosed().subscribe(result => {
      console.log('The dialog was closed');
    });
  }

}
