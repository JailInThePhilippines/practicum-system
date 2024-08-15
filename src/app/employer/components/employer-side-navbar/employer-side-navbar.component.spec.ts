import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EmployerSideNavbarComponent } from './employer-side-navbar.component';

describe('EmployerSideNavbarComponent', () => {
  let component: EmployerSideNavbarComponent;
  let fixture: ComponentFixture<EmployerSideNavbarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [EmployerSideNavbarComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(EmployerSideNavbarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
