<?php
// $Id: insert_info.php,v 1.5 2005/03/14 20:05:43 jsaucier Exp $
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
// Include the module header
$_COOKIE['xoops_session'] = htmlspecialchars ( $_GET['PHPSESSID'], ENT_QUOTES );
include '../../mainfile.php';
include_once 'include/functions.php';
include_once 'class/mbfunction.class.php';	// Include MB function

// Select the current operation
$op = isset($_POST['op']) ? htmlspecialchars ( $_POST['op'] , ENT_QUOTES ) : "";
if (!$op && isset($_GET['op']) ) $op = htmlspecialchars ( $_GET['op'] , ENT_QUOTES );
/*
if ( !in_array( $op ,
	 array('insert_folder', 'erase_folder', 'mod_folder', 'insert_file', 'erase_file', 'mod_file', 'maj_file'))
)
	 exit(1);
*/
$folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : NULL;
if (!is_null($folder_id) && isset($_GET['folder_id']) ) $folder_id = intval( $_GET['folder_id']);

$file_id = isset($_POST['file_id']) ? intval($_POST['file_id']) : NULL;
if (!is_null($file_id) && isset($_GET['file_id']) ) $file_id = intval( $_GET['file_id']);

// XOOPS user id
$owner_uid = 0;
if(!empty($xoopsUser)) {
    $owner_uid = $xoopsUser->getVar('uid');
}
// Function to insert a folder in the current parent dir
function insert_folder($folder_id) {
    
    global $xoopsDB, $xoopsUser, $owner_uid;
    
    $insert_folder = false;
    $mb = new mb_func();
    
    // Check if we have a folder name
    if (!empty($_POST['folder_name'])) {
		$folder_name = htmlspecialchars ( $mb->utf8tointernal( $_POST['folder_name']) , ENT_QUOTES );
        $sql = sprintf("SELECT COUNT(folders_parent_id) FROM %s WHERE folders_parent_id=%u AND folders_name='%s'", $xoopsDB->prefix("fcabix_folders"), $folder_id, $folder_name);
        $result = $xoopsDB->query($sql);
		list ($count)=$xoopsDB->fetchRow($result);
		if ( $count > 0 ) {
		    return array('status' => FALSE, 'message' => _MD_FILE_EXIST, 'folder' => $current_dir);
		}
        
        $hidden = 0;
        $inherit_rights = 1;
        $create_date = time();
        
        
        // Select the config path
        $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
        $result = $xoopsDB->query($sql);
        $myconfig = $xoopsDB->fetchArray($result);


        // Get current dir path
        $current_dir = get_current_dir_path($folder_id);
        
        if ( is_writable($current_dir) && if_folder_exist($folder_id) ) {
            
            do {
            
                $folder_nameondisk = ereg_replace('[0-9]', null, md5(time())).rand();
                $dir_to_create = $current_dir.$folder_nameondisk;
                
                if ( !file_exists($dir_to_create) ) {
                
                    $sql = sprintf("INSERT INTO %s (folders_id, folders_name, folders_nameondisk, folders_createddate, folders_modificationdate, folders_inheritrights, folders_hidden, folders_owner, folders_usermod, folders_parent_id) VALUES (%u, '%s', '%s', %u, %u, %u, %u, %u, %u, %u)", 
                    	$xoopsDB->prefix("fcabix_folders"), '', $folder_name, $folder_nameondisk, $create_date, $create_date, $inherit_rights, $hidden, $owner_uid, $owner_uid, $folder_id);
                
                    
                    // Insert the dir and redirect to the fcabix center
                    if ($dir_to_create !== "/" && strpos($dir_to_create, $myconfig['configs_path']) !== false && strpos($dir_to_create, "..") !== true && strpos($dir_to_create, " ") !== true) {
                    
                        if ($result = $xoopsDB->query($sql)) {
                        
                            $new_dir = $xoopsDB->getInsertId();
                            
                            // Create the folder
                            mkdir($dir_to_create, 0755);
                            
                            return array('status' => TRUE, 'message' => _MD_CREATEFOLDER_SAVE_GOOD, 'folder' => $new_dir);
                            
                        }else {
                            return array('status' => FALSE, 'message' => _MD_CREATEFOLDER_SAVE_ERROR_BD, 'folder' => $current_dir);
                            $insert_folder = true;
                        }
                    }else {
                        return array('status' => FALSE, 'message' => _MD_CREATEFOLDER_SAVE_ERROR_PATH, 'folder' => $current_dir);
                        $insert_folder = true;
                    }
                }
                
            } while ($insert_folder == false);
        }else {
            return array('status' => FALSE, 'message' => _MD_CREATEFOLDER_SAVE_ERROR_WRITABLE, 'folder' => $current_dir);
        }
    }else {
	    return array('status' => FALSE, 'message' => _MD_CREATEFOLDER_SAVE_ERROR_NAME, 'folder' => $current_dir);
    }
}



// Function to erase a folder and everything that is inside
function erase_folder($folder_id) {
    
    if ( if_folder_exist($folder_id) ) {

        $dir_to_erase = get_current_dir_path($folder_id);
        
        if ( is_writable($dir_to_erase) && file_exists($dir_to_erase) ) {
            // Delete on disk and on DB
            if (delete_current_dir_on_bd($folder_id) && delete_current_dir_on_disk($dir_to_erase)) {
			    return array('status' => TRUE, 'message' => _MD_ERASEFOLDER_GOOD, 'folder' => 1);
            }else {
			    return array('status' => FALSE, 'message' => _MD_ERASEFOLDER_BAD, 'folder' => $folder_id);
            }
        }else {
		    return array('status' => FALSE, 'message' => _MD_ERASEFOLDER_ERASE_RIGHTS, 'folder' => $folder_id);
        }
    }else {
	    return array('status' => FALSE, 'message' => _MD_ERASEFOLDER_ERASE_DONTEXIST, 'folder' => 1);
    }
}



// Function to mod a folder
function mod_folder($folder_id) {

    global $xoopsDB, $owner_uid;
    
    $mb = new mb_func();

    // Check if we have a folder name
    if (!empty($_POST['new_folder_name'])) {
    
		$folder_name = htmlspecialchars ( $mb->utf8tointernal($_POST['new_folder_name']) , ENT_QUOTES );
        
        $user_mod = $owner_uid;
        $mod_date = time();
        
        
        if ( if_folder_exist($folder_id) ) {
            $sql = sprintf("UPDATE %s SET folders_name = '%s', folders_modificationdate = %u, folders_usermod = %u WHERE folders_id = %u", $xoopsDB->prefix("fcabix_folders"), $folder_name, $mod_date, $user_mod, $folder_id);
            
            if ($result = $xoopsDB->query($sql)) {
			    return array('status' => TRUE, 'message' => _MD_MODFOLDER_SAVE_GOOD, 'folder' => $folder_id);
            }else {
			    return array('status' => FALSE, 'message' => _MD_MODFOLDER_SAVE_BAD, 'folder' => $folder_id);
            }
        }else {
		    return array('status' => FALSE, 'message' => _MD_MODFOLDER_SAVE_DONTEXIST, 'folder' => 1);
        }
    }else {
	    return array('status' => FALSE, 'message' => _MD_MODFOLDER_BAD_NAME, 'folder' => $folder_id);
    } 
    
}

// Function to insert a file in the current parent dir
function insert_file($folder_id) {
    
    global $xoopsDB, $owner_uid;
    
    $insert_file = false;
    //$filesname = "file_to_add";
    $filesname = "Filedata";

    // Check if we have a file
    if (is_uploaded_file($_FILES[$filesname]['tmp_name'])) {
        
		$file_name = htmlspecialchars ($_FILES[$filesname]['name'], ENT_QUOTES );
        $extension = strtolower(strrchr($file_name, "."));
        $sql = sprintf("SELECT COUNT(files_foldersid) FROM %s WHERE files_foldersid=%u AND files_name='%s'", $xoopsDB->prefix("fcabix_files"), $folder_id, $file_name);
        $result = $xoopsDB->query($sql);
		list ($count)=$xoopsDB->fetchRow($result);
		if ( $count > 0 ) {
		    return array('status' => FALSE, 'message' => _MD_FILE_EXIST, 'folder' => $folder_id);
		}        
        $create_date = time();
        $file_type = check_file_type($file_name);
        $file_space = ceil(($_FILES[$filesname]['size'] / 1000));
        
        
        // Select the config path
        $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
        $result = $xoopsDB->query($sql);
        $myconfig = $xoopsDB->fetchArray($result);        


        $current_dir = get_current_dir_path($folder_id);
        
        if ( is_writable($current_dir) && if_folder_exist($folder_id) ) {
            
            do {
            
                $file_nameondisk = ereg_replace('[0-9]', null, md5(time())).rand().$extension;
                $where_to_put_file = $current_dir.$file_nameondisk;

                if ( !file_exists($where_to_put_file) ) {
                    
                    $sql = sprintf(
                    	"INSERT INTO %s (files_id, files_name, files_nameondisk, files_type, files_space, files_createddate, files_modificationdate, files_owner, files_usermod, files_foldersid) VALUES (%u, '%s', '%s', '%s', %u, %u, %u, %u, %u, %u)"
                    		, $xoopsDB->prefix("fcabix_files"), '', $file_name, $file_nameondisk, $file_type, $file_space, $create_date, $create_date, $owner_uid, $owner_uid, $folder_id);
        
                    // Insert the file and redirect to the fcabix center
                    if ($where_to_put_file !== "/" && strpos($where_to_put_file, $myconfig['configs_path']) !== false && strpos($where_to_put_file, "..") !== true && strpos($where_to_put_file, " ") !== true) {
            
                        if ($result = $xoopsDB->query($sql)) {
                    
                            $new_file = $xoopsDB->getInsertId();
                
                            // Move the file
                            move_uploaded_file($_FILES[$filesname]['tmp_name'], $where_to_put_file);
                
						    return array('status' => TRUE, 'message' => MD_CREATEFILE_SAVE_GOOD, 'folder' => $folder_id);
                        }else {
 						    return array('status' => FALSE, 'message' => MD_CREATEFILE_SAVE_ERROR_BD, 'folder' => $folder_id);
                           $insert_file = true;
                        }
                    } else {
					    return array('status' => FALSE, 'message' => MD_CREATEFILE_SAVE_ERROR_PATH, 'folder' => 1);
                        $insert_file = true;
                    }
                }
                
            } while ($insert_file == false);
        }
        else {
		    return array('status' => FALSE, 'message' => MD_CREATEFILE_SAVE_WRITABLE, 'folder' => $folder_id);
        }
    }
    else {
        switch($_FILES[$filesname]['error']){
            case 0:
		    	return array('status' => FALSE, 'message' => MD_CREATEFILE_UPLOAD_PROBLEM, 'folder' => $folder_id);
                break;
            case 1:
            case 2:
		    	return array('status' => FALSE, 'message' => MD_CREATEFILE_UPLOAD_TOOBIG, 'folder' => $folder_id);
                break;
            case 3:
		    	return array('status' => FALSE, 'message' => MD_CREATEFILE_UPLOAD_PARTIAL, 'folder' => $folder_id);
                break;
            case 4:
		    	return array('status' => FALSE, 'message' => MD_CREATEFILE_UPLOAD_NOFILE, 'folder' => $folder_id);
                break;
            default:
		    	return array('status' => FALSE, 'message' => MD_CREATEFILE_UPLOADPROBLEM, 'folder' => $folder_id);
                break;
        }
    }    
}



// Function to erase a file
function erase_file($folder_id) {
    
    global $xoopsDB;
    
    $file_ids = explode(",", rtrim($_POST['items'],","));
    
    // Select the config path
    $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
    $result = $xoopsDB->query($sql);
    $myconfig = $xoopsDB->fetchArray($result);
    
    $delete_results = array();
    
    foreach($file_ids as $fid){
    	
    	$fid = intval($fid);
    	
	    if ( if_file_is_in_dir($fid, $folder_id) ) {
	    
	        // Get the path to the file with the nameondisk
	        $file_to_erase = get_current_dir_path($folder_id).get_file_name($fid, 2);
	        
	        if ( is_writable($file_to_erase) && file_exists($file_to_erase) ) {
	        
	            $sql = sprintf("DELETE FROM %s WHERE files_id = %u", $xoopsDB->prefix("fcabix_files"), $fid);
	            
	            
	            // Check to see if its safe to delete the file
	            if ($file_to_erase === "/" || strpos($file_to_erase, $myconfig['configs_path']) === false || strpos($file_to_erase, "..") === true || strpos($file_to_erase, " ") === true) {
			    	$delete_results[] = array('status' => FALSE, 'message' => _MD_ERASEFILE_ERROR_PATH, 'folder' => 1);
	            }
	            else {
	                if ($result = $xoopsDB->query($sql) && unlink($file_to_erase)) {        
			    		$delete_results[] = array('status' => TRUE, 'message' => _MD_ERASEFILE_GOOD, 'folder' => $folder_id);
	                }
	                else {
			    		$delete_results[] = array('status' => FALSE, 'message' => _MD_ERASEFILE_ERROR_BD, 'folder' => $folder_id);
	                }
	            }
	        }
	        else {
	    		$delete_results[] = array('status' => FALSE, 'message' => _MD_ERASEFILE_ERROR_RIGHTS, 'folder' => $folder_id);
	        }
	    }
	    else {
			$delete_results[] = array('status' => FALSE, 'message' => _MD_ERASEFILE_ERROR_DONTEXIST, 'folder' => $folder_id);
	    }
	    
    }
    
    return $delete_results;
}



// Function to mod a file
function mod_file($folder_id,$file_id) {

    global $xoopsDB, $owner_uid;

    $mb = new mb_func();

    $insert_file = false;
    $rename = false;
    
    // Check if we have a file name
    if (!empty($_POST['new_file_name'])) {
    
		$file_name = htmlspecialchars ( $mb->utf8tointernal($_POST['new_file_name']), ENT_QUOTES );
        $extension = strtolower(strrchr($file_name, "."));
        $file_type = check_file_type($file_name);
        
        $user_mod = $owner_uid;
        $mod_date = time();
        
        
        $sql = "SELECT files_id, files_name, files_nameondisk, files_foldersid, files_type FROM ".$xoopsDB->prefix("fcabix_files")." WHERE files_id = ".$file_id;
        $result = $xoopsDB->query($sql);
        $myrow = $xoopsDB->fetchArray($result); 
        
        
        if (strcmp($extension, strtolower(strrchr($myrow['files_nameondisk'], "."))) != 0) {
            
            $current_dir = get_current_dir_path($folder_id);
            
            do {
                $file_nameondisk = ereg_replace('[0-9]', null, md5(time())).rand().$extension;
                $where_to_put_file = $current_dir.$file_nameondisk;
                    
                if ( !file_exists($where_to_put_file) ) {
                    $insert_file = true;
                }
            } while ($insert_file == false);
            
            if ( is_writable($current_dir) && is_writable($current_dir.$myrow['files_nameondisk']) ) {
                rename($current_dir.$myrow['files_nameondisk'], $where_to_put_file);
                $rename = true;
            }
            else {
                $rename = false;
            }
        }
        else {
            $file_nameondisk = $myrow['files_nameondisk'];
            $rename = true;
        }
        
        
        
        $sql = sprintf("UPDATE %s SET files_name = '%s', files_nameondisk = '%s', files_type = '%s', files_modificationdate = %u, files_usermod = %u WHERE files_id = %u", $xoopsDB->prefix("fcabix_files"), $file_name, $file_nameondisk, $file_type, $mod_date, $user_mod, $file_id);
        
        
        if ( $rename == true && if_file_is_in_dir($file_id, $folder_id) ) {
            if ($result = $xoopsDB->query($sql)) {
				return array('status' => TRUE, 'message' => _MD_MODFILE_SAVE_GOOD, 'folder' => $folder_id);
            } else {
				return array('status' => FALSE, 'message' => _MD_MODFILE_SAVE_BAD, 'folder' => $folder_id);
            }
        }
        else {
			return array('status' => FALSE, 'message' => _MD_MODFILE_SAVE_DONTEXIST, 'folder' => $folder_id);
        }
    }
    else {
		return array('status' => FALSE, 'message' => _MD_MODFILE_BAD_NAME, 'folder' => $folder_id);
    } 
    
}



// Function to update a file in the current parent dir
function maj_file($folder_id,$file_id) {
    
    global $xoopsDB, $owner_uid;
    
    $insert_file = false;
    
    
    // Check if we have a file
    if (is_uploaded_file($_FILES['file_to_maj']['tmp_name'])) {
        
        $sql = "SELECT files_id, files_name, files_nameondisk, files_foldersid, files_type FROM ".$xoopsDB->prefix("fcabix_files")." WHERE files_id = ".$file_id;
        $result = $xoopsDB->query($sql);
        $myrow = $xoopsDB->fetchArray($result);      
        
        
		$file_name = htmlspecialchars ($_FILES['file_to_maj']['name'], ENT_QUOTES );
        $extension = strtolower(strrchr($file_name, "."));
        $file_type = check_file_type($file_name);
        
        $usermod = $owner_uid;
        $mod_date = time();
        $file_space = ceil(($_FILES['file_to_maj']['size'] / 1000));
        
        
        
        // Select the config path
        $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
        $result = $xoopsDB->query($sql);
        $myconfig = $xoopsDB->fetchArray($result); 
        
        
        
        $current_dir = get_current_dir_path($folder_id);
        
        
        if ( if_file_is_in_dir($file_id, $folder_id) ) {
            
            if (strcmp($extension, strtolower(strrchr($myrow['files_nameondisk'], "."))) != 0) {
        
                do {
                    $file_nameondisk = ereg_replace('[0-9]', null, md5(time())).rand().$extension;
                    $where_to_put_file = $current_dir.$file_nameondisk;
                        
                    if ( !file_exists($where_to_put_file) ) {
                        $insert_file = true;
                    }
                } while ($insert_file == false);
                
                if ( is_writable($current_dir) && is_writable($current_dir.$myrow['files_nameondisk']) ) {
                    rename($current_dir.$myrow['files_nameondisk'], $where_to_put_file);
                }
            }
            else {
                $file_nameondisk = $myrow['files_nameondisk'];
                $where_to_put_file = $current_dir.$file_nameondisk;
            }


            if ( is_writable($where_to_put_file) && file_exists($where_to_put_file) ) {
            
                // Update the file and redirect to the fcabix center
                if ($where_to_put_file !== "/" && strpos($where_to_put_file, $myconfig['configs_path']) !== false && strpos($where_to_put_file, "..") !== true && strpos($where_to_put_file, " ") !== true) {
                
                    $sql = sprintf("UPDATE %s SET files_name = '%s', files_nameondisk = '%s', files_type = '%s', files_space = %u, files_modificationdate = %u, files_usermod = %u WHERE files_id = %u", $xoopsDB->prefix("fcabix_files"), $file_name, $file_nameondisk, $file_type, $file_space, $mod_date, $usermod, $file_id);
                    
                    if ($result = $xoopsDB->query($sql)) {
                    
                        // Move the file
                        move_uploaded_file($_FILES['file_to_maj']['tmp_name'], $where_to_put_file);
                        
						return array('status' => TRUE, 'message' => _MD_MAJFILE_SAVE_GOOD, 'folder' => $folder_id);
                    }
                    else {
						return array('status' => FALSE, 'message' => _MD_MAJFILE_SAVE_ERROR_BD, 'folder' => $folder_id);
                    }
                }
                else {
					return array('status' => FALSE, 'message' => _MD_MAJFILE_SAVE_ERROR_PATH, 'folder' => $folder_id);
                }
            }
            else {
				return array('status' => FALSE, 'message' => _MD_MAJFILE_SAVE_ERROR_WRITABLE, 'folder' => $folder_id);
            }
        }
        else {
			return array('status' => FALSE, 'message' => _MD_MAJFILE_SAVE_ERROR_DONTEXIST, 'folder' => $folder_id);
        }
    }
    else {
        switch($_FILES['file_to_maj']['error']){
            case 0:
				return array('status' => FALSE, 'message' => _MD_CREATEFILE_UPLOAD_PROBLEM, 'folder' => $folder_id);
                break;
            case 1:
            case 2:
				return array('status' => FALSE, 'message' => _MD_CREATEFILE_UPLOAD_TOOBIG, 'folder' => $folder_id);
                break;
            case 3:
				return array('status' => FALSE, 'message' => _MD_CREATEFILE_UPLOAD_PARTIAL, 'folder' => $folder_id);
                break;
            case 4:
				return array('status' => FALSE, 'message' => _MD_CREATEFILE_UPLOAD_NOFILE, 'folder' => $folder_id);
                break;
            default:
				return array('status' => FALSE, 'message' => _MD_CREATEFILE_UPLOADPROBLEM, 'folder' => $folder_id);
                break;
        }
    }    
}

$result = array();

// Redirect to the good function
switch ($op) {
    case "insert_folder":
        if ( have_create_right_for_folder($folder_id) ) $result = insert_folder($folder_id);
        else $result = array('status'=> false,'message'=>"No Way");
        break;
    case "erase_folder":
    	if ( have_erase_right_to_folder($folder_id) ) $result = erase_folder($folder_id);
        else $result = array('status'=> false,'message'=>"No Way");
        break;
    case "mod_folder":
        if ( have_mod_right_to_folder($folder_id) ) $result = mod_folder($folder_id);
        else $result = array('status'=> false,'message'=>"No Way");
        break;        
    case "insert_file":
        if ( have_create_right_for_file($folder_id) ) $result = insert_file($folder_id);
        else $result = array('status'=> false,'message'=>"No Way");
        break;
    case "erase_file":
    	if ( have_erase_right_to_file($folder_id) ) $result = erase_file($folder_id);
        else $result = array('status'=> false,'message'=>"No Way");
        break;
    case "mod_file":
        if ( have_mod_right_to_file($folder_id) ) $result = mod_file($folder_id,$file_id);
        else $result = array('status'=> false,'message'=>"No Way");
        break;         
//    case "maj_file":
//        $result = maj_file($folder_id,$file_id);
//        break;        
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";

if(isset($result['status']) && isset($result['message'])){
	$json .= 'status:'.($result['status'] ? 'true' : 'false').',';
	$json .= 'message:"'.$result['message'].'"';
}

$json .= "\n}";
echo $json;

?>