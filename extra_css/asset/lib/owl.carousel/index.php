<!DOCTYPE html>
<html lang="en">
  <head>

    <!-- head -->
    <meta charset="utf-8">
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="How to use responsive options">
    <meta name="author" content="Bartosz Wojciechowski">
    <title>
      Responsive Demo | Owl Carousel | 2.0.0-beta.2.4
    </title>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- Owl Stylesheets -->
    <link rel="stylesheet" href="assets/owl.carousel.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- Favicons -->


    <!-- Yeah i know js should not be in header. Its required for demos.-->

    <!-- javascript -->
   
    <script src="owl.carousel.js"></script>
	<style>
	.item h4 {
		width:100px;
		height:100px;
		background-color:red;
	}
	</style>
	<script>
	 
            $(document).ready(function() {
              $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 10,
                responsiveClass: true,
                responsive: {
                  0: {
                    items: 1,
                    nav: true
                  },
                  600: {
                    items: 3,
                    nav: false
                  },
                  1000: {
                    items: 5,
                    nav: true,
                    loop: false,
                    margin: 20
                  }
                }
              });
			  
			   $('.owl-carousel2').owlCarousel({
                loop: true,
                margin: 10,
                responsiveClass: true,
                responsive: {
                  0: {
                    items: 1,
                    nav: true
                  },
                  600: {
                    items: 3,
                    nav: false
                  },
                  1000: {
                    items: 5,
                    nav: true,
                    loop: false,
                    margin: 20
                  }
                }
              })
            })
         
	</script>
  </head>
  <body>

    <!-- header -->
    <header class="header">
      <div class="row">
        <div class="large-12 columns">
          <div class="brand left">
            <h3>
              <a href="/">owl.carousel.js</a> 
            </h3>
          </div>
          <a id="toggle-nav" class="right">
            <span></span> <span></span> <span></span> 
          </a> 
        
        </div>
      </div>
    </header>

    <!-- title -->
    <section class="title">
      <div class="row">
        <div class="large-12 columns">
          <h1>Responsive</h1>
        </div>
      </div>
    </section>

    <!--  Demos -->
    <section id="demos">
      <div class="row">
        <div class="large-12 columns">
          <div class="owl-carousel">
            <div class="item">
              <h4>1</h4>
            </div>
            <div class="item">
              <h4>2</h4>
            </div>
            <div class="item">
              <h4>3</h4>
            </div>
            <div class="item">
              <h4>4</h4>
            </div>
            <div class="item">
              <h4>5</h4>
            </div>
            <div class="item">
              <h4>6</h4>
            </div>
            <div class="item">
              <h4>7</h4>
            </div>
            <div class="item">
              <h4>8</h4>
            </div>
            <div class="item">
              <h4>9</h4>
            </div>
            <div class="item">
              <h4>10</h4>
            </div>
            <div class="item">
              <h4>11</h4>
            </div>
            <div class="item">
              <h4>12</h4>
            </div>
          </div>
         
         
        </div>
      </div>
	  
	  
	  <div class="row">
        <div class="large-12 columns">
          <div class="owl-carousel2">
            <div class="item">
              <h4>1</h4>
            </div>
            <div class="item">
              <h4>2</h4>
            </div>
            <div class="item">
              <h4>3</h4>
            </div>
            <div class="item">
              <h4>4</h4>
            </div>
            <div class="item">
              <h4>5</h4>
            </div>
            <div class="item">
              <h4>6</h4>
            </div>
            <div class="item">
              <h4>7</h4>
            </div>
            <div class="item">
              <h4>8</h4>
            </div>
            <div class="item">
              <h4>9</h4>
            </div>
            <div class="item">
              <h4>10</h4>
            </div>
            <div class="item">
              <h4>11</h4>
            </div>
            <div class="item">
              <h4>12</h4>
            </div>
          </div>
         
         
        </div>
      </div>
    </section>

    <!-- footer -->
    
  </body>
</html>