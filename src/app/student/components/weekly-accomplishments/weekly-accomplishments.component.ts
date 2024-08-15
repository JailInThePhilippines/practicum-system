import { Component, OnInit, ViewChild } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';
import { MatDialog } from '@angular/material/dialog';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';

interface FileData {
  id?: number;
  name: string;
  size: number;
  action: string;
  weekly_status: string;
}

@Component({
  selector: 'app-weekly-accomplishments',
  templateUrl: './weekly-accomplishments.component.html',
  styleUrl: './weekly-accomplishments.component.css'
})
export class WeeklyAccomplishmentsComponent implements OnInit {
  displayedColumns: string[] = ['fileUploaded', 'weekly_status', 'delete'];
  dataSource = new MatTableDataSource<FileData>();
  userId!: number;

  @ViewChild(MatPaginator) paginator!: MatPaginator;

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog
  ) {}

  ngOnInit() {
    this.userId = this.authService.getCurrentUserId()!;
    this.loadUploadedFiles();
  }

  ngAfterViewInit() {
    this.dataSource.paginator = this.paginator;
  }

  loadUploadedFiles() {
    this.dataService.getWeeklyAccomplishmentsForStudent(this.userId).subscribe(
      (files: any[]) => {
        this.dataSource.data = files.map((file) => ({
          id: file.file_id,
          name: file.file_name,
          size: file.size,
          action: 'upload',
          weekly_status: file.weekly_status,
        }));
      },
      (error) => {
        console.error('Error fetching uploaded files:', error);
      }
    );
  }

  onFileUpload(event: any) {
    const files = event.target.files;
    const formData = new FormData();
  
    for (let file of files) {
      formData.append('file', file, file.name);
    }
  
    this.dataService.uploadWeeklyAccomplishments(formData, this.userId).subscribe(
      (response) => {
        for (let file of files) {
          this.dataSource.data.push({
            id: response.file_id, // Assuming the response contains file_id
            name: file.name,
            size: file.size,
            action: 'upload',
            weekly_status: 'Unverified',
          });
        }
        // Trigger change detection to update the table
        this.dataSource.data = [...this.dataSource.data];
  
        // Show success message
        this.snackBar.open('File Uploaded Successfully', 'Close', {
          duration: 3000,
        });
      },
      (error) => {
        console.error('Error uploading files:', error);
      }
    );
  }

  deleteFile(file: FileData) {
    if (file.id) {
      const dialogRef = this.dialog.open(ConfirmdialogComponent, {
      });

      dialogRef.afterClosed().subscribe(result => {
        if (result) {
          console.log('Attempting to delete file with ID:', file.id!);
          this.dataService.deleteWeeklyAccomplishments(file.id!, this.userId).subscribe(
            (response) => {
              console.log('Delete response:', response);
              this.snackBar.open('File Deleted Successfully', 'Close', {
                duration: 3000,
              });
              this.dataSource.data = this.dataSource.data.filter(
                (item) => item.id !== file.id
              );
              console.log('Updated dataSource:', this.dataSource);
            },
            (error) => {
              console.error('Error deleting file:', error);
            }
          );
        }
      });
    }
  }

  getDocumentStatusClass(status: string): string {
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

}
