import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { DomSanitizer, SafeUrl } from '@angular/platform-browser';
import { FormControl, FormGroup } from '@angular/forms';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
})
export class ProfileComponent implements OnInit {
  studentProfile: any;
  profilePicture: SafeUrl = '';
  program: string = '';
  block: string = '';
  mobileNumber: string = '';
  selectedFile: File | null = null;
  sanitizedProfilePicture: SafeUrl = '';
  apiUrl: string = 'https://practiease.site/PractiEase/api/';
  profileForm!: FormGroup;
  profileSavedSuccessfully: boolean = false;
  companyAddress: string = '';

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private sanitizer: DomSanitizer,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.loadUserProfile();
    this.profileForm = new FormGroup({
      profilePicture: new FormControl(null),
    });
  }

  loadUserProfile(): void {
    if (this.authService.isAuthenticated()) {
      const userId = this.authService.getCurrentUserId();
      if (userId) {
        this.dataService.getStudentProfile(userId).subscribe((profileData) => {
          this.studentProfile = profileData;
        });
        this.dataService.getProfilePicture(userId).subscribe(
          (response) => {
            if (response && response.success) {
              let relativePath = response.image_path;
              const imagePath = relativePath.replace(/^\.\.\//, '');
              const apiUrl = this.apiUrl.endsWith('/')
                ? this.apiUrl.slice(0, -1)
                : this.apiUrl;
              const absoluteUrl = `${apiUrl}/${imagePath}`;
              this.profilePicture = this.sanitizeImageUrl(absoluteUrl);
              console.log(
                'Sanitized Profile Picture URL:',
                this.profilePicture
              );
            } else {
              console.error('Failed to load profile picture.');
            }
          },
          (error) => {
            console.error('Error fetching profile picture:', error);
          }
        );
      } else {
        console.error('User ID not found.');
      }
    } else {
      console.error('User is not authenticated.');
    }
  }

  openFileExplorer(): void {
    console.log('Opening file explorer...');
    const uploadInput = document.getElementById('uploadProfilePicture');
    if (uploadInput) {
      uploadInput.click();
    } else {
      console.error('Upload input element not found.');
    }
  }

  onFileSelected(event: any): void {
    this.selectedFile = event.target.files[0];
    if (this.selectedFile) {
      const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
      if (!allowedTypes.includes(this.selectedFile.type)) {
        this.snackBar.open('Invalid file type. Only PNG and JPG files are allowed.', 'Close', {
          duration: 3000,
          horizontalPosition: 'center',
          verticalPosition: 'bottom',
          panelClass: ['custom-snackbar'],
        });
        return;
      }
  
      const reader: FileReader = new FileReader();
      reader.onload = (e: any) => {
        this.profilePicture = this.sanitizeImageUrl(e.target.result);
        this.sanitizedProfilePicture = this.profilePicture;
        if (this.profileForm.get('profilePicture')) {
          this.profileForm.get('profilePicture')!.setValue(this.selectedFile);
        }
      };
      reader.readAsDataURL(this.selectedFile);
    }
  }
  

  saveInformation(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      if (this.mobileNumber && this.companyAddress) {
        this.dataService
          .updateStudentInformation(
            userId,
            this.mobileNumber,
            this.companyAddress
          )
          .subscribe(
            (response) => {
              console.log(
                'Mobile number and company address updated successfully:',
                response
              );
              this.snackBar.open(
                'Mobile number and company address updated successfully.',
                'Close',
                {
                  duration: 3000,
                  horizontalPosition: 'center',
                  verticalPosition: 'bottom',
                  panelClass: ['custom-snackbar'],
                }
              );
              this.loadUserProfile();
            },
            (error) => {
              console.error(
                'Error updating mobile number and company address:',
                error
              );
            }
          );
      } else if (this.mobileNumber) {
        this.dataService
          .updateStudentMobileNumber(userId, this.mobileNumber)
          .subscribe(
            (response) => {
              console.log('Mobile number updated successfully:', response);
              this.snackBar.open(
                'Mobile number updated successfully.',
                'Close',
                {
                  duration: 3000,
                  horizontalPosition: 'center',
                  verticalPosition: 'bottom',
                  panelClass: ['custom-snackbar'],
                }
              );
              this.loadUserProfile();
            },
            (error) => {
              console.error('Error updating mobile number:', error);
            }
          );
      } else if (this.companyAddress) {
        this.dataService
          .updateStudentCompanyAddress(userId, this.companyAddress)
          .subscribe(
            (response) => {
              console.log('Company address updated successfully:', response);
              this.snackBar.open(
                'Company address updated successfully.',
                'Close',
                {
                  duration: 3000,
                  horizontalPosition: 'center',
                  verticalPosition: 'bottom',
                  panelClass: ['custom-snackbar'],
                }
              );
              this.loadUserProfile();
            },
            (error) => {
              console.error('Error updating company address:', error);
            }
          );
      } else {
        console.error('No information to update.');
      }
    } else {
      console.error('User ID not found.');
    }
  }

  sanitizeImageUrl(url: string): SafeUrl {
    return this.sanitizer.bypassSecurityTrustUrl(url);
  }

  // Function to handle profile picture upload
  uploadProfilePicture(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId && this.selectedFile) {
      const formData: FormData = new FormData();
      formData.append('file', this.profileForm.get('profilePicture')!.value);
      formData.append('userId', userId.toString());

      this.dataService.uploadProfilePicture(formData, userId).subscribe(
        (response) => {
          console.log('Profile picture uploaded successfully:', response);
          this.profileSavedSuccessfully = true;
          setTimeout(() => {
            this.profileSavedSuccessfully = false;
          }, 5000);
        },
        (error) => {
          console.error('Error uploading profile picture:', error);
        }
      );
    } else {
      console.error('User ID or selected file not found.');
    }
  }
}
