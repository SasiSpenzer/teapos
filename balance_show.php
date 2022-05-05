<?php
session_start();
ob_start();

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}
?>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="extra_css/asset/js/bootbox.min.js"></script>
<script type="text/javascript" src="jquery.plugin.js"></script>
<script type="text/javascript" src="jquery.keypad.js"></script>
<link type="text/css" rel="stylesheet" href="css/jquery.keypad.css" />
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
</head>
<script type="text/javascript">
    $(document).ready(function(){


    });
</script>
<body>
<?php

$user_obj = new User();
//Prevent the user visiting the logged in page if he/she is already logged in
/*if (isUserLoggedIn()) {
    header("Location: index.php");
    die();
}
*/

//Forms posted
if(isset($_POST['continue'])){

}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="well login-box">

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <legend>Balance Cash Counter</legend>

                    <div class="form-group">
                        <label for="username">Order Balance Cash</label>
                        <input value='<?php echo $_GET['balance'] ; ?>'  id="today_start" name="today_start"  type="text" class="form-control" />
                    </div>

                    <div class="form-group text-center">

                        <input type="submit" name="continue" class="btn btn-success btn-login-submit" value="Continue" />
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="extra_css/asset/js/bootstrap.min.js"></script>

<script type="text/javascript">

    var windowHeight = $(window).height();
    var loginBoxHeight = $('.login-box').innerHeight();
    var welcomeTextHeight = $('.welcome-text').innerHeight();
    var loggedIn = $('.logged-in').innerHeight();

    var mathLogin = (windowHeight / 2) - (loginBoxHeight / 2);
    var mathWelcomeText = (windowHeight / 2) - (welcomeTextHeight / 2);
    var mathLoggedIn = (windowHeight / 2) - (loggedIn / 2);
    $('.login-box').css('margin-top', mathLogin);
    $('.welcome-text').css('margin-top', mathWelcomeText);
    $('.logged-in').css('margin-top', mathLoggedIn);




</script>