<html>
<head>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<title>Xoops Server configuration</title>
</head>
<body bgcolor="#CCCCFF">
<center>
<script language="JavaScript">
<!--
function close_window() {
daddy = window.self;
daddy.opener = window.self;
daddy.close();
}
//-->
</script>
<font size=5>The Xoops Sever configuration</font><br><br>
<?php
//-------------------------------------------------------------------- variables

# -- Determines Apache version.
if (preg_match("/Apache\/2/i", $_SERVER["SERVER_SOFTWARE"])) {
	$Apache2=True;
} else {
	$Apache2=False;
}
# --

$apache = new Config ("/usr/local/apache2/conf/httpd.conf","#");
$apache->Var_Name =array ("ServerName","ServerAdmin","DirectoryIndex","AddHandler server-parsed","ServerSignature");
$apache->Var_Text =array ("Server name","Webmaster email","Directory Index files","Server-Side Includes in",
"Add a line containing the server signature");
$apache->Var_Help =array ("http://httpd.apache.org/docs/mod/core.html#servername",
"http://httpd.apache.org/docs/mod/core.html#serveradmin",
"http://httpd.apache.org/docs/mod/mod_dir.html#directoryindex",
"http://httpd.apache.org/docs/mod/mod_include.html",
"http://httpd.apache.org/docs/mod/core.html#serversignature");

# -- Determines PHP mode
if (!preg_match("/^cgi/",php_sapi_name())) {
	$PHPmod=True;
} else {
	$PHPmod=False;
}
# --

$PHP = new Config ("/usr/local/APACHE2/php.ini",";");
$PHP->Var_Name =array ("safe_mode =","expose_php =", "register_globals =", "max_execution_time =",
"memory_limit =", "display_errors =", "post_max_size =", "upload_max_filesize =", "magic_quotes_gpc =", "SMTP =", "sendmail_from =");
$PHP->Var_Text =array ("Safe mode","Show PHP in apache signature", "Register globals",
"Maximum execution time of each script, in seconds",
"Maximum amount of memory a script may consume (8MB)",
"Print out errors (as a part of the output)",
"Maximum size of POST data that PHP will accept",
"Maximum allowed size for uploaded files",
"magic_quotes_gpc",
"SMTP (mail configuration)",
"From (mail configuration)");
$PHP->Var_Help =array ("http://php.benscom.com/manual/en/features.safe-mode.php",
"http://www.php.net/manual/en/security.hiding.php#security.hiding",
"http://www.php.net/release_4_1_0.php",
"http://www.php.net/manual/en/configuration.php#ini.max-execution-time",
"http://www.php.net/manual/en/configuration.php#ini.memory-limit",
"http://www.php.net/manual/en/configuration.php#ini.display-errors",
"http://www.php.net/manual/en/features.file-upload.php#features.file-upload.post-method",
"http://www.php.net/manual/en/configuration.php#ini.upload-max-filesize",
"http://www.php.net/manual/en/function.get-magic-quotes-gpc.php",
"http://www.php.net/manual/en/ref.mail.php",
"http://www.php.net/manual/en/ref.mail.php");
//-------------------------------------------------------------------- main program
//import_request_variables("gP", "st");
$step=$HTTP_POST_VARS['Submit'];
if ($step=="next") {
	echo "something<br>";
}else{
if ($step=="Save") {
	$apache->replace_values ($HTTP_POST_VARS);
	$PHP->replace_values ($HTTP_POST_VARS);
	echo "<br><font color=red>The changes have been sucssesfuly saved.<br> The changes will have effect after restart of the server!</font><br>";
}?>

<form action="
<?php
	echo $_SERVER["PHP_SELF"]."\" name=f method=\"POST\"><font size=4>Apache configuration:</font><br>";
	$apache->echo_values ();
	echo "<br><font size=4>PHP configuration:</font><br>";
	$PHP->echo_values ();
?>
<br><input type=submit value="Save" name=Submit>
</form><br><font color=red>
<?php
if ($PHPmod==True) {
	echo "At the moment PHP is loaded as Apache module.";
} else {
	echo "At the moment PHP scripts are executed thought Apache CGI interface.";
}
}
//-------------------------------------------------------------------- functions
class Config
{
var $contents;
var $name;
var $comments;
var $Var_Name;
var $Var_Text;
var $Var_Help;
var $classnum;
function Config ($file_name, $comments)
{
	$this->comments=$comments;
	$this->name=$file_name;
	$fd = fopen ($this->name, "r");
	$this->contents = fread ($fd, filesize ($file_name));
	fclose ($fd);
	$this->classnumber=$GLOBALS["$Configclassnumber"]=$GLOBALS["$Configclassnumber"]+1;
}
function f_write ()
{
	$fd = fopen ($this->name, "w");
	$ok = fwrite ($fd, $this->contents);
	fclose ($fd);
}
function echo_values ()
{
	echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$item=0;
	foreach ($this->Var_Name as $loop){
		$Var_ID="C".$this->classnumber."i".$item;
		$Var_Name=$this->Var_Name[$item];
		$Var_Text=$this->Var_Text[$item];
		$Var_Help=$this->Var_Help[$item];
		$comments=$this->comments;
		preg_match("/\n\s*$Var_Name\s+([^$comments^\n]+)/i", $this->contents, $tag);
		echo "<tr><td align=\"right\" width=\"50%\">$Var_Text:</td><td><input type=text name=\"$Var_ID\" size=31 maxlength=2048 value='$tag[1]'> ";
		if ($Var_Help != "") {echo "<a href=\"$Var_Help\" target=blank>help</a>";};
		echo "</td></tr>";
		$item=$item+1;
	}
	echo "</table>";
}
function replace_values ($HTTP_POST_VARS)
{
	$item=0;
	foreach ($this->Var_Name as $loop){
		$Var_ID="C".$this->classnumber."i".$item;
		$data=$HTTP_POST_VARS[$Var_ID];
		$comments=$this->comments;
		$this->contents=preg_replace("/\n(\s*$loop)\s+([^$comments]+)/i", "\n\\1 $data\n", $this->contents, 1);
		$item=$item+1;
		}
	$this->f_write ();
}

}
?>
</font><br><br>Be sure that you know what you are doing.
<br><a href="#" onClick="close_window()">Close This Window</a>
</center>
</body></html>