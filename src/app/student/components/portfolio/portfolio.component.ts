import { Component, OnInit } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { forkJoin } from 'rxjs';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDialog } from '@angular/material/dialog';
import { ConfirmdialogComponent } from '../../../confirmdialog/confirmdialog.component';

@Component({
  selector: 'app-portfolio',
  templateUrl: './portfolio.component.html',
  styleUrls: ['./portfolio.component.css'],
})
export class PortfolioComponent implements OnInit {
  education: string = '';
  school: string = '';
  year: string = '';
  skills: string = '';
  proficiency: string = '';
  educationRecords: any[] = [];
  skillsRecords: any[] = [];
  educationFetched: boolean = false;
  skillsFetched: boolean = false;

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private snackBar: MatSnackBar,
    public dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.fetchPortfolioData();
  }

  fetchPortfolioData(): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      forkJoin([
        this.dataService.getEducationRecords(userId),
        this.dataService.getSkillRecords(userId),
      ]).subscribe(
        ([educationResponse, skillsResponse]) => {
          if (educationResponse.success) {
            this.educationRecords = educationResponse.education_records || [];
            this.educationFetched = true;
            console.log('Fetched Education Records:', this.educationRecords);
          } else {
            console.error(educationResponse.message);
          }

          if (skillsResponse.success) {
            this.skillsRecords = skillsResponse.skills_records.map(
              (record: any) => ({
                ...record,
                skills: JSON.parse(record.skills).join(', '),
                proficiency: JSON.parse(record.proficiency).join(', '),
              })
            );
            this.skillsFetched = true;
            console.log('Fetched Skills Records:', this.skillsRecords);
          } else {
            console.error(skillsResponse.message);
          }
        },
        (error) => {
          console.error(error);
        }
      );
    } else {
      console.error('User ID not available');
    }
  }

  updateEducation(): void {
    if (!this.education || !this.school || !this.year) {
      this.snackBar.open('Please fill in all education fields.', 'Close', {
        duration: 3000,
        horizontalPosition: 'center',
        verticalPosition: 'bottom',
        panelClass: ['custom-snackbar'],
      });
      return;
    }

    const user_id = this.authService.getCurrentUserId();
    if (user_id) {
      const educationData = {
        education: this.education,
        school: this.school,
        year: this.year,
        user_id: user_id,
      };

      console.log('Education Data:', educationData);

      this.dataService.recordEducationPortfolio(educationData).subscribe(
        (response) => {
          if (response.success) {
            this.snackBar.open('Education Recorded.', 'Close', {
              duration: 3000,
              horizontalPosition: 'center',
              verticalPosition: 'bottom',
              panelClass: ['custom-snackbar'],
            });
            const newEducationRecord = {
              education: response.education,
              school: response.school,
              year: response.year,
            };

            // Add the newly created record to the list
            this.educationRecords.push(newEducationRecord);
            console.log('New Education Record Added:', newEducationRecord);

            // Clear input fields after successful submission
            this.education = '';
            this.school = '';
            this.year = '';
          } else {
            console.error(response.message);
          }
        },
        (error) => {
          console.error(error); // You can handle errors here
        }
      );
    } else {
      console.error('User ID not available');
    }
  }

  updateSkills(): void {
    if (!this.skills || !this.proficiency) {
      this.snackBar.open('Please fill in all skills fields.', 'Close', {
        duration: 3000,
        horizontalPosition: 'center',
        verticalPosition: 'bottom',
        panelClass: ['custom-snackbar'],
      });
      return;
    }

    const user_id = this.authService.getCurrentUserId();
    if (user_id) {
      // Split skills and proficiency strings into arrays
      const skillsArray = this.skills.split(',').map((skill) => skill.trim());
      const proficiencyArray = this.proficiency
        .split(',')
        .map((proficiency) => proficiency.trim());

      // Combine skills and proficiency into array of objects
      const skillsData = {
        skills: skillsArray.map((skill, index) => ({
          name: skill,
          proficiency: proficiencyArray[index] || null,
        })),
        user_id: user_id,
      };

      console.log('Skills Data:', skillsData); // Log skillsData here

      this.dataService.recordSkillsPortfolio(skillsData).subscribe(
        (response) => {
          if (response.success) {
            this.snackBar.open('Skills Recorded.', 'Close', {
              duration: 3000,
              horizontalPosition: 'center',
              verticalPosition: 'bottom',
              panelClass: ['custom-snackbar'],
            });
            const newSkillsRecord = {
              skills: response.skills,
              proficiency: response.proficiency,
            };

            // Add the newly created record to the list
            this.skillsRecords.push(newSkillsRecord);
            console.log('New Skills Record Added:', newSkillsRecord);

            // Clear input fields after successful submission
            this.skills = '';
            this.proficiency = '';
          } else {
            console.error(response.message);
          }
        },
        (error) => {
          console.error(error); // You can handle errors here
        }
      );
    } else {
      console.error('User ID not available');
    }
  }

  confirmDelete(record: any, type: string): void {
    const dialogRef = this.dialog.open(ConfirmdialogComponent);
    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        this.deleteRecord(record, type);
      }
    });
  }

  deleteRecord(record: any, type: string): void {
    const userId = this.authService.getCurrentUserId();
    if (userId) {
      if (type === 'education') {
        this.dataService
          .deleteEducation(record.portfolio_education_id, userId)
          .subscribe(
            (response) => {
              if (response.success) {
                this.snackBar.open(
                  'Education record deleted successfully',
                  'Close',
                  {
                    duration: 2000,
                  }
                );
                this.educationRecords = this.educationRecords.filter(
                  (r) => r !== record
                );
              } else {
                console.error(response.message);
              }
            },
            (error) => {
              console.error(error);
              this.snackBar.open('Failed to delete education record', 'Close', {
                duration: 2000,
              });
            }
          );
      } else if (type === 'skills') {
        this.dataService
          .deleteSkill(record.portfolio_skills_id, userId)
          .subscribe(
            (response) => {
              if (response.success) {
                this.snackBar.open(
                  'Skills record deleted successfully',
                  'Close',
                  {
                    duration: 2000,
                  }
                );
                this.skillsRecords = this.skillsRecords.filter(
                  (r) => r !== record
                );
              } else {
                console.error(response.message);
              }
            },
            (error) => {
              console.error(error);
              this.snackBar.open('Failed to delete skills record', 'Close', {
                duration: 2000,
              });
            }
          );
      }
    } else {
      console.error('User ID not available');
    }
  }
}
