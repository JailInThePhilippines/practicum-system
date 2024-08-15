import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';

interface FileData {
  id?: number;
  name: string;
  size: number;
  action: string;
  report_status: string;
}

@Component({
  selector: 'app-final-report',
  templateUrl: './final-report.component.html',
  styleUrls: ['./final-report.component.css'],
})
export class FinalReportComponent implements OnInit {
  displayedColumns: string[] = ['fileUploaded', 'report_status', 'delete'];
  dataSource: FileData[] = [];
  userId!: number;

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

  loadUploadedFiles() {
    this.dataService.getFinalReportForStudent(this.userId).subscribe(
      (files: any[]) => {
        this.dataSource = files.map((file) => ({
          id: file.file_id,
          name: file.file_name,
          size: file.size,
          action: 'upload',
          report_status: file.report_status,
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
  
    this.dataService.uploadFinalReport(formData, this.userId).subscribe(
      (response) => {
        this.dataSource = [];
  
        for (let file of files) {
          this.dataSource.push({
            id: response.file_id,
            name: file.name,
            size: file.size,
            action: 'upload',
            report_status: response.report_status,
          });
        }
        this.dataSource = [...this.dataSource];
  
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
      const dialogRef = this.dialog.open(ConfirmdialogComponent, {});

      dialogRef.afterClosed().subscribe(result => {
        if (result) {
          this.dataService.deleteFinalReport(file.id!, this.userId).subscribe(
            (response) => {
              this.snackBar.open('File Deleted Successfully', 'Close', {
                duration: 3000,
              });
              this.dataSource = this.dataSource.filter(
                (item) => item.id !== file.id
              );
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
      case 'Cleared':
        return 'text-success';
      case 'Not Cleared':
        return 'text-danger';
      case 'Currently Verifying':
        return 'text-warning';
      case 'Not Yet Cleared':
        return 'text-warning';
      default:
        return '';
    }
  }

  isAnyFileCleared(): boolean {
    return this.dataSource.some(file => file.report_status === 'Cleared');
  }
}
