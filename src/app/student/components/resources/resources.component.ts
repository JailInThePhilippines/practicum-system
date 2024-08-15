import { Component, OnInit } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import JSZip from 'jszip';
import { saveAs } from 'file-saver';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ChangeDetectorRef } from '@angular/core';

interface Document {
  name: string;
  id: string;
  type: string;
  status: 'complete' | 'incomplete';
  fileName?: string;
}

@Component({
  selector: 'app-resources',
  templateUrl: './resources.component.html',
  styleUrls: ['./resources.component.css'],
})
export class ResourcesComponent implements OnInit {
  uploadedFiles: any = {}; // Define the uploadedFiles property
  documents: Document[] = [
    { name: 'Endorsement Letter', id: 'endorsementLetterUpload', type: 'endorsement', status: 'incomplete' },
    { name: 'Application Letter', id: 'applicationLetterUpload', type: 'application', status: 'incomplete' },
    { name: 'Parent or Guardian Waiver', id: 'parentOrGuardianUpload', type: 'waiver', status: 'incomplete' },
    { name: 'Acceptance Letter', id: 'acceptanceLetterUpload', type: 'acceptance', status: 'incomplete' },
    { name: 'Resume', id: 'resumeUpload', type: 'resume', status: 'incomplete' },
    { name: 'MOA', id: 'moaUpload', type: 'moa', status: 'incomplete' },
  ];

  displayedColumns: string[] = ['name', 'fileName', 'status', 'upload'];
  dataSource = new MatTableDataSource<Document>(this.documents);

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private snackBar: MatSnackBar,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.fetchUploadedFiles();
  }

  fetchUploadedFiles(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      this.dataService.getUploadedFiles(userId).subscribe(
        (response) => {
          console.log('Uploaded files:', response);
          this.uploadedFiles = response.file_names;
          this.updateDocumentStatus();
        },
        (error) => {
          console.error('Error fetching uploaded files:', error);
        }
      );
    } else {
      console.error('User ID not available.');
    }
  }

  updateDocumentStatus() {
    this.documents.forEach(doc => {
      const matchingFile = this.uploadedFiles.find((file: any) => {
        return this.matchDocumentType(file.document_type, doc.type);
      });
  
      if (matchingFile) {
        doc.status = 'complete';
        doc.fileName = matchingFile.file_name; // Update the fileName property
      } else {
        doc.status = 'incomplete';
        doc.fileName = ''; // Reset the fileName if not found
      }
    });
    this.dataSource.data = this.documents;
  }

  matchDocumentType(uploadedType: string, documentType: string): boolean {
    const typeMapping: { [key: string]: string } = {
      'Endorsement Letter': 'endorsement',
      'Application Letter': 'application',
      "Parent's Consent": 'waiver',
      'Acceptance Letter': 'acceptance',
      'Resume': 'resume',
      'MOA': 'moa',
    };
    return typeMapping[uploadedType] === documentType;
  }

  onFileSelected(event: any, documentType: string) {
    const fileInput = event.target;
    const file: File = fileInput.files[0];
    const formData = new FormData();
    formData.append('file', file);

    const userId = this.authService.getCurrentUserId();

    if (userId !== null) {
      let uploadMethod;
      switch (documentType) {
        case 'endorsement':
          uploadMethod = this.dataService.uploadEndorsementLetter;
          break;
        case 'application':
          uploadMethod = this.dataService.uploadApplicationLetter;
          break;
        case 'moa':
          uploadMethod = this.dataService.uploadMOA;
          break;
        case 'waiver':
          uploadMethod = this.dataService.uploadParentsConsentLetter;
          break;
        case 'acceptance':
          uploadMethod = this.dataService.uploadAcceptanceLetter;
          break;
        case 'resume':
          uploadMethod = this.dataService.uploadResume;
          break;
        default:
          console.error('Unsupported document type:', documentType);
          return;
      }

      uploadMethod.call(this.dataService, formData, userId).subscribe(
        (response) => {
          console.log('File upload successful:', response);
          this.snackBar.open('File Uploaded Successfully.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });

          // Update the status of the document immediately
          const document = this.documents.find(doc => doc.type === documentType);
          if (document) {
            document.status = 'complete';
          }
          this.dataSource.data = this.documents;
          this.cdr.detectChanges();

          this.fetchUploadedFiles();
        },
        (error) => {
          console.error('Error uploading file:', error);
          this.snackBar.open('Error Uploading File.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        }
      );
    } else {
      console.error('User ID not available.');
    }
  }

  openFileExplorer(fileInputId: string) {
    const fileInputElement = document.getElementById(fileInputId);
    if (fileInputElement) {
      fileInputElement.click();
    }
  }

  downloadAllLetters() {
    const zip = new JSZip();
    const files = [
      { name: 'Letter of Endorsement.docx', path: '/assets/Letter of Endorsement.docx' },
      { name: 'Letter of Application.docx', path: '/assets/Letter of Application.docx' },
      { name: 'Parent Consent.docx', path: '/assets/Parent Consent.docx' },
      { name: 'MOA.docx', path: '/assets/MOA.docx' },
      { name: 'Letter of Acceptance.docx', path: '/assets/Letter of Acceptance.docx' }
    ];

    const fetchFile = (file: { name: string, path: string }) => {
      return fetch(file.path)
        .then(response => response.blob())
        .then(blob => {
          zip.file(file.name, blob);
        });
    };

    Promise.all(files.map(file => fetchFile(file)))
      .then(() => {
        return zip.generateAsync({ type: 'blob' });
      })
      .then(content => {
        saveAs(content, 'all_letters.zip');
      })
      .catch(error => {
        console.error('Error creating ZIP file:', error);
      });
  }
}