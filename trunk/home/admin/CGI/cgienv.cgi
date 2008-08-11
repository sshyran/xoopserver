#!/usr/bin/perl

sub urldecode{ 
local($val)=@_; 
$val=~s/\+/ /g; 
$val=~s/%([0-9A-H]{2})/pack('C',hex($1))/ge; 
return $val;
}
print "Content-Type: text/html\n\n"; 
print "<HTML><HEAD><TITLE>CGI-Variables</TITLE><META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\"><META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\"></HEAD>\n"; 
print "<body>\n";
print "<font color=red><center><b>All CGI enviroment:</b></center></font><HR><BR>\n"; foreach $env_var (keys %ENV){ print "<I><b>$env_var</b>=$ENV{$env_var}</I><BR>\n"; } 
print '<script language="JavaScript">
<!--
function close_window() {
daddy = window.self;
daddy.opener = window.self;
daddy.close();
}
//-->
</script>
<br><center><a href="#" onClick="close_window()">Close This Window</a></center>';
print "</BODY></HTML>\n";

#(c) Anatoliy and Taras Slobodskyy