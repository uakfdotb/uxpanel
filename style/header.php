<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>UXPanel</title>

    <!-- Le styles -->
    <link href="<?= basePath() ?>/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="<?= basePath() ?>/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="./">uxpanel</a>
          <div class="nav">
            <ul class="nav">
              <? foreach($navbar as $i_page => $desc) { ?>
                <li <? if($page == $i_page) echo "active"; ?>><a href="<?= $i_page ?>"><?= $desc ?></a></li>
              <? } ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

