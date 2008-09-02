<?

include_once "/home/admin/www/plugins/themeheader.html";

echo '<h2>Dot HTAccesser - .htaccess ファイル ジェネレータ</h2>
<h3>Sometimes it\'s easier online</h3>';

if (isset($_REQUEST['submit']))
{
//export the form submits to global variables
while (isset($_REQUEST)&&(list($k,$v)=each($_REQUEST)))
{ if ($v) { $$k=$v; } } 

$output="";
$options="";
$cgi_hand="";
if (isset($opt_execCGI))
{
$options.=" +execCGI"; 
if (isset($handle_cgi)) { $cgi_hand.=" cgi"; }
if (isset($handle_pl)) { $cgi_hand.=" pl"; }
if (isset($handle_exe)) { $cgi_hand.=" exe"; }
if (isset($handle_sh)) { $cgi_hand.=" sh"; }
if (isset($cgi_hand)) {
$output.="\nAddHandler cgi-script $cgi_hand"; 
}
};

if (isset($opt_include))
{ $options.=" +Includes"; }
else
{
if (isset($opt_includeNOEXEC))
{ $options.=" +IncludeNOEXEC"; }
}

if (isset($opt_FollowSymLinks))
{ $options.=" +FollowSymLinks"; }

if (isset($opt_FollowSymLinksIfOwnerMatch))
{ $options.=" +FollowSymLinksIfOwnerMatch"; }

if (isset($opt_indexes))
{ $options.=" +Indexes"; }

if (isset($opt_multiview))
{ $options.=" +MultiViews";}

if (isset($auth_name)||isset($auth_user)||isset($auth_group))
{ $output.="\nAuthType Basic"; }

if (isset($auth_name))
{ $output.="\nAuthName \"$auth_name\""; }

if (isset($auth_user))
{  $output.="\nAuthUserFile $auth_userpath"; }

if (isset($auth_group))
{  $output.="\nAuthGroupFile $auth_userpath"; }

if (isset($auth_denyall))
{ $output.="\nOrder allow,deny"; }
else
{ $output.="\nOrder deny,allow"; }

if (isset($satisfy_any))
{ $output.="\nSatisfy Any"; }

if (isset($auth_valid_user))
{ $output.="\nRequire valid-user"; }

if (isset($auth_allow_users))
{ $output.="\nRequire user $auth_allow_users"; }

if (isset($auth_allow_groups))
{ $output.="\nRequire group $auth_allow_groups"; }

if (isset($auth_allow_ip))
{ $output.="\nAllow from $auth_allow_ip"; }

if (isset($auth_deny_ip))
{ $output.="\nDeny from $auth_deny_ip"; }

if (isset($mime_types))
{
if (is_array($mime_types))
{
while (list($k,$v)=each($mime_types))
{
$output.="\nAddType $v";
}
} else
{
$output.="\nAddType $mime_types";
}
}

if (isset($opt_includeNOEXEC)||isset($opt_include))
{ 
if (isset($opt_include_ext))
{ $output.="\nAddType text/html $opt_include_ext\nAddHandler server-parsed $opt_include_ext";};
};

if (isset($protect)) {
$output.="\n<Files .htaccess .htpasswd .htuser .htgroups $protect_files>";
$output.="\norder allow,deny\ndeny from all\n</Files>"; 
}


if (isset($redirect)) {
$output.="\nRedirect permanent /$redirect_file $redirect_url";
}

if (isset($force_ssl))
{
$output.="\n<IfModule !mod_ssl.c>";
$output.="\nRedirect permanent / https://$force_ssl_domain/";
$output.="\n</IfModule>";

}

if (isset($no_index)) {
$output.="\nIndexIgnore */*";
}

if (isset($cache))
{
$output.="\nExpiresActive on\nExpiresDefault ";
if (isset($cache_server))
{ $output.="M"; }
else
{ $output.="A"; }
$output.=$cachelength;
}

if (isset($check_media_referrer)) { $modrewrite="true"; }
if (isset($failed_redirect))	{ $modrewrite="true"; }
if (isset($user_dir)) { $modrewrite="true"; }
if (isset($timed_pages)) { $modrewrite="true"; }
if (isset($block_harvesters)) { $modrewrite="true"; }
if (isset($rewrite_browser_page)) { $modrewrite="true"; }
if (isset($remap_script)&&isset($remap_folder)) { $modrewrite="true"; }

if (isset($modrewrite)&&($modrewrite!="false"))
{
$output.="\nRewriteEngine  on";

if (isset($check_media_referrer)) {
$output.="\n".'RewriteCond %{HTTP_REFERER} !^$';
$output.="\n".'RewriteCond %{HTTP_REFERER} !^http://(www\.)?'.$referrer_domain.'/.*$ [NC]';
$output.="\n".'RewriteRule \.(gif|jpg|png|mp3|mpg|avi|mov)$ - [F]  ';
}

if (isset($failed_redirect))
{
$output.="\n".'RewriteCond   %{REQUEST_URI} !-U';
$output.="\n".'RewriteRule   ^(.+)          http://'.$failed_redirect_server.'/$1';
}

if (isset($user_dir)) {
$user_domain=str_replace('.','\.',$user_domain);
$output.="\n".'RewriteCond   %{HTTP_HOST}                 ^www\.[^.]+\.'.$user_domain.'$';
$output.="\n".'RewriteRule   ^(.+)                        %{HTTP_HOST}$1          [C]';
$output.="\n".'RewriteRule   ^www\.([^.]+)\.'.$user_domain.'(.*) /'.$user_dir_path.'$1$2';
}

if (isset($timed_pages))
{
$timed_page=str_replace('.','\.',$timed_page);
$output.="\n".'RewriteCond   %{TIME_HOUR}%{TIME_MIN} >'.$timed_page_start;
$output.="\n".'RewriteCond   %{TIME_HOUR}%{TIME_MIN} <'.$timed_page_end;
$output.="\n".'RewriteRule   ^'.$timed_page.'$	'.$timed_page_day;
$output.="\n".'RewriteRule   ^'.$timed_page.'$	'.$timed_page_night;
}
if (isset($block_harvesters)) {
$output.="\nRewriteCond %{HTTP_USER_AGENT} Wget [OR] ";
$output.="\nRewriteCond %{HTTP_USER_AGENT} CherryPickerSE [OR] ";
$output.="\nRewriteCond %{HTTP_USER_AGENT} CherryPickerElite [OR] ";
$output.="\nRewriteCond %{HTTP_USER_AGENT} EmailCollector [OR] ";
$output.="\nRewriteCond %{HTTP_USER_AGENT} EmailSiphon [OR] ";
$output.="\nRewriteCond %{HTTP_USER_AGENT} EmailWolf [OR] ";
$output.="\nRewriteCond %{HTTP_USER_AGENT} ExtractorPro ";
$output.="\nRewriteRule ^.*$ $block_doc [L]";
}

if (isset($rewrite_browser_page))
{ //rewrite browser pages
$rw_page='^'.str_replace('.','\.',$rewrite_browser_page).'$';
if (isset($geoip_country))
{
$output.="\nRewriteCond %{ENV:GEOIP_COUNTRY_CODE} $geoip_country [NC]";
$output.="\nRewriteRule $rw_page $geoip_page [L]\n";
}
if (isset($rewrite_browser_page_ns))
{
$output.="\n".'RewriteCond %{HTTP_USER_AGENT}  ^Mozilla/[345].*Gecko*';
$output.="\nRewriteRule $rw_page $rewrite_browser_page_ns [L]\n";
}
if (isset($rewrite_browser_page_ie))
{
$output.="\n".'RewriteCond %{HTTP_USER_AGENT}  ^Mozilla/[345].*MSIE*';
$output.="\nRewriteRule $rw_page $rewrite_browser_page_ie [L]\n";
}
if (isset($rewrite_browser_page_lynx))
{
$output.="\n".'RewriteCond %{HTTP_USER_AGENT}  ^Mozilla/[12].* [OR]';
$output.="\n".'RewriteCond %{HTTP_USER_AGENT}  ^Lynx/*';
$output.="\nRewriteRule $rw_page $rewrite_browser_page_lynx [L]\n";
}

if (isset($rewrite_browser_page_default))
{
$output.="\nRewriteRule $rw_page $rewrite_browser_page_default [L]\n";
}

}
if (isset($remap_script)&&isset($remap_folder))
{
$output.="\nRewriteRule $remap_folder(.*) /$remap_script$1 [PT]";
}
}
if (isset($error_400)) { $output.="\nErrorDocument 400 $error_400"; }
if (isset($error_401)) { $output.="\nErrorDocument 401 $error_401"; }
if (isset($error_403)) { $output.="\nErrorDocument 403 $error_403"; }
if (isset($error_404)) { $output.="\nErrorDocument 404 $error_404"; }
if (isset($error_500)) { $output.="\nErrorDocument 500 $error_500"; }
if (isset($default_page)) { $output.="\nDirectoryIndex $default_page"; }

if ($options) { $output="Options $options\n".$output; }
?>


<h3>Your .htaccess file contents</h3>
<p>Copy the lines below and paste them into your .htaccess file</p>

<textarea cols=80 rows=20><?=$output;?></textarea>
<?php };?>

<form method="post" action="<?=$_SERVER["PHP_SELF"]?>">

<table class="outer">
<tr>
<th colspan="2">Default Page(デフォルトページ)</th>
</tr>
<tr>
<td class="head">ユーザが特に指定していない場合にロードするページ (通常 index.html または index.php)</td>
<td class="odd">
Directory Index : <input name="default_page" type="text" size="40">
<br />
リスト中に複数指定可 (例 index.php index.html index.htm default.htm</td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Options(オプション)</th>
</tr>
<tr>
<td width="50%" class="head" >mod_cgiを使ったCGIスクリプトの実行は許可されている。</td>
<td class="odd">
<input type=checkbox name="opt_execCGI" value="false"> execute CGI programs</td>
</tr>
<tr>
<td class="head" >ファイル拡張</td>
<td class="even"> 
<input type=checkbox name="handle_cgi" value="false"> .cgi
<br /><input type=checkbox name="handle_pl" value="false"> .pl
<br /><input type=checkbox name="handle_exe" value="false"> .exe
<br /><input type=checkbox name="handle_sh" value="false"> .sh  </td>
</tr>
<tr>   
<td class="head">mod_includeが提供するServer-side includes は許可されている。</td>
<td class="odd">
<input type=checkbox name="opt_include" value="false"> include files (SSI)
<br /><input type=checkbox name="opt_includeNOEXEC" value="false"> or without #exec 
<br /></td>
</tr>
<tr>
  <td class="head">ファイル拡張</td>
  <td class="even"><input type=text name="opt_include_ext" value="shtml"></td>
</tr>
<tr>
<td class="head">サーバはこのディレクトリ中のシンボリックリンクをフォローする。</td>
<td class="odd"><input type=checkbox name="opt_FollowSymLinks" value="false"> Follow Symbolic Links</td>
</tr>
<tr>
<td class="head">サーバはターゲットファイルまたはディレクトリがリンクと同じユーザＩＤに所有されている時のみシンボリックリンクをフォローする。</td>
<td class="even"><input type=checkbox name="opt_SymLinksIfOwnerMatch" value="false"> Follow Symbolic Links if owner matches</td>
</tr>
<tr>
<td class="head">ディレクトリにマッピングされるURLがリクエストされた時、ディレクトリ中にディレクトリインデックス(例 index.html) が無い場合、mod_autoindex はディレクトリの整形されたリストをリターンする。        </td>
<td class="odd"> <input type=checkbox name="opt_indexes" value="false"> Indexes</td>
</tr>
<tr>
<td class="head">"MultiViews"ネゴシエイトされたコンテンツは mod_negotiationを使用可。</td>
<td class="even"> <input type=checkbox name="opt_multiview" value="false"> Content Negotiation (MultiViews)</td>
</tr>
<tr>
<td class="head">HTTP リクエストを強制的に HTTPSにリダイレクト</td>
<td class="odd"> <input type=checkbox name="force_ssl" value="false"> Force SSL<br />		</td>
</tr>
<tr>
<td class="head">SSL ドメイン</td>
<td class="even"><input type=text name="force_ssl_domain" value="www.domain.com" size="40"></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Authentication(認証)</th>
</tr>
<tr>
<td class="head" width="50%">アクセスパーミッション定義</td>
<td class="odd">
<input type=checkbox name="auth_denyall" value="false">
Deny by default<br>
<input type=checkbox name="auth_valid_user" value="false"> 
Require valid username<br>
<input type=checkbox name="satisfy_any" value="false">
All if user OR ip matches<br>
<input type=checkbox name="auth_user" value="false">
User Authentication<br>
<input type=checkbox name="auth_group" value="false"> Group Authentication
</td>
</tr>

<tr>
<td class="head">エリア名</td>
<td class="even"> <input type=text name="auth_name" size="40"></td>
</tr>
<tr>
<td class="head">ユーザファイルへのパス</td>
<td class="odd"><input type=text name="auth_userpath" size="40"> </td>
</tr>
<tr>
<td class="head">グループファイルへのパス</td>
<td class="even"><input type=text name="auth_grouppath" size="40"> </td>
</tr>
<tr>
<td class="head">許可ユーザ</td>
<td class="odd"><input type=text name="auth_allow_users" value="" size="40"></td>
</tr>
<tr>
<td class="head">許可グループ</td>
<td class="even"><input type=text name="auth_allow_groups" value="" size="40"></td>
</tr>
<tr>
<td class="head">許可されたIPアドレス(wildcards and names allowed)</td>
<td class="odd"><input type=text name="auth_allow_ip" value="" size="40"></td>
</tr>
<tr>
<td class="head">ブロックされたIPアドレス(wildcards and names allowed)</td>
<td class="even"><input type=text name="auth_deny_ip" value="" size="40"></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Additional Mime Types(Mime タイプ追加)</td>  </tr>
<tr>
<td class="head" width="50%"><br />
<p align="center">Mimeタイプに対するファイル拡張のマッピングは次のフォーマット: </p>
<br />
<div style="width:50%; margin:5px auto;"> mime/type ext 
<br /> for example 
<br /> text/html html 
<br /> application/x-gzip gz<br />
</div></td>
<td class="odd">
<p>
<select name="mime_types[]" size="12" multiple=true>
<?
$fp=fopen("./mime.types","r");
if ($fp)
{ while (!feof($fp))
{
$line=trim(fgets($fp,4096));
$ext=strstr($line," ");
echo "<option value=\"$line\">$ext</option>";
}
fclose($fp);
}
?>
</select>
</p></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Protect System Files(システムファイル保護)</th>
</tr>
<tr>
<td class="head" width="50%">保護ファイル追加</td>
<td class="odd">
<input type=text name="protect_files" size="40"></td>
</tr>
<tr>
<td class="head">&nbsp;</td>
<td class="even"><input type=checkbox name="protect"> Protect .htaccess and user and group files</td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">File Cache Control(ファイルキャッシュ制御)</th>
</tr>
<tr>
<td width="50%" class="head">client/proxy のファイルリフレッシュ頻度</td>
<td class="odd"><input type=checkbox name="cache"> キャッシュ時間を指定</td>
</tr>
<tr>
<td class="head">全ての clients/proxiesが同時に期限切れとなる</td>
<td class="even"><input type=checkbox name="cache_server"> Modification Based</td>
</tr>
<tr>
<td class="head">キャッシュ時間</td>
<td class="odd">
<select name=cachelength>
<OPTION VALUE="31536000">1 年</OPTION>
<OPTION VALUE="15768000">6 ヵ月</OPTION>
<OPTION VALUE="78844000">3 ヵ月</OPTION>
<OPTION VALUE="2592000">1 ヵ月</OPTION>
<OPTION VALUE="604800" SELECTED>1 週</OPTION>
<OPTION VALUE="86400">1 日</OPTION>
<OPTION VALUE="3600">1 時間</OPTION>
<OPTION VALUE="60">1 分</OPTION>
</select>
</td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">ModRewrite</th>
</tr>
<tr>
<td class="head" width="50%">メディアファイル保護<br />
リファラードメインの画像、音楽、サウンドファイルをチェックする</td>
<td class="odd"><input type=checkbox name="check_media_referrer"> On</td>
</tr>
<tr>
<td class="head">許可ドメイン:</td>
<td class="even"><input type=text name="referrer_domain" value="yourdomain.com" size="40"></td>
</tr>
<tr>
<td class="head">E-mail 収集をブロック<br />
e-mailアドレス収集プログラムのアクセスを禁止。</td>
<td class="odd">
<input type=checkbox name="block_harvesters"> On</td>
</tr>
<tr>
<td class="head">サーバへのページ:</td>
<td class="even"> <input type=text name="block_doc" value="deny.html" size="40"></td>
</tr>
<tr>
<td class="head">時刻依存ページ<br />
日時指定でページを提供</td>
<td class="odd"><input type=checkbox name="timed_pages"> On</td>
</tr>
<tr>
<td class="head">ページ名 : </td>
<td class="even"><input type=text name="timed_page" value="page.html" size="40"></td>
</tr>
<tr>
<td class="head">日時開始 :</td>
<td class="odd"><input type=text name="timed_page_start" value="0600" size="40"></td>
</tr>
<tr>
<td class="head">日時終了 :</td>
<td class="even"> <input type=next name="timed_page_end" value="1800" size="40"></td>
</tr>
<tr>
<td class="head">昼間ページ :</td>
<td class="odd"> <input type=text name="timed_page_day" value="page.day.html" size="40"><br /></td>
</tr>
<tr>
<td class="head">夜間ページ :</td>
<td class="even"> <input type=text name="timed_page_night" value="page.night.html" size="40"></td>
</tr>
<tr>
<td class="head">フォルダーへのバーチャル DNS<br />
バーチャルサブドメインをサブフォルダーにリライト。例: rewrite www.foo.domain.com to www.domain.com/subdomains/foo.  バーチャルユーザドメインに有効。</td>
<td class="odd"><input type=checkbox name="user_dir"> On</td>
</tr>
<tr>
<td class="head">ベースドメイン:</td>
<td class="even"><input type=text name="user_domain" value="domain.com" size="40"></td>
</tr>
<tr>
<td class="head">フォルダーへのパス:</td>
<td class="odd"><input type=text name="user_dir_path" value="home" size="40"></td>
</tr>
<tr>
<td class="head">失敗したURLを他サーバへリダイレクト<br />
不正なURLや、エラーを引き起こすURLはセカンダリサーバへリダイレクト。</th>
<td class="even">
On: <input type=checkbox name="failed_redirect"><br /></td>
</tr>
<tr>
<td class="head">セカンダリサーバ:
<td class="odd"><input type=text name="failed_redirect_server" value="server2.domain.com"></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2"><h3>Rewrite Condition(書き換え条件)</h3></th>
</tr>
<tr>
<td class="head" width="50%">URL中にリクエストされたページを書き換える。ページ名 :</td>
<td class="odd"><input type=text name="rewrite_browser_page" size="40"><br /></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Browser Dependant Page(ブラウザ依存ページ)</th>
</tr>
<tr>
<td class="head" width="50%">Netscape用ページ</td>
<td class="odd"><input type=text name="rewrite_browser_page_ns" size="40"></td>
</tr>
<tr>
<td class="head">IE用ページ</td>
<td class="even"><input type=text name="rewrite_browser_page_ie" size="40"></td>
</tr>
<tr>
<td class="head">Lynx用ページ. テキストモードを使用。</td>
<td class="odd"> <input type=text name="rewrite_browser_page_lynx" size="40"></td>
</tr>
<tr>
<td class="head">その他ブラウザのデフォルトページ</td>
<td class="even"> <input type=text name="rewrite_browser_page_default" size="40"></td>
</tr>
</table>
<br />
<br />
<table class="outer">     
<tr>
<th colspan="2">Country Specific Page(各国固有ページ)</th>
</tr>
<tr>
<td colspan="2" class="odd"><a href="http://www.maxmind.com/app/mod_geoip">mod_geoip</a>のセットアップとサーバの設定が必要です。ソフトウェアはフリーですが、データファイルは商用プロダクトです。居住国に従い、ユーザを特定のページにリダイレクトできます。</td>
</tr>         
<tr>
<td class="head" width="50%">
国コード<br />
US = 合衆国, GB = イギリス, CA = カナダ, MX = メキシコ, FR = フランス, NL = オランダ, A1 = 匿名<br /></td>
<td class="even"><input type=text name="geoip_country" size="40"></td>
</tr>           
<tr>
<td class="head">その国からの訪問者をリダイレクトする国固有のURLページ (index.us.html or index.fr.html)</td>
<td class="odd">
<input type=text name="geoip_page" size="40"></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Map Folder To Script</th>
</tr>
<tr>
<td colspan="2" class="odd">このトリックにより、URL中にパラメータを持つスクリプトを実行することができます。利用者が /login/home.html や /login/preferences.html などにアクセスして両方とも login.php にとばす、といったことができるカスタムホームページスクリプトを記述できます。</td>
</tr>
<tr>
<td class="head" width="50%">フォルダー名<br />hrefやurlで参照するフォルダー (例 login)</td>
<td class="even"><input type=text name="remap_folder" size="40"></td>
</tr>
<tr>
<td class="head">スクリプト名<br />実行されるスクリプト (例 login.php, login.cgi, or login.pl) パスの残り部分をポスト変数としたければ、"login.php?page="のように記述してください。</td>
<td class="odd"><input type=text name="remap_script" size="40"></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Custom Error Documents(カスタムエラードキュメント)</th></tr>
<tr><td colspan="2" class="odd">エラー条件の時に特定のカスタムドキュメントを表示</td>
</tr>
<tr>
<td class="head" width="50%">Error 400 - Bad Request</td>
<td class="even"><input type=text name=error_400 size="40"></td>
</tr>
<tr>
<td class="head">Error 401 - Authentication Required</td>
<td class="odd"><input type=text name=error_401 size="40"></td>
</tr>
<tr>
<td class="head">Error 403 - Forbidden</td>
<td class="even"><input type=text name=error_403 size="40"></td>
</tr>
<tr>
<td class="head">Error 404 - Not Found</td>
<td class="odd"><input type=text name=error_404 size="40"></td>
</tr>
<tr>
<td class="head">Error 500 - Server Error </td>
<td class="even"><input type=text name=error_500 size="40"></td>
</tr>
</table>
<br />
<br />
<table class="outer">
<tr>
<th colspan="2">Redirection(リダイレクト)</th>
</tr>
<tr>
<td class="head" width="50%">ドキュメントが新しいURLに移動された時にこのオプションを使用。<br />
利用者にとって自動リダイレクトは注意が必要</td>
<td class="odd"><input type=checkbox name="redirect">
移動されたドキュメントをリダイレクト</td>
<tr>
<td class="head">移動されたドキュメント</td>
<td class="even"><input type=text name=redirect_file size="40"></td>
</tr>
<tr>
<td class="head">新しいURL</td>
<td class="odd"><input type=text name=redirect_url size="40"></td>
</tr>
</table>
<br />
<br />
<center><input type=reset name=reset value="フォームをクリア"><input type=submit name=submit value=".htaccess ファイルを生成"></center>
</form>
<br />
<br />
<p align="right">
dot htaccesser provided by <a href="http://www.bitesizeinc.net/">Bite Size, Inc</a></p>
<?php

include_once "/home/admin/www/plugins/themefooter.html";

?>