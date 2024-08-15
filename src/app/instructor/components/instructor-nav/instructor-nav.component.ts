import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../auth.service';
import { DataService } from '../../../data.service';
import { Router } from '@angular/router';
import { FilterService } from '../../../filter.service';
import { FilterDialogComponent } from '../../../filter-dialog/filter-dialog.component';
import { MatDialog } from '@angular/material/dialog';

@Component({
  selector: 'app-instructor-nav',
  templateUrl: './instructor-nav.component.html',
  styleUrls: ['./instructor-nav.component.css']
})
export class InstructorNavComponent implements OnInit {
  instructorName: string = '';

  filterProgram: string = '';
  filterYear: string = '';
  filterBlock: string = '';
  searchQuery: string = '';

  uniquePrograms: string[] = [];
  uniqueYears: string[] = [];
  uniqueBlocks: string[] = [];

  constructor(
    private authService: AuthService,
    private dataService: DataService,
    private router: Router,
    private filterService: FilterService,
    public dialog: MatDialog
  ) {}

  isActive(route: string): boolean {
    return this.router.url.includes(route);
  }

  ngOnInit(): void {
    this.fetchInstructorName();
    this.fetchFilterOptions();
  }

  fetchInstructorName(): void {
    const instructorId = this.authService.getCurrentInstructorId();
    if (instructorId) {
      this.dataService.getInstructorName(instructorId).subscribe(
        (response) => {
          if (response && response.instructor_name) {
            this.instructorName = response.instructor_name;
          }
        },
        (error) => {
          console.error('Error fetching instructor name:', error);
        }
      );
    }
  }

  fetchFilterOptions(): void {
    const instructorId = this.authService.getCurrentInstructorId();
    if (instructorId) {
      this.dataService.getAssociatedStudentsForInstructor(instructorId).subscribe(
        (students) => {
          if (Array.isArray(students)) {
            this.uniquePrograms = [...new Set(students.map(s => s.program))];
            this.uniqueYears = [...new Set(students.map(s => s.student_year))];
            this.uniqueBlocks = [...new Set(students.map(s => s.block))];
          } else {
            console.error('Error: Data returned is not an array.');
          }
        },
        (error) => {
          console.error('Error fetching filter options:', error);
        }
      );
    }
  }

  openFilterDialog(): void {
    const dialogRef = this.dialog.open(FilterDialogComponent, {
      data: {
        filterProgram: this.filterProgram,
        filterYear: this.filterYear,
        filterBlock: this.filterBlock,
        searchQuery: this.searchQuery,
        uniquePrograms: this.uniquePrograms,
        uniqueYears: this.uniqueYears,
        uniqueBlocks: this.uniqueBlocks
      }
    });
  
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.filterProgram = result.filterProgram;
        this.filterYear = result.filterYear;
        this.filterBlock = result.filterBlock;
        this.searchQuery = result.searchQuery;
        this.filterStudents();
      }
    });
  }  

  filterStudents(): void {
    const criteria = {
      searchQuery: this.searchQuery,
      filterProgram: this.filterProgram,
      filterYear: this.filterYear,
      filterBlock: this.filterBlock
    };
    this.filterService.setFilterCriteria(criteria);
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/instructor-login']);
  }
  
  toggleSidebar(): void {
    const sidebar = document.getElementById('offcanvasNavbar');
    if (sidebar) {
      sidebar.style.display = sidebar.style.display === 'block' ? 'none' : 'block';
    }
  }

}