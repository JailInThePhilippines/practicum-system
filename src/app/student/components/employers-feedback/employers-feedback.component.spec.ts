import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EmployersFeedbackComponent } from './employers-feedback.component';

describe('EmployersFeedbackComponent', () => {
  let component: EmployersFeedbackComponent;
  let fixture: ComponentFixture<EmployersFeedbackComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [EmployersFeedbackComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(EmployersFeedbackComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
