import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ShowListPage } from './show-list.page';

describe('ShowListPage', () => {
  let component: ShowListPage;
  let fixture: ComponentFixture<ShowListPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
