import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './auth.guard';
import { StudentComponent } from './student/student/student.component';
import { DashboardComponent } from './student/components/dashboard/dashboard.component';
import { DtrComponent } from './student/components/dtr/dtr.component';
import { PortfolioComponent } from './student/components/portfolio/portfolio.component';
import { ProfileComponent } from './student/components/profile/profile.component';
import { ProofOfEvidencesComponent } from './student/components/proof-of-evidences/proof-of-evidences.component';
import { ResourcesComponent } from './student/components/resources/resources.component';
import { InstructorComponent } from './instructor/instructor/instructor.component';
import { InstructorDashboardComponent } from './instructor/components/instructor-dashboard/instructor-dashboard.component';
import { LandingComponent } from './main/landing/landing.component';
import { StudentLoginComponent } from './login/student-login/student-login.component';
import { InstructorLoginComponent } from './login/instructor-login/instructor-login.component';
import { EmployerLoginComponent } from './login/employer-login/employer-login.component';
import { EmployerComponent } from './employer/employer/employer.component';
import { EmployerDashboardComponent } from './employer/components/employer-dashboard/employer-dashboard.component';
import { FeedbackComponent } from './employer/components/feedback/feedback.component';
import { EmployersFeedbackComponent } from './student/components/employers-feedback/employers-feedback.component';
import { COCComponent } from './student/components/coc/coc.component';
import { AnnouncementsComponent } from './instructor/components/announcements/announcements.component';
import { AdminComponent } from './admin/admin/admin.component';
import { AdminDashboardComponent } from './admin/components/admin-dashboard/admin-dashboard.component';
import { AdminLoginComponent } from './login/admin-login/admin-login.component';
import { InstructorAccountComponent } from './admin/components/accounts/instructor-account/instructor-account.component';
import { LinkComponent } from './admin/components/link/link.component';
import { LandingPageComponent } from './landing-page/landing-page.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { FinalReportComponent } from './student/components/final-report/final-report.component';
import { WeeklyAccomplishmentsComponent } from './student/components/weekly-accomplishments/weekly-accomplishments.component';
import { ExitPollComponent } from './student/components/exit-poll/exit-poll.component';
import { MainDashboardComponent } from './instructor/components/main-dashboard/main-dashboard.component';

export const routes: Routes = [
  { path: '', component: StudentLoginComponent },
  { path: 'landing', component: LandingComponent },
  { path: 'landing-page', component: LandingPageComponent },
  { path: 'student-login', component: StudentLoginComponent },
  { path: 'employer-login', component: EmployerLoginComponent },
  { path: 'instructor-login', component: InstructorLoginComponent },
  { path: 'admin-login', component: AdminLoginComponent },
  { 
    path: 'student', 
    component: StudentComponent, 
    canActivate: [AuthGuard], 
    data: { expectedRole: 'student' },
    children: [
      { path: '', component: DashboardComponent },
      { path: 'dashboard', component: DashboardComponent },
      { path: 'profile', component: ProfileComponent },
      { path: 'weekly-accomplishments', component: WeeklyAccomplishmentsComponent },
      { path: 'portfolio', component: PortfolioComponent },
      { path: 'proof-of-evidences', component: ProofOfEvidencesComponent },
      { path: 'dtr', component: DtrComponent },
      { path: 'requirements', component: ResourcesComponent },
      { path: 'feedback', component: EmployersFeedbackComponent },
      { path: 'certificate-of-completion', component: COCComponent },
      { path: 'final-report', component: FinalReportComponent },
      { path: 'exit-poll', component: ExitPollComponent }
    ]
  },
  { 
    path: 'employer', 
    component: EmployerComponent, 
    canActivate: [AuthGuard], 
    data: { expectedRole: 'employer' },
    children: [
      { path: '', component: EmployerDashboardComponent },
      { path: 'dashboard', component: EmployerDashboardComponent },
      { path: 'feedback', component: FeedbackComponent },
    ]
  },
  { 
    path: 'instructor', 
    component: InstructorComponent, 
    canActivate: [AuthGuard], 
    data: { expectedRole: 'instructor' },
    children: [
      { path: '', component: MainDashboardComponent },
      { path: 'dashboard', component: MainDashboardComponent },
      { path: 'class', component: InstructorDashboardComponent },
      { path: 'announcement', component: AnnouncementsComponent }
    ]
  },
  { 
    path: 'admin', 
    component: AdminComponent, 
    canActivate: [AuthGuard], 
    data: { expectedRole: 'admin' },
    children: [
      { path: '', component: AdminDashboardComponent },
      { path: 'dashboard', component: AdminDashboardComponent },
      { path: 'accounts', component: InstructorAccountComponent },
      { path: 'link-accounts', component: LinkComponent }
    ]
  },
  { path: '**', component: NotFoundComponent } // Wildcard route for a 404 page
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }