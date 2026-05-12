import { ComponentFixture, TestBed } from '@angular/core/testing';
import { SubscriptionPaymentPage } from './subscription-payment.page';

describe('SubscriptionPaymentPage', () => {
  let component: SubscriptionPaymentPage;
  let fixture: ComponentFixture<SubscriptionPaymentPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(SubscriptionPaymentPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
