import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MovieVideoPage } from './movie-video.page';

describe('MovieVideoPage', () => {
  let component: MovieVideoPage;
  let fixture: ComponentFixture<MovieVideoPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(MovieVideoPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
