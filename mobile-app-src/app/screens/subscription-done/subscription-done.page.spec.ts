import { ComponentFixture, TestBed } from '@angular/core/testing';
import { SubscriptionDonePage } from './subscription-done.page';

describe('SubscriptionDonePage', () => {
  let component: SubscriptionDonePage;
  let fixture: ComponentFixture<SubscriptionDonePage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(SubscriptionDonePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
