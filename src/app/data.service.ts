import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, map, of, tap, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class DataService {
  public apiUrl = 'http://localhost/PractiEase/api';

  constructor(private http: HttpClient) {}

  createStudentAccount(data: any) {
    return this.http.post<any>(`${this.apiUrl}/student_create_account`, data);
  }

  createInstructorAccount(data: any) {
    return this.http.post<any>(
      `${this.apiUrl}/instructor_create_account`,
      data
    );
  }

  studentLogin(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/student_login`, {
      email,
      password,
    });
  }

  employerLogin(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/employer_login`, {
      email,
      password,
    });
  }

  instructorLogin(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/instructor_login`, {
      email,
      password,
    });
  }

  adminLogin(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/admin_login`, {
      email,
      password,
    });
  }

  createEmployerAccount(data: any) {
    return this.http.post<any>(`${this.apiUrl}/employer_create_account`, data);
  }

  getStudentCount(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_student_count`);
  }

  getEmployerCount(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_employer_count`);
  }

  getInstructorCount(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_instructor_count`);
  }

  getLinkedAccountsCount(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_linked_accounts_count`);
  }

  getStudentsForAdmin(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_students_for_admin`);
  }

  getInstructorsForAdmin(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_instructors_for_admin`);
  }

  getEmployersForAdmin(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_employers_for_admin`);
  }

  getLinkedAccountsForAdmin(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/get_linked_accounts_for_admin`);
  }

  deleteStudent(userId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_student`, {
      user_id: userId,
    });
  }

  deleteInstructor(instructorId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_instructor`, {
      instructor_id: instructorId,
    });
  }

  deleteEmployer(employerId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_employer`, {
      employer_id: employerId,
    });
  }

  deleteLinkedAccount(employerId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_linked_account`, {
      employer_id: employerId,
    });
  }

  linkStudentAndEmployer(data: any) {
    return this.http.post<any>(
      `${this.apiUrl}/link_student_and_employer`,
      data
    );
  }

  getStudentProfile(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_student_profile?student_id=${userId}`
    );
  }

  getInstructorName(instructorId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_instructor_name?instructor_id=${instructorId}`
    );
  }

  getEmployerName(employerId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_employer_name?employer_id=${employerId}`
    );
  }

  getUploadedFiles(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_student_signed_info?user_id=${userId}`
    );
  }

  updateStudentInformation(
    userId: number,
    mobileNumber: string,
    companyAddress: string
  ): Observable<any> {
    const data = {
      user_id: userId,
      student_mobile_number: mobileNumber,
      company_address: companyAddress,
    };
    return this.http.post<any>(
      `${this.apiUrl}/student_update_information`,
      data
    );
  }

  updateStudentMobileNumber(
    userId: number,
    mobileNumber: string
  ): Observable<any> {
    const data = {
      user_id: userId,
      student_mobile_number: mobileNumber,
    };
    return this.http.post<any>(
      `${this.apiUrl}/update_student_mobile_number`,
      data
    );
  }

  updateStudentCompanyAddress(
    userId: number,
    companyAddress: string
  ): Observable<any> {
    const data = {
      user_id: userId,
      company_address: companyAddress,
    };
    return this.http.post<any>(
      `${this.apiUrl}/update_student_company_address`,
      data
    );
  }

  uploadEndorsementLetter(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_endorsement_letter?user_id=${userId}`,
      formData
    );
  }

  uploadApplicationLetter(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_application_letter?user_id=${userId}`,
      formData
    );
  }

  uploadMOA(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_moa?user_id=${userId}`,
      formData
    );
  }

  uploadMedicalCertificate(
    formData: FormData,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_medical_certificate?user_id=${userId}`,
      formData
    );
  }

  uploadResume(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_resume?user_id=${userId}`,
      formData
    );
  }

  uploadBarangayClearance(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_barangay_clearance?user_id=${userId}`,
      formData
    );
  }

  uploadVaccinationCard(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_vaccination_card?user_id=${userId}`,
      formData
    );
  }

  uploadAcceptanceLetter(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_acceptance_letter?user_id=${userId}`,
      formData
    );
  }

  uploadParentsConsentLetter(
    formData: FormData,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_parents_consent_letter?user_id=${userId}`,
      formData
    );
  }

  recordTimeIn(userId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/record_time_in`, {
      user_id: userId,
    });
  }

  recordTimeOut(userId: number, hoursWorked: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/record_time_out`, {
      user_id: userId,
      hours_worked: hoursWorked,
    });
  }

  getPastTimeRecords(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_past_time_records?user_id=${userId}`
    );
  }

  uploadCCSPicture(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_ccs_picture?user_id=${userId}`,
      formData
    );
  }

  uploadDTR(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_file_dtr?user_id=${userId}`,
      formData
    );
  }

  uploadWeeklyAccomplishments(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_weekly_accomplishments?user_id=${userId}`,
      formData
    );
  }

  uploadDocumentation(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_documentation?user_id=${userId}`,
      formData
    );
  }

  uploadAcquaintancePicture(
    formData: FormData,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_acquaintance_picture?user_id=${userId}`,
      formData
    );
  }

  uploadSeminarCertificate(
    formData: FormData,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_seminar_certificate?user_id=${userId}`,
      formData
    );
  }

  uploadFinalReport(
    formData: FormData,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_final_report?user_id=${userId}`,
      formData
    );
  }

  uploadSportsfestPicture(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_sportsfest_picture?user_id=${userId}`,
      formData
    );
  }

  uploadFoundationWeekPicture(
    formData: FormData,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_foundation_week_picture?user_id=${userId}`,
      formData
    );
  }

  getProofOfEvidencesFiles(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_proof_of_evidences_files?user_id=${userId}`
    );
  }

  deleteProofOfEvidenceFile(
    fileName: string,
    category: string,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_proof_of_evidence_file`, {
      file_name: fileName,
      category,
      user_id: userId,
    });
  }

  insertDailyAccomplishments(data: any): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/record_daily_accomplishments`,
      data
    );
  }

  getRecordedDailyAccomplishments(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_daily_accomplishments?user_id=${userId}`
    );
  }

  deleteDailyAccomplishment(
    dailyAccomplishmentId: number,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_daily_accomplishments`, {
      daily_accomplishments_id: dailyAccomplishmentId,
      user_id: userId,
    });
  }

  recordSkillsPortfolio(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/record_skills_portfolio`, data);
  }

  recordEducationPortfolio(data: any): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/record_education_portfolio`,
      data
    );
  }

  getEducationRecords(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_education_records?user_id=${userId}`
    );
  }

  getSkillRecords(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_skills_records?user_id=${userId}`
    );
  }

  deleteSkill(portfolioSkillsId: number, userId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_skills`, {
      portfolio_skills_id: portfolioSkillsId,
      user_id: userId,
    });
  }

  deleteDTR(dtrId: number, userId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_dtr`, {
      dtr_id: dtrId,
      user_id: userId,
    });
  }

  

  deleteCertificate(fileId: number, employerId: number, studentId: number): Observable<any> {
    const payload = {
      file_id: fileId,
      employer_id: employerId,
      student_id: studentId
    };
    console.log('Sending delete certificate request with payload:', payload);
    return this.http.post<any>(`${this.apiUrl}/delete_certificate`, payload)
      .pipe(
        tap(response => console.log('Delete certificate response from server:', response)),
        catchError(error => {
          console.error('Error occurred during delete certificate request:', error);
          return throwError(error);
        })
      );
  }  
  
  deleteDTRFile(fileId: number, userId: number): Observable<any> {
    const payload = {
      file_id: fileId,
      user_id: userId
    };
    console.log('Sending delete certificate request with payload:', payload);
    return this.http.post<any>(`${this.apiUrl}/delete_dtr_file`, payload)
      .pipe(
        tap(response => console.log('Delete certificate response from server:', response)),
        catchError(error => {
          console.error('Error occurred during delete certificate request:', error);
          return throwError(error);
        })
      );
  } 

  deleteFinalReport(fileId: number, userId: number): Observable<any> {
    const payload = {
      file_id: fileId,
      user_id: userId
    };
    console.log('Sending delete certificate request with payload:', payload);
    return this.http.post<any>(`${this.apiUrl}/delete_final_report`, payload)
      .pipe(
        tap(response => console.log('Delete certificate response from server:', response)),
        catchError(error => {
          console.error('Error occurred during delete certificate request:', error);
          return throwError(error);
        })
      );
  } 

  deleteWeeklyAccomplishments(fileId: number, userId: number): Observable<any> {
    const payload = {
      file_id: fileId,
      user_id: userId
    };
    console.log('Sending delete certificate request with payload:', payload);
    return this.http.post<any>(`${this.apiUrl}/delete_weekly_accomplishments`, payload)
      .pipe(
        tap(response => console.log('Delete certificate response from server:', response)),
        catchError(error => {
          console.error('Error occurred during delete certificate request:', error);
          return throwError(error);
        })
      );
  } 

  deleteDocumentation(fileId: number, userId: number): Observable<any> {
    const payload = {
      file_id: fileId,
      user_id: userId
    };
    console.log('Sending delete certificate request with payload:', payload);
    return this.http.post<any>(`${this.apiUrl}/delete_documentation`, payload)
      .pipe(
        tap(response => console.log('Delete certificate response from server:', response)),
        catchError(error => {
          console.error('Error occurred during delete certificate request:', error);
          return throwError(error);
        })
      );
  } 

  deleteEducation(
    portfolioEducationId: number,
    userId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_education`, {
      portfolio_education_id: portfolioEducationId,
      user_id: userId,
    });
  }

  uploadProfilePicture(formData: FormData, userId: number): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/upload_user_photo?user_id=${userId}`,
      formData
    );
  }

  getProfilePicture(userId: number): Observable<any> {
    // Construct the absolute URL for the profile picture based on your project structure
    const apiUrl = `${this.apiUrl}/get_user_photo?user_id=${userId}`;

    // Return the HTTP GET request observable
    return this.http.get<any>(apiUrl).pipe(
      map((response) => {
        if (response.success && response.image_path) {
          // Adjust the image path to remove any leading '../'
          response.image_path = response.image_path.replace(/^\.\.\//, '');
        }
        return response;
      })
    );
  }

  getStudentProfilePicture(
    employerId: number,
    studentId: number
  ): Observable<any> {
    // Construct the URL with parameters using apiUrl
    const url = `${this.apiUrl}/get_student_profile_picture?employer_id=${employerId}&student_id=${studentId}`;
    // Make the HTTP GET request
    return this.http.get<any>(url);
  }

  getStudentProfilePictureForInstructor(
    instructorId: number,
    studentId: number
  ): Observable<any> {
    // Construct the URL with parameters using apiUrl
    const url = `${this.apiUrl}/get_student_profile_picture_for_instructor?instructor_id=${instructorId}&student_id=${studentId}`;
    // Make the HTTP GET request
    return this.http.get<any>(url);
  }

  getStudentName(userId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_student_name?user_id=${userId}`
    );
  }

  // New method to fetch time-in and time-out records for the employer
  getTimeInOutForEmployer(
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_time_in_and_out_for_employer?employer_id=${employerId}&student_id=${studentId}`
    );
  }

  getAccomplishmentOfStudents(
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_accomplishment_records_for_employer?employer_id=${employerId}&student_id=${studentId}`
    );
  }

  updateDtrStatus(
    dtrData: any,
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_dtr_status`, {
      dtr: dtrData,
      employer_id: employerId,
      student_id: studentId,
    });
  }

  updateOJTStatus(
    status: any,
    instructorId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_ojt_status`, {
      status: status,
      instructor_id: instructorId,
      student_id: studentId,
    });
  }

  updateWeeklyAccomplishmentsStatus(
    weeklyAccomplishmentsData: any,
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_weekly_accomplishments_file_status`, {
      weekly_accomplishments: weeklyAccomplishmentsData,
      employer_id: employerId,
      student_id: studentId,
    });
  }

  updateFinalReportStatus(
    finalReportData: any,
    instructorId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_final_report_status`, {
      report: finalReportData, // Change this to 'report'
      instructor_id: instructorId,
      student_id: studentId,
    });
  }

  updateDocumentationStatus(
    documentationData: any,
    instructorId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_documentation_status`, {
      documentation: documentationData,
      instructor_id: instructorId,
      student_id: studentId,
    });
  }

  updateFileDtrStatus(
    dtrData: any,
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_dtr_file_status`, {
      dtr: dtrData,
      employer_id: employerId,
      student_id: studentId,
    });
  }

  updateAccomplishmentStatus(
    accomplishment: any,
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_accomplishment_status`, {
      accomplishment: accomplishment,
      employer_id: employerId,
      student_id: studentId,
    });
  }

  insertEmployerFeedback(feedbackData: any): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/insert_employer_feedback`,
      feedbackData
    );
  }

  getEmployerFeedback(studentId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_employer_feedback?student_id=${studentId}`
    );
  }

  getAssociatedStudents(employerId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_associated_students?employer_id=${employerId}`
    );
  }

  uploadCertificate(
    formData: FormData,
    employerId: number,
    studentId: number
  ): Observable<any> {
    formData.append('student_id', studentId.toString());
    formData.append('employer_id', employerId.toString());
    const file = formData.get('file');
    if (file) {
      formData.append('certificate', file);
      formData.delete('file'); // Remove the old 'file' key
    }
    return this.http.post<any>(`${this.apiUrl}/upload_certificate`, formData);
  }

  getUploadedCertificates(
    employerId: number,
    studentId: number
  ): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_uploaded_certificates?employer_id=${employerId}&student_id=${studentId}`
    );
  }

  getStudentPortfolioForEmployer(employerId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_student_portfolio_for_employer?employer_id=${employerId}`
    );
  }

  getEducationRecordsForEmployer(employerId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_education_records_for_employer?employer_id=${employerId}`;
    return this.http.get<any[]>(url);
  }

  getSkillsRecordsForEmployer(employerId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_skills_records_for_employer?employer_id=${employerId}`;
    return this.http.get<any[]>(url);
  }

  getCertificateOfCompletion(studentId: number): Observable<any> {
    const url = `${this.apiUrl}/get_certificate_of_completion?student_id=${studentId}`;
    return this.http.get<any>(url);
  }

  getAssociatedStudentsForInstructor(instructorId: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/get_associated_students_for_instructor?instructor_id=${instructorId}`
    );
  }

  getStudentDTRForInstructor(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_time_in_and_out_for_instructor?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any[]>(url);
  }

  getStudentAccomplishmentsForInstructor(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_accomplishments_for_instructor?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any[]>(url);
  }

  getCertificates(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_certificates?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any[]>(url);
  }

  getApplicationLetter(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_application_letter`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getMedicalCertificate(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_medical_certificate`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getAcceptanceLetter(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_acceptance_letter`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getResume(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_resume`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getMOA(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_moa_letter`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getVaccinationCard(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_vaccination_card`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getBarangayClearance(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_barangay_clearance`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getEndorsementLetter(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_endorsement_letter`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getParentsConsent(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_parents_consent`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getCCSPOE(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_ccs_poe`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getDTRForInstructor(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_dtr_for_instructor`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching DTR:', error);
        return throwError('Failed to load DTR');
      })
    );
  }

  getDTRForEmployer(employerId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_dtr_for_employer`;
    const params = {
      employer_id: employerId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching DTR:', error);
        return throwError('Failed to load DTR');
      })
    );
  }

  getSeminarPOE(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_seminar_poe`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getFinalReportForInstructor(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_final_report_for_instructor`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getWeeklyAccomplishmentsForInstructor(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_weekly_accomplishments_for_instructor`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getDocumentationForInstructor(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_documentation_for_instructor`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getWeeklyAccomplishmentsForEmployer(employerId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_weekly_accomplishments_for_employer`;
    const params = {
      employer_id: employerId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getGCEvents(instructorId: number, studentId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_gc_events_poe`;
    const params = {
      instructor_id: instructorId.toString(),
      student_id: studentId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getDTRForStudent(userId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_dtr?user_id=${userId}`;
    const params = {
      user_id: userId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getWeeklyAccomplishmentsForStudent(userId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_weekly_accomplishments?user_id=${userId}`;
    const params = {
      user_id: userId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getDocumentationForStudent(userId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_student_documentation?user_id=${userId}`;
    const params = {
      user_id: userId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getFinalReportForStudent(userId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_final_report_for_student?user_id=${userId}`;
    const params = {
      user_id: userId.toString(),
    };

    return this.http.get<any[]>(url, { params }).pipe(
      catchError((error) => {
        console.error('Error fetching documents:', error);
        return throwError('Failed to load document records');
      })
    );
  }

  getEmployerFeedbackForInstructor(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_employer_feedback_for_instructor?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any[]>(url);
  }

  updateRequirementsStatus(
    studentId: number,
    instructorId: number,
    statusUpdates: any
  ): Observable<any> {
    const url = `${this.apiUrl}/update_requirements_status`;
    const body = {
      student_id: studentId,
      instructor_id: instructorId,
      status_updates: statusUpdates,
    };
    return this.http.post<any>(url, body);
  }

  getRequirementStatusForInstructor(
    instructorId: number,
    studentId: number
  ): Observable<any[]> {
    const url = `${this.apiUrl}/get_requirement_status_for_instructor?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any[]>(url);
  }

  getRequirementStatusForStudent(userId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_requirement_status_for_student?user_id=${userId}`;
    return this.http.get<any[]>(url);
  }

  insertInstructorAnnouncement(feedbackData: any): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/insert_instructor_announcement`,
      feedbackData
    );
  }

  getAnnouncementsForInstructor(instructorId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_announcements_for_instructor?instructor_id=${instructorId}`;
    return this.http.get<any[]>(url);
  }

  getAnnouncementsForStudent(userId: number): Observable<any[]> {
    const url = `${this.apiUrl}/get_announcements_for_student?user_id=${userId}`;
    return this.http.get<any[]>(url);
  }

  updateDTRRemarks(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update_dtr_remarks`, data)
      .pipe(
        catchError(error => {
          console.error('Error occurred during update DTR remarks request:', error);
          return throwError(error);
        })
      );
  }

  deleteAnnouncement(
    instructorId: number,
    announcementId: number
  ): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/delete_announcement`, {
      instructor_id: instructorId,
      announcement_id: announcementId,
    });
  }

  updateTimeIn(dtrId: number, timeIn: string, employerId: number, studentId: number): Observable<any> {
    const payload = {
      dtr_id: dtrId,
      time_in: timeIn,
      employer_id: employerId,
      student_id: studentId
    };
    return this.http.post<any>(`${this.apiUrl}/update_time_in`, payload);
  }

  updateTimeOut(dtrId: number, timeOut: string, employerId: number, studentId: number): Observable<any> {
    const payload = {
      dtr_id: dtrId,
      time_out: timeOut,
      employer_id: employerId,
      student_id: studentId
    };
    return this.http.post<any>(`${this.apiUrl}/update_time_out`, payload);
  }

  insertExitPoll(exitPollData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/insert_exit_poll`, exitPollData);
  }

  getExitPoll(userId: number): Observable<any> {
    const url = `${this.apiUrl}/get_exit_poll?user_id=${userId}`;
    return this.http.get<any>(url).pipe(
      map(response => response[0] || null),
      catchError(error => {
        console.error('Error fetching exit poll:', error);
        return of(null);
      })
    );
  }

  getExitPollForInstructor(instructorId: number, studentId: number): Observable<any> {
    const url = `${this.apiUrl}/get_exit_poll_for_instructor?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any>(url).pipe(
      map(response => response[0] || null),
      catchError(error => {
        console.error('Error fetching exit poll:', error);
        return of(null);
      })
    );
  }

  getExitPollForEmployer(employerId: number, studentId: number): Observable<any> {
    const url = `${this.apiUrl}/get_exit_poll_for_employer?employer_id=${employerId}&student_id=${studentId}`;
    return this.http.get<any>(url).pipe(
      map(response => response[0] || null),
      catchError(error => {
        console.error('Error fetching exit poll:', error);
        return of(null);
      })
    );
  }

  editExitPoll(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/edit_exit_poll`, data).pipe(
      map((response: any) => response),
      catchError((error) => {
        console.error('Error editing exit poll', error);
        return throwError(error);
      })
    );
  }

  getAllRequirements(instructorId: number, studentId: number): Observable<any> {
    const url = `${this.apiUrl}/get_all_requirements?instructor_id=${instructorId}&student_id=${studentId}`;
    return this.http.get<any>(url).pipe(
      catchError((error) => {
        console.error('Error fetching requirements:', error);
        return throwError(error);
      })
    );
  }
  

}
