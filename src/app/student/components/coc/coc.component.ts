import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatTableDataSource } from '@angular/material/table';

interface Certificate {
  file_path: string;
}

@Component({
  selector: 'app-coc',
  templateUrl: './coc.component.html',
  styleUrls: ['./coc.component.css'],
})
export class COCComponent implements OnInit {
  certificateInfo: any = {};
  dataSource = new MatTableDataSource<any>();
  displayedColumns: string[] = ['fileName', 'view'];
  baseUrl: string = 'https://practiease.site/PractiEase/api';

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      this.dataService.getCertificateOfCompletion(userId).subscribe(
        (response) => {
          if (response.success) {
            this.certificateInfo = response.certificate_info;
            this.dataSource.data = [this.certificateInfo];  // Assuming there's only one certificate info object
          } else {
            console.error('Failed to fetch certificate info:', response.message);
          }
        },
        (error) => {
          console.error('Error fetching certificate info:', error);
        }
      );
    } else {
      console.error('User ID not found');
    }
  }

  downloadCertificate(certificate: Certificate) {
    const certificateUrl = certificate.file_path;
    if (certificateUrl) {
      const fullUrl = `${this.baseUrl}/${certificateUrl}`;
      window.open(fullUrl, '_blank');
    } else {
      this.snackBar.open('No certificate found', 'Close', {
        duration: 3000,
        horizontalPosition: 'center',
        verticalPosition: 'bottom',
        panelClass: ['custom-snackbar'],
      });
    }
  }
}
