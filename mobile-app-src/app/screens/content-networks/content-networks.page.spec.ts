import { ComponentFixture, TestBed } from '@angular/core/testing';
import { ContentNetworksPage } from './content-networks.page';

describe('ContentNetworksPage', () => {
  let component: ContentNetworksPage;
  let fixture: ComponentFixture<ContentNetworksPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(ContentNetworksPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
