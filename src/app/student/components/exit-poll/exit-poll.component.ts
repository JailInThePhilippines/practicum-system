import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-exit-poll',
  templateUrl: './exit-poll.component.html',
  styleUrls: ['./exit-poll.component.css'],
})
export class ExitPollComponent implements OnInit {
  isEditing: boolean = false;
  exitPollData: any = {
    user_id: null,
    student_name: '',
    course_and_year: '',
    name_of_company: '',
    assigned_position: '',
    department: '',
    job_description: '',
    supervisor_name: '',
    ojt_duration: '',
    total_hours: null,
    work_related_to_academic_program: null,
    orientation_on_company_organization: null,
    given_job_description: null,
    work_hours_clear: null,
    felt_safe_and_secure: null,
    no_difficulty_going_to_and_from_work: null,
    provided_with_allowance: null,
    allowance_amount: '',
    achievement_1_description: '',
    achievement_1_rating: null,
    achievement_2_description: '',
    achievement_2_rating: null,
    achievement_3_description: '',
    achievement_3_rating: null,
    achievement_4_description: '',
    achievement_4_rating: null,
    achievement_5_description: '',
    achievement_5_rating: null,
    overall_training_experience: '',
    improvement_suggestion: '',
  };
  isSubmitted: boolean = false;

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit() {
    this.exitPollData.user_id = this.authService.getCurrentUserId();
    this.getExitPoll();
  }

  onSubmit() {
    this.convertNumberToBoolean(this.exitPollData);

    this.dataService.insertExitPoll(this.exitPollData).subscribe(
      (response) => {
        console.log('Exit poll submitted successfully', response);
        this.isSubmitted = true; // Set the flag to true on successful submission
        this.clearInputs();
        this.snackBar.open('Exit poll submitted successfully', 'Dismiss', {
          duration: 3000,
        });
      },
      (error) => {
        console.error('Error submitting exit poll', error);
        this.snackBar.open('Error submitting exit poll', 'Dismiss', {
          duration: 3000,
        });
      }
    );
  }

  clearInputs() {
    this.exitPollData = {
      user_id: this.authService.getCurrentUserId(),
      student_name: '',
      course_and_year: '',
      name_of_company: '',
      assigned_position: '',
      department: '',
      job_description: '',
      supervisor_name: '',
      ojt_duration: '',
      total_hours: null,
      work_related_to_academic_program: null,
      orientation_on_company_organization: null,
      given_job_description: null,
      work_hours_clear: null,
      felt_safe_and_secure: null,
      no_difficulty_going_to_and_from_work: null,
      provided_with_allowance: null,
      allowance_amount: '',
      achievement_1_description: '',
      achievement_1_rating: null,
      achievement_2_description: '',
      achievement_2_rating: null,
      achievement_3_description: '',
      achievement_3_rating: null,
      achievement_4_description: '',
      achievement_4_rating: null,
      achievement_5_description: '',
      achievement_5_rating: null,
      overall_training_experience: '',
      improvement_suggestion: '',
    };
  }

  getExitPoll() {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      this.dataService.getExitPoll(userId).subscribe(
        (exitPoll) => {
          if (exitPoll) {
            console.log('Exit poll retrieved successfully', exitPoll);
            this.exitPollData = exitPoll;
            this.isSubmitted = true; // Set the flag to true if data is retrieved
            this.convertBooleanToNumber(this.exitPollData);
          } else {
            console.log('No exit poll found for this user');
            this.snackBar.open('No exit poll found', 'Dismiss', {
              duration: 3000,
            });
          }
        },
        (error) => {
          console.error('Error retrieving exit poll', error);
          this.snackBar.open('Error retrieving exit poll', 'Dismiss', {
            duration: 3000,
          });
        }
      );
    } else {
      console.error('User ID is null');
      this.snackBar.open('User not authenticated', 'Dismiss', {
        duration: 3000,
      });
    }
  }
  
  convertBooleanToNumber(data: any) {
    const booleanFields = [
      'work_related_to_academic_program',
      'orientation_on_company_organization',
      'given_job_description',
      'work_hours_clear',
      'felt_safe_and_secure',
      'no_difficulty_going_to_and_from_work',
      'provided_with_allowance',
    ];
    booleanFields.forEach((field) => {
      if (data[field] !== null && data[field] !== undefined) {
        data[field] = data[field] ? 1 : 0;
      }
    });
  }
  convertNumberToBoolean(data: any) {
    const booleanFields = [
      'work_related_to_academic_program',
      'orientation_on_company_organization',
      'given_job_description',
      'work_hours_clear',
      'felt_safe_and_secure',
      'no_difficulty_going_to_and_from_work',
      'provided_with_allowance',
    ];
    booleanFields.forEach((field) => {
      if (data[field] !== null && data[field] !== undefined) {
        data[field] = data[field] === 1;
      }
    });
  }

  onEdit() {
    this.isEditing = true;
  }

  onSave() {
    this.convertNumberToBoolean(this.exitPollData);

    this.dataService.editExitPoll(this.exitPollData).subscribe(
      (response) => {
        console.log('Exit poll updated successfully', response);
        this.isSubmitted = true;
        this.isEditing = false;
        this.snackBar.open('Exit poll updated successfully', 'Dismiss', {
          duration: 3000,
        });
      },
      (error) => {
        console.error('Error updating exit poll', error);
        this.snackBar.open('Error updating exit poll', 'Dismiss', {
          duration: 3000,
        });
      }
    );
  }

}
