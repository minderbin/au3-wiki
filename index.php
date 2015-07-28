<?php
require('theme/theme.php');
define('pw','$2y$11$6c734ed1feabd58fc18dbeY5ORTNbWt7chYuQsVfZ9L/.nGIwN6tm');
define('BASE_PATH',	'./BASE/');
define('IMG_PATH',	'./IMG/');
define('IMG_TYPES',	'(\.jpg|\.jpeg|\.png|\.gif)');
$pw_valid=((isset($_FILES["img"]) || isset($_POST["s"])) && crypt($_POST['pw'], pw) == pw); //check password, if required
$wiki_patterns=array(
	'/^TITLE\s*=(.*)\n/'			        => function($m)	{global $conf_vars;$conf_vars['title']=$m[1];return "";},
	'/\{\{\{(.*?)\}\}\}/ms'			        => function($m)	{return wp("<pre>$m[1]</pre>",0); },
	'/!!(.*?)!!/'					=> function($m)	{return "<b>$m[1]</b>";},
	'/__(.*?)__/'					=> function($m)	{return "<i>$m[1]</i>";},
	'/--(.*?)--/'					=> function($m)	{return "<s>$m[1]</s>";},
	'/^[-]{4}.*$/m'					=> function()	{return "<hr>";},
	'/^(={1,6})(.*)\n/m' 				=> function($m)	{$c=strlen($m[1]);return "<h$c>$m[2]</h$c>\n";},
	'/\{\{([^|}]*?)'.IMG_TYPES.'\|([^|}]*?)\}\}/'	=> function($m) {return wp("<img src='$m[1]$m[2]' alt='$m[3]'>",0);},
	'/\{([^|}]*?)'.IMG_TYPES.'\|([^|}]*?)\}/'	=> function($m) {return wp("<img src='".IMG_PATH."$m[1]$m[2]' alt='$m[3]'>",0);},
	'/\{\{([^|}]*?)\|([^|}]*?)\}\}/'		=> function($m) {return wp("<a href='$m[1]'>$m[2]</a>",0);},
	'/\{([^|}]*?)\|([^|}]*?)\}/'			=> function($m) {return wp("<a href='?$m[1]'>$m[2]</a>",0);},
	'/^([.#])(.*?)(?:\n|\Z)(?!(\1|(([.#]){2})))/ms'	=> function($m) {$d=(preg_replace('/\n+/','',listify($m[1].$m[2]))); return $d;},
	'/^\{\|(.*?)\|\}/ms'				=> function($m) {return wp("<table><tr>".tabulate($m[1])."</tr></table>",1);},
	'/(?<!(<hr>|\/td>|\/th>|\/h[1-6]>))[\n]+/'	=> function()	{return '<br>';},
);
function tabulate($l){
	$l=preg_replace('/^\s*\|-/m','</tr><tr>',$l);
        $l=preg_replace('/\|\+([^|\n]*)/m','<th>$1</th>',$l);
        $l=preg_replace('/\|([^|\n]*)/m','<td>$1</td>',$l);
	return $l;
}
function listify($l) {
        $tag=($l[0]==='.')?'ul':'ol';
        $l=preg_replace('/^([.#])(?!\1)(.*?)(?:(\Z|\n(?=(\1)(?!(\1)))))/ms',"<li>$2\n</li>\n",$l);
        $l=preg_replace('/^([.#])\1/m',"$1",$l);
        $l=preg_replace_callback('/^([.#])(.*?)(?:\n|\Z)(?!(\1|(([.#]){2})))/ms', function($m) {return listify($m[1].$m[2]);}, $l);
        return "<$tag>\n$l</$tag>";
}
$conf_vars=array(
	'WIKINAME'=>'au3',
	'title'=>'',
	'page'=>'',
	'content'=>'',
	'rawdata'=>'',
	'alert'=>'',
	'head'=>'',
	'editform'=>'',
	'menu'=>render(htmlspecialchars(file_get_contents('./BASE/menu')),$wiki_patterns)
	);
// Load plugins
foreach (glob('plugins/*.php') as $plugin) @include($plugin);
// Handle image uploads
if (isset($_FILES["img"]) && $_FILES["img"]["error"]==UPLOAD_ERR_OK && preg_match('/(.*)\.(.*)/',$_FILES['img']['name'],$match) && exif_imagetype($_FILES['img']['tmp_name'])) {
	if ($pw_valid) {
		move_uploaded_file($_FILES['img']['tmp_name'], IMG_PATH.$match[1].'_'.time().'.'.$match[2]);
		$conf_vars['alert'] = $match[1].'_'.time().'.'.$match[2] . ' uploaded.';
	} else {
		$conf_vars['alert'] = 'Invalid password!';
	}
}
// Fallback to index page if no page specified in url
if (!$conf_vars['page']=$conf_vars['title']=basename(reset(array_keys($_GET)))) {
	$conf_vars['page']=$conf_vars['title']="index";
}
// Save edited page if requested
if (isset($_POST["s"])) {
	if ($pw_valid) {
		file_put_contents(realpath(BASE_PATH)."/".$conf_vars['page'],$_POST["t"]);
	} else {
		$conf_vars['alert'] = 'Invalid password!';
	}
}
// Render page
$conf_vars['rawdata']=(file_exists(BASE_PATH.$conf_vars['page']))?htmlspecialchars(file_get_contents(BASE_PATH.$conf_vars['page'])):'';
if (isset($_POST["e"]) || (isset($_FILES['img'])&&($_FILES["img"]["error"]==UPLOAD_ERR_OK)||($conf_vars['alert']))) {
	if ($conf_vars['alert']) {
		$conf_vars['rawdata'] = $_POST['t']; // preserve changes made by user
	}
	$conf_vars['content']=tpl_render($edittemplate,$conf_vars);
} else {
	$conf_vars['content']=render($conf_vars['rawdata'],$wiki_patterns);
	$conf_vars['editform']=tpl_render($editform,$conf_vars);
}
echo tpl_render($wikitemplate,$conf_vars);
$preserved_strings = array(array(),array());
function wp($text,$depth) {
        global $preserved_strings;
        $gensym = '_GS_' .$depth.'_'.count($preserved_strings[$depth]);
        $preserved_strings[$depth][$gensym] = $text;
        return "\$$gensym";
}
function render($data,$patterns=array()) {
        global $preserved_strings;
        $preserved_strings = array(array(),array());
	$tmp=$data;
	foreach ($patterns as $key => $val) {
		$tmp=preg_replace_callback($key,$val,$tmp);
	}
        return tpl_render(tpl_render($tmp, $preserved_strings[1]), $preserved_strings[0]);
}
function tpl_render($text, $conf_vars) {
	global $cv;
	$cv=$conf_vars;
        return preg_replace_callback('/\\$(\\w+)/',function($m){global $cv; return isset($cv[$m[1]]) ? $cv[$m[1]] : "$$m[1]";}, $text);
}
?>
