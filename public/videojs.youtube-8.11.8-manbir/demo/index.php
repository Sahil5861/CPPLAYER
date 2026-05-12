<!-- prettier-ignore -->


<!DOCTYPE html>
<html lang="en" class="h-100">
    <head>
        <meta charset="utf-8" />

        <!-- General medatada -->
        <meta name="author" content="Daniel Rossi" />
        <meta name="title" content="Youtube" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />


        <!-- Page title -->
        <title>Youtube</title>
 

        <!-- Block for additional page specific header tags -->
        <!-- prettier-ignore -->
            
        <!-- CSS -->
        <link rel="stylesheet" href="css/main.css" />

    <script src="plugins/videojs/js/video.min.js"></script>
    <link rel="stylesheet" href="plugins/videojs/css/video-js.min.css" />
    

         <!-- Plugin Stylesheets -->


             <!-- Plugin Scripts -->
      
                    <script src="js/qualitymenu.min.js"></script>



        <script src="js/youtube-8.11.8.js"></script>






        



 

        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
    </head>

    <body class="font-sans text-xl flex flex-col h-screen">



 


<!--<header class="flex flex-col my-5 items-center justify-center">
            <h1 id="page-title">
                <a
                    href="../../../plugins/videojs/youtube/"
                    class="text-4xl font-bold"
                    rel="bookmark"
                    title="Youtube"
                    >Youtube</a
                >
            </h1>
            <h2><p>Adds Youtube streaming into Video.JS</p></h2>
</header>-->

        <!-- Page content block -->
<section class="bg-gradient-to-b from-gray-100 to-gray-200 markdown">
    <div class="text-gray-700 container px-1 md:px-8 lg:px-4 md:py-32 lg:py-24 mx-auto bg-gray" style="width: 100vw;height: 100vh;padding: 0;">
        <div class="flex flex-col text-left lg:text-center w-full">
        
  
  <div class="flex w-full h-auto my-auto">
      <video class="video-js vjs-default-skin vjs-fluid " crossorigin="anonymous" controls="" id="youtube"></video>
  </div>
<script type="text/javascript">

var player;

 document.addEventListener("DOMContentLoaded", (event) => {
    player = videojs("youtube", {
    "autoplay": true,
    "controlBar": {
        "pictureInPictureToggle": false
    },
    "plugins": {
        "qualitymenu": {},
        "youtube": {}
    },
    "sources": [
        {
            "src": "https://www.youtube.com/watch?v=<?php echo $_GET['youtubeId']; ?>",
            "type": "video/youtube"
        }
    ]
});




 
  });


</script>

        </div>        
    </div>
</section>


        <!-- Footer -->
       


    </body>
</html>