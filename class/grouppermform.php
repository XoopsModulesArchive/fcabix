<?php 

class XoopsGroupPermForm
{
    var $_modid;
    var $_itemTree;
    var $_permName;
    
    function XoopsGroupPermForm($modid, $permname)
    {
        $this->_modid = intval($modid);
        $this->_permName = $permname;
    } 

    function addItem($itemId, $itemName, $itemParent = 0)
    {
        $this->_itemTree[$itemParent]['children'][] = $itemId;
        $this->_itemTree[$itemId]['parent'] = $itemParent;
        $this->_itemTree[$itemId]['name'] = $itemName;
        $this->_itemTree[$itemId]['id'] = $itemId;
    } 

    function _loadAllChildItemIds($itemId, &$childIds)
    {
        if (!empty($this->_itemTree[$itemId]['children'])) {
            $first_child = $this->_itemTree[$itemId]['children'];
            foreach ($first_child as $fcid) {
                array_push($childIds, $fcid);
                if (!empty($this->_itemTree[$fcid]['children'])) {
                    foreach ($this->_itemTree[$fcid]['children'] as $_fcid) {
                        array_push($childIds, $_fcid);
                        $this->_loadAllChildItemIds($_fcid, $childIds);
                    }
                }
            }
        }
    }

    function assign()
    {
    	
	    // load all child ids for javascript codes
	    foreach (array_keys($this->_itemTree) as $item_id) {
	        $this->_itemTree[$item_id]['allchild'] = array();
	        $this->_loadAllChildItemIds($item_id, $this->_itemTree[$item_id]['allchild']);
	    }
	    $gperm_handler =& xoops_gethandler('groupperm');
	    $member_handler =& xoops_gethandler('member');

	    $groups = $member_handler->getGroupList();
	    $ret = array('name' => 'groupperm_form', 'action' => 'groupperm.php');
	    $ret['hiddens'] = array();
	    $ret['hiddens'][] = array('name' => 'modid', 'value' => $this->_modid);
	    $ret['groups'] = array();
	    foreach($groups as $k => $v){
	    	$checked = $gperm_handler->getItemIds($this->_permName, $k, $this->_modid);
	    	$temp = array('id' => $k, 'name' => $v, 'rights' => array());
	    	foreach($this->_itemTree[0]['children'] as $topitem){
	    		$temp['rights'][] = array(
		    		'checkbox' => array(
			    		'name' => 'perms['.$this->_permName.'][groups]['.$k.']['.$this->_itemTree[$topitem]['id'].']',
	    				'checked' => in_array($this->_itemTree[$topitem]['id'], $checked) ? 1 : 0,
						'label' => $this->_itemTree[$topitem]['name'],
						'value' => 1
					),
					'hiddens' => array(
						array('name' => 'perms['.$this->_permName.'][parents]['.$this->_itemTree[$topitem]['id'].']', 'value' => ''),
						array('name' => 'perms['.$this->_permName.'][itemname]['.$this->_itemTree[$topitem]['id'].']', 'value' => htmlspecialchars($this->_itemTree[$topitem]['name']))
					)
	    		);
	    	}
	    	$ret['groups'][] = $temp;
	    }
		
	    return $ret;
    }
}


?>