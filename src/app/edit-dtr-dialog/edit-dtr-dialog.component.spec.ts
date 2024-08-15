import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditDtrDialogComponent } from './edit-dtr-dialog.component';

describe('EditDtrDialogComponent', () => {
  let component: EditDtrDialogComponent;
  let fixture: ComponentFixture<EditDtrDialogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [EditDtrDialogComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(EditDtrDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
