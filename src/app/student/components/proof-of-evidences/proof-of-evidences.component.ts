import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';
import { MatTableDataSource } from '@angular/material/table';

interface FileCategory {
  category: string;
  file_name?: string;
  type?: string;
}

@Component({
  selector: 'app-proof-of-evidences',
  templateUrl: './proof-of-evidences.component.html',
  styleUrls: ['./proof-of-evidences.component.css'],
})
export class ProofOfEvidencesComponent implements OnInit {
  categories = new MatTableDataSource<FileCategory>([
    { category: 'Activities Documentation', file_name: '', type: '' },
    { category: 'Trainings/Seminars', file_name: '', type: '' },
    { category: 'OJT Documentation', file_name: '', type: '' }
  ]);

  files: any[] = [];
  displayedColumns: string[] = ['category', 'fileName', 'upload', 'action'];

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar,
    private dialog: MatDialog,
    private changeDetectorRef: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.fetchProofOfEvidencesFiles();
  }

  fetchProofOfEvidencesFiles() {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      this.dataService.getProofOfEvidencesFiles(userId).subscribe(
        (response) => {
          const uploadedFiles = response.file_names || [];
          // Update categories data source
          this.categories.data = this.categories.data.map(category => {
            const file = uploadedFiles.find((f: FileCategory) => f.category === category.category);
            return file ? {...category, ...file} : category;
          });
        },
        (error) => {
          console.error('Failed to fetch proof of evidences files:', error);
        }
      );
    }
  }

  openFileExplorer(category: string) {
    let inputElement: HTMLInputElement | null = document.createElement('input');
    inputElement.type = 'file';
    inputElement.accept = 'application/pdf';
    inputElement.multiple = false;

    inputElement.onchange = (event: Event) => {
      const files = (event.target as HTMLInputElement).files;
      if (files && files.length > 0) {
        const file = files[0];
        if (file.type !== 'application/pdf') {
          this.snackBar.open('Only PDF files are allowed', 'Close', {
            duration: 3000,
            panelClass: ['custom-snackbar'],
          });
          return;
        }
        this.uploadFile(file, category);
      }
    };

    if (inputElement) {
      inputElement.click();
    }
  }

  uploadFile(file: File, category: string) {
    console.log(`Starting upload for category: ${category} with file: ${file.name}`);
    if (file) {
      const formData = new FormData();
      formData.append('file', file);
      const userId = this.authService.getCurrentUserId();
      console.log(`User ID: ${userId}`);
      if (userId) {
        let uploadObservable;
        switch (category) {
          case 'Activities Documentation':
            uploadObservable = this.dataService.uploadCCSPicture(formData, userId);
            break;
          case 'Trainings/Seminars':
            uploadObservable = this.dataService.uploadSeminarCertificate(formData, userId);
            break;
          case 'OJT Documentation':
            uploadObservable = this.dataService.uploadSportsfestPicture(formData, userId);
            break;
        }
        console.log(`Upload observable created for category: ${category}`);
        if (uploadObservable) {
          uploadObservable.subscribe(
            (response) => {
              console.log(`Upload response for ${category}: `, response);
              if (response.success) {
                // Update the specific category with the new file name
                this.categories.data = this.categories.data.map(categoryItem => {
                  if (categoryItem.category === category) {
                    return { ...categoryItem, file_name: file.name };
                  }
                  return categoryItem;
                });
                this.snackBar.open('File Uploaded Successfully', 'Close', {
                  duration: 3000,
                  panelClass: ['custom-snackbar'],
                });
                this.changeDetectorRef.detectChanges();  // Ensure change detection is triggered
              } else {
                this.snackBar.open('Upload Failed', 'Close', {
                  duration: 3000,
                  panelClass: ['custom-snackbar'],
                });
              }
            },
            (error) => {
              console.error('Failed to upload file:', error);
              this.snackBar.open('Failed to upload file', 'Close', {
                duration: 3000,
                panelClass: ['custom-snackbar'],
              });
            }
          );
        }
      }
    }
  } 

  deleteFile(file: any) {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);

    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        this.performDeleteFile(file);
      }
    });
  }

  performDeleteFile(file: any) {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      this.dataService.deleteProofOfEvidenceFile(file.file_name, file.category, userId).subscribe(
        (response: any) => {
          if (response.success) {
            // Update the categories data source to clear the file_name for the deleted file's category
            this.categories.data = this.categories.data.map(category => {
              if (category.category === file.category) {
                return { ...category, file_name: '', type: '' }; // Reset file_name and type
              }
              return category;
            });
            this.fetchProofOfEvidencesFiles();
            this.snackBar.open('File Deleted Successfully', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          } else {
            this.snackBar.open('Failed to delete file', 'Close', {
              duration: 3000,
              panelClass: ['custom-snackbar'],
            });
          }
        },
        (error) => {
          this.snackBar.open('Error deleting file', 'Close', {
            duration: 3000,
            panelClass: ['custom-snackbar'],
          });
        }
      );
    }
  }
}