// time-tracking.service.ts

import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class TimeTrackingService {
  private hasTimedOutKey = 'hasTimedOut';

  constructor() {}

  setHasTimedOut(value: boolean) {
    localStorage.setItem(this.hasTimedOutKey, JSON.stringify(value));
  }

  getHasTimedOut(): boolean {
    const value = localStorage.getItem(this.hasTimedOutKey);
    const parsedValue = value ? JSON.parse(value) : false;
    console.log('Retrieved hasTimedOut state:', parsedValue);
    return parsedValue;
  }  
  
}

