import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocumentInfoDialogComponent } from './document-info-dialog.component';

describe('DocumentInfoDialogComponent', () => {
  let component: DocumentInfoDialogComponent;
  let fixture: ComponentFixture<DocumentInfoDialogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [DocumentInfoDialogComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(DocumentInfoDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
