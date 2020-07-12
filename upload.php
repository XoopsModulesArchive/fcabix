<?php
//  ------------------------------------------------------------------------ //
//                      fCabiX - File Cabinet for XOOPS                      //
//                     Copyright (c) 2009 Bluemoon inc.                      //
//                       <http://www.bluemooninc.biz/>                       //
//                       Special thanks to Makinosuke                        //
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
//
// Flash does not support cookie and session. Salvage myself.
//
// ################# Loading for using xoopsDB ##############
$xoopsOption['nocommon']=1;
include '../../mainfile.php';
include_once XOOPS_ROOT_PATH.'/include/functions.php';
include_once XOOPS_ROOT_PATH.'/class/logger.php';
$xoopsLogger =& XoopsLogger::instance();
$xoopsLogger->startTime();
include_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';
define('XOOPS_DB_PROXY', 1);
$xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
// ################# Include class manager file #############
require_once XOOPS_ROOT_PATH.'/kernel/object.php';
require_once XOOPS_ROOT_PATH.'/class/criteria.php';
// ################# Load Config Settings ###################
$config_handler =& xoops_gethandler('config');
$xoopsConfig =& $config_handler->getConfigsByCat(XOOPS_CONF);
// ################# Get Session ID from uploder.swf ########
$_COOKIE["PHPSESSID"] = $_COOKIE[ $xoopsConfig['session_name'] ] = htmlspecialchars ( $_GET['upload_ticket'], ENT_QUOTES );
// ################# $xoopUser from common.php ##############
$xoopsUser = '';
$xoopsUserIsAdmin = false;
$member_handler =& xoops_gethandler('member');
$sess_handler =& xoops_gethandler('session');
if ($xoopsConfig['use_ssl'] && isset($_POST[$xoopsConfig['sslpost_name']]) && $_POST[$xoopsConfig['sslpost_name']] != '') {
    session_id($_POST[$xoopsConfig['sslpost_name']]);
} elseif ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
    if (isset($_COOKIE[$xoopsConfig['session_name']])) {
        session_id($_COOKIE[$xoopsConfig['session_name']]);
    } else {
        // no custom session cookie set, destroy session if any
        $_SESSION = array();
    }
    @ini_set('session.gc_maxlifetime', $xoopsConfig['session_expire'] * 60);
}
session_set_save_handler(
	array(&$sess_handler, 'open'), 
	array(&$sess_handler, 'close'), 
	array(&$sess_handler, 'read'), 
	array(&$sess_handler, 'write'), 
	array(&$sess_handler, 'destroy'), 
	array(&$sess_handler, 'gc'));
session_start();
if (!empty($_SESSION['xoopsUserId'])) {
    $xoopsUser =& $member_handler->getUser($_SESSION['xoopsUserId']);
    if (!is_object($xoopsUser)) {
        $xoopsUser = '';
        $_SESSION = array();
    } else {
        if ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '') {
            setcookie($xoopsConfig['session_name'], session_id(), time()+(60*$xoopsConfig['session_expire']), '/',  '', 0);
        }
        $xoopsUser->setGroups($_SESSION['xoopsUserGroups']);
        $xoopsUserIsAdmin = $xoopsUser->isAdmin();
    }
}
// ################# Loading language file ##############
include_once './language/'.$xoopsConfig['language'].'/main.php';
// ##########################################################
include './include/functions.php';
include_once "./class/mbfunction.class.php";
include_once "./class/gtickets.php";

// Select the current operation
$op = isset($_POST['op']) ? htmlspecialchars ( $_POST['op'] , ENT_QUOTES ) : "";
if (!$op && isset($_GET['op']) ) $op = htmlspecialchars ( $_GET['op'] , ENT_QUOTES );

$folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : NULL;
if (is_null($folder_id) && isset($_GET['folder_id']) ) $folder_id = intval( $_GET['folder_id']);

if(!empty($xoopsUser)) {
    $owner = $xoopsUser->getVar('uid');
}else{
    $owner = $_SESSION['xoopsUserId'];
}
$module_handler =& xoops_gethandler('module');
$xoopsModule =& $module_handler->getByDirname("fcabix");
// set config values for this module
if ( $xoopsModule->getVar( 'hasconfig' ) == 1 ) {
    $config_handler = & xoops_gethandler( 'config' );
    $xoopsModuleConfig = & $config_handler->getConfigsByCat( 0, $xoopsModule->getVar( 'mid' ) );
}

$mb = new mb_func();

// check ticket right for $_GET['XOOPS_G_TICKET']
if ( ! $xoopsGTicket->check( FALSE ) ) {
	echo $mb->internal2utf8( $xoopsGTicket->getErrors() );
    return array('status' => FALSE, 'message' => $xoopsGTicket->getErrors() , 'folder' => $folder_id);
    exit(-1);
}

// check access right (needs upload permission)
if ( have_create_right_for_file($folder_id) ) {
	$insert_file = false;
}else{
	echo $mb->internal2utf8(_MD_CREATEFILE_SAVE_WRITABLE);
    return array('status' => FALSE, 'message' => _MD_CREATEFILE_SAVE_WRITABLE, 'folder' => $folder_id);
    exit(-1);
}
$insert_file = false;
if (empty($_FILES)) exit -1;

// Check if we have a file
if (is_uploaded_file($_FILES['file_to_add']['tmp_name'])) {
	$file_name = htmlspecialchars ($_FILES['file_to_add']['name'], ENT_QUOTES );
	$file_name = $mb->utf8tointernal($file_name, TRUE);
    $extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $ext_filter = explode(",", $xoopsModuleConfig['ext_filter']);
    if ( !in_array($extension, $ext_filter) ){
		echo $mb->internal2utf8(_MD_CREATEFILE_UPLOAD_EXTFILTER);
	    return array('status' => FALSE, 'message' => _MD_CREATEFILE_UPLOAD_EXTFILTER, 'folder' => $folder_id);
	    exit(1);
    }
    $sql = sprintf("SELECT COUNT(files_foldersid) FROM %s WHERE files_foldersid=%u AND files_name='%s'", $xoopsDB->prefix("fcabix_files"), $folder_id, $file_name);
    $result = $xoopsDB->query($sql);
	list ($count)=$xoopsDB->fetchRow($result);
	if ( $count > 0 ) {
		echo $mb->internal2utf8(_MD_FILE_EXIST);
	    return array('status' => FALSE, 'message' => _MD_FILE_EXIST, 'folder' => $folder_id);
	    exit(1);
	} 
    $create_date = time();
    $file_type = check_file_type($file_name);
    $file_space = ceil(($_FILES['file_to_add']['size'] / 1000));
    
    // Select the config path
    $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
    $result = $xoopsDB->query($sql);
    $myconfig = $xoopsDB->fetchArray($result);        
    $current_dir = get_current_dir_path($folder_id);
    if ( is_writable($current_dir) && if_folder_exist($folder_id) ) {
        //do {
            $file_nameondisk = ereg_replace('[0-9]', null, md5(time())).rand().$extension;
            $where_to_put_file = $current_dir.$file_nameondisk;
            if ( !file_exists($where_to_put_file) ) {
                $sql = sprintf("INSERT INTO %s ( files_name, files_nameondisk, files_type, files_space, files_createddate, files_modificationdate, files_owner, files_usermod, files_foldersid) VALUES ('%s', '%s', '%s', %u, %u, %u, %u, %u, %u)",
                   $xoopsDB->prefix("fcabix_files"), $file_name, $file_nameondisk, $file_type, $file_space, $create_date, $create_date, $owner, $owner, $folder_id);
                // Insert the file and redirect to the fcabix center
                if ($where_to_put_file !== "/" 
                	&& strpos($where_to_put_file, $myconfig['configs_path']) !== false 
                	&& strpos($where_to_put_file, "..") !== true 
                	&& strpos($where_to_put_file, " ") !== true) {
                  if ($result = $xoopsDB->queryF($sql)) {
                      $new_file = $xoopsDB->getInsertId();
                      // Move the file
                      move_uploaded_file($_FILES['file_to_add']['tmp_name'], $where_to_put_file);
                      //echo "index.php?curent_file=".$new_file."&curent_dir=".$folder_id. " " ._MD_CREATEFILE_SAVE_GOOD;
                  } else {
                      echo _MD_CREATEFILE_SAVE_ERROR_BD;
                      echo "<br />$sql<br />";
                      echo _MD_GO_BACK;
                      $insert_file = true;
                  }
                } else {
                    echo _MD_CREATEFILE_SAVE_ERROR_PATH;
                    echo "<br /><br />";
                    echo _MD_GO_BACK;
                    $insert_file = true;
                }
            }else{
                $insert_file = true;
            }
        //} while ($insert_file == false);
    } else {
        echo _MD_CREATEFILE_SAVE_WRITABLE;
        echo "<br /><br />";
        echo _MD_GO_BACK;
    }
} else {
    switch($_FILES['file_to_add']['error']){
        case 0:
            echo _MD_CREATEFILE_UPLOAD_PROBLEM;
            echo "<br /><br />";
            echo _MD_GO_BACK;
            break;
        case 1:
            echo _MD_CREATEFILE_UPLOAD_TOOBIG;
            echo "<br /><br />";
            echo _MD_GO_BACK;                
            break;
        case 2:
            echo _MD_CREATEFILE_UPLOAD_TOOBIG;
            echo "<br /><br />";
            echo _MD_GO_BACK;                
            break;
        case 3:
            echo _MD_CREATEFILE_UPLOAD_PARTIAL;
            echo "<br /><br />";
            echo _MD_GO_BACK;                
            break;
        case 4:
            echo _MD_CREATEFILE_UPLOAD_NOFILE;
            echo "<br /><br />";
            echo _MD_GO_BACK;                
            break;
        default:
            echo _MD_CREATEFILE_UPLOADPROBLEM;
            echo "<br /><br />";
            echo _MD_GO_BACK;                
            break;
    }
}    
include('json_filelist.php');
?>