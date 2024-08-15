import { Component, OnInit, ViewChild } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatTableDataSource } from '@angular/material/table';
import { MatPaginator } from '@angular/material/paginator';

@Component({
  selector: 'app-employers-feedback',
  templateUrl: './employers-feedback.component.html',
  styleUrls: ['./employers-feedback.component.css'],
})
export class EmployersFeedbackComponent implements OnInit {
  displayedColumns: string[] = ['category', 'comment'];
  subjectDisplayedColumns: string[] = ['subject', 'score'];
  criteriaDisplayedColumns: string[] = ['criteria', 'description', 'score'];
  dataSource: MatTableDataSource<any> = new MatTableDataSource();
  subjectDataSource: MatTableDataSource<any> = new MatTableDataSource();
  criteriaDataSource: MatTableDataSource<any> = new MatTableDataSource();

  employerComments: any = {};

  @ViewChild(MatPaginator) paginator!: MatPaginator;

  constructor(
    private dataService: DataService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    const studentId = this.authService.getCurrentUserId(); // Assuming student ID is used here
    if (studentId) {
      this.dataService.getEmployerFeedback(studentId).subscribe(
        (response: any[]) => {
          if (response) {
            if (response.length > 0) {
              const feedback = response[0];
              this.employerComments = {
                employerName: feedback['supervisor'],
                officeAssigned: feedback['office_department_branch'],
                hoursWorked: feedback['hours_worked'],
              };

              const detailedFeedback = [
                { category: 'Major Strong Points', comment: feedback['major_strongpoints'] },
                { category: 'Major Weak Points', comment: feedback['major_weakpoints'] },
                { category: 'Other Comments', comment: feedback['other_comments'] },
                { category: 'Suggestions for Strong Points', comment: feedback['suggestions_strongpoints'] },
                { category: 'Suggestions for Weak Points', comment: feedback['suggestions_weakpoints'] },
                { category: 'Recommendation', comment: feedback['recommendation'] },
                { category: 'Overall Performance', comment: feedback['overall_performance'] },
              ];

              this.dataSource.data = detailedFeedback;

              const subjectFeedback = [
                { subject: 'Knowledge', score: feedback['knowledge_score'] },
                { subject: 'Skills', score: feedback['skills_score'] },
                { subject: 'Attitude', score: feedback['attitude_score'] },
              ];

              this.subjectDataSource.data = subjectFeedback;

              const criteriaFeedback = [
                { criteria: 'Knowledge', description: 'Identifies problems, gathers data related to the problem, analyzes the data gathered and selects appropriate actions.', score: feedback['knowledge_criteria_1'] },
                { criteria: 'Knowledge', description: 'Sets priorities in the workplace based on the identified needs.', score: feedback['knowledge_criteria_2'] },
                { criteria: 'Knowledge', description: 'Formulates plan based on priority needs and problems in the workplace.', score: feedback['knowledge_criteria_3'] },
                { criteria: 'Knowledge', description: 'Promotes safety measures in all aspects of the job assigned to him/her.', score: feedback['knowledge_criteria_4'] },
                { criteria: 'Knowledge', description: 'Applies appropriate IT/CS principles on the tasks on hand.', score: feedback['knowledge_criteria_5'] },
                { criteria: 'Skills', description: 'Analyzes the tasks assigned to him/her.', score: feedback['skills_criteria_1'] },
                { criteria: 'Skills', description: 'Works with thoroughness and accuracy, orderliness and neatness.', score: feedback['skills_criteria_2'] },
                { criteria: 'Skills', description: 'Sets systems objectives in their order of priority.', score: feedback['skills_criteria_3'] },
                { criteria: 'Skills', description: 'Has the initiative and ingenuity in finding ways and means to accomplish objectives.', score: feedback['skills_criteria_4'] },
                { criteria: 'Skills', description: 'Relates IT/CS principles correctly to the assigned task.', score: feedback['skills_criteria_5'] },
                { criteria: 'Skills', description: 'Prepares and administers necessary and appropriate evaluation for the accomplished work.', score: feedback['skills_criteria_6'] },
                { criteria: 'Skills', description: 'Maintains accurate and updated documentation of the work done.', score: feedback['skills_criteria_7'] },
                { criteria: 'Skills', description: 'Provides safety measures for the prevention of accidents.', score: feedback['skills_criteria_8'] },
                { criteria: 'Attitude', description: 'Reports in the workplace on time regularly.', score: feedback['attitude_criteria_1'] },
                { criteria: 'Attitude', description: 'Never leaves the area without permission from his/her superior.', score: feedback['attitude_criteria_2'] },
                { criteria: 'Attitude', description: 'Practices good grooming.', score: feedback['attitude_criteria_3'] },
                { criteria: 'Attitude', description: 'Carries self with dignity and respect, projects a positive self-image.', score: feedback['attitude_criteria_4'] },
                { criteria: 'Attitude', description: 'Observes personal and professional decorum.', score: feedback['attitude_criteria_5'] },
                { criteria: 'Attitude', description: 'Establishes friendliness but not familiarity.', score: feedback['attitude_criteria_6'] },
                { criteria: 'Attitude', description: 'Gives due respect to the superiors; always tactful when dealing with them.', score: feedback['attitude_criteria_7'] },
                { criteria: 'Attitude', description: 'Behaves in accordance with the set policies and standards of the university and company.', score: feedback['attitude_criteria_8'] },
                { criteria: 'Attitude', description: 'Works collaboratively and cooperates with other members of the company as necessary.', score: feedback['attitude_criteria_9'] },
                { criteria: 'Attitude', description: 'Works harmoniously with others towards overall efficiency of the organization.', score: feedback['attitude_criteria_10'] },
                { criteria: 'Attitude', description: 'Accepts constructive criticisms and suggestions given by superior and co-worker.', score: feedback['attitude_criteria_11'] },
                { criteria: 'Attitude', description: 'Helps in keeping the immediate environment clean and orderly.', score: feedback['attitude_criteria_12'] },
                { criteria: 'Attitude', description: 'Submits requirements on time.', score: feedback['attitude_criteria_13'] },
              ];

              this.criteriaDataSource.data = criteriaFeedback;
              this.criteriaDataSource.paginator = this.paginator;
            }
          }
        },
        (error) => {
          console.error('Error fetching employer feedback', error);
        }
      );
    }
  }
}