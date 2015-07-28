<?php
$wiki_patterns=array('/_C_(.*?)_C_/ms' => function($m) {return wp("<pre class='prettyprint linenums'>$m[1]</pre>",0); })+$wiki_patterns;
$conf_vars['head'].='<link href="./plugins/gcp/vibrant-ink.css" type="text/css" rel="stylesheet" />';
$conf_vars['head'].='<script type="text/javascript" src="./plugins/gcp/prettify.js"></script>';
$conf_vars['head'].='<script type="text/javascript">window.onload=function(){prettyPrint();};</script>';
?>
