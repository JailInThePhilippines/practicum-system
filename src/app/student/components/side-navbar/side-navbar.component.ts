import { Component } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { Router } from '@angular/router';
import { DataService } from '../../../data.service';

@Component({
  selector: 'app-side-navbar',
  templateUrl: './side-navbar.component.html',
  styleUrls: ['./side-navbar.component.css'], // Use styleUrls instead of styleUrl
})
export class SideNavbarComponent {
  profilePictureUrl: string | null = null; // Property to store profile picture URL
  studentName: string | null = null; // Property to store student name

  constructor(
    private authService: AuthService,
    private router: Router,
    private dataService: DataService // Inject DataService
  ) {}

  isActive(route: string): boolean {
    return this.router.url.includes(route);
  }

  ngOnInit(): void {
    // Load user profile picture URL when component initializes
    this.loadUserProfile();
  }

  loadUserProfile(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      // Fetch student name and profile picture URL
      this.dataService.getStudentName(userId).subscribe(
        (nameData) => {
          console.log('Student Name Response:', nameData);
          if (nameData && nameData.student_name !== null) {
            this.studentName = nameData.student_name;
            console.log('Student Name Set:', this.studentName);
          } else {
            console.error(
              'Failed to load student name:',
              nameData ? nameData.message : 'Unknown error'
            );
          }
        },
        (error) => {
          console.error('Error fetching student name:', error);
        }
      );

      // Fetch profile picture URL
      this.dataService.getProfilePicture(userId).subscribe(
        (response) => {
          console.log('Profile Picture Response:', response);
          if (response && response.success && response.image_path) {
            const imagePath = response.image_path.replace(/^\.\.\//, '');
            this.profilePictureUrl = `${this.dataService.apiUrl}/${imagePath}`;
          }
        },
        (error) => {
          console.error('Error fetching profile picture:', error);
        }
      );
    } else {
      console.error('User ID not found.');
    }
  }

  logout() {
    this.authService.logout();
    // Redirect to the login page
    this.router.navigate(['/student-login']);
  }
}
