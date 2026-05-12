import { ComponentFixture, TestBed } from '@angular/core/testing';
import { TournamentListPage } from './tournament-list.page';

describe('TournamentListPage', () => {
  let component: TournamentListPage;
  let fixture: ComponentFixture<TournamentListPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(TournamentListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
