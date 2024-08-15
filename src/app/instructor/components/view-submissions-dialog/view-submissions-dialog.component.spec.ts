import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewSubmissionsDialogComponent } from './view-submissions-dialog.component';

describe('ViewSubmissionsDialogComponent', () => {
  let component: ViewSubmissionsDialogComponent;
  let fixture: ComponentFixture<ViewSubmissionsDialogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ViewSubmissionsDialogComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ViewSubmissionsDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
