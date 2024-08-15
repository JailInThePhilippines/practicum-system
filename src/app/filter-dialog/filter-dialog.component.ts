import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-filter-dialog',
  templateUrl: './filter-dialog.component.html',
  styleUrls: ['./filter-dialog.component.css']
})
export class FilterDialogComponent implements OnInit {

  constructor(
    public dialogRef: MatDialogRef<FilterDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {}

  ngOnInit(): void {
    // Ensure that data.uniquePrograms, data.uniqueYears, and data.uniqueBlocks are available
    if (!this.data.uniquePrograms) {
      this.data.uniquePrograms = [];
    }
    if (!this.data.uniqueYears) {
      this.data.uniqueYears = [];
    }
    if (!this.data.uniqueBlocks) {
      this.data.uniqueBlocks = [];
    }
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onApply(): void {
    this.dialogRef.close(this.data);
  }

  onClearFilters(): void {
    this.data.filterProgram = null;
    this.data.filterYear = null;
    this.data.filterBlock = null;
  }

}