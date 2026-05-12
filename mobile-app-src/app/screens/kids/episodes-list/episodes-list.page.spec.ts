import { ComponentFixture, TestBed } from '@angular/core/testing';
import { EpisodesListPage } from './episodes-list.page';

describe('EpisodesListPage', () => {
  let component: EpisodesListPage;
  let fixture: ComponentFixture<EpisodesListPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(EpisodesListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
