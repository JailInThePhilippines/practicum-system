import { Component } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-announcements',
  templateUrl: './announcements.component.html',
  styleUrls: ['./announcements.component.css'],
})
export class AnnouncementsComponent {
  title: string = '';
  body: string = '';
  announcements: any[] = [];

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.refreshAnnouncements();
  }

  onSubmit() {
    if (!this.title || !this.body) {
      this.snackBar.open('Incomplete fields, please retry.', 'Close', {
        duration: 3000,
        horizontalPosition: 'center',
        verticalPosition: 'bottom',
        panelClass: ['custom-snackbar'],
      });
      return;
    }

    const instructorId = this.authService.getCurrentInstructorId();

    const formData = {
      instructor_id: instructorId,
      title: this.title,
      body: this.body,
    };

    this.dataService.insertInstructorAnnouncement(formData).subscribe(
      () => {
        this.snackBar.open('Announcement Created Successfully!', 'Close', {
          duration: 3000,
          horizontalPosition: 'center',
          verticalPosition: 'bottom',
        });

        this.title = '';
        this.body = '';

        this.refreshAnnouncements();
      },
      (error) => {
        console.error(error);
        this.snackBar.open(
          'An error occurred while processing your request.',
          'Close',
          {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
          }
        );
      }
    );
  }

  deleteAnnouncement(announcementId: number) {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);

    dialogRef.afterClosed().subscribe((result) => {
      if (result === true) {
        const instructorId = this.authService.getCurrentInstructorId();

        // Check if instructorId is not null before calling deleteAnnouncement
        if (instructorId !== null) {
          this.dataService.deleteAnnouncement(instructorId, announcementId).subscribe(
            () => {
              this.snackBar.open('Announcement Deleted Successfully!', 'Close', {
                duration: 3000,
                horizontalPosition: 'center',
                verticalPosition: 'bottom',
              });

              this.refreshAnnouncements();
            },
            (error) => {
              console.error(error);
              this.snackBar.open(
                'An error occurred while deleting the announcement.',
                'Close',
                {
                  duration: 3000,
                  horizontalPosition: 'center',
                  verticalPosition: 'bottom',
                }
              );
            }
          );
        } else {
          console.error('Instructor ID is null');
          // Handle the case when instructorId is null
        }
      }
    });
  }

  refreshAnnouncements() {
    const instructorId = this.authService.getCurrentInstructorId();
    if (instructorId !== null) {
      this.dataService.getAnnouncementsForInstructor(instructorId).subscribe(
        (announcements) => {
          this.announcements = announcements;
        },
        (error) => {
          console.error(error);
        }
      );
    } else {
      console.error('Instructor ID is null');
    }
  }
}
