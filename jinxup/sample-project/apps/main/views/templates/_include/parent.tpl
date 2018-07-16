<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="/author" content="">

        <title>{!$APP.name!}</title>

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{!$APP.basePath!}/assets/css/bootstrap.css">
        <link rel="stylesheet" href="{!$APP.basePath!}/assets/css/bootstrap-extend.css">
        <link rel="stylesheet" href="{!$APP.basePath!}/assets/css/sample.css">
        <link rel='stylesheet' href='//fonts.googleapis.com/css?family=Russo+One|Roboto:300,400,500,300italic'>
        {!block "styles"!}{!/block!}
    </head>

    <body class="animsition site-navbar-small site-menubar-hide">
        {!block "content"!}{!/block!}
        <script src="{!$APP.basePath!}/assets/js/jquery.min.js"></script>
        <script src="{!$APP.basePath!}/assets/js/sample.js"></script>
        {!block "scripts"!}{!/block!}
    </body>
</html>