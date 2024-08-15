import {
  Component,
  OnInit,
  ViewChild,
  AfterViewInit,
  OnDestroy,
} from '@angular/core';
import { DataService } from '../../../data.service';
import { AuthService } from '../../../auth.service';
import { MatPaginator } from '@angular/material/paginator';
import { FilterService } from '../../../filter.service';
import { Subscription } from 'rxjs';
import { MatTableDataSource } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { ViewSubmissionsDialogComponent } from '../view-submissions-dialog/view-submissions-dialog.component';
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-instructor-dashboard',
  templateUrl: './instructor-dashboard.component.html',
  styleUrls: ['./instructor-dashboard.component.css'],
})
export class InstructorDashboardComponent
  implements OnInit, AfterViewInit, OnDestroy
{
  displayedColumns: string[] = [
    'student_name',
    'program_year_block',
    'school_id',
    'actions',
    'mark_as_done',
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
            this.dataService
              .getStudentProfilePictureForInstructor(
                instructorId,
                student.user_id
              )
              .subscribe(
                (result) => {
                  student.profile_picture = result.success
                    ? result.image_path
                    : 'assets/default-profile-picture.png';
                },
                (error) => {
                  console.error('Error fetching profile picture:', error);
                  student.profile_picture =
                    'assets/default-profile-picture.png';
                }
              );
          });
          this.dataSource.data = students.sort((a, b) =>
            a.student_name.localeCompare(b.student_name)
          );
        } else {
          console.error('Error: Data returned is not an array.');
        }
      },
      (error) => {
        console.error('Error fetching associated students:', error);
      }
    );
  }

  filterStudents(): void {
    this.dataSource.filterPredicate = (data, filter) => {
        const filterObject = JSON.parse(filter);

        const matchesSearchQuery = filterObject.searchQuery
            ? data.student_name.toLowerCase().includes(filterObject.searchQuery.toLowerCase())
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
            ? filterObject.filterDoneStatus === (data.ojt_status === 'done' ? 'done' : 'not done')
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

  markAsDone(student: any): void {
    const newStatus = student.ojt_status === 'done' ? 'Not Yet Done' : 'done';
    const instructorId = this.authService.getCurrentInstructorId();

    if (instructorId !== null) {
      this.dataService
        .updateOJTStatus(newStatus, instructorId, student.user_id)
        .subscribe(
          (response) => {
            if (response.success) {
              student.ojt_status = newStatus;
              this.filterStudents();
              this.snackBar.open('OJT status updated successfully', 'Close', {
                duration: 3000,
                panelClass: 'snackbar-success',
              });
            } else {
              console.error('Error updating OJT status:', response.message);
            }
          },
          (error) => {
            console.error('Error updating OJT status:', error);
          }
        );
    } else {
      console.error('Error: Instructor ID is null.');
    }
  }

  getProfilePictureUrl(student: any): string {
    return student.profile_picture
      ? 'http://localhost/PractiEase/api/' + student.profile_picture
      : 'assets/default-profile-picture.png';
  }

  openDialog(student: any): void {
    const instructorId = this.authService.getCurrentInstructorId();
    const screenWidth = window.innerWidth;
    let dialogPosition = { left: '15%' };
  
    if (screenWidth === 1366) {
      dialogPosition = { left: '17%' }; // Adjust the value as needed
    }
  
    const dialogRef = this.dialog.open(ViewSubmissionsDialogComponent, {
      width: '90%',
      data: { student, instructorId },
      position: dialogPosition,
    });
  
    dialogRef.afterClosed().subscribe((result) => {
      console.log('The dialog was closed');
    });
  }
}
