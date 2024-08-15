import { ComponentFixture, TestBed } from '@angular/core/testing';

import { COCComponent } from './coc.component';

describe('COCComponent', () => {
  let component: COCComponent;
  let fixture: ComponentFixture<COCComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [COCComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(COCComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
