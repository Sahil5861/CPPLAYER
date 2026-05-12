import { ComponentFixture, TestBed } from '@angular/core/testing';
import { LiveDetailsPage } from './live-details.page';

describe('LiveDetailsPage', () => {
  let component: LiveDetailsPage;
  let fixture: ComponentFixture<LiveDetailsPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(LiveDetailsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
