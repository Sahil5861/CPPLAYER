<!DOCTYPE html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8" />
    <meta name="author" content="Daniel Rossi" />
    <meta name="title" content="Youtube VR360" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Youtube VR360</title>
    <!-- CSS -->
    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="plugins/videojs/css/video-js.min.css" />
    <script src="plugins/videojs/js/video.min.js"></script>
    <!-- Optional fallback -->
    <link rel="stylesheet" href="../../plugins/videojs/css/video-js.min.css" />
    <script src="../../plugins/videojs/js/video.min.js"></script>
    <link rel="stylesheet" href="../../css/main.css" />
    <script src="../../js/youtube-8.11.8.js"></script>
    <script src="../../js/qualitymenu.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .vr-dimensions.vjs-fluid:not(.vjs-audio-only-mode) {
        padding-top: 100vh;
      }
      
      /* Unmute button styling */
      .unmute-overlay {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 2147483647 !important;
        background: rgba(0, 0, 0, 0.85);
        color: white;
        padding: 20px 30px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        display: none;
        transition: all 0.3s ease;
        touch-action: manipulation;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
        pointer-events: auto !important;
      }
      
      .unmute-overlay:active {
        background: rgba(0, 0, 0, 1);
        transform: translate(-50%, -50%) scale(0.95) !important;
      }
      
      .unmute-overlay.show {
        display: block !important;
      }
      
      /* Mobile specific */
      @media (max-width: 768px) {
        .unmute-overlay {
          font-size: 20px;
          padding: 25px 40px;
        }
      }
      
      /* Hide default VideoJS unmute button and big play button when our button is shown */
      .video-js .vjs-mute-control[style*="position: absolute"] {
        /*display: none !important;*/
        position:fixed !important;
        left: calc(100vw - 90px) !important;
        top: 15px !important;
        transform: unset !important;
        font-size:1.75em !important;
      }
      
      .video-js .vjs-big-play-button {
        z-index: 1 !important;
        pointer-events: auto !important;
      }
      
      /* CRITICAL: Ensure controls are fully accessible on mobile */
      .video-js .vjs-control-bar {
        z-index: 10 !important;
        pointer-events: auto !important;
      }
      
      .video-js .vjs-control-bar * {
        pointer-events: auto !important;
      }
      
      /* Make sure video tech doesn't block controls */
      .video-js .vjs-tech {
        pointer-events: none !important;
      }
      
      /* Allow clicks on video for play/pause */
      .video-js.vjs-user-inactive .vjs-tech {
        pointer-events: auto !important;
      }
    </style>
  </head>
  <body class="font-sans text-xl flex flex-col h-screen">
    <section class="bg-gradient-to-b from-gray-100 to-gray-200 markdown">
      <div class="text-gray-700 mx-auto bg-gray">
        <div class="flex flex-col text-left lg:text-center w-full">
          <div class="flex w-full h-auto my-auto" style="position: relative;">
            <!-- Unmute button overlay - inside video container -->
            <video
              id="vr"
              class="video-js vjs-default-skin vjs-fluid"
              crossorigin="anonymous"
              controls
              autoplay
              muted
            ></video>
            
            <div id="unmuteBtn" class="unmute-overlay">
              🔇 Click to Unmute
            </div>
          </div>
          <script type="text/javascript">
            let player;
            
            document.addEventListener("DOMContentLoaded", () => {
              player = videojs("vr", {
                autoplay: true,
                muted: true, // Start muted for autoplay policy
                controls: true,
                plugins: {
                  qualitymenu: {},
                  youtube: { vr: 1 }
                },
                sources: [
                  {
                    src: "https://www.youtube.com/watch?v=<?php echo $_GET['youtubeId'];?>",
                    type: "video/youtube"
                  }
                ]
              });
              
              const unmuteBtn = document.getElementById('unmuteBtn');
              let hasUnmuted = false;
              
              // Show unmute button when video starts playing
              player.on("playing", () => {
                if (!hasUnmuted && player.muted()) {
                  unmuteBtn.classList.add('show');
                  console.log('Unmute button shown');
                }
              });
              
              // Also show on ready if muted
              player.on("ready", () => {
                setTimeout(() => {
                  if (!hasUnmuted && player.muted()) {
                    unmuteBtn.classList.add('show');
                    console.log('Unmute button shown on ready');
                  }
                }, 1000);
              });
              
              // Unmute on button click (both click and touch events)
              unmuteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                player.muted(false);
                player.volume(1);
                hasUnmuted = true;
                unmuteBtn.classList.remove('show');
                unmuteBtn.textContent = '🔊 Sound On!';
                setTimeout(() => {
                  unmuteBtn.style.display = 'none';
                }, 1000);
              });
              
              // Touch event for mobile
              unmuteBtn.addEventListener('touchend', (e) => {
                e.preventDefault();
                e.stopPropagation();
                player.muted(false);
                player.volume(1);
                hasUnmuted = true;
                unmuteBtn.classList.remove('show');
                unmuteBtn.textContent = '🔊 Sound On!';
                setTimeout(() => {
                  unmuteBtn.style.display = 'none';
                }, 1000);
              });
              
              // Also try auto-unmute after 2 seconds (may work in some browsers)
              player.on("play", () => {
                setTimeout(() => {
                  if (!hasUnmuted) {
                    player.muted(false);
                    player.volume(1);
                    // Check if unmute was successful
                    setTimeout(() => {
                      if (!player.muted()) {
                        hasUnmuted = true;
                        unmuteBtn.classList.remove('show');
                        console.log('Auto-unmute successful!');
                      }
                    }, 500);
                  }
                }, 2000);
              });
              
              // Hide button if user manually unmutes via controls
              player.on('volumechange', () => {
                if (!player.muted() && !hasUnmuted) {
                  hasUnmuted = true;
                  unmuteBtn.classList.remove('show');
                }
              });
            });
          </script>
        </div>
      </div>
    </section>
  </body>
</html>