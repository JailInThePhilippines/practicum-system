import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-edit-dtr-dialog',
  templateUrl: './edit-dtr-dialog.component.html',
  styleUrls: ['./edit-dtr-dialog.component.css']
})
export class EditDtrDialogComponent {
  private originalDateIn: string;
  private originalDateOut: string;

  constructor(
    public dialogRef: MatDialogRef<EditDtrDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any
  ) {
    // Extract and store the original date part
    this.originalDateIn = this.extractDate(this.data.time_in);
    this.originalDateOut = this.extractDate(this.data.time_out);

    // Format the time_in and time_out to only include hours and minutes
    this.data.time_in = this.formatTime(this.data.time_in);
    this.data.time_out = this.formatTime(this.data.time_out);
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  onSaveClick(): void {
    // Combine the original date with the edited time
    this.data.time_in = this.combineDateAndTime(this.originalDateIn, this.data.time_in);
    this.data.time_out = this.combineDateAndTime(this.originalDateOut, this.data.time_out);
    this.dialogRef.close(this.data);
  }

  private formatTime(dateTimeString: string): string {
    const date = new Date(dateTimeString);
    return date.toTimeString().slice(0, 5); // Extract HH:MM
  }

  private extractDate(dateTimeString: string): string {
    return dateTimeString.split(' ')[0]; // Extract YYYY-MM-DD
  }

  private combineDateAndTime(date: string, time: string): string {
    return `${date} ${time}:00`; // Combine date and time with seconds set to 00
  }
}