import { ComponentFixture, TestBed } from '@angular/core/testing';
import { PopularOnAppPage } from './popular-on-app.page';

describe('PopularOnAppPage', () => {
  let component: PopularOnAppPage;
  let fixture: ComponentFixture<PopularOnAppPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(PopularOnAppPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
