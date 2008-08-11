#!/usr/bin/perl

####################################

# error.log
$logfile = "/tmp/error.log";
#
####################################

print "Content-type: text/html\n\n";
print "<HTML><HEAD><TITLE>Apache log analyzer</TITLE><META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\"><META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\"></HEAD>\n"; 
print "<body>\n";

open (LOG, "$logfile")|| die "Can't open data file!\n";
@log = <LOG>;
close (LOG);

@log=reverse(@log);
splice @log, 50;

print "<font color=red><center>There is a list of recent messages of the Apache Web Server. From the last:</center></font><br>";
foreach $logs (@log) {

#if ($logs=~/error/) {
  print "$logs<br>\n";
#}
}

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

exit;

#(c) Anatoliy and Taras Slobodskyy