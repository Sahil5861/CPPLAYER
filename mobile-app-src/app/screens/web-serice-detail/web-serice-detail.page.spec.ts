import { ComponentFixture, TestBed } from '@angular/core/testing';
import { WebSericeDetailPage } from './web-serice-detail.page';

describe('WebSericeDetailPage', () => {
  let component: WebSericeDetailPage;
  let fixture: ComponentFixture<WebSericeDetailPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(WebSericeDetailPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
