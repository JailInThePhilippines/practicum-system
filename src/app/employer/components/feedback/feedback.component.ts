import { Component } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-feedback',
  templateUrl: './feedback.component.html',
  styleUrls: ['./feedback.component.css'],
})
export class FeedbackComponent {
  feedbackData: any = {}; // Define feedbackData property
  overall_performance: string = '';
  major_strongpoints: string = '';
  major_weakpoints: string = '';
  other_comments: string = '';
  suggestions_strongpoints: string = '';
  suggestions_weakpoints: string = '';
  recommendation: string = '';

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private snackBar: MatSnackBar
  ) {}

  submitFeedback() {
    // Get the current employer ID from AuthService
    const employerId = this.authService.getCurrentEmployerId();
    if (employerId) {
      // Add employer ID to feedback data
      this.feedbackData.employer_id = employerId; // Use snake_case for consistency with the backend
      this.feedbackData.overall_performance = this.overall_performance;
      this.feedbackData.major_strongpoints = this.major_strongpoints;
      this.feedbackData.major_weakpoints = this.major_weakpoints;
      this.feedbackData.other_comments = this.other_comments;
      this.feedbackData.suggestions_strongpoints =
        this.suggestions_strongpoints;
      this.feedbackData.suggestions_weakpoints = this.suggestions_weakpoints;
      this.feedbackData.recommendation = this.recommendation;
      // Call the data service to insert the feedback
      this.dataService.insertEmployerFeedback(this.feedbackData).subscribe(
        (response) => {
          // Handle success response
          console.log('Feedback submitted successfully:', response);
          this.snackBar.open('Feedback Submitted Successfully!', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
          });
          this.feedbackData = {};
          this.overall_performance = '';
          this.major_strongpoints = '';
          this.major_weakpoints = '';
          this.other_comments = '';
          this.suggestions_strongpoints = '';
          this.suggestions_weakpoints = '';
          this.recommendation = '';
        },
        (error) => {
          // Handle error response
          console.error('Error submitting feedback:', error);
          this.snackBar.open('Could not submit feedback. Please check all the required fields and try again.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
          });
        }
      );
    } else {
      console.error('Employer ID not found');
      // Optionally, handle the case where employer ID is not found
    }
  }
}
