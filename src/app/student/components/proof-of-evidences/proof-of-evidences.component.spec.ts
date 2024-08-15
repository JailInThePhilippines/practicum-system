import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProofOfEvidencesComponent } from './proof-of-evidences.component';

describe('ProofOfEvidencesComponent', () => {
  let component: ProofOfEvidencesComponent;
  let fixture: ComponentFixture<ProofOfEvidencesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ProofOfEvidencesComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ProofOfEvidencesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
