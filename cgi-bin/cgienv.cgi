#!/usr/bin/perl

sub urldecode{ 
local($val)=@_; 
$val=~s/\+/ /g; 
$val=~s/%([0-9A-H]{2})/pack('C',hex($1))/ge; 
return $val;
}
print "Content-Type: text/html\n\n"; 
print "<HTML><HEAD><TITLE>CGI-Variables</TITLE></HEAD>\n"; 
print "<BODY>\n";
print "<font color=red><center><b>All CGI enviroment:</b></center></font><HR><BR>\n"; foreach $env_var (keys %ENV){ print "<I><b>$env_var</b>=$ENV{$env_var}</I><BR>\n"; } 
print "<br><center><a href=\"/\">Back</a></center></center>";
print "</BODY></HTML>\n";

#(c) Anatoliy and Taras Slobodskyy