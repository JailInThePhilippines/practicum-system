import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css'],
})
export class DashboardComponent implements OnInit {
  requirementStatus: any[] = [];
  announcements: any[] = [];
  documentationStatus: string = '';

  constructor(
    private dataService: DataService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    const userId = this.authService.getCurrentUserId();

    if (userId) {
      this.dataService.getRequirementStatusForStudent(userId).subscribe(
        (requirementStatusData) => {
          this.requirementStatus = requirementStatusData;
          console.log('Requirement status:', this.requirementStatus);
        },
        (requirementStatusError) => {
          console.error('Error fetching requirement status:', requirementStatusError);
        }
      );

      this.dataService.getAnnouncementsForStudent(userId).subscribe(
        (announcementsData) => {
          this.announcements = announcementsData.sort(
            (a, b) =>
              new Date(b.announcement_timestamp).getTime() -
              new Date(a.announcement_timestamp).getTime()
          );
          console.log('Announcements:', this.announcements);
        },
        (announcementsError) => {
          console.error('Error fetching announcements:', announcementsError);
        }
      );

      this.dataService.getDocumentationForStudent(userId).subscribe(
        (documentationData) => {
          if (documentationData.length > 0) {
            this.documentationStatus = documentationData[0].documentation_status;
          }
          console.log('Documentation status:', this.documentationStatus);
        },
        (documentationError) => {
          console.error('Error fetching documentation status:', documentationError);
        }
      );
    }
  }

  getStatusColor(status: string): string {
    switch (status) {
      case 'Cleared':
        return 'green';
      case 'Not Cleared':
        return 'red';
      case 'Currently Verifying':
        return '#FFBF00';
      case 'Not Yet Cleared':
        return '#E49B0F';
      default:
        return 'inherit';
    }
  }

}