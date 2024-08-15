import { Component, Inject, OnInit, ViewChild, ChangeDetectorRef, AfterViewInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatTableDataSource } from '@angular/material/table';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';
import { MatDialog } from '@angular/material/dialog';
import { EditDtrDialogComponent } from '../../../edit-dtr-dialog/edit-dtr-dialog.component';
import { DatePipe } from '@angular/common';

interface DtrRecord {
  dtr_id: number;
  time_in: string;
  time_out: string;
  user_id: number;
  school_id: number;
  dtr_status: string;
  hours_worked: string;
  remarks?: string;
}

interface UploadedCertificate {
  file_id: number;
  file_name: string;
  user_id: number;
  category: string;
}

interface Document {
  name: string;
  path: string | null;
  status: string;
  file_name?: string;
  dtr_status?: string;
  file_id?: number;
}

@Component({
  selector: 'app-view-submissions-for-employer-dialog',
  templateUrl: './view-submissions-for-employer-dialog.component.html',
  styleUrls: ['./view-submissions-for-employer-dialog.component.css'],
})
export class ViewSubmissionsForEmployerDialogComponent implements OnInit, AfterViewInit {
  profilePictureUrl: string = 'assets/default-profile-picture.png'; // Default profile picture
  studentDetails: any = null;
  dtrDocuments: Document[] = [];
  dtrRecords: DtrRecord[] = [];
  totalHoursWorked: number = 0;
  accomplishmentDocuments = new MatTableDataSource<any>();
  uploadedCertificates: UploadedCertificate[] = [
    {
      file_id: 0,
      file_name: 'Not Available',
      user_id: 0,
      category: 'Certificate of Completion',
    },
  ];
  selectedFile: File | null = null;

  paginatedDtrRecords: DtrRecord[] = [];
  pageSize = 5;
  pageIndex = 0;

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild('weeklyPaginator') weeklyPaginator!: MatPaginator;

  constructor(
    public dialogRef: MatDialogRef<ViewSubmissionsForEmployerDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dataService: DataService,
    private snackBar: MatSnackBar,
    private changeDetectorRef: ChangeDetectorRef,
    private dialog: MatDialog,
    private datePipe: DatePipe
  ) {}

  ngOnInit(): void {
    this.fetchStudentProfilePicture();
    this.fetchStudentDtrRecords();
    this.fetchStudentDetails();
    this.fetchStudentCertificates();
    this.fetchStudentDocuments();
    this.fetchWeeklyAccomplishmentRecords();
    this.calculateTotalHoursWorked();
  }

  fetchStudentDetails(): void {
    const employerId = this.data.employerId;
    this.dataService.getAssociatedStudents(employerId).subscribe(
      (data) => {
        // Assuming the first student is the one we need
        this.studentDetails = data[0];
      },
      (error) => {
        console.error('Error fetching student details:', error);
      }
    );
  }

  fetchStudentDocuments(): void {
    const studentId = this.data.student.user_id;
    const employerId = this.data.employerId;
  
    const dtrDocumentFetchers = [
      {
        method: this.dataService.getDTRForEmployer,
        name: 'DTR Files',
      },
    ];
  
    let fetchedDTRDocuments: Document[] = [];
  
    const fetchDTRDocumentsPromises = dtrDocumentFetchers.map(
      ({ method, name }) => {
        return method
          .call(this.dataService, employerId, studentId)
          .toPromise()
          .then(
            (documents: any[] | undefined) => {
              if (documents && documents.length > 0) {
                documents.forEach((doc) => {
                  fetchedDTRDocuments.push({
                    name,
                    path: doc.file_path,
                    status: 'Not Set',
                    file_name: doc.file_name,
                    dtr_status: doc.dtr_status,
                    file_id: doc.file_id
                  });
                });
              } else {
                fetchedDTRDocuments.push({ name, path: null, status: 'Not Set' });
              }
            },
            (error: any) => {
              console.error(`Error fetching ${name}:`, error);
            }
          );
      }
    );
  
    Promise.all(fetchDTRDocumentsPromises)
      .then(() => {
        this.dtrDocuments = fetchedDTRDocuments;
      })
      .catch((error) => {
        console.error('Error in fetching documents:', error);
      });
  }

  openDocument(filePath: string | null): void {
    if (filePath) {
      const baseUrl = 'http://localhost/PractiEase/api'; // Replace with your backend base URL
      window.open(`${baseUrl}/${filePath}`, '_blank');
    }
  }

  fetchStudentProfilePicture(): void {
    const studentId = this.data.student.user_id;
    this.dataService
      .getStudentProfilePicture(this.data.employerId, studentId)
      .subscribe(
        (result) => {
          this.profilePictureUrl = result.success
            ? `http://localhost/PractiEase/api/${result.image_path}`
            : 'assets/default-profile-picture.png';
        },
        (error) => {
          console.error('Error fetching profile picture:', error);
          this.profilePictureUrl = 'assets/default-profile-picture.png';
        }
      );
  }

  fetchStudentDtrRecords(): void {
    const studentId = this.data.student.user_id;
    const employerId = this.data.employerId;
    this.dataService.getTimeInOutForEmployer(employerId, studentId).subscribe(
      (dtrRecords) => {
        this.dtrRecords = dtrRecords;
        this.updatePaginatedDtrRecords();
        this.calculateTotalHoursWorked();
      },
      (error) => {
        console.error('Error fetching DTR records:', error);
      }
    );
  }

  calculateTotalHoursWorked(): void {
    this.totalHoursWorked = this.dtrRecords.reduce((total, record) => {
      if (record.time_in && record.time_out && record.dtr_status !== 'Rejected') {
        return total + Math.floor(parseFloat(record.hours_worked) * 100);
      }
      return total;
    }, 0) / 100;
  }

  ngAfterViewInit() {
    this.accomplishmentDocuments.paginator = this.weeklyPaginator;
    console.log('Paginator set:', this.weeklyPaginator); // Log paginator
  }

  fetchWeeklyAccomplishmentRecords(): void {
    const studentId = this.data.student.user_id;
    const employerId = this.data.employerId;
    this.dataService
      .getWeeklyAccomplishmentsForEmployer(employerId, studentId)
      .subscribe(
        (accomplishmentRecords) => {
          console.log('Fetched Weekly Accomplishments:', accomplishmentRecords); // Log fetched data
          this.accomplishmentDocuments.data = accomplishmentRecords.map(record => ({
            ...record,
            path: record.file_path,
            file_id: record.file_id,
            file_name: record.file_name
          }));
        },
        (error) => {
          console.error('Error fetching accomplishment records:', error);
        }
      );
  }

  updatePaginatedDtrRecords(): void {
    const startIndex = this.pageIndex * this.pageSize;
    const endIndex = startIndex + this.pageSize;
    this.paginatedDtrRecords = this.dtrRecords.slice(startIndex, endIndex);
  }

  formatDate(dateTimeString: string): string {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString();
  }

  formatDateTime(dateTimeString: string): string {
    return this.datePipe.transform(dateTimeString, 'yyyy-MM-dd HH:mm:ss') || 'Invalid date';
  }

  formatTime(dateTimeString: string | null): string {
    if (!dateTimeString) {
      return 'N/A';
    }
    const time = new Date(dateTimeString);
    return time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  formatHoursWorked(hours: number): string {
    return Math.floor(hours).toString();
  }

  floorHours(hours: number): number {
    return Math.floor(hours);
  }

  getDtrStatusStyle(status: string): { [key: string]: string } {
    let color = 'inherit';
    switch (status) {
      case 'Approved':
        color = 'green';
        break;
      case 'Rejected':
        color = 'red';
        break;
      case 'Unverified':
        color = '#FFBF00';
        break;
    }
    return { color: color };
  }

  getAccomplishmentStatusStyle(status: string): { [key: string]: string } {
    let color = 'inherit';
    switch (status) {
      case 'Approved':
        color = 'green';
        break;
      case 'Pending':
        color = 'orange';
        break;
      case 'Rejected':
        color = 'red';
        break;
      case 'Unverified':
        color = '#FFBF00';
        break;
    }
    return { color: color };
  }

  onDtrPageChange(event: PageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.updatePaginatedDtrRecords();
  }

  onAccomplishmentPageChange(event: PageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  setDTRStatus(element: DtrRecord, status: string): void {
    const employerId = this.data.employerId;
    const studentId = element.user_id;
  
    this.dataService
      .updateDtrStatus(
        { dtr_id: element.dtr_id, status },
        employerId,
        studentId
      )
      .subscribe(
        (response) => {
          if (response.success) {
            element.dtr_status = status;
            this.snackBar.open('DTR status updated successfully.', 'Close', {
              duration: 3000,
            });
            this.calculateTotalHoursWorked(); // Recalculate total hours worked
          } else {
            this.snackBar.open('Failed to update DTR status.', 'Close', {
              duration: 3000,
            });
          }
        },
        (error) => {
          console.error('Error updating DTR status:', error);
          this.snackBar.open('Error updating DTR status.', 'Close', {
            duration: 3000,
          });
        }
      );
  }

  updateDtrStatus(element: Document, status: string): void {
    const employerId = this.data.employerId;
    const studentId = this.data.student.user_id;
  
    if (element.file_id) {
      this.dataService
        .updateFileDtrStatus(
          { file_id: element.file_id, status },
          employerId,
          studentId
        )
        .subscribe(
          (response) => {
            if (response.success) {
              element.dtr_status = status;
              this.snackBar.open('DTR status updated successfully.', 'Close', {
                duration: 3000,
              });
            } else {
              this.snackBar.open('Failed to update DTR status.', 'Close', {
                duration: 3000,
              });
            }
          },
          (error) => {
            console.error('Error updating DTR status:', error);
            this.snackBar.open('Error updating DTR status.', 'Close', {
              duration: 3000,
            });
          }
        );
    } else {
      this.snackBar.open('File ID is missing.', 'Close', {
        duration: 3000,
      });
    }
  }

  updateDTRRemarks(element: DtrRecord): void {
    const data = {
      dtr_id: element.dtr_id,
      student_id: this.data.student.user_id, // Add student_id
      employer_id: this.data.employerId, // Add employer_id
      remarks: element.remarks
    };
  
    this.dataService.updateDTRRemarks(data).subscribe(
      (response) => {
        if (response.success) {
          this.snackBar.open('Remarks updated successfully.', 'Close', {
            duration: 3000,
          });
        } else {
          console.log('Failed to update remarks:', response);
        }
      },
      (error) => {
        console.error('Error updating remarks:', error);
        this.snackBar.open('Error updating remarks.', 'Close', {
          duration: 3000,
        });
      }
    );
  }

  setWeeklyAccomplishmentStatus(element: any, status: string): void {
    const employerId = this.data.employerId;
    const studentId = this.data.student.user_id;

    if (element.file_id) {
      this.dataService
        .updateWeeklyAccomplishmentsStatus(
          { file_id: element.file_id, status },
          employerId,
          studentId
        )
        .subscribe(
          (response) => {
            if (response.success) {
              element.weekly_status = status;
              this.snackBar.open('Weekly accomplishment status updated successfully.', 'Close', {
                duration: 3000,
              });
            } else {
              this.snackBar.open('Failed to update weekly accomplishment status.', 'Close', {
                duration: 3000,
              });
            }
          },
          (error) => {
            console.error('Error updating weekly accomplishment status:', error);
            this.snackBar.open('Error updating weekly accomplishment status.', 'Close', {
              duration: 3000,
            });
          }
        );
    } else {
      this.snackBar.open('File ID is missing.', 'Close', {
        duration: 3000,
      });
    }
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.selectedFile = input.files[0];
    }
  }

  uploadCertificate(): void {
    if (this.selectedFile) {
      const formData = new FormData();
      formData.append('file', this.selectedFile); // Use 'file' key here
      const employerId = this.data.employerId;
      const studentId = this.data.student.user_id;

      console.log('Uploading file:', this.selectedFile.name);
      console.log('Employer ID:', employerId);
      console.log('Student ID:', studentId);

      // Log FormData content
      formData.forEach((value, key) => {
        console.log(`${key}:`, value);
      });

      this.dataService
        .uploadCertificate(formData, employerId, studentId)
        .subscribe(
          (response) => {
            console.log('Upload response:', response);
            if (response.success) {
              this.snackBar.open(
                'Certificate uploaded successfully.',
                'Close',
                {
                  duration: 3000,
                }
              );
              // Optionally, refresh the list of uploaded certificates
              this.fetchStudentCertificates();
            } else {
              this.snackBar.open('Failed to upload certificate.', 'Close', {
                duration: 3000,
              });
            }
          },
          (error) => {
            console.error('Error uploading certificate:', error);
            this.snackBar.open('Error uploading certificate.', 'Close', {
              duration: 3000,
            });
          }
        );
    } else {
      this.snackBar.open('No file selected.', 'Close', {
        duration: 3000,
      });
    }
  }

  deleteCertificate(element: UploadedCertificate): void {
    const dialogRef = this.dialog.open(ConfirmdialogComponent, {
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        const employerId = this.data.employerId;
        const studentId = this.data.student.user_id;
  
        this.dataService.deleteCertificate(element.file_id, employerId, studentId).subscribe(
          (response) => {
            if (response.success) {
              this.snackBar.open('Certificate deleted successfully.', 'Close', {
                duration: 3000,
              });
              element.file_name = 'Not Available';
              this.changeDetectorRef.detectChanges();
            } else {
              this.snackBar.open('Failed to delete certificate.', 'Close', {
                duration: 3000,
              });
            }
          },
          (error) => {
            console.error('Error deleting certificate:', error);
            this.snackBar.open('Error deleting certificate.', 'Close', {
              duration: 3000,
            });
          }
        );
      }
    });
  } 

  fetchStudentCertificates(): void {
    const studentId = this.data.student.user_id;
    const employerId = this.data.employerId;
    this.dataService.getUploadedCertificates(employerId, studentId).subscribe(
      (response) => {
        if (response.success) {
          this.uploadedCertificates = response.certificates;
          this.changeDetectorRef.detectChanges(); // Trigger change detection
        } else {
          console.error('Failed to fetch certificates:', response.message);
        }
      },
      (error) => {
        console.error('Error fetching certificates:', error);
      }
    );
  }  

  openEditDialog(element: DtrRecord): void {
    const dialogRef = this.dialog.open(EditDtrDialogComponent, {
      width: '45%',
      data: { ...element }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.updateTimeInOut(result);
      }
    });
  }

  updateTimeInOut(data: DtrRecord): void {
    const { dtr_id, time_in, time_out, user_id } = data;
    const employerId = this.data.employerId;
  
    // Update time_in
    this.dataService.updateTimeIn(dtr_id, time_in, employerId, user_id).subscribe(
      response => {
        if (response.success) {
          this.snackBar.open('Time In updated successfully.', 'Close', { duration: 3000 });
        } else {
          this.snackBar.open('Failed to update Time In.', 'Close', { duration: 3000 });
        }
      },
      error => {
        console.error('Error updating Time In:', error);
        this.snackBar.open('Error updating Time In.', 'Close', { duration: 3000 });
      }
    );
  
    // Update time_out
    this.dataService.updateTimeOut(dtr_id, time_out, employerId, user_id).subscribe(
      response => {
        if (response.success) {
          this.snackBar.open('Time Out updated successfully.', 'Close', { duration: 3000 });
          this.fetchStudentDtrRecords(); // Refresh the records
        } else {
          this.snackBar.open('Failed to update Time Out.', 'Close', { duration: 3000 });
        }
      },
      error => {
        console.error('Error updating Time Out:', error);
        this.snackBar.open('Error updating Time Out.', 'Close', { duration: 3000 });
      }
    );
  }
  

}
