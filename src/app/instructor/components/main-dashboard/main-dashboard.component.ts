import { Component, OnInit, ViewChild, AfterViewInit, OnDestroy } from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatPaginator } from '@angular/material/paginator';
import { FilterService } from '../../../filter.service';
import { Subscription } from 'rxjs';
import { MatTableDataSource } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { ViewSubmissionsDialogComponent } from '../view-submissions-dialog/view-submissions-dialog.component';
import { MatSnackBar } from '@angular/material/snack-bar';
import { DocumentInfoDialogComponent } from '../../../document-info-dialog/document-info-dialog.component';

@Component({
  selector: 'app-main-dashboard',
  templateUrl: './main-dashboard.component.html',
  styleUrl: './main-dashboard.component.css'
})
export class MainDashboardComponent implements OnInit, AfterViewInit, OnDestroy {
  displayedColumns: string[] = [
    'school_id',
    'instructor_requirements_status',
    'student_dtr_files_status',
    'student_weekly_accomplishments_status',
    'student_final_reports_status',
    'certificates_of_completion_status',
    'student_exitpolls_status',
    'overall_status'
  ];
  dataSource = new MatTableDataSource<any>();
  searchQuery: string = '';
  filterProgram: string = '';
  filterYear: string = '';
  filterBlock: string = '';
  filterDoneStatus: string = '';

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  filterSubscription!: Subscription;

  constructor(
    private dataService: DataService,
    private authService: AuthService,
    private filterService: FilterService,
    public dialog: MatDialog,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    const instructorId = this.authService.getCurrentInstructorId();
    if (instructorId) {
      this.fetchAssociatedStudents(instructorId);
    }
    this.filterSubscription = this.filterService.filterCriteria$.subscribe(
      (criteria) => {
        this.searchQuery = criteria.searchQuery;
        this.filterProgram = criteria.filterProgram;
        this.filterYear = criteria.filterYear;
        this.filterBlock = criteria.filterBlock;
        this.filterStudents();
      }
    );
  }

  ngOnDestroy(): void {
    if (this.filterSubscription) {
      this.filterSubscription.unsubscribe();
    }
  }

  ngAfterViewInit(): void {
    this.dataSource.paginator = this.paginator;
  }

  fetchAssociatedStudents(instructorId: number): void {
    this.dataService.getAssociatedStudentsForInstructor(instructorId).subscribe(
      (students) => {
        if (Array.isArray(students)) {
          students.forEach((student) => {
            student.done = student.ojt_status === 'done';
            this.dataService.getStudentProfilePictureForInstructor(instructorId, student.user_id).subscribe(
              (result) => {
                student.profile_picture = result.success ? result.image_path : 'assets/default-profile-picture.png';
              },
              (error) => {
                console.error('Error fetching profile picture:', error);
                student.profile_picture = 'assets/default-profile-picture.png';
              }
            );

            // Fetch requirements for each student
            this.dataService.getAllRequirements(instructorId, student.user_id).subscribe(
              (requirements) => {
                student.requirements = requirements;
                student.instructor_requirements_status = this.getRequirementStatus(requirements.instructor_requirements, 'endorsement_status', 'application_status', 'consent_status', 'ccs_status', 'seminar_status', 'sportsfest_status', 'acceptance_status', 'moa_status', 'resume_status');
                student.student_dtr_files_status = this.getRequirementStatus(requirements.student_dtr_files, 'dtr_status');
                student.student_exitpolls_status = this.getRequirementStatus(requirements.student_exitpolls);
                student.student_final_reports_status = this.getRequirementStatus(requirements.student_final_reports, 'report_status');
                student.student_weekly_accomplishments_status = this.getRequirementStatus(requirements.student_weekly_accomplishments, 'weekly_status');
                student.certificates_of_completion_status = this.getRequirementStatus(requirements.certificates_of_completion);
                student.overall_status = this.getOverallStatus(student);
              },
              (error) => {
                console.error('Error fetching requirements:', error);
              }
            );
          });
          this.dataSource.data = students.sort((a, b) => a.student_name.localeCompare(b.student_name));
        } else {
          console.error('Error: Data returned is not an array.');
        }
      },
      (error) => {
        console.error('Error fetching associated students:', error);
      }
    );
  }

  getRequirementStatus(requirements: any[], ...statusKeys: string[]): string {
    if (!requirements || requirements.length === 0) return 'Incomplete';
    if (statusKeys.length === 0) return 'Submitted';
    for (const requirement of requirements) {
      for (const key of statusKeys) {
        if (key === 'moa_status') continue;
        if (requirement[key] !== 'Cleared' && requirement[key] !== 'Approved') {
          return 'Incomplete';
        }
      }
    }
    return 'Submitted';
  }

  getOverallStatus(student: any): string {
    const statuses = [
      student.instructor_requirements_status,
      student.student_dtr_files_status,
      student.student_exitpolls_status,
      student.student_final_reports_status,
      student.student_weekly_accomplishments_status,
      student.certificates_of_completion_status
    ];

    const allSubmitted = statuses.every(status => status === 'Submitted');
    const noneSubmitted = statuses.every(status => status === 'Incomplete');

    if (allSubmitted) {
      return 'Passed';
    } else if (noneSubmitted) {
      return 'Failed';
    } else {
      return 'Incomplete';
    }
  }

  filterStudents(): void {
    this.dataSource.filterPredicate = (data, filter) => {
      const filterObject = JSON.parse(filter);
  
      const matchesSearchQuery = filterObject.searchQuery
        ? data.school_id.toString().includes(filterObject.searchQuery)
        : true;
      const matchesProgram = filterObject.filterProgram
        ? data.program === filterObject.filterProgram
        : true;
      const matchesYear = filterObject.filterYear
        ? data.student_year === filterObject.filterYear
        : true;
      const matchesBlock = filterObject.filterBlock
        ? data.block === filterObject.filterBlock
        : true;
      const matchesDoneStatus = filterObject.filterDoneStatus
        ? data.overall_status === filterObject.filterDoneStatus
        : true;
  
      return matchesSearchQuery && matchesProgram && matchesYear && matchesBlock && matchesDoneStatus;
    };
  
    const filterObject = {
      searchQuery: this.searchQuery,
      filterProgram: this.filterProgram,
      filterYear: this.filterYear,
      filterBlock: this.filterBlock,
      filterDoneStatus: this.filterDoneStatus,
    };
  
    this.dataSource.filter = JSON.stringify(filterObject);
  }

  clearSearch(): void {
    this.searchQuery = '';
    this.filterProgram = '';
    this.filterYear = '';
    this.filterBlock = '';
    this.filterDoneStatus = '';
    this.dataSource.filter = '';
  }

  getProfilePictureUrl(student: any): string {
    return student.profile_picture
      ? 'http://localhost/PractiEase/api/' + student.profile_picture
      : 'assets/default-profile-picture.png';
  }

  getStatusClass(status: string): string {
    switch (status) {
      case 'Submitted':
      case 'Passed':
        return 'status-green';
      case 'Incomplete':
        return 'status-yellow';
      case 'Failed':
        return 'status-red';
      default:
        return '';
    }
  }

  openDocumentInfoDialog(student: any): void {
    this.dialog.open(DocumentInfoDialogComponent, {
      data: student.requirements
    });
  }

}