import {
  Component,
  Inject,
  OnInit,
  ViewChild,
  AfterViewInit,
} from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { DataService } from '../../../data.service';
import { MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatTableDataSource } from '@angular/material/table';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';

interface Document {
  name: string;
  path: string | null;
  status: string;
  file_name?: string;
  dtr_status?: string;
  report_status?: string;
  weekly_status?: string;
  file_id?: number;
  documentation_status?: string;
}

interface DtrRecord {
  dtr_id: number;
  time_in: string;
  time_out: string;
  user_id: number;
  school_id: number;
  dtr_status: string;
  hours_worked: string;
}

interface AccomplishmentRecord {
  date: string;
  description_of_activities: string;
  start_time: string;
  end_time: string;
  number_of_hours: string;
  accomplishment_status: string;
}

interface EmployerFeedback {
  knowledge_score: number;
  skills_score: number;
  attitude_score: number;
  overall_performance: string;
}

@Component({
  selector: 'app-view-submissions-dialog',
  templateUrl: './view-submissions-dialog.component.html',
  styleUrls: ['./view-submissions-dialog.component.css'],
})
export class ViewSubmissionsDialogComponent implements OnInit, AfterViewInit {
  profilePictureUrl: string = 'assets/default-profile-picture.png'; // Default profile picture
  signedDocuments: Document[] = [];
  totalHoursWorked: number = 0;
  studentDetails: any = null;
  activitiesDocuments: Document[] = [];
  certificateDocuments: Document[] = [];
  dtrDocuments: Document[] = [];
  finalReport: Document[] = [];
  dtrRecords: DtrRecord[] = [];
  accomplishmentDocuments = new MatTableDataSource<any>();
  documentationDocuments = new MatTableDataSource<Document>();
  accomplishmentRecords: AccomplishmentRecord[] = [];
  employerFeedback = new MatTableDataSource<EmployerFeedback>();
  displayedColumns: string[] = [
    'knowledge_score',
    'skills_score',
    'attitude_score',
    'overall_performance',
  ];
  exitPollData = new MatTableDataSource<any>();
  exitPollColumns: string[] = ['category', 'data'];
  paginatedDtrRecords: DtrRecord[] = [];
  paginatedAccomplishmentRecords: AccomplishmentRecord[] = [];
  pageSize = 5;
  pageIndex = 0;

  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild('weeklyPaginator') weeklyPaginator!: MatPaginator;
  @ViewChild('documentationPaginator') documentationPaginator!: MatPaginator;
  @ViewChild('exitPollPaginator') exitPollPaginator!: MatPaginator;

  constructor(
    public dialogRef: MatDialogRef<ViewSubmissionsDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dataService: DataService,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.fetchStudentDocuments();
    this.fetchStudentProfilePicture();
    this.fetchStudentDtrRecords();
    this.fetchEmployerFeedback();
    this.fetchStudentDetails();
    this.fetchWeeklyAccomplishmentRecords();
    this.fetchDocumentationDocuments();
    this.calculateTotalHoursWorked();
    this.fetchExitPollData();
  }

  fetchStudentDetails(): void {
    const instructorId = this.data.instructorId;
    this.dataService.getAssociatedStudentsForInstructor(instructorId).subscribe(
      (data) => {
        // Assuming the first student is the one we need
        this.studentDetails = data[0];
      },
      (error) => {
        console.error('Error fetching student details:', error);
      }
    );
  }

  fetchExitPollData(): void {
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    this.dataService.getExitPollForInstructor(instructorId, studentId).subscribe(
      (data) => {
        if (data) {
          const achievements = [];
          for (let i = 1; i <= 5; i++) {
            const description = data[`achievement_${i}_description`];
            const rating = data[`achievement_${i}_rating`];
            if (description && rating !== undefined) {
              achievements.push(`${description}: ${rating}%`);
            }
          }
  
          const transformedData = [
            { category: 'Company Name', data: data.name_of_company },
            { category: 'Assigned Position', data: data.assigned_position },
            { category: 'Department', data: data.department },
            { category: 'Job Description', data: data.job_description },
            { category: 'Supervisor Name', data: data.supervisor_name },
            { category: 'OJT Duration', data: data.ojt_duration },
            { category: 'Total Hours', data: data.total_hours },
            { category: 'My scope of work is directly related to the academic program I am pursuing', data: data.work_related_to_academic_program ? 'Yes' : 'No' },
            { category: 'I was given an orientation on the company organization and operations', data: data.orientation_on_company_organization ? 'Yes' : 'No' },
            { category: 'I was given a job description on my specific duties and reporting relationships', data: data.given_job_description ? 'Yes' : 'No' },
            { category: 'My office/work hours were clear and convenient for me', data: data.work_hours_clear ? 'Yes' : 'No' },
            { category: 'I felt safe and secure in my work location and environment', data: data.felt_safe_and_secure ? 'Yes' : 'No' },
            { category: 'I had no difficulty going to and from work', data: data.no_difficulty_going_to_and_from_work ? 'Yes' : 'No' },
            { category: 'The company provided me with an allowance, stipend, or subsidy', data: data.provided_with_allowance ? 'Yes' : 'No' },
            { category: 'Allownace amount', data: data.allowance_amount },
            { category: 'Achievements', data: achievements.join(', ') },
            { category: 'Overall Training Experience', data: data.overall_training_experience },
            { category: 'Improvement Suggestion', data: data.improvement_suggestion },
          ];
  
          this.exitPollData.data = transformedData;
        }
      },
      (error) => {
        console.error('Error fetching exit poll data:', error);
      }
    );
  }

  fetchStudentDocuments(): void {
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
  
    const signedDocumentFetchers = [
      { method: this.dataService.getApplicationLetter, name: 'Application Letter' },
      { method: this.dataService.getResume, name: 'Resume' },
      { method: this.dataService.getParentsConsent, name: 'Parent Consent' },
      { method: this.dataService.getEndorsementLetter, name: 'Endorsement Letter' },
      { method: this.dataService.getAcceptanceLetter, name: 'Acceptance Letter' },
      { method: this.dataService.getMOA, name: 'MOA' },
    ];
  
    const activitiesDocumentFetchers = [
      { method: this.dataService.getSeminarPOE, name: "Trainings/Seminars" },
      { method: this.dataService.getGCEvents, name: 'OJT Documentation' },
      { method: this.dataService.getCCSPOE, name: 'Activities Documentation' },
    ];
  
    const certificateDocumentFetchers = [
      { method: this.dataService.getCertificates, name: 'Certificate of Completion' },
    ];
  
    const dtrDocumentFetchers = [
      { method: this.dataService.getDTRForInstructor, name: 'DTR File' },
    ];
  
    const finalReportFetchers = [
      { method: this.dataService.getFinalReportForInstructor, name: 'Final Report' },
    ];
  
    let fetchedSignedDocuments: Document[] = [];
    let fetchedActivitiesDocuments: Document[] = [];
    let fetchedCertificateDocuments: Document[] = [];
    let fetchedDTRDocuments: Document[] = [];
    let fetchedFinalReport: Document[] = [];
  
    const fetchSignedDocumentsPromises = signedDocumentFetchers.map(
      ({ method, name }) => {
        return method
          .call(this.dataService, instructorId, studentId)
          .toPromise()
          .then(
            (documents: any[] | undefined) => {
              const path = documents && documents.length > 0 ? documents[0].file_path : null;
              fetchedSignedDocuments.push({ name, path, status: 'Not Set' });
            },
            (error: any) => {
              console.error(`Error fetching ${name}:`, error);
            }
          );
      }
    );
  
    const fetchActivitiesDocumentsPromises = activitiesDocumentFetchers.map(
      ({ method, name }) => {
        return method
          .call(this.dataService, instructorId, studentId)
          .toPromise()
          .then(
            (documents: any[] | undefined) => {
              const path = documents && documents.length > 0 ? documents[0].file_path : null;
              fetchedActivitiesDocuments.push({ name, path, status: 'Not Set' });
            },
            (error: any) => {
              console.error(`Error fetching ${name}:`, error);
            }
          );
      }
    );
  
    const fetchCertificateDocumentsPromises = certificateDocumentFetchers.map(
      ({ method, name }) => {
        return method
          .call(this.dataService, instructorId, studentId)
          .toPromise()
          .then(
            (documents: any[] | undefined) => {
              const path = documents && documents.length > 0 ? documents[0].file_path : null;
              fetchedCertificateDocuments.push({ name, path, status: 'Not Set' });
            },
            (error: any) => {
              console.error(`Error fetching ${name}:`, error);
            }
          );
      }
    );
  
    const fetchDTRDocumentsPromises = dtrDocumentFetchers.map(
      ({ method, name }) => {
        return method
          .call(this.dataService, instructorId, studentId)
          .toPromise()
          .then(
            (documents: any[] | undefined) => {
              if (documents && documents.length > 0) {
                documents.forEach((doc) => {
                  fetchedDTRDocuments.push({
                    name,
                    path: doc.file_path,
                    status: 'Not Set',
                    file_name: doc.file_name,
                    dtr_status: doc.dtr_status
                  });
                });
              } else {
                fetchedDTRDocuments.push({ name, path: null, status: 'Not Set' });
              }
            },
            (error: any) => {
              console.error(`Error fetching ${name}:`, error);
            }
          );
      }
    );
  
    const fetchFinalReportPromises = finalReportFetchers.map(
      ({ method, name }) => {
        return method
          .call(this.dataService, instructorId, studentId)
          .toPromise()
          .then(
            (documents: any[] | undefined) => {
              if (documents && documents.length > 0) {
                documents.forEach((doc) => {
                  fetchedFinalReport.push({
                    name,
                    path: doc.file_path,
                    status: doc.report_status || 'Not Set',
                    file_name: doc.file_name,
                    file_id: doc.file_id
                  });
                });
              } else {
                fetchedFinalReport.push({ name, path: null, status: 'Not Set' });
              }
            },
            (error: any) => {
              console.error(`Error fetching ${name}:`, error);
            }
          );
      }
    );
  
    Promise.all([
      ...fetchSignedDocumentsPromises,
      ...fetchActivitiesDocumentsPromises,
      ...fetchCertificateDocumentsPromises,
      ...fetchDTRDocumentsPromises,
      ...fetchFinalReportPromises,
    ])
      .then(() => {
        // Define the desired order
        const order = ['Resume', 'Application Letter', 'Parent Consent', 'Endorsement Letter', 'Acceptance Letter', 'MOA'];
  
        // Sort the fetchedSignedDocuments array based on the desired order
        this.signedDocuments = fetchedSignedDocuments.sort((a, b) => order.indexOf(a.name) - order.indexOf(b.name));
        this.activitiesDocuments = fetchedActivitiesDocuments;
        this.certificateDocuments = fetchedCertificateDocuments;
        this.dtrDocuments = fetchedDTRDocuments;
        this.finalReport = fetchedFinalReport;
  
        this.dataService
          .getRequirementStatusForInstructor(instructorId, studentId)
          .subscribe(
            (statuses: any[]) => {
              if (statuses.length > 0) {
                const status = statuses[0];
                this.signedDocuments.forEach((document) => {
                  switch (document.name) {
                    case 'Application Letter':
                      document.status = status.application_status;
                      break;
                    case 'Resume':
                      document.status = status.resume_status;
                      break;
                    case 'Parent Consent':
                      document.status = status.consent_status;
                      break;
                    case 'Endorsement Letter':
                      document.status = status.endorsement_status;
                      break;
                      case 'Acceptance Letter':
                        document.status = status.acceptance_status;
                        break;
                      case 'MOA':
                        document.status = status.moa_status;
                        break;
                    }
                  });
                  this.activitiesDocuments.forEach((document) => {
                    switch (document.name) {
                      case 'Activities Documentation':
                        document.status = status.ccs_status;
                        break;
                      case 'OJT Documentation':
                        document.status = status.sportsfest_status;
                        break;
                      case 'Trainings/Seminars':
                        document.status = status.seminar_status;
                        break;
                      default:
                        document.status = 'Not Set';
                    }
                  });
                }
              },  
              (error: any) => {
                console.error('Error fetching requirement statuses:', error);
              }
            );
        })
        .catch((error) => {
          console.error('Error fetching documents:', error);
        });
    }

  fetchStudentProfilePicture(): void {
    const studentId = this.data.student.user_id;
    this.dataService
      .getStudentProfilePictureForInstructor(this.data.instructorId, studentId)
      .subscribe(
        (result) => {
          this.profilePictureUrl = result.success
            ? `http://localhost/PractiEase/api/${result.image_path}`
            : 'assets/default-profile-picture.png';
        },
        (error) => {
          console.error('Error fetching profile picture:', error);
          this.profilePictureUrl = 'assets/default-profile-picture.png';
        }
      );
  }

  floorHours(hours: number): number {
    return Math.floor(hours);
  }

  fetchStudentDtrRecords(): void {
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    this.dataService
      .getStudentDTRForInstructor(instructorId, studentId)
      .subscribe(
        (dtrRecords) => {
          this.dtrRecords = dtrRecords;
          this.updatePaginatedDtrRecords();
          this.calculateTotalHoursWorked(); // Recalculate total hours worked after fetching records
        },
        (error) => {
          console.error('Error fetching DTR records:', error);
        }
      );
  }

  calculateTotalHoursWorked(): void {
    this.totalHoursWorked = this.dtrRecords.reduce((total, record) => {
      if (record.time_in && record.time_out && record.dtr_status !== 'Rejected') {
        return total + Math.floor(parseFloat(record.hours_worked) * 100);
      }
      return total;
    }, 0) / 100;
  }

  ngAfterViewInit() {
    this.accomplishmentDocuments.paginator = this.weeklyPaginator;
    this.documentationDocuments.paginator = this.documentationPaginator;
    this.exitPollData.paginator = this.exitPollPaginator; // Connect paginator to exitPollData
    console.log('Paginator set:', this.weeklyPaginator); // Log paginator
  }

  fetchWeeklyAccomplishmentRecords(): void {
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    this.dataService
      .getWeeklyAccomplishmentsForInstructor(instructorId, studentId)
      .subscribe(
        (accomplishmentRecords) => {
          console.log('Fetched Weekly Accomplishments:', accomplishmentRecords); // Log fetched data
          this.accomplishmentDocuments.data = accomplishmentRecords.map(
            (record) => ({
              ...record,
              path: record.file_path, // Ensure path is set correctly
            })
          );
        },
        (error) => {
          console.error('Error fetching accomplishment records:', error);
        }
      );
  }

  fetchDocumentationDocuments(): void {
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    this.dataService
      .getDocumentationForInstructor(instructorId, studentId)
      .subscribe(
        (documentationRecords) => {
          this.documentationDocuments.data = documentationRecords.map(
            (record) => ({
              ...record,
              path: record.file_path, // Ensure path is set correctly
            })
          );
        },
        (error) => {
          console.error('Error fetching documentation records:', error);
        }
      );
  }

  fetchEmployerFeedback(): void {
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    this.dataService
      .getEmployerFeedbackForInstructor(instructorId, studentId)
      .subscribe(
        (feedback) => {
          this.employerFeedback.data = feedback.map((item) => ({
            knowledge_score: item.knowledge_score,
            skills_score: item.skills_score,
            attitude_score: item.attitude_score,
            overall_performance: item.overall_performance,
          }));
        },
        (error) => {
          console.error('Error fetching employer feedback:', error);
        }
      );
  }

  openDocument(filePath: string | null): void {
    if (filePath) {
      const baseUrl = 'http://localhost/PractiEase/api'; // Replace with your backend base URL
      window.open(`${baseUrl}/${filePath}`, '_blank');
    }
  }

  setStatus(element: Document, status: string): void {
    element.status = status;
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    const statusUpdates: any = {};

    // Map document names to the corresponding database columns
    switch (element.name) {
      case 'Application Letter':
        statusUpdates.application_status = status;
        break;
      case 'Resume':
        statusUpdates.resume_status = status;
        break;
      case 'Parent Consent':
        statusUpdates.consent_status = status;
        break;
      case 'Endorsement Letter':
        statusUpdates.endorsement_status = status;
        break;
      case 'MOA':
        statusUpdates.moa_status = status;
        break;
      case 'Acceptance Letter':
        statusUpdates.acceptance_status = status;
        break;
      case 'Trainings/Seminars':
        statusUpdates.seminar_status = status;
        break;
      case 'Activities Documentation':
        statusUpdates.ccs_status = status;
        break;
      case 'OJT Documentation':
        statusUpdates.sportsfest_status = status;
        break;
      // Add more case statements for additional documents
      default:
        console.error('Unknown document name:', element.name);
        return; // Exit the method if the document name is not recognized
    }

    this.dataService
      .updateRequirementsStatus(studentId, instructorId, statusUpdates)
      .subscribe(
        (response) => {
          console.log('Status updated successfully:', response);
          this.snackBar.open('Status Updated Successfully.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        },
        (error) => {
          console.error('Error updating status:', error);
          this.snackBar.open('Failed to update status.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        }
      );
  }

  setReportStatus(element: Document, status: string): void {
    element.status = status;
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    const statusUpdates = {
      status: status, // Change this to 'status'
      file_name: element.file_name,
      file_id: element.file_id // Include file_id here
    };

    this.dataService
      .updateFinalReportStatus(statusUpdates, instructorId, studentId)
      .subscribe(
        (response) => {
          console.log('Final report status updated successfully:', response);
          this.snackBar.open('Final Report Status Updated Successfully.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        },
        (error) => {
          console.error('Error updating final report status:', error);
          this.snackBar.open('Failed to update final report status.', 'Close', {
            duration: 3000,
            horizontalPosition: 'center',
            verticalPosition: 'bottom',
            panelClass: ['custom-snackbar'],
          });
        }
      );
  }

  setDocumentationStatus(element: Document, status: string): void {
    element.documentation_status = status;
    const studentId = this.data.student.user_id;
    const instructorId = this.data.instructorId;
    const statusUpdates = {
      status: status,
      file_name: element.file_name,
      file_id: element.file_id, // Include file_id here
    };

    this.dataService
      .updateDocumentationStatus(statusUpdates, instructorId, studentId)
      .subscribe(
        (response) => {
          console.log('Documentation status updated successfully:', response);
          this.snackBar.open(
            'Documentation Status Updated Successfully.',
            'Close',
            {
              duration: 3000,
              horizontalPosition: 'center',
              verticalPosition: 'bottom',
              panelClass: ['custom-snackbar'],
            }
          );
        },
        (error) => {
          console.error('Error updating documentation status:', error);
          this.snackBar.open(
            'Failed to update documentation status.',
            'Close',
            {
              duration: 3000,
              horizontalPosition: 'center',
              verticalPosition: 'bottom',
              panelClass: ['custom-snackbar'],
            }
          );
        }
      );
  }

  formatDate(dateTimeString: string): string {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString();
  }

  formatTime(dateTimeString: string | null): string {
    if (!dateTimeString) {
      return 'N/A';
    }
    const time = new Date(dateTimeString);
    return time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  formatHoursWorked(hours: number): string {
    return Math.floor(hours).toString();
  }

  getSortedDocuments(): Document[] {
    const order = ['OJT Documentation', 'Activities Documentation', 'Trainings/Seminars', 'Final Report'];
    const combinedDocuments = this.activitiesDocuments.concat(this.finalReport);

    return combinedDocuments.sort((a, b) => order.indexOf(a.name) - order.indexOf(b.name));
  }

  getAccomplishmentStatusStyle(status: string): { [key: string]: string } {
    let color = 'inherit';
    switch (status) {
      case 'Approved':
        color = 'green';
        break;
      case 'Pending':
        color = 'orange';
        break;
      case 'Rejected':
        color = 'red';
        break;
      case 'Unverified':
        color = '#FFBF00';
        break;
    }
    return { color: color };
  }

  getDocumentStatusStyle(status: string): { [key: string]: string } {
    let color = 'inherit';
    switch (status) {
      case 'Cleared':
        color = 'green';
        break;
      case 'Not Yet Cleared':
        color = 'orange';
        break;
      case 'Not Cleared':
        color = 'red';
        break;
      case 'Currently Verifying':
        color = '#FFBF00';
        break;
    }
    return { color: color };
  }

  getDtrStatusStyle(status: string): { [key: string]: string } {
    let color = 'inherit';
    switch (status) {
      case 'Approved':
        color = 'green';
        break;
      case 'Rejected':
        color = 'red';
        break;
      case 'Unverified':
        color = '#FFBF00';
        break;
    }
    return { color: color };
  }

  getDocumentationStatusStyle(status: string): { [key: string]: string } {
    let color = 'inherit';
    switch (status) {
      case 'Cleared':
        color = 'green';
        break;
      case 'Not Yet Cleared':
        color = 'orange';
        break;
      case 'Not Cleared':
        color = 'red';
        break;
      case 'Currently Verifying':
        color = '#FFBF00';
        break;
    }
    return { color: color };
  }

  updatePaginatedDtrRecords(): void {
    const startIndex = this.pageIndex * this.pageSize;
    const endIndex = startIndex + this.pageSize;
    this.paginatedDtrRecords = this.dtrRecords.slice(startIndex, endIndex);
  }

  updatePaginatedAccomplishmentRecords(): void {
    const startIndex = this.pageIndex * this.pageSize;
    const endIndex = startIndex + this.pageSize;
    this.paginatedAccomplishmentRecords = this.accomplishmentRecords.slice(
      startIndex,
      endIndex
    );
  }

  onDtrPageChange(event: PageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.updatePaginatedDtrRecords();
  }

  onAccomplishmentPageChange(event: PageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.updatePaginatedAccomplishmentRecords();
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  generatePDF(): void {
    const doc = new jsPDF();

    // Load the background image
    const img = new Image();
    img.src = 'assets/documentbackground.png'; // Replace with the path to your image

    img.onload = () => {
      // Add the background image
      doc.addImage(
        img,
        'PNG',
        0,
        0,
        doc.internal.pageSize.width,
        doc.internal.pageSize.height
      );

      // Add metadata
      doc.setProperties({
        title: 'Student Summary Report',
        subject: 'Student Summary Report',
      });

      // Define the initial y-coordinate
      let currentY = 40;

      // Add header with underline
      doc.setFontSize(18);
      doc.setTextColor(0, 0, 0);
      doc.setFont('arial', 'bold');
      doc.text('STUDENT SUMMARY REPORT', 105, currentY, { align: 'center' });
      doc.setLineWidth(0.5);
      doc.line(50, currentY + 2, 160, currentY + 2); // Underline

      currentY += 15;

      // Add student and company details
      doc.setFontSize(12);
      doc.setFont('helvetica', 'normal');
      const detailsIncrement = 10; // Increment for each detail line
      const currentDate = new Date().toLocaleDateString();
      doc.text(
        `Name: ${this.studentDetails?.student_name || 'N/A'}`,
        14,
        currentY
      );
      doc.text(`Date: ${currentDate}`, 160, currentY); // Add current date
      currentY += detailsIncrement;
      doc.text(
        `Student ID: ${this.studentDetails?.school_id || 'N/A'}`,
        14,
        currentY
      );
      currentY += detailsIncrement;
      doc.text(
        `Company: ${this.studentDetails?.company_name || 'N/A'}`,
        14,
        currentY
      );
      currentY += detailsIncrement;
      doc.text(`Position: Student Trainee`, 14, currentY);
      currentY += detailsIncrement;
      doc.text(
        `Company Address: ${this.studentDetails?.company_address || 'N/A'}`,
        14,
        currentY
      );
      currentY += detailsIncrement;

      // Add horizontal line
      doc.setLineWidth(0.5);
      doc.line(14, currentY, 196, currentY);
      currentY += 15;

      // Add Daily Time Record Table
      doc.setFontSize(14);
      doc.setFont('arial', 'bold');
      doc.text('DAILY TIME RECORD', 14, currentY);

      autoTable(doc, {
        startY: currentY + 5,
        head: [['Date', 'Time In', 'Time Out', 'Total Hours', 'Status']],
        body: this.dtrRecords.map((item) => [
          this.formatDate(item.time_in),
          this.formatTime(item.time_in),
          this.formatTime(item.time_out),
          item.hours_worked,
          item.dtr_status,
        ]),
        styles: {
          fontSize: 10,
        },
        headStyles: {
          fillColor: [0, 0, 0],
          textColor: [255, 255, 255],
          fontSize: 12,
        },
        alternateRowStyles: {
          fillColor: [240, 240, 240],
        },
        margin: { top: 10 },
      });

      // Get the finalY position after rendering the first table
      let finalY = (doc as any).lastAutoTable.finalY;

      // Add total hours worked
      doc.setFontSize(12);
      doc.setFont('helvetica', 'normal');
      doc.text(
        `Total Hours Worked: ${this.totalHoursWorked.toFixed(2)}`,
        14,
        finalY + 10
      );
      finalY += 15;

      // Add Student Performance Evaluation Table
      doc.setFontSize(14);
      doc.setFont('arial', 'bold');
      doc.text('STUDENT PERFORMANCE EVALUATION', 14, finalY + 10);

      autoTable(doc, {
        startY: finalY + 15,
        head: [['Knowledge', 'Skills', 'Attitude', 'Overall Performance']],
        body: this.employerFeedback.data.map((item) => [
          item.knowledge_score,
          item.skills_score,
          item.attitude_score,
          item.overall_performance,
        ]),
        styles: {
          fontSize: 10,
        },
        headStyles: {
          fillColor: [0, 0, 0],
          textColor: [255, 255, 255],
          fontSize: 12,
        },
        alternateRowStyles: {
          fillColor: [240, 240, 240],
        },
        margin: { top: 10 },
      });

      doc.save('Student Summary Report.pdf');
    };
  }
}
