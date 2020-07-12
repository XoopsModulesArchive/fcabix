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
include 'header.php';
$xoopsOption['nocommon']=1;

// Include Xoops header
require(XOOPS_ROOT_PATH.'/header.php');

// Set the template
$xoopsOption['template_main'] = 'fcabix_insertinfo.html';

// Select the current operation
$op = isset($_POST['op']) ? htmlspecialchars ( $_POST['op'] , ENT_QUOTES ) : "";
if (!$op && isset($_GET['op']) ) $op = htmlspecialchars ( $_GET['op'] , ENT_QUOTES );

$folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : NULL;
if (!is_null($folder_id) && isset($_GET['folder_id']) ) $folder_id = intval( $_GET['folder_id']);

// Function to insert a folder in the current parent dir
function insert_folder($folder_id) {
    
    global $xoopsDB, $xoopsUser;
    
    $insert_folder = false;
    
    
    // Check if we have a folder name
    if (!empty($_POST['folder_name'])) {
		$folder_name = htmlspecialchars ( $_POST['folder_name'] , ENT_QUOTES );

        $sql = sprintf("SELECT COUNT(folders_parent_id) FROM %s WHERE folders_parent_id=%u AND folders_name='%s'", $xoopsDB->prefix("fcabix_folders"), $folder_id, $folder_name);
        $result = $xoopsDB->query($sql);
		list ($count)=$xoopsDB->fetchRow($result);
		if ( $count > 0 ) {
			redirect_header("index.php?curent_dir=".$folder_id,2,_MD_FILE_EXIST);
			exit();
		}
        
        $hidden = 0;
        $inherit_rights = 1;
        $owner = $xoopsUser->uid();
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
                
                    $sql = sprintf("INSERT INTO %s (folders_id, folders_name, folders_nameondisk, folders_createddate, folders_modificationdate, folders_inheritrights, folders_hidden, folders_owner, folders_usermod, folders_parent_id) VALUES (%u, '%s', '%s', %u, %u, %u, %u, %u, %u, %u)", $xoopsDB->prefix("fcabix_folders"), '', $folder_name, $folder_nameondisk, $create_date, $create_date, $inherit_rights, $hidden, $owner, $owner, $folder_id);
                
                    
                    // Insert the dir and redirect to the fcabix center
                    if ($dir_to_create !== "/" && strpos($dir_to_create, $myconfig['configs_path']) !== false && strpos($dir_to_create, "..") !== true && strpos($dir_to_create, " ") !== true) {
                    
                        if ($result = $xoopsDB->query($sql)) {
                        
                            $new_dir = $xoopsDB->getInsertId();                
                            
                            // Create the folder
                            mkdir($dir_to_create, 0755);
                            
                            redirect_header("index.php?curent_dir=".$new_dir."",1,_MD_CREATEFOLDER_SAVE_GOOD);
                        }
                        else {
                            echo _MD_CREATEFOLDER_SAVE_ERROR_BD;
                            echo "<br /><br />";
                            echo _MD_GO_BACK;
                            $insert_folder = true;
                        }
                    }
                    else {
                        echo _MD_CREATEFOLDER_SAVE_ERROR_PATH;
                        echo "<br /><br />";
                        echo _MD_GO_BACK;
                        $insert_folder = true;
                    }
                }
                
            } while ($insert_folder == false);
        }
        else {
            echo _MD_CREATEFOLDER_SAVE_ERROR_WRITABLE;
            echo "<br /><br />";
            echo _MD_GO_BACK;
        }        
    }
    else {
        echo _MD_CREATEFOLDER_SAVE_ERROR_NAME;
        echo "<br /><br />";
        echo _MD_GO_BACK;
    }    
}



// Function to erase a folder and everything that is inside
function erase_folder($folder_id) {
    
    if ( if_folder_exist($folder_id) ) {

        $dir_to_erase = get_current_dir_path($folder_id);
        
        if ( is_writable($dir_to_erase) && file_exists($dir_to_erase) ) {
            // Delete on disk and on DB
            if (delete_current_dir_on_bd($folder_id) && delete_current_dir_on_disk($dir_to_erase)) {
                redirect_header("index.php",1,_MD_ERASEFOLDER_GOOD);
            }
            else {
                echo _MD_ERASEFOLDER_BAD;
                echo "<br /><br />";
                echo _MD_GO_BACK;
            }
        }
        else {
            echo _MD_ERASEFOLDER_ERASE_RIGHTS;
            echo "<br /><br />";
            echo _MD_GO_BACK;
        }
    }
    else {
        echo _MD_ERASEFOLDER_ERASE_DONTEXIST;
        echo "<br /><br />";
        echo _MD_GO_BACK;    
    }
}



// Function to mod a folder
function mod_folder($folder_id) {

    global $xoopsDB, $xoopsUser;
    
    
    // Check if we have a folder name
    if (!empty($_POST['new_folder_name'])) {
    
		$folder_name = htmlspecialchars ( $_POST['new_folder_name'] , ENT_QUOTES );
        
        $user_mod = $xoopsUser->uid();
        $mod_date = time();
        
        
        if ( if_folder_exist($folder_id) ) {
            $sql = sprintf("UPDATE %s SET folders_name = '%s', folders_modificationdate = %u, folders_usermod = %u WHERE folders_id = %u", $xoopsDB->prefix("fcabix_folders"), $folder_name, $mod_date, $user_mod, $folder_id);
            
            
            if ($result = $xoopsDB->query($sql)) {
            
                redirect_header("index.php?curent_dir=".$folder_id."",1,_MD_MODFOLDER_SAVE_GOOD);
            }
            else {
                echo _MD_MODFOLDER_SAVE_BAD;
                echo "<br /><br />";
                echo _MD_GO_BACK;
            }
        }
        else {
            echo _MD_MODFOLDER_SAVE_DONTEXIST;
            echo "<br /><br />";
            echo _MD_GO_BACK;
        }
    }
    else {
        echo _MD_MODFOLDER_BAD_NAME;
        echo "<br /><br />";
        echo _MD_GO_BACK;
    } 
    
}

function insert_singlefile( $folder_id, $_files ) {
    global $xoopsDB, $xoopsUser,$xoopsModuleConfig;
    $insert_file = false;
    $emsg = NULL;
    // Check if we have a file
    if (is_uploaded_file($_files['tmp_name'])) {
		$file_name = htmlspecialchars ($_files['name'], ENT_QUOTES );
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $ext_filter = explode(",", $xoopsModuleConfig['ext_filter']);
        if ( !in_array($extension, $ext_filter) ){
        	return _MD_CREATEFILE_UPLOAD_EXTFILTER;
        }
        $sql = sprintf("SELECT COUNT(files_foldersid) FROM %s WHERE files_foldersid=%u AND files_name='%s'", $xoopsDB->prefix("fcabix_files"), $folder_id, $file_name);
        $result = $xoopsDB->query($sql);
		list ($count)=$xoopsDB->fetchRow($result);
		if ( $count > 0 ) {
			//redirect_header("index.php?curent_dir=".$folder_id,2,_MD_FILE_EXIST);
			return _MD_FILE_EXIST;
		}
        $owner = $xoopsUser->uid();
        $create_date = time();
        $file_type = check_file_type($file_name);
        $file_space = ceil(($_files['size'] / 1000));
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
                    $sql = sprintf("INSERT INTO %s (files_id, files_name, files_nameondisk, files_type, files_space, files_createddate, files_modificationdate, files_owner, files_usermod, files_foldersid) VALUES (%u, '%s', '%s', '%s', %u, %u, %u, %u, %u, %u)",
                       $xoopsDB->prefix("fcabix_files"), '', $file_name, $file_nameondisk, $file_type, $file_space, $create_date, $create_date, $owner, $owner, $folder_id);
                    // Insert the file and redirect to the fcabix center
                    if ($where_to_put_file !== "/" && strpos($where_to_put_file, $myconfig['configs_path']) !== false && strpos($where_to_put_file, "..") !== true && strpos($where_to_put_file, " ") !== true) {
                        if ($result = $xoopsDB->query($sql)) {
                            $new_file = $xoopsDB->getInsertId();
                            // Move the file
                            move_uploaded_file($_files['tmp_name'], $where_to_put_file);
                            $insert_file = true;
                        } else {
                            $emsg = _MD_CREATEFILE_SAVE_ERROR_BD;
                            $insert_file = true;
                        }
                    } else {
                        $emsg =  _MD_CREATEFILE_SAVE_ERROR_PATH;
                        $insert_file = true;
                    }
                }
            } while ($insert_file == false);
        } else {
            $emsg = _MD_CREATEFILE_SAVE_WRITABLE.$current_dir.if_folder_exist($folder_id);
        }
    } else {
        switch($_files['error']){
            case 0: $emsg = _MD_CREATEFILE_UPLOAD_PROBLEM; break;
            case 1: $emsg = _MD_CREATEFILE_UPLOAD_TOOBIG;  break;
            case 2: $emsg = _MD_CREATEFILE_UPLOAD_TOOBIG;  break;
            case 3: $emsg = _MD_CREATEFILE_UPLOAD_PARTIAL; break;
            case 4: $emsg = _MD_CREATEFILE_UPLOAD_NOFILE;  break;
            default:$emsg = _MD_CREATEFILE_UPLOADPROBLEM;  break;
        }
    }
    return $emsg;
}

// Function to insert a file in the current parent dir
function insert_file( $folder_id ) {
	$err = FALSE;
	for ( $i=0; $i<count( $_FILES["file_to_add"]["name"] ) ;$i++){
		if ( $_FILES["file_to_add"]["tmp_name"][$i] ){
			$_files = array(
			"name" => $_FILES["file_to_add"]["name"][$i],
			"type" => $_FILES["file_to_add"]["type"][$i],
			"tmp_name" => $_FILES["file_to_add"]["tmp_name"][$i],
			"error" => $_FILES["file_to_add"]["error"][$i],
			"size" => $_FILES["file_to_add"]["size"][$i]
			);
			$emsg = insert_singlefile( $folder_id, $_files );
			$_file_status[$i] = array(
				"name" => $_FILES["file_to_add"]["name"][$i],
				"emsg" => $emsg
				);
			if ( $emsg ) $err = TRUE;
		}
	}
	if( $err==TRUE ){
		return $_file_status ;
	} else {
    	redirect_header("index.php?curent_file=".$new_file."&curent_dir=".$folder_id."",1,_MD_CREATEFILE_SAVE_GOOD);
    }
}



// Function to erase a file
function erase_file($folder_id) {
    
    global $xoopsDB, $_POST;
    
    
    $file_id = (int) $_POST['file_id'];
    
    
    // Select the config path
    $sql = "SELECT configs_path FROM ".$xoopsDB->prefix("fcabix_configs");
    $result = $xoopsDB->query($sql);
    $myconfig = $xoopsDB->fetchArray($result);
    
    
    if ( if_file_is_in_dir($file_id, $folder_id) ) {
    
        // Get the path to the file with the nameondisk
        $file_to_erase = get_current_dir_path($folder_id).get_file_name($file_id, 2);
        
        
        if ( is_writable($file_to_erase) && file_exists($file_to_erase) ) {
        
            $sql = sprintf("DELETE FROM %s WHERE files_id = %u", $xoopsDB->prefix("fcabix_files"), $file_id);
            
            
            // Check to see if its safe to delete the file
            if ($file_to_erase === "/" || strpos($file_to_erase, $myconfig['configs_path']) === false || strpos($file_to_erase, "..") === true || strpos($file_to_erase, " ") === true) {
                echo _MD_ERASEFILE_ERROR_PATH;
                echo "<br /><br />";
                echo _MD_GO_BACK;
            }
            else {
                if ($result = $xoopsDB->query($sql) && unlink($file_to_erase)) {        
                    redirect_header("index.php?curent_dir=".$folder_id,1,_MD_ERASEFILE_GOOD);
                }
                else {
                    echo _MD_ERASEFILE_ERROR_BD;
                    echo "<br /><br />";
                    echo _MD_GO_BACK;
                }
            }
        }
        else {
            echo _MD_ERASEFILE_ERROR_RIGHTS;
            echo "<br /><br />";
            echo _MD_GO_BACK;        
        }
    }
    else {
        echo _MD_ERASEFILE_ERROR_DONTEXIST;
        echo "<br /><br />";
        echo _MD_GO_BACK;    
    }
}



// Function to mod a file
function mod_file($folder_id) {

    global $xoopsDB, $xoopsUser;
    
    $insert_file = false;
    $rename = false;
    
    
    // Check if we have a file name
    if (!empty($_POST['new_file_name'])) {
    
		$file_name = htmlspecialchars ($_POST['new_file_name'], ENT_QUOTES );
        $extension = strtolower(strrchr($file_name, "."));
        $file_id = (int) $_POST['file_id'];
        $file_type = check_file_type($file_name);
        
        if ($xoopsUser)
        	$user_mod = $xoopsUser->uid();
        else
        	$user_mod =0;

        $mod_date = time();
        
        
        $sql = "SELECT files_id, files_name, files_nameondisk, files_foldersid, files_type FROM ".
        	$xoopsDB->prefix("fcabix_files")." WHERE files_id = ".$file_id;
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
                redirect_header("index.php?curent_dir=".$folder_id."&curent_file=".$file_id."",1,_MD_MODFILE_SAVE_GOOD);
            }
            else {
                echo _MD_MODFILE_SAVE_BAD;
                echo "<br /><br />";
                echo _MD_GO_BACK;    
            }
        }
        else {
            echo _MD_MODFILE_SAVE_DONTEXIST;
            echo "<br /><br />";
            echo _MD_GO_BACK;        
        }
    }
    else {
        echo _MD_MODFILE_BAD_NAME;
        echo "<br /><br />";
        echo _MD_GO_BACK;
    } 
    
}



// Function to update a file in the current parent dir
function maj_file($folder_id) {
    
    global $xoopsDB, $xoopsUser;
    
    $insert_file = false;
    
    
    // Check if we have a file
    if (is_uploaded_file($_FILES['file_to_maj']['tmp_name'])) {
        
        $file_id = (int) $_POST['file_id'];


        $sql = "SELECT files_id, files_name, files_nameondisk, files_foldersid, files_type FROM ".$xoopsDB->prefix("fcabix_files")." WHERE files_id = ".$file_id;
        $result = $xoopsDB->query($sql);
        $myrow = $xoopsDB->fetchArray($result);      
        
        
		$file_name = htmlspecialchars ($_FILES['file_to_maj']['name'], ENT_QUOTES );
        $extension = strtolower(strrchr($file_name, "."));
        $file_type = check_file_type($file_name);
        
        $usermod = $xoopsUser->uid();
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
                        
                        redirect_header("index.php?curent_file=".$file_id."&curent_dir=".$folder_id."",1,_MD_MAJFILE_SAVE_GOOD);
                    }
                    else {
                        echo _MD_MAJFILE_SAVE_ERROR_BD;
                        echo "<br /><br />";
                        echo _MD_GO_BACK;
                    }
                }
                else {
                    echo _MD_MAJFILE_SAVE_ERROR_PATH;
                    echo "<br /><br />";
                    echo _MD_GO_BACK;
                }
            }
            else {
                echo _MD_MAJFILE_SAVE_ERROR_WRITABLE;
                echo "<br /><br />";
                echo _MD_GO_BACK;
            }
        }
        else {
            echo _MD_MAJFILE_SAVE_ERROR_DONTEXIST;
            echo "<br /><br />";
            echo _MD_GO_BACK;
        }
    }
    else {
        switch($_FILES['file_to_maj']['error']){
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
}


// Redirect to the good function
switch ($op) {
    case "insert_folder":
        insert_folder($folder_id);
        break;
    case "erase_folder":
        erase_folder($folder_id);
        break;
    case "mod_folder":
        mod_folder($folder_id);
        break;        
    case "insert_file":
        $tpl_vars['file_status'] = insert_file( $folder_id );
        break;
    case "erase_file":
        erase_file($folder_id);
        break;
    case "mod_file":
        mod_file($folder_id);
        break;         
    case "maj_file":
        maj_file($folder_id);
        break;        
}
$xoopsTpl->assign('fcabix', $tpl_vars);


// Include Xoops footer
include 'footer.php';

?>