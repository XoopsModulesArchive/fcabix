<?php
// $Id: rights.php,v 1.3 2005/01/17 16:33:07 jsaucier Exp $
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
include 'header.php';

// Include Xoops header
require(XOOPS_ROOT_PATH.'/header.php');

include_once 'class/grouppermform.php';
$module_id = $xoopsModule->getVar('mid');

// Select the current operation
$op = isset($_POST['op']) ? htmlspecialchars ( $_POST['op'] , ENT_QUOTES ) : "";
if (!$op && isset($_GET['op']) ) $op = htmlspecialchars ( $_GET['op'] , ENT_QUOTES );

// Select the current_dir that we are in
$folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : 0;
if (!$folder_id && isset($_GET['curent_dir']) ) $folder_id = intval( $_GET['curent_dir']);
if (!$folder_id){
	redirect_header("index.php",1,_MD_RIGHTS_NOVALID_FOLDER);
}
if (!$is_mod_admin){
	redirect_header("index.php",1,_MD_RIGHTS_NOVALID_FOLDER);
}
// Option
$is_hidden=$is_inherit=0;
if ( isset($_POST['is_hidden']) ) $is_hidden = ($_POST['is_hidden'] == "on" ? 1 : 0);
if ( isset($_POST['is_inherit']) ) $is_inherit = ($_POST['is_inherit'] == "on" ? 1 : 0);

switch ($op) {
    case "set_rights":
		// Update the rights infos
	    if ($folder_id > 0) {
			$sql = "UPDATE ".$xoopsDB->prefix("fcabix_folders")." SET folders_hidden = ".$is_hidden.", folders_inheritrights = ".$is_inherit." WHERE folders_id = ".$folder_id;
			if ($result = $xoopsDB->query($sql)) {
			    redirect_header("attr_folder.php?curent_dir=".$folder_id,1,_MD_RIGHTS_UPDATE_GOOD);
			} else {
		    	redirect_header("attr_folder.php?curent_dir=".$folder_id,3,_MD_RIGHTS_UPDATE_BAD);
			}
		}
		break;
    default:
		// Select the info of the current dir
		$sql = "SELECT folders_id, folders_name, folders_inheritrights, folders_hidden FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_id = ".$folder_id;
		$result = $xoopsDB->query($sql);
		$current_folder = $xoopsDB->fetchArray($result);
		// If the current folder exist
		if (!empty($current_folder)) {
		    // Check if it's the primary folder, if yes, check out the constant name
		    if ($folder_id == 1) {
		        $folder_name = constant($current_folder['folders_name']);
		    } else {
		        $folder_name = $current_folder['folders_name'];
		    }
		    // Fill up form information
		    $title_of_form = "";
		    $perm_desc = _MD_RIGHTS_DESC;
		    $perm_name = $folder_id;
		    $checked_inherit = ($current_folder['folders_inheritrights']==1 ? 1 : 0);
		    $checked_hidden = ($current_folder['folders_hidden']==1 ? 1 : 0);
		    // Select all the rights
		    $sql = "SELECT rights_id, rights_name FROM ".$xoopsDB->prefix("fcabix_rights")." WHERE rights_applied_to_folders = 1";
		    $result = $xoopsDB->query($sql);
		    // Build the item list
		    while ($rights_array = $xoopsDB->fetchArray($result)) {
		        $item_list[$rights_array['rights_id']] = constant($rights_array['rights_name']);
		    }
		    // Make the form
		    $form = new XoopsGroupPermForm($module_id, $perm_name);
		    foreach ($item_list as $item_id => $item_name) {
		        $form->addItem($item_id, $item_name);
		    }
		    $tpl_vars = array('content'=> array(),'langs'=> array(),'config'=> array());
	    	$tpl_vars['config']['show_groupperm'] = $checked_inherit ? FALSE : TRUE;
			$tpl_vars['content']['groupperm_settings'] = $form->assign();
		    // Build the form to add some rights (hidden and inherit)
		    // If it's the parent folder, dont permit hidden and inherit
			$tpl_vars['config']['folder_id'] = $folder_id;
			$tpl_vars['config']['folder_name'] = $folder_name;
			$tpl_vars['content']['basic_settings'] = array(
				'checkboxes' => array(
					array('name' => 'is_hidden', 'checked' => $checked_hidden, 'label' => _MD_ATTRFOLDER_HIDDEN_FOLDER),
					array('name' => 'is_inherit', 'checked' => $checked_inherit, 'label' => _MD_ATTRFOLDER_INHERIT_FOLDER)
				)
			);
    	    $tpl_vars['langs'] = array(
				'main_title' => _MD_FCABIX_MAIN_TITLE,
				'attrfolder_title' => _MD_ATTRFOLDER_TITLE,
				'basic_settings_title' => _MD_ATTRFOLDER_BASIC_SETTINGS_TITLE,
				'basic_settings_submit' => _MD_ATTRFOLDER_BASIC_SETTINGS_SUBMIT,
				'groupperm_settings_title' => _MD_ATTRFOLDER_GROUPPERM_SETTINGS_TITLE,
				'groupperm_settings_notice' => _MD_ATTRFOLDER_GROUPPERM_SETTINGS_NOTICE,
				'groupperm_settings_submit' => _MD_ATTRFOLDER_GROUPPERM_SETTINGS_SUBMIT,
				'groupperm_settings_reset' => _MD_ATTRFOLDER_GROUPPERM_SETTINGS_RESET
			);
			$xoopsTpl->assign('fcabix', $tpl_vars);
		} else {
		    echo _MD_RIGHTS_NOVALID_FOLDER;
		}
		break;
}

$xoops_module_header = '
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="style.css" />
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/ui/themes/ui-lightness/jquery-ui-1.7.1.custom.css" />
';
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);


// Set the template
$xoopsOption['template_main'] = 'fcabix_attrfolder.html';
// Set the footer
include 'footer.php';
?>