<?php
// $Id: get_file.php,v 1.9 2005/03/14 20:05:43 jsaucier Exp $
//  ------------------------------------------------------------------------ //
//                           Document Manager                                //
//            Copyright (c) 2004 Informatique Strategique IS                 //
//                  <http://www.infostrategique.com/>                        //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

// Include the Xoops mainfile
include '../../mainfile.php';

// Set the URL for the module
$fcabix_url = XOOPS_URL."/modules/fcabix";

require_once './include/functions.php';			// Include own function
require_once './include/zip.lib.php';			// Include zip function
include_once './include/browser.php';
include_once './class/mbfunction.class.php';	// Include MB function
include_once './class/download.class.php';

// Check if the user as admin right to the module
if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
        $is_mod_admin = true;
} else {
        $is_mod_admin = false;
}

$mb = new mb_func();

// Select the current_file
if (!empty($_GET['curent_file'])) {
    $items = rtrim( htmlspecialchars ( $_GET['curent_file'] , ENT_QUOTES ) , ",");
    if (strpos($items, ",")>0) {
        $current_file = explode(",", $_GET['curent_file'] );
        $zipfile = new zipfile();
        $files_name = "fcabix" . date( "YmdHis" ) . ".zip";
    }else{
        $current_file = (int) $items;
    }
}else if (!empty($_POST['curent_file']) && is_array($_POST['curent_file'])) {
    $items = implode(',', $_POST['curent_file']);
    $current_file = $_POST['curent_file'];
    $zipfile = new zipfile();
    $files_name = "fcabix" . date( "YmdHis" ) . ".zip";
} else {
    $current_file = 0;
}


// Select the current_dir that we are in
$current_dir = !empty($_GET['curent_dir']) ? (int) $_GET['curent_dir'] : (!empty($_POST['curent_dir']) ? (int) $_POST['curent_dir'] : 0);

// Test if the file exist in the current dir, if we have the right to access it
if ( have_access_right_to_folder($current_dir) ) {
    // Select the config path
    $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
    $result = $xoopsDB->query($sql);
    $myconfig = $xoopsDB->fetchArray($result);
    
    $err_stop = FALSE;
    $sql = "SELECT files_id, files_name, files_foldersid FROM ".$xoopsDB->prefix("fcabix_files")." WHERE files_id in ( ".$items.")";
    $result = $xoopsDB->query($sql);
    while($myrow = $xoopsDB->fetchArray($result) ){
        $file_path = get_current_dir_path($myrow['files_foldersid']).get_file_name($myrow['files_id'],2);
        if ( !if_file_is_in_dir($myrow['files_id'], $current_dir) ) $err_stop = TRUE;
        if ( $file_path !== "/" 
            && strpos($file_path, $myconfig['configs_path']) !== false 
            && strpos($file_path, "..") !== true 
            && strpos($file_path, " ") !== true 
            && file_exists($file_path) === true 
            && is_readable($file_path) === true) {
            if ( !is_array($current_file) ){
                $files_name = $myrow['files_name'];
            }else{
                $handle = fopen($file_path, "rb");
                $contents = fread($handle, filesize($file_path));
                fclose($handle);
                $zipfile->addFile($contents, $mb->internal2x($myrow['files_name'],"SJIS") );
            }
        }else{
            $err_stop = TRUE;
        }
    }
    if ( $err_stop ){
        echo _MD_FILE_ERROR_RIGHTS;
        echo "<br /><br />";
        echo _MD_GO_BACK;
        exit();
    }
	if ( is_array($current_file) ){
	    $zip_buffer = $zipfile->file();
		$tmpfname = tempnam("/tmp", "tmp_");
		$handle = fopen($tmpfname, "wb");
		fwrite($handle, $zip_buffer );
		fclose($fp);
		$fpathname = $tmpfname;
	}else{
		$fpathname = $file_path;
	}
	$down = new download($fpathname);
	$dl_filename = $files_name;
	$ctype = $down->contentType() ;
	// from newbb_fileup
	ob_clean();
	$browser = $version =0;
	UsrBrowserAgent($browser,$version);
	@ignore_user_abort();
	@set_time_limit(0);
	if ($browser == 'IE' && (ini_get('zlib.output_compression')) ) {
	    ini_set('zlib.output_compression', 'Off');
	}
	/*if (!empty($content_encoding)) {
	    header('Content-Encoding: ' . $content_encoding);
	}*/
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($fpathname) );
	header("Content-type: " . $ctype);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Last-Modified: ' . date("D M j G:i:s T Y"));
	header("Content-Disposition: attachment; " . $mb->cnv_mbstr4browser($dl_filename,$browser));
	//header('Content-Disposition: attachment; filename="' . $dl_filename . '"');
	//header("Content-Disposition: inline; " . $mb->cnv_mbstr4browser($dl_filename,$browser));
	if ($browser == 'IE') {
	    header('Pragma: public');
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	} else {
	    header('Pragma: no-cache');
	}
	$fp=fopen($fpathname,'r');
	while(!feof($fp)) {
		$buffer = fread($fp, 1024*6); //speed-limit 64kb/s
		print $buffer;
		flush();
		ob_flush();
		usleep(10000); 
	}
	fclose($fp);
	if ( is_array($current_file) ){
		unlink($tmpfname);
	}
} else {
    // Display the error
    echo _MD_FILE_ERROR_RIGHTS;
    echo "<br /><br />";
    echo _MD_GO_BACK;
}
?>