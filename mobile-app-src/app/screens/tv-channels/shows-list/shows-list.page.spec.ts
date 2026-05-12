import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ShowsListPage } from './shows-list.page';

describe('ShowsListPage', () => {
  let component: ShowsListPage;
  let fixture: ComponentFixture<ShowsListPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowsListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
