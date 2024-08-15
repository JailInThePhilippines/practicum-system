import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewSubmissionsForEmployerDialogComponent } from './view-submissions-for-employer-dialog.component';

describe('ViewSubmissionsForEmployerDialogComponent', () => {
  let component: ViewSubmissionsForEmployerDialogComponent;
  let fixture: ComponentFixture<ViewSubmissionsForEmployerDialogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ViewSubmissionsForEmployerDialogComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ViewSubmissionsForEmployerDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
