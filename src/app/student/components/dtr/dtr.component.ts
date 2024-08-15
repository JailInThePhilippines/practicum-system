import { Component, OnInit, ViewChild } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { TimeTrackingService } from '../../../time-tracking.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmationDialogComponent } from '../../../confirmation-dialog/confirmation-dialog.component';
import { ChangeDetectorRef } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';

@Component({
  selector: 'app-dtr',
  templateUrl: './dtr.component.html',
  styleUrls: ['./dtr.component.css'],
})
export class DtrComponent implements OnInit {
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  timeRecords: {
    date: string;
    timeIn: string;
    timeOut: string;
    dtr_status: string;
    hours_worked: string;
    remarks: string;
  }[] = [];
  hasTimedIn: boolean = false;
  hasTimedOut: boolean = false;
  canTimeOut: boolean = true;
  displayedColumns: string[] = [
    'dayNo',
    'date',
    'timeIn',
    'timeOut',
    'hoursWorked',
    'status',
    'remarks',
    'delete'
  ];
  dataSource = new MatTableDataSource(this.timeRecords);
  uploadedFiles: { file_id: number; file_name: string; dtr_status: string }[] =
    [];
  uploadedFileColumns: string[] = ['fileName', 'dtrStatus', 'delete'];

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private timeTrackingService: TimeTrackingService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog,
    private cdr: ChangeDetectorRef
  ) {}

  getStatusClass(status: string): string {
    switch (status) {
      case 'Approved':
        return 'text-success';
      case 'Rejected':
        return 'text-danger';
      case 'Unverified':
        return 'text-warning';
      default:
        return '';
    }
  }

  ngOnInit(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      this.fetchTimeRecords(userId);
      this.fetchUploadedFiles(userId);
    }

    this.canTimeOut = this.timeTrackingService.getHasTimedOut();
    console.log('Initial canTimeOut state:', this.canTimeOut);
  }

  ngAfterViewInit(): void {
    this.dataSource.paginator = this.paginator;
  }

  private fetchTimeRecords(userId: number): void {
    this.dataService.getPastTimeRecords(userId).subscribe(
      (records) => {
        if (Array.isArray(records)) {
          this.timeRecords = records.map((record: any) => ({
            dtr_id: record.dtr_id,
            date: this.formatDate(record.time_in),
            timeIn: this.formatTime(record.time_in),
            timeOut: record.time_out ? this.formatTime(record.time_out) : 'N/A',
            remarks: record.remarks ? record.remarks : '',
            dtr_status: record.dtr_status,
            hours_worked:
              record.hours_worked !== undefined ? record.hours_worked : '0.00',
          }));

          this.updateTimedInAndOutState();
          this.dataSource.data = this.timeRecords;
        } else {
          console.error('Records is not an array:', records);
        }
      },
      (error) => {
        console.error('Failed to fetch past time records:', error);
      }
    );
  }

  private updateTimedInAndOutState(): void {
    const currentDate = new Date();
    const formattedDate = currentDate.toISOString().split('T')[0];
    const todayRecords = this.timeRecords.filter(
      (record) => record.date === formattedDate
    );
    this.hasTimedIn = todayRecords.some((record) => record.timeIn !== 'N/A');
    this.hasTimedOut = todayRecords.some((record) => record.timeOut !== 'N/A');
  }

  recordTimeIn(): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      data: { message: 'Are you sure you want to time in?' },
    });
  
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        const userId = this.authService.getCurrentUserId();
        if (userId && !this.hasTimedIn) {
          const currentDate = new Date();
          const formattedDate = currentDate.toISOString().split('T')[0];
          const formattedTime = currentDate.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
          });
          const timeRecord = {
            date: formattedDate,
            timeIn: formattedTime,
            timeOut: 'N/A',
            dtr_status: 'Unverified', // Set initial status
            hours_worked: '0.00', // Initialize hours_worked
            remarks: '', // Initialize remarks
          };
          this.timeRecords.push(timeRecord);
          this.hasTimedIn = true;
          this.canTimeOut = false;
          this.timeTrackingService.setHasTimedOut(false);
  
          console.log('Time In recorded. Timeout will be enabled in 1 hour.');
  
          setTimeout(() => {
            this.canTimeOut = true;
            this.timeTrackingService.setHasTimedOut(true);
            console.log('Timeout enabled after 1 hour.');
            this.cdr.detectChanges();
          }, 5000);
  
          this.dataService.recordTimeIn(userId).subscribe(
            (response) => {
              console.log('Time In recorded successfully:', response);
              this.snackBar.open('Timed In Successfully', 'Close', {
                duration: 3000,
                horizontalPosition: 'center',
                verticalPosition: 'bottom',
                panelClass: ['custom-snackbar'],
              });
              this.dataSource.data = this.timeRecords; // Update dataSource
              this.cdr.detectChanges(); // Trigger change detection
            },
            (error) => {
              console.error('Failed to record Time In:', error);
            }
          );
        } else {
          console.log('User has already timed in for today');
          this.snackBar.open('You have already timed in for today.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        }
      }
    });
  }

  recordTimeOut(): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      data: { message: 'Are you sure you want to time out?' },
    });
  
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        const userId = this.authService.getCurrentUserId();
        if (userId && !this.hasTimedOut && this.hasTimedIn && this.canTimeOut) {
          const currentDate = new Date();
          const formattedTime = currentDate.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
          });
          const lastIndex = this.timeRecords.length - 1;
          if (lastIndex >= 0) {
            this.timeRecords[lastIndex].timeOut = formattedTime;
            this.hasTimedOut = true;
  
            // Calculate hours worked
            const hoursWorked = this.calculateHoursWorked(
              this.timeRecords[lastIndex].timeIn,
              formattedTime
            );
            this.timeRecords[lastIndex].hours_worked = hoursWorked;
  
            this.dataService.recordTimeOut(userId, hoursWorked).subscribe(
              (response) => {
                console.log('Time Out recorded successfully:', response);
                this.snackBar.open('Timed Out Successfully.', 'Close', {
                  duration: 3000,
                  horizontalPosition: 'center',
                  verticalPosition: 'bottom',
                  panelClass: ['custom-snackbar'],
                });
                this.dataSource.data = [...this.timeRecords]; // Update dataSource
                this.cdr.detectChanges(); // Trigger change detection
              },
              (error) => {
                console.error('Failed to record Time Out:', error);
              }
            );
            this.timeTrackingService.setHasTimedOut(true);
          }
        } else if (!this.hasTimedIn) {
          console.log('User must time in before timing out');
          this.snackBar.open('You must time in before timing out.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        } else {
          console.log('User has already timed out for today');
          this.snackBar.open('You have already timed out for today.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        }
      }
    });
  }

  private formatTime(timeString: string): string {
    // Assuming timeString is in 'YYYY-MM-DD HH:mm:ss' format
    const date = new Date(timeString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  private formatDate(dateString: string): string {
    // Check if the dateString contains 'T' or a space to determine the format
    if (dateString.includes('T')) {
      return dateString.split('T')[0];
    } else if (dateString.includes(' ')) {
      return dateString.split(' ')[0];
    }
    return dateString; // Return as is if it doesn't match expected formats
  }

  private calculateHoursWorked(timeIn: string, timeOut: string): string {
    if (timeIn === 'N/A' || timeOut === 'N/A') {
      return '0';
    }
    const [timeInHours, timeInMinutes] = timeIn.split(':').map(Number);
    const [timeOutHours, timeOutMinutes] = timeOut.split(':').map(Number);
  
    if (isNaN(timeInHours) || isNaN(timeInMinutes) || isNaN(timeOutHours) || isNaN(timeOutMinutes)) {
      return '0';
    }
  
    const totalHours = timeOutHours - timeInHours;
    const totalMinutes = timeOutMinutes - timeInMinutes;
    const decimalMinutes = totalMinutes / 60;
    const totalWorked = totalHours + decimalMinutes;
  
    return Math.floor(totalWorked).toString();
  }

  floorHours(hours: number): number {
    return Math.floor(hours);
  }

  calculateTotalHoursWorked(): string {
    let totalHours = 0;
    this.timeRecords.forEach((record) => {
      if (record.timeOut !== 'N/A' && record.hours_worked !== 'N/A' && record.dtr_status !== 'Rejected') {
        totalHours += Math.floor(parseFloat(record.hours_worked));
      }
    });
    return totalHours.toString();
  }

  deleteRecord(index: number, dtrId: number): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      data: { message: 'Are you sure you want to delete this record?' },
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        const userId = this.authService.getCurrentUserId();
        if (userId) {
          this.dataService.deleteDTR(dtrId, userId).subscribe(
            (response) => {
              console.log('DTR record deleted successfully:', response);
              this.timeRecords.splice(index, 1);
              this.dataSource.data = this.timeRecords; // Update dataSource
              this.snackBar.open('Record deleted successfully.', 'Close', {
                duration: 3000,
                horizontalPosition: 'center',
                verticalPosition: 'bottom',
                panelClass: ['custom-snackbar'],
              });
            },
            (error) => {
              console.error('Failed to delete DTR record:', error);
              this.snackBar.open('Failed to delete record.', 'Close', {
                duration: 3000,
                horizontalPosition: 'center',
                verticalPosition: 'bottom',
                panelClass: ['custom-snackbar'],
              });
            }
          );
        }
      }
    });
  }

  private fetchUploadedFiles(userId: number): void {
    this.dataService.getDTRForStudent(userId).subscribe(
      (files) => {
        if (Array.isArray(files)) {
          this.uploadedFiles = files.map((file: any) => ({
            file_id: file.file_id,
            file_name: file.file_name,
            dtr_status: file.dtr_status,
          }));
        } else {
          this.uploadedFiles = [];
        }
      },
      (error) => {
        console.error('Failed to fetch uploaded files:', error);
      }
    );
  }

  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.uploadFile(file);
    }
  }

  private uploadFile(file: File): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      const formData = new FormData();
      formData.append('file', file);

      this.dataService.uploadDTR(formData, userId).subscribe(
        (response) => {
          console.log('File uploaded successfully:', response);
          this.snackBar.open('File uploaded successfully', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
          this.fetchUploadedFiles(userId); // Refresh the uploaded files list
        },
        (error) => {
          console.error('Failed to upload file:', error);
          this.snackBar.open('Failed to upload file', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        }
      );
    }
  }

  isUploadDisabled(): boolean {
    return this.uploadedFiles.some((file) => file.dtr_status === 'Approved');
  }

  onDeleteFile(fileId: number): void {
    const dialogRef = this.dialog.open(ConfirmationDialogComponent, {
      data: { message: 'Are you sure you want to delete this file?' },
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        const userId = this.authService.getCurrentUserId();
        if (userId) {
          this.dataService.deleteDTRFile(fileId, userId).subscribe(
            (response) => {
              console.log('File deleted successfully:', response);
              this.uploadedFiles = this.uploadedFiles.filter(
                (file) => file.file_id !== fileId
              );
              this.snackBar.open('File deleted successfully', 'Close', {
                duration: 3000,
                horizontalPosition: 'center',
                verticalPosition: 'bottom',
                panelClass: ['custom-snackbar'],
              });
            },
            (error) => {
              console.error('Failed to delete file:', error);
              this.snackBar.open('Failed to delete file', 'Close', {
                duration: 3000,
                horizontalPosition: 'center',
                verticalPosition: 'bottom',
                panelClass: ['custom-snackbar'],
              });
            }
          );
        }
      }
    });
  }
}
