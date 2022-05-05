<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Swiper demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

    <!-- Link Swiper's CSS -->
    <link rel="stylesheet" href="http://www.idangero.us/swiper/dist/css/swiper.min.css">
    <link rel="stylesheet" href="http://www.idangero.us/swiper/css/main.css">

    <!-- Demo styles -->
    <style>
        body {
            background: #fff;
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color:#000;
            margin: 0;
            padding: 0;
        }
        .swiper-container {
            width: 100%;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .swiper-slide {
            background-position: center;
            background-size: cover;
            width: 300px;
            height: 300px;
        }
    </style>


</head>
<body>
<div class="content center">
    <div class="demo">
        <div class="swiper-container swiper-container-horizontal swiper-container-3d swiper-container-coverflow">
            <div class="swiper-wrapper">
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/1)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/2)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/3)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/4)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/5)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/6)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/7)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/8)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/9)">
                    <img  src="uploads/5.jpg">
                </div>
                <div class="swiper-slide" style="background-image:url(http://lorempixel.com/600/600/nature/10)">
                    <img  src="uploads/5.jpg">
                </div>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="jquery-2.1.3.js"></script>
<!-- Swiper -->
<script src="js/idangerous.swiper.min.js"></script>
<script src="http://www.idangero.us/swiper/dist/js/swiper.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
        // pagination: '.swiper-pagination',
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        coverflow: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows : true
        }
    });
</script>
</body>
</html>