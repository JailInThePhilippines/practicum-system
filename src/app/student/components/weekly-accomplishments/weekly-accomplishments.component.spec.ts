import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WeeklyAccomplishmentsComponent } from './weekly-accomplishments.component';

describe('WeeklyAccomplishmentsComponent', () => {
  let component: WeeklyAccomplishmentsComponent;
  let fixture: ComponentFixture<WeeklyAccomplishmentsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [WeeklyAccomplishmentsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(WeeklyAccomplishmentsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
