import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class FilterService {
  private filterCriteriaSubject = new Subject<any>();
  filterCriteria$ = this.filterCriteriaSubject.asObservable();

  setFilterCriteria(criteria: any) {
    this.filterCriteriaSubject.next(criteria);
  }
}