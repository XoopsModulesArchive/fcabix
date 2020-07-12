<?php
// $Id: groupperm.php,v 1.2 2005/03/18 12:52:38 onokazu Exp $

include 'header.php';

$folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : 0;
$is_inherit = isset($_POST['is_inherit']) ? (intval($_POST['is_inherit']) == 1 ? 1 : 0) : -1;
$is_hidden = isset($_POST['is_hidden']) ? (intval($_POST['is_hidden']) == 1 ? 1 : 0) : -1;
$modid = isset($_POST['modid']) ? intval($_POST['modid']) : 0;

$ret = array();

if ($is_mod_admin && $folder_id > 0 && $modid == $xoopsModule->getVar('mid')){
	if($is_inherit > -1 && $is_hidden > -1){
		$sql = "UPDATE ".$xoopsDB->prefix("fcabix_folders")
			. " SET folders_hidden = ".$is_hidden
			. ", folders_inheritrights = ".$is_inherit
			. " WHERE folders_id = ".$folder_id;
		$result = $xoopsDB->query($sql);
		$ret[] = array('success' => TRUE, 'message' => 'is_inherit: '.$is_inherit.' / is_hidden: '.$is_hidden);
	}
	
	if($is_inherit < 1){
	
		$module_handler =& xoops_gethandler('module');
		$module =& $module_handler->get($modid);
		$member_handler =& xoops_gethandler('member');
		$group_list =& $member_handler->getGroupList();
		if (is_array($_POST['perms']) && !empty($_POST['perms'])) {
			$gperm_handler = xoops_gethandler('groupperm');
			foreach ($_POST['perms'] as $perm_name => $perm_data) {
				if (false != $gperm_handler->deleteByModule($modid, $perm_name)) {
					foreach ($perm_data['groups'] as $group_id => $item_ids) {
						foreach ($item_ids as $item_id => $selected) {
							if ($selected == 1) {
								// make sure that all parent ids are selected as well
								if ($perm_data['parents'][$item_id] != '') {
									$parent_ids = explode(':', $perm_data['parents'][$item_id]);
									foreach ($parent_ids as $pid) {
										if ($pid != 0 && !in_array($pid, array_keys($item_ids))) {
											// one of the parent items were not selected, so skip this item
											$ret[] = array('success' => FALSE, 'message' => sprintf(_MD_PERMADDNG, '"'.$perm_name.'"', '"'.$perm_data['itemname'][$item_id].'"', '"'.$group_list[$group_id].'"').' ('._MD_PERMADDNGP.')');
											continue 2;
										}
									}
								}
								$gperm =& $gperm_handler->create();
								$gperm->setVar('gperm_groupid', $group_id);
								$gperm->setVar('gperm_name', $perm_name);
								$gperm->setVar('gperm_modid', $modid);
								$gperm->setVar('gperm_itemid', $item_id);
								if (!$gperm_handler->insert($gperm)) {
									$ret[] = array(
										'success' => FALSE,
										'message' => sprintf(_MD_PERMADDNG, '"'.$perm_name.'"', '"'.$perm_data['itemname'][$item_id].'"', '"'.$group_list[$group_id].'"')
									);
								} else {
									$ret[] = array(
										'success' => TRUE,
										'message' => sprintf(_MD_PERMADDOK, '"'.$perm_name.'"', '"'.$perm_data['itemname'][$item_id].'"', '"'.$group_list[$group_id].'"')
									);
								}
								unset($gperm);
							}
						}
					}
				} else {
					$ret[] = array(
						'success' => FALSE,
						'message' => sprintf(_MD_PERMRESETNG, $module->getVar('name').'('.$perm_name.')')
					);
				}
			}
		}
	}

}else{
	$ret[] = array(
		'success' => FALSE,
		'message' => '_MD_RIGHTS_NOVALID_FOLDER'
	);
}

VAR_DUMP($ret);

?>