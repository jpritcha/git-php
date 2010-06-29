<?php

  /* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
  // +------------------------------------------------------------------------+
  // | git-php - PHP front end to git repositories                            |
  // +------------------------------------------------------------------------+
  // | Copyright (c) 2006 Zack Bartel                                         |
  // +------------------------------------------------------------------------+
  // | This program is free software; you can redistribute it and/or          |
  // | modify it under the terms of the GNU General Public License            |
  // | as published by the Free Software Foundation; either version 2         |
  // | of the License, or (at your option) any later version.                 |
  // |                                                                        |
  // | This program is distributed in the hope that it will be useful,        |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
  // | GNU General Public License for more details.                           |
  // |                                                                        |
  // | You should have received a copy of the GNU General Public License      |
  // | along with this program; if not, write to the Free Software            |
  // | Foundation, Inc., 59 Temple Place - Suite 330,                         |
  // | Boston, MA  02111-1307, USA.                                           |
  // +------------------------------------------------------------------------+
  // | Author: Zack Bartel <zack@bartel.com>                                  |
  // | Author: Peeter Vois http://people.proekspert.ee/peeter/blog            |
  // | Author: Xan Manning http://knoxious.co.uk/                             |
  // +------------------------------------------------------------------------+ 

  // this functions existance starts from php5
function array_diff_ukey1($array1, $array2) {
	if(!is_array($array1)) {
		return array();
	}
	$a1 = array_keys($array1);
	$res = array();

	foreach($a1 as $b) {
		if(isset($array2[$b])) {
			continue;
		}
		$res[$b] = $array1[$b];
	}
	return $res;
}

// creates a href= beginning and keeps record with the carryon arguments
function html_ahref($arguments, $class = ""){
	$ahref = "<a ";
	if($class != "") {
		$ahref .= "class=\"" . $class . "\" ";
	}
	$ahref .= "href=\"";

	return html_ref($arguments, $ahref);
}

function html_ref($arguments, $prefix) {
	global $keepurl;

	if(!is_array($keepurl)) {
		$keepurl = array();
	}

	$diff = array_diff_key($keepurl, $arguments);
	$ahref = $prefix . sanitized_url();
	$a = array_keys($diff);
	foreach($a as $d) {
		if($diff[$d] != "") {
			$ahref .= "$d={$diff[$d]}&";
		}
	}
	$a = array_keys($arguments);
	foreach($a as $d) {
		if($arguments[$d] != "") {
			$ahref .= "$d={$arguments[$d]}&";
		}
	}
	$now = floor(time()/15/60); // one hour
	$ahref .= "tm=" . $now;
	$ahref .= "\">";
	return $ahref;
}

function html_header() {
	global $CONFIG;
	global $git_embed;

	if (!$git_embed) {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";
		echo "<head>\n";
		echo "\t<title>" . $CONFIG['repo_title'] . "</title>\n";
		echo "\t<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
		echo "\t<meta NAME=\"ROBOTS\" CONTENT=\"NOFOLLOW\" />\n";
		echo "</head>\n";
		echo "<body>\n";
	}
	/* Add rss2 link */
	if (isset($_GET['p']))  {
		echo "<link rel=\"alternate\" title=\"" . $_GET['p'] . "\" href=\"" . sanitized_url() . "p=" . $_GET['p'] . "&dl=rss2\" type=\"application/rss+xml\" />\n";
	}
	echo "<div id=\"gitbody\">\n";
}

function html_breadcrumbs() {
	echo "<div class=\"githead\">\n";
	$crumb = "<a href=\"" . sanitized_url() . "\">projects</a> / ";

	if (isset($_GET['p'])){
		$crumb .= html_ahref(array('p'=>$_GET['p'], 'pg'=>"")) . $_GET['p'] ."</a> / ";
	}
	if (isset($_GET['b'])){
		$crumb .= "blob";
	}
	if (isset($_GET['t'])){
		$crumb .= "tree";
	}
	if ($_GET['a'] == 'commitdiff'){
		$crumb .= 'commitdiff';
	}

	echo $crumb;
	echo "</div>\n";
}

function html_pages() {
	global $CONFIG;
	if(isset($_GET['p'])) {
		html_spacer();
		$now = floor(time() / 15 / 60); // one hour
		echo "<center>";
		echo "<a href=\"git.php?p=" . $_GET['p'] . "&tm=" . $now . "\">browse</a>";
		if($CONFIG['git_bundle_active']) {
			echo " | <a href=\"commit.php?p=" . $_GET['p'] . "\">commit</a>";
		}
		echo "</center>";
	}
}

function html_footer()  {
	global $git_embed;
	global $CONFIG;

	echo "<div class=\"gitfooter\">\n";

	if (isset($_GET['p']))  {
		echo "<a class=\"rss_logo\" href=\"" . sanitized_url() . "p=" . $_GET['p'] . "&dl=rss2\" >RSS</a>\n";
	}
	if ($CONFIG['git_logo'])    {
		echo "<a href=\"http://www.kernel.org/pub/software/scm/git/docs/\">" . 
		"<img src=\"" . sanitized_url() . "dl=git_logo\" style=\"border-width: 0px;\"/></a>\n";
	}

	echo "</div>\n";
	echo "</div>\n";
	if (!$git_embed) {
		echo "</body>\n";
		echo "</html>\n";
	}
}

/* TODO: cache this */
// returns URL of this script
// including any set GET-parameters of p, dl, b, a, h, t
function sanitized_url() {
	global $git_embed;

	/* the sanitized url */
	$url = $_SERVER['SCRIPT_NAME'] . "?";

	if (!$git_embed) {
		return $url;
	}

	/* the GET vars used by git-php */
	$git_get = array('p', 'dl', 'b', 'a', 'h', 't');


	foreach ($_GET as $var => $val) {
		if (!in_array($var, $git_get))   {
			$get[$var] = $val;
			$url .= $var . "=" . $val . "&amp;";
		}
	}
	return $url;
}

function html_spacer($text = "&nbsp;") {
	echo "<div class=\"gitspacer\">" . $text . "</div>\n";
}

function html_title($text = "&nbsp;") {
	echo "<div class=\"gittitle\">" . $text . "</div>\n";
}

function html_style()   {
	global $CONFIG;

	if (file_exists("style.css")) {
		echo "<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" />\n";
	}
	if ($CONFIG['git_css']) {
		echo "<link rel=\"stylesheet\" href=\"gitstyle.css\" type=\"text/css\" />\n";
	}
}

// *****************************************************************************
// Icons, hardcoded pictures ...
//

function cache_image($proj, $blob, $name) {
    global $CONFIG;

    $tempImg = $CONFIG['cache_directory'] . $proj . "/" . $name;

    // $cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git cat-file blob " . escapeshellarg($blob) . " | hexdump -e '16/1 " . '"U%02x" "\n"' . "' | sed 's/U  //g' | sed 's/G/" . '"' . "/g' | sed 's/U/\\x/g'";

    $cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git cat-file blob " . escapeshellarg($blob) . " >" . escapeshellarg($tempImg);
    exec($cmd, &$out);

    $image = file_get_contents($tempImg);
    chmod($tempImg, 0666);
    unlink($tempImg);

    $pinfo = pathinfo($name);
    
    $filesize = strlen($image);
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: image/" . $pinfo['extension']);
	header("Content-Length: " . $filesize);
	header("Content-Disposition: attachment; filename=" . $name . ";");
	header("Expires: +1d");
	echo $image;
	die();
}

$icondesc = array('git_logo', 'icon_folder', 'icon_plain', 'icon_color', 'icon_image');
$flagdesc = array();

function write_img_png($imgptr) {
    $img['icon_folder']['name'] = "icon_folder.png";
    $img['icon_folder']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
        "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xff" .
        "\x61\x00\x00\x00\x04\x67\x41\x4d\x41\x00\x00\xaf\xc8\x37\x05\x8a" .
        "\xe9\x00\x00\x00\x19\x74\x45\x58\x74\x53\x6f\x66\x74\x77\x61\x72" .
        "\x65\x00\x41\x64\x6f\x62\x65\x20\x49\x6d\x61\x67\x65\x52\x65\x61" .
        "\x64\x79\x71\xc9\x65\x3c\x00\x00\x01\xab\x49\x44\x41\x54\x38\xcb" .
        "\xc5\x93\xbb\x8a\x14\x41\x14\x86\xbf\xea\xed\x19\x5b\xb1\x87\x05" .
        "\x45\x74\x51\x04\x13\x67\x37\x77\x41\xd8\x4c\x43\x13\x9f\xc0\xc4" .
        "\x57\x10\x03\x41\x30\xd4\x17\xd0\xcc\xc0\xcc\x50\x30\x13\x31\xf0" .
        "\x01\x16\xcd\x14\x37\x70\x11\xdd\xf5\x32\x3d\xd7\xed\xae\xae\x3a" .
        "\xbf\x41\xb7\x33\x63\x62\x32\x81\x27\xa9\xe2\x70\xfe\xef\xdc\xaa" .
        "\x9c\x24\x56\xb1\x84\x15\x6d\x65\x40\x0a\xf0\xe1\xe5\x95\xdd\xac" .
        "\xb7\xb5\xe5\xdc\x32\x4f\x54\xe3\x4f\xfb\x31\x14\x57\x81\xea\x2f" .
        "\x95\x03\x60\x7a\xf9\xc6\xfb\x90\x02\x38\x25\xfd\x0b\x3b\x4f\x3b" .
        "\xce\x39\x50\x23\xc6\x39\x42\xf9\xe3\xd2\xc1\xee\x83\x7d\xb5\x5e" .
        "\x80\xb5\x6e\x4f\x8a\xf5\xe1\xe4\xeb\xeb\x3b\xc0\xf3\x14\x80\xa0" .
        "\x12\xf3\xc7\xaa\x6f\x8f\x89\x7e\x0d\x92\x1e\xce\xe5\x64\x1b\xd7" .
        "\xd8\xd8\x7e\xd8\x45\x02\x0c\x64\x80\x70\xc9\xc9\xb3\x1f\x5f\xec" .
        "\xdc\x5f\x00\x6a\x97\xc8\x3c\xd1\x77\x91\x09\x3b\xfa\x4c\x3d\x3d" .
        "\x60\x72\xf8\x86\x4e\x76\x0e\x54\x83\x79\xa4\x00\x04\xf2\xfe\xdd" .
        "\x2e\xb2\x8b\xf3\x19\xe0\x94\x59\x38\xc2\xcf\x40\x7e\x4a\x3d\x1e" .
        "\x91\xf7\x6f\x91\x9d\xda\x6c\x1b\xfe\x53\x81\x9a\x0a\xd2\x75\x88" .
        "\x49\xba\x00\xd4\x09\xe9\xf1\x33\xac\x6f\xde\x9e\x07\x0a\xc3\xfc" .
        "\x17\x42\xf1\x0a\xc5\x21\x0a\x43\x14\x46\x28\x4e\xc8\xce\xdf\x83" .
        "\x4a\x2c\x01\x24\xc5\x12\x9b\xbd\x6b\x82\xe3\x18\x85\x02\xab\x07" .
        "\x28\x0e\x21\x14\xad\x7f\x84\xe2\x14\x54\x41\xbd\xb4\x46\x2a\x24" .
        "\x0b\x58\xfd\xbd\xc9\x14\x87\x28\x14\xed\x7d\xd0\x66\x1e\xcd\x01" .
        "\x52\x98\x2f\xb6\x01\x78\xc9\x11\x51\xf8\xd5\x0a\x8b\x45\xd9\x71" .
        "\x88\xc2\x78\x21\xb6\xb2\x69\x33\x2c\x01\xe4\x6d\x10\x66\x3f\x4f" .
        "\xd4\x93\x6e\x8e\xe5\x89\xd4\x01\xcb\x41\xa7\x51\xac\x00\x8f\x92" .
        "\xe6\x74\x49\x24\x96\xa5\xe4\xad\x5a\x00\xca\xea\xd1\xde\x93\x9b" .
        "\xdb\x88\xeb\x38\xf2\x7f\xbe\x5d\x01\xee\xad\xc7\xf4\xac\x59\xe0" .
        "\xff\xfe\x8d\xbf\x01\xbe\x88\x1f\x7f\xe1\xdb\x9a\x4e\x00\x00\x00" .
        "\x00\x49\x45\x4e\x44\xae\x42\x60\x82";

    $img['icon_plain']['name'] = "icon_plain.png";
	$img['icon_plain']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
	    "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x04\x00\x00\x00\xb5\xfa\x37" .
	    "\xea\x00\x00\x00\x04\x67\x41\x4d\x41\x00\x00\xaf\xc8\x37\x05\x8a" .
	    "\xe9\x00\x00\x00\x19\x74\x45\x58\x74\x53\x6f\x66\x74\x77\x61\x72" .
	    "\x65\x00\x41\x64\x6f\x62\x65\x20\x49\x6d\x61\x67\x65\x52\x65\x61" .
	    "\x64\x79\x71\xc9\x65\x3c\x00\x00\x00\xe8\x49\x44\x41\x54\x18\x19" .
	    "\x05\xc1\x31\x6e\x53\x41\x18\x06\xc0\xd9\xc7\x9e\x81\x34\x41\xb8" .
	    "\x82\x8e\x82\x28\xa1\x49\x93\x53\x70\x2e\x4b\x50\x70\x22\x84\xd2" .
	    "\x51\xd2\x41\x8a\x08\x37\x18\x25\x8e\x83\xdf\xfe\xfb\x31\xd3\xa2" .
	    "\x9d\x3b\xf3\x02\x00\xb0\xf3\x2b\x13\x84\x8b\xe7\xfd\x5a\x23\x23" .
	    "\x23\x23\x23\x23\xcf\xf9\xb4\xb5\xb1\x44\x08\x57\x6b\x3d\xe6\x90" .
	    "\x43\x0e\xf9\x9b\x3f\xf9\x9d\x91\xfb\x7c\xde\xda\x58\x82\xe6\x6a" .
	    "\xe4\x98\x7f\x39\xe6\x98\x87\xec\xb3\xcb\xc8\x53\x1e\xf3\x65\x6b" .
	    "\x13\x4d\x73\x39\xbe\x95\xaf\x4e\x4e\x56\xab\xd5\x47\x40\xff\xe0" .
	    "\xb6\x03\x8b\x6b\x44\x29\xc3\xde\x34\xbc\x04\x1d\x68\x6e\xad\x86" .
	    "\x93\xa1\x94\xe9\x06\xd0\x01\x2e\x01\x51\xa6\x12\x40\x07\xf8\x6e" .
	    "\x18\x4a\x29\x31\x5d\x03\x3a\xc0\x3b\x00\x31\x11\x40\x07\xf8\xa1" .
	    "\x0c\xd3\x54\x98\xde\x03\x3a\xc0\x1b\x04\x01\x00\x74\x80\x9f\xa6" .
	    "\x69\x9a\x02\xde\x02\x3a\x88\xf6\x1a\x00\x80\x49\xb2\x24\x66\x1d" .
	    "\x4d\x00\x00\xb3\x9e\x14\x2d\xda\x2b\x67\x16\x00\x00\x94\x5d\xee" .
	    "\xfe\x03\x03\xb4\x95\xcf\x5e\x4c\x76\x13\x00\x00\x00\x00\x49\x45" .
	    "\x4e\x44\xae\x42\x60\x82";

    $img['icon_color']['name'] = "icon_color.png";
	$img['icon_color']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
	    "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xff" .
	    "\x61\x00\x00\x00\x04\x67\x41\x4d\x41\x00\x00\xaf\xc8\x37\x05\x8a" .
	    "\xe9\x00\x00\x00\x19\x74\x45\x58\x74\x53\x6f\x66\x74\x77\x61\x72" .
	    "\x65\x00\x41\x64\x6f\x62\x65\x20\x49\x6d\x61\x67\x65\x52\x65\x61" .
	    "\x64\x79\x71\xc9\x65\x3c\x00\x00\x02\x0d\x49\x44\x41\x54\x18\x19" .
	    "\x05\xc1\xbf\xaf\x9e\x73\x18\x07\xe0\xeb\xfe\x3e\xcf\x7b\xda\xd3" .
	    "\x53\x47\x95\x88\x33\x11\xf1\x3b\x31\xd0\x88\x18\x84\x85\x81\x2e" .
	    "\x06\x93\xc5\x62\x63\x23\x36\x31\xd9\x6c\x92\x8e\x06\x49\xff\x03" .
	    "\xa3\x41\x4b\x44\x22\x24\x82\x18\x58\x24\x0d\xda\x54\x1b\x44\xcf" .
	    "\x79\xcf\xf3\xdc\x1f\xd7\x55\x49\xbc\xf0\xde\xa5\xd7\x77\xf7\x4f" .
	    "\xbc\x36\xca\x39\x9c\x44\x81\x42\x8a\xb4\xa5\xfb\xd7\x9b\xd7\xf3" .
	    "\x59\xfd\x7d\xe3\x9d\xcb\x17\xce\x6f\x01\x2a\x89\x97\x3e\xf8\xf2" .
	    "\xf2\x27\x6f\x9d\x7b\x74\x8c\x3a\x13\x26\x01\x20\x81\xb8\xf6\xef" .
	    "\xb1\x8b\xdf\xdc\xf4\xc7\x6f\x47\xff\xfd\xf4\xc3\xcf\x67\xbf\xb8" .
	    "\x70\xfe\x08\x66\x18\x53\x3d\xbc\x33\x4f\x67\x7e\xbc\x6a\x1a\x55" .
	    "\x14\x69\xd6\xb4\x65\x89\x5b\xc7\xab\xe7\x1f\xbc\xcd\xd3\x0f\xcd" .
	    "\xbe\xee\x6b\xa7\xd2\x8f\xdc\x78\xee\xcd\x4f\xef\xf8\xfc\xa3\x97" .
	    "\x8f\x06\x54\xd5\xe9\x24\xd3\xce\x34\x6c\x06\x73\x31\x0d\xa6\xa2" .
	    "\x8a\xa1\xd0\x0e\xf6\x77\x9c\x7f\xea\xc0\x7d\x0f\x9c\xd8\x9d\xf7" .
	    "\xf7\x3e\x86\x01\xa1\x60\x60\x14\xa3\xca\xa8\x32\xaa\x8c\x2a\x63" .
	    "\x2a\xbf\xff\x75\xcb\xbd\xa7\x8f\x3d\x76\xb6\xbc\xf1\xcc\x9d\xa6" .
	    "\xcd\xf4\x2a\xcc\x00\x09\xf3\x44\x29\xa9\xd2\x6b\x18\x65\x9e\xd8" .
	    "\xc9\xf0\xcb\xf5\xd8\xf6\xa2\x7b\xeb\xd9\xfb\x4f\xa8\x79\x2c\x30" .
	    "\x43\x42\x30\xd7\x10\x11\xd4\x28\x85\x34\x99\x06\x3b\x8c\x2e\xdd" .
	    "\x93\x18\xd4\x00\x33\x04\x8d\x2a\x4a\x09\x82\x34\x9b\x09\x50\x43" .
	    "\xad\x25\x23\x1a\x09\x30\x43\x2f\x24\x34\x0a\x49\xc0\x18\xe8\x32" .
	    "\x4f\x21\x45\x88\xb2\x36\x6b\x03\x33\x74\x47\x12\x42\x04\x80\x00" .
	    "\xa5\x54\x98\x07\x8d\x93\x1b\xd6\x25\x60\x86\xb5\x59\x43\x9a\x06" .
	    "\x10\xd0\x61\x6d\x82\x0e\x29\x36\xd3\xb0\x6c\x17\x30\x43\x2f\xd1" .
	    "\x89\xf7\xbf\xdb\x2a\xd1\x45\x52\x34\xa9\x80\x84\x06\x5c\xbc\x67" .
	    "\xe3\xf8\xa8\xc1\x0c\xcb\xb2\x5a\x57\x0e\xf6\x37\x9e\xbc\x7b\xe8" .
	    "\xb0\x86\xb5\xe8\x10\x24\x24\x7c\x7f\x75\x91\x8e\xe3\xc3\x05\xcc" .
	    "\xb0\x1c\x75\x2f\xdd\xd9\xae\xea\xab\x2b\xab\xae\x88\x02\x1d\x4a" .
	    "\x74\xc8\x28\xa3\xcb\x3c\x57\x2f\x6b\x2f\x30\xc3\xf1\xe1\xfa\xe7" .
	    "\xde\xce\x74\xd7\xbb\x8f\x6f\xf7\x92\x0c\xa1\x01\xb7\xef\xce\x20" .
	    "\x61\x77\x53\xaa\xf4\x66\xaa\x7f\xd6\xa5\xaf\xc0\x0c\x55\xf9\xf0" .
	    "\xc5\xb7\x2f\xbd\x52\x55\x4f\x44\x4e\x01\x08\x41\x04\x08\x38\xec" .
	    "\xee\x6f\xe7\xe1\x22\xfc\x0f\x44\x38\x1e\x56\x35\x83\x6a\x12\x00" .
	    "\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82";

    $img['icon_image']['name'] = "icon_image.png";
	$img['icon_image']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
	    "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xff" .
	    "\x61\x00\x00\x00\x04\x67\x41\x4d\x41\x00\x00\xaf\xc8\x37\x05\x8a" .
	    "\xe9\x00\x00\x00\x19\x74\x45\x58\x74\x53\x6f\x66\x74\x77\x61\x72" .
	    "\x65\x00\x41\x64\x6f\x62\x65\x20\x49\x6d\x61\x67\x65\x52\x65\x61" .
	    "\x64\x79\x71\xc9\x65\x3c\x00\x00\x01\x96\x49\x44\x41\x54\x18\x19" .
	    "\xa5\xc1\x3f\x6b\x53\x61\x18\xc6\xe1\xdf\xf3\x9e\x37\x9e\x9c\x2a" .
	    "\xad\x83\xa7\xa0\x28\x08\xe2\xa6\x93\x8b\x82\x9f\xc0\xcd\xc9\xc1" .
	    "\x0e\x22\x88\xe2\x96\xbd\x93\x68\x17\xd1\xcd\x59\xc4\x49\x04\x77" .
	    "\x07\x3f\x85\x14\x3a\x16\x03\x62\x23\xb6\x44\x6a\x9a\x9c\x9c\xf7" .
	    "\xb9\x3d\xa9\x89\x7f\x21\x05\x7b\x5d\x26\x89\xc3\x88\x34\x56\x56" .
	    "\x5f\x3f\x49\xb5\xdd\xdc\x1e\xa4\xf6\xee\x88\x7f\x39\x7f\x28\x72" .
	    "\x58\x2c\x78\xfe\xe6\xf1\x8d\x4e\xa4\x91\xdc\x56\x1e\xde\xbd\x54" .
	    "\x2e\x97\xa5\x99\x45\x04\x08\x70\x26\x0c\x09\x24\x70\x26\x44\xaa" .
	    "\x2b\x6e\x3f\x78\x7b\x0b\xe8\x44\x1a\xdb\xbb\x29\x2f\x4f\x94\x76" .
	    "\xff\xe9\x4b\x4e\x9d\x39\x4e\x9e\x39\x49\x39\x84\x25\x82\x41\x30" .
	    "\x08\xc1\xc8\x82\xf1\x61\xe3\x3d\x8f\x3a\xf7\x18\x27\x33\x1a\x91" .
	    "\xc6\x60\x04\x21\x44\xce\x9f\x3b\xcb\x85\xcb\x57\xc9\x42\x86\x94" .
	    "\xc0\x22\x98\x11\xcc\x30\x0b\x84\x10\x68\x65\x01\x77\x7e\x8a\x4c" .
	    "\x39\x90\xe7\x6d\xda\xc5\x31\x90\x98\x47\x08\x10\x13\x91\x29\x07" .
	    "\xc6\xd5\x90\xf1\x70\x0f\xcc\x90\x1c\x57\xc2\x3d\x60\x80\x24\x32" .
	    "\x4b\xd4\xd5\x00\xc9\x99\x89\x4c\x49\xf0\xb9\xff\x91\x2f\xbd\x3e" .
	    "\x63\xaf\x71\x25\xbe\x8e\xb6\xa8\xd2\x69\x8c\x1f\x96\xf3\x1d\xf6" .
	    "\xbe\xf5\x41\x81\x99\xc8\x94\x04\x9f\xb6\x16\x68\x75\x87\xec\x93" .
	    "\x40\x25\xd2\x90\x99\x1d\x0a\xba\x5d\xf1\xbb\x48\xc3\x01\x09\x42" .
	    "\x76\x84\x90\xe5\xcc\x93\xb5\xda\x88\x5f\x02\x53\x12\xff\x25\xd2" .
	    "\x90\x3b\x06\xf4\x36\xd7\x39\x48\x6f\x73\x1d\x74\x0d\x77\xf6\x45" .
	    "\x1a\xc6\x84\x78\xb6\xb6\x0a\x18\xf3\x5d\x07\x2a\x66\x22\x8d\xc5" .
	    "\x22\xf4\xbd\x1e\x2e\x9d\x5c\x30\x30\x43\x4c\x08\x04\xe2\x6f\x46" .
	    "\x5d\x57\xe4\x2d\x13\x8d\x48\xe3\x68\x1e\x5e\xdd\x59\x7b\x77\x65" .
	    "\x30\xd6\x45\x77\x0e\x94\xb7\xa0\x88\xbc\xa0\x61\x92\x38\x8c\xef" .
	    "\xd3\xf2\xab\x56\x14\x9a\xd9\x0a\x00\x00\x00\x00\x49\x45\x4e\x44" .
	    "\xae\x42\x60\x82";


	$img['git_logo']['name'] = "git-logo.png";
	$img['git_logo']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
	    "\x00\x00\x00\x48\x00\x00\x00\x1b\x04\x03\x00\x00\x00\x2d\xd9\xd4" .
	    "\x2d\x00\x00\x00\x01\x73\x52\x47\x42\x00\xae\xce\x1c\xe9\x00\x00" .
	    "\x00\x18\x50\x4c\x54\x45\xff\xff\xff\x60\x60\x5d\xb0\xaf\xaa\x00" .
	    "\x80\x00\xce\xcd\xc7\xc0\x00\x00\xe8\xe8\xe6\xf7\xf7\xf6\x95\x0c" .
	    "\xa7\x47\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0b\x13\x00\x00" .
	    "\x0b\x13\x01\x00\x9a\x9c\x18\x00\x00\x00\x07\x74\x49\x4d\x45\x07" .
	    "\xda\x06\x1d\x02\x05\x39\x73\xb4\x47\x28\x00\x00\x00\x4b\x49\x44" .
	    "\x41\x54\x28\xcf\x63\x60\x18\x05\x44\x83\xd0\xd0\x50\x38\x86\x01" .
	    "\x63\x03\xe2\x14\x31\x1b\x13\x61\x01\x5e\x45\xcc\xc6\xc6\x06\x40" .
	    "\x04\xa4\xf0\xa8\x02\x2a\x31\xc0\x62\x9d\x31\x12\xa6\x48\x11\xc8" .
	    "\x72\x18\x26\x56\x11\xc4\xe1\x60\x97\xe3\xb6\x0e\x4b\x48\x62\x2a" .
	    "\x62\x36\x36\x1e\x4d\x85\xe4\x00\x00\x57\x98\x17\x05\x03\x18\xd1" .
	    "\xd4\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82";

	if(!isset($img[$imgptr]['name'])) {
		$img[$imgptr]['name'] = $imgptr . ".png";
	}

	$filesize = strlen($img[$imgptr]['bin']);
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: image/png");
	header("Content-Length: ". $filesize);
	header("Content-Disposition: attachment; filename=" . $img[$imgptr]['name'] . ";");
	header("Expires: +1d");
	echo $img[$imgptr]['bin'];
	die();
}
?>
