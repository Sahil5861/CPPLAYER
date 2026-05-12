import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ChannelsListPage } from './channels-list.page';

describe('ChannelsListPage', () => {
  let component: ChannelsListPage;
  let fixture: ComponentFixture<ChannelsListPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(ChannelsListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
