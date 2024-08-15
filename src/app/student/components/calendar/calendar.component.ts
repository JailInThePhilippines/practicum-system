import { Component } from '@angular/core';

@Component({
  selector: 'app-calendar',
  templateUrl: './calendar.component.html',
  styleUrl: './calendar.component.css'
})

export class CalendarComponent {
  currentDate: Date = new Date();

  getCurrentMonth(): string {
    return this.currentDate.toLocaleString('default', { month: 'long' }) + ' ' + this.currentDate.getFullYear();
  }

  prevMonth(): void {
    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
  }

  nextMonth(): void {
    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
  }

  isToday(date: Date): boolean {
    const today = new Date();
    return date.getFullYear() === today.getFullYear() &&
           date.getMonth() === today.getMonth() &&
           date.getDate() === today.getDate();
  }

  getCalendarWeeks(date: Date): Date[][] {
    const weeks: Date[][] = [];
    const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
    const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
    const startDate = new Date(firstDayOfMonth);
    startDate.setDate(firstDayOfMonth.getDate() - firstDayOfMonth.getDay()); // Move back to the beginning of the week
  
    while (startDate <= lastDayOfMonth) {
      const week: Date[] = [];
      for (let i = 0; i < 7; i++) {
        week.push(new Date(startDate)); // Push a copy of the date to avoid mutating the original
        startDate.setDate(startDate.getDate() + 1);
      }
      weeks.push(week);
    }
    return weeks;
  }
  
}
