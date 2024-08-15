import { Component } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-confirmdialog',
  template: `
    <h1 mat-dialog-title class="modal-title">Confirmation</h1>
    <div mat-dialog-content class="modal-body">
      Are you sure you want to perform this operation?
    </div>
    <div mat-dialog-content class="modal-body text-danger">Note: This action can not be undone.</div>
    <div mat-dialog-actions class="modal-footer button-right">
      <button
        mat-raised-button
        color="primary"
        (click)="dialogRef.close(false)"
      >
        Cancel
      </button>
      <button
        mat-raised-button
        color="warn"
        (click)="dialogRef.close(true)"
      >
        Delete
      </button>
    </div>
  `,
  styles: [`
    .button-right {
      display: flex;
      justify-content: flex-end;
    }
  `]
})
export class ConfirmdialogComponent {
  constructor(public dialogRef: MatDialogRef<ConfirmdialogComponent>) {}
}