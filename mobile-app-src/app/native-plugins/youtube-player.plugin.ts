import { registerPlugin, PluginListenerHandle } from '@capacitor/core';

export interface YouTubePlayerOpenOptions {
  videoId: string;
  title?: string;
  appName?: string;
  introDurationMs?: number;
  outroDurationMs?: number;
}

export interface YouTubePlayerClosedEvent {
  reason: 'dismissed' | 'ended' | 'error';
}

export interface YouTubePlayerPlugin {
  open(options: YouTubePlayerOpenOptions): Promise<void>;
  close(): Promise<void>;
  addListener(
    eventName: 'playerClosed',
    listenerFunc: (event: YouTubePlayerClosedEvent) => void,
  ): Promise<PluginListenerHandle> & PluginListenerHandle;
  removeAllListeners(): Promise<void>;
}

export const YouTubePlayer = registerPlugin<YouTubePlayerPlugin>('YouTubePlayer');
