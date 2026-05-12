import { ComponentFixture, TestBed } from '@angular/core/testing';
import { WebPlayerPage } from './web-player.page';

describe('WebPlayerPage', () => {
  let component: WebPlayerPage;
  let fixture: ComponentFixture<WebPlayerPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(WebPlayerPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
