import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-document-info-dialog',
  templateUrl: './document-info-dialog.component.html',
  styleUrls: ['./document-info-dialog.component.css']
})
export class DocumentInfoDialogComponent {
  constructor(@Inject(MAT_DIALOG_DATA) public data: any) {}

  getRequirementStatus(requirement: any, key: string, displayName: string): string {
    return `${displayName} - ${requirement[key] === 'Cleared' ? 'Submitted' : 'Incomplete'}`;
  }

  getClearedCount(requirements: any): number {
    return Object.entries(requirements)
      .filter(([key, status]) => key !== 'moa_status' && status === 'Cleared')
      .length;
  }
}