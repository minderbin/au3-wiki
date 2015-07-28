<?php
$wikitemplate=<<<WIKITEMPLATE
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="A layout example with a side menu that hides on mobile, just like the Pure website.">
    <title>\$WIKINAME - \$title</title>
<link rel="stylesheet" href="theme/pure-min.css">
<link rel="stylesheet" href="theme/theme.css">
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="theme/side-menu-old-ie.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="theme/side-menu.css">
    <!--<![endif]-->
<!--[if lt IE 9]>
    <script src="theme/html5shiv.js"></script>
<![endif]-->
\$head
</head>
<body>
<div id="layout">
    <a href="#menu" id="menuLink" class="menu-link">
        <span></span>
    </a>
    <div id="menu">
        <div class="pure-menu pure-menu-open">
            <a class="pure-menu-heading" href="./">au3</a>
		\$menu
        </div>
    </div>

    <div id="main">
        <div class="header">
            <h1>\$title</h1>
        </div>

        <div class="content">
	    \$content
        </div>
	\$editform
    </div>
</div>
<script src="theme/ui.js"></script>
</body>
</html>
WIKITEMPLATE;

$workingmenu='
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>

                <li class="menu-item-divided pure-menu-selected">
                    <a href="#">Services</a>
                </li>

                <li><a href="#">Contact</a></li>
            </ul>
';

$editform=<<<EDITFORM
<div class='ed'>
        <form action='?\$page' method='post'>
                <input type='hidden' name='e'>
                <input class='pure-button' type='submit' value='EDIT'>
        </form>
</div>
EDITFORM;

$edittemplate=<<<EDITTEMPLATE
<div class='alert'>\$alert</div>
<form action='?\$page' method="post" enctype="multipart/form-data">
        <div class='t'>
                <textarea rows='20' name='t'>\$rawdata</textarea>
        </div>
        <div class='pass'>
                <input type='password' name='pw'><label for='pw'>Password</label>
	</div>
        <div class='sa'>
                <input type='hidden' name='s'>
                <input class='pure-button' type='submit' value='SAVE'>
        </div>
        <div class='upload'>
                Upload Image: <input type='file' name='img' size='25' />
                <input class='pure-button' type='submit' value='Upload' />
        </div>
</form>
EDITTEMPLATE;
