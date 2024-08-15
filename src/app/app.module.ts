import { NgModule } from '@angular/core';
import { BrowserModule, provideClientHydration } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { DatePipe } from '@angular/common';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatExpansionModule } from '@angular/material/expansion';
import { MatIconModule } from '@angular/material/icon';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { MatCardModule } from '@angular/material/card';
import { MatDialogModule } from '@angular/material/dialog';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatSelectModule } from '@angular/material/select';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatTableModule } from '@angular/material/table';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatButtonModule } from '@angular/material/button';
import { MatTabsModule } from '@angular/material/tabs';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatListModule } from '@angular/material/list';
import { MatToolbarModule } from '@angular/material/toolbar';
import {MatInputModule} from '@angular/material/input';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { DashboardComponent } from './student/components/dashboard/dashboard.component';
import { SideNavbarComponent } from './student/components/side-navbar/side-navbar.component';
import { StudentComponent } from './student/student/student.component';
import { CalendarComponent } from './student/components/calendar/calendar.component';
import { DtrComponent } from './student/components/dtr/dtr.component';
import { PortfolioComponent } from './student/components/portfolio/portfolio.component';
import { ProfileComponent } from './student/components/profile/profile.component';
import { ProofOfEvidencesComponent } from './student/components/proof-of-evidences/proof-of-evidences.component';
import { ResourcesComponent } from './student/components/resources/resources.component';
import { InstructorDashboardComponent } from './instructor/components/instructor-dashboard/instructor-dashboard.component';
import { InstructorComponent } from './instructor/instructor/instructor.component';
import { InstructorNavComponent } from './instructor/components/instructor-nav/instructor-nav.component';
import { LandingComponent } from './main/landing/landing.component';
import { InstructorLoginComponent } from './login/instructor-login/instructor-login.component';
import { StudentLoginComponent } from './login/student-login/student-login.component';
import { EmployerLoginComponent } from './login/employer-login/employer-login.component';
import { EmployerDashboardComponent } from './employer/components/employer-dashboard/employer-dashboard.component';
import { EmployerSideNavbarComponent } from './employer/components/employer-side-navbar/employer-side-navbar.component';
import { EmployerComponent } from './employer/employer/employer.component';
import { FeedbackComponent } from './employer/components/feedback/feedback.component';
import { EmployersFeedbackComponent } from './student/components/employers-feedback/employers-feedback.component';
import { provideAnimationsAsync } from '@angular/platform-browser/animations/async';
import { COCComponent } from './student/components/coc/coc.component';
import { AnnouncementsComponent } from './instructor/components/announcements/announcements.component';
import { AdminLoginComponent } from './login/admin-login/admin-login.component';
import { AdminDashboardComponent } from './admin/components/admin-dashboard/admin-dashboard.component';
import { AdminComponent } from './admin/admin/admin.component';
import { InstructorAccountComponent } from './admin/components/accounts/instructor-account/instructor-account.component';
import { AdminSideNavComponent } from './admin/components/admin-side-nav/admin-side-nav.component';
import { LinkComponent } from './admin/components/link/link.component';
import { ConfirmdialogComponent } from './confirmdialog/confirmdialog.component';
import { LandingPageComponent } from './landing-page/landing-page.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { FilterDialogComponent } from './filter-dialog/filter-dialog.component';
import { ConfirmationDialogComponent } from './confirmation-dialog/confirmation-dialog.component';
import { ViewSubmissionsDialogComponent } from './instructor/components/view-submissions-dialog/view-submissions-dialog.component';
import { FinalReportComponent } from './student/components/final-report/final-report.component';
import { ViewSubmissionsForEmployerDialogComponent } from './employer/components/view-submissions-for-employer-dialog/view-submissions-for-employer-dialog.component';
import { WeeklyAccomplishmentsComponent } from './student/components/weekly-accomplishments/weekly-accomplishments.component';
import { DocumentationComponent } from './student/components/documentation/documentation.component';
import { EditDtrDialogComponent } from './edit-dtr-dialog/edit-dtr-dialog.component';
import { ExitPollComponent } from './student/components/exit-poll/exit-poll.component';
import { MainDashboardComponent } from './instructor/components/main-dashboard/main-dashboard.component';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { DocumentInfoDialogComponent } from './document-info-dialog/document-info-dialog.component';


@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    SideNavbarComponent,
    StudentComponent,
    CalendarComponent,
    DtrComponent,
    PortfolioComponent,
    ProfileComponent,
    ProofOfEvidencesComponent,
    ResourcesComponent,
    InstructorDashboardComponent,
    InstructorComponent,
    InstructorNavComponent,
    LandingComponent,
    InstructorLoginComponent,
    StudentLoginComponent,
    EmployerLoginComponent,
    EmployerDashboardComponent,
    EmployerSideNavbarComponent,
    EmployerComponent,
    FeedbackComponent,
    EmployersFeedbackComponent,
    COCComponent,
    AnnouncementsComponent,
    AdminLoginComponent,
    AdminDashboardComponent,
    AdminComponent,
    InstructorAccountComponent,
    AdminSideNavComponent,
    LinkComponent,
    ConfirmdialogComponent,
    LandingPageComponent,
    NotFoundComponent,
    FilterDialogComponent,
    ConfirmationDialogComponent,
    ViewSubmissionsDialogComponent,
    FinalReportComponent,
    ViewSubmissionsForEmployerDialogComponent,
    WeeklyAccomplishmentsComponent,
    DocumentationComponent,
    EditDtrDialogComponent,
    ExitPollComponent,
    MainDashboardComponent,
    DocumentInfoDialogComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule,
    MatSnackBarModule,
    BrowserAnimationsModule,
    MatExpansionModule,
    MatIconModule,
    MatCardModule,
    MatDialogModule,
    MatPaginatorModule,
    MatSelectModule,
    MatFormFieldModule,
    MatTableModule,
    MatTooltipModule,
    MatButtonModule,
    MatTabsModule,
    MatSidenavModule,
    MatListModule,
    MatToolbarModule,
    MatInputModule,
    MatDatepickerModule,
    MatNativeDateModule
  ],
  providers: [
    provideClientHydration(),
    DatePipe,
    provideAnimationsAsync()
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
