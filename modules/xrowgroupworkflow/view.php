<?php

$module = $Params['Module'];
$namedParameters = $module->NamedParameters;
$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();
$ini = eZINI::instance();

$stategroup = eZContentObjectStateGroup::fetchByIdentifier(xrowGroupWorkflow::STATE_GROUP);
$tpl->setVariable('stategroup', $stategroup);
$tpl->setVariable('statedisabled', xrowGroupWorkflow::DISABLED);
$tpl->setVariable('statedone', xrowGroupWorkflow::DONE);

$dateLocaleExplodeSign = array('en' => array('sign' => '/', 'dateOrder' => array('m' => 0, 'd' => 1, 'Y' => 2)),
                               'de' => array('sign' => '.', 'dateOrder' => array('d' => 0, 'm' => 1, 'Y' => 2)));

// add new groupworkflow or update existing one
$groupData = array();
$error = '';
$errorIsSet = false;
if(($http->hasPostVariable('xrowGroupWorkflowAddGroup') || $http->hasPostVariable('xrowGroupWorkflowSave')) && $http->hasPostVariable('xrowGroupWorkflow') )
{
    $groupData = $http->postVariable( 'xrowGroupWorkflow' );
    $groupDataTmp = $groupData;
    $localeTmp = $groupData['locale'];
    unset($groupData['locale']);
    if($http->hasPostVariable('xrowGroupWorkflowSave') && isset($namedParameters['GroupID']) && $namedParameters['GroupID'] > 0 && isset($groupData[$namedParameters['GroupID']]))
    {
        $GroupID = $namedParameters['GroupID'];
        $groupData = $groupData[$GroupID];
    }

    if(trim($groupData['groupname']) == '' || trim($groupData['date']) == '')
    {
        $error = ezpI18n::tr('extension/xrowgroupworkflow', 'Please enter ');
        if(trim($groupData['groupname']) == '')
        {
            $errorIsSet = true;
            $error .= ezpI18n::tr('extension/xrowgroupworkflow', 'groupname');
        }
        if(trim($groupData['date']) == '')
        {
            if($errorIsSet)
                $error .= ezpI18n::tr('extension/xrowgroupworkflow', ' and ');
            $errorIsSet = true;
            $error .= ezpI18n::tr('extension/xrowgroupworkflow', 'date');
        }
        $error .= '.';
    }
    if($errorIsSet === false)
    {
        if(isset($dateLocaleExplodeSign[$localeTmp]))
        {
            $locale = $dateLocaleExplodeSign[$localeTmp];
            $return = setWorkflowData($groupData, $locale);
            if($return['status'] !== true)
            {
                $errorIsSet = true;
                $error = $return['error'];
                $groupData = $groupDataTmp;
            }
        }
    }
}
// Browse
if($http->hasPostVariable('xrowGroupWorkflowBrowseButton') && isset($namedParameters['GroupID']) && $namedParameters['GroupID'] > 0)
{
    $groupID = $namedParameters['GroupID'];
    $customActionButton = $http->postVariable('xrowGroupWorkflowBrowseButton');
    if (isset($customActionButton[$groupID . '_browse_related_node']))
    {
        $ignoreNodesSelect = array();
        $ignoreNodesSelectSubtree = array();
        if( $http->hasPostVariable( 'ignoreNodesSelect' ) )
        {
            $ignoreNodesSelect = $http->postVariable( 'ignoreNodesSelect' );
        }
        if( $http->hasPostVariable( 'ignoreNodesSelectSubtree' ) )
        {
            $ignoreNodesSelectSubtree = $http->postVariable( 'ignoreNodesSelectSubtree' );
        }
        $ignoreNodesSelect = array_unique( $ignoreNodesSelect );
        $ignoreNodesSelectSubtree = array_unique( $ignoreNodesSelectSubtree );
        $ignoreNodesClick = $ignoreNodesSelectSubtree;
        $http->removeSessionVariable( 'BrowseParameters' );
        $browseParameters = array(
            'action_name' => 'AddNodeToGroupWorkflow',
            'browse_custom_action' => array(
                    'name' => 'CustomActionButton[' . $groupID . '_set_related_node]' ,
                    'value' => $groupID
            ),
            'selection' => 'multiple',
            'start_node' => 2,
            'ignore_nodes_select' => $ignoreNodesSelect,
            'ignore_nodes_select_subtree' => $ignoreNodesSelectSubtree,
            'ignore_nodes_click' => $ignoreNodesClick,
            'custom_action_data' => $http->postVariable('xrowGroupWorkflow'),
            'persistent_data' => array(
                    'GroupID' => $groupID,
                    'GroupData' => serialize($http->postVariable('xrowGroupWorkflow'))
            ),
            'from_page' => '/xrowgroupworkflow/view/' . $groupID
        );
        return eZContentBrowse::browse( $browseParameters, $module );
    }
}
// back from browse
if($http->hasPostVariable('BrowseActionName') && $http->postVariable('BrowseActionName') == 'AddNodeToGroupWorkflow' && $http->hasPostVariable('GroupID') && $http->postVariable('GroupID') > 0)
{
    if($http->hasPostVariable('SelectedNodeIDArray') && count($http->postVariable('SelectedNodeIDArray')) > 0)
    {
        $SelectedNodeIDArray = $http->postVariable('SelectedNodeIDArray');
        $GroupID = $http->postVariable('GroupID');
        $groupData = unserialize($http->postVariable('GroupData'));
        $selected_node_ids_tmp = array();
        foreach($SelectedNodeIDArray as $SelectedNodeID)
        {
            if(isset($groupData[$GroupID]['children']))
            {
                if(!array_key_exists($SelectedNodeID, $groupData[$GroupID]['children']))
                {
                    $selected_node_ids_tmp[$SelectedNodeID] = $SelectedNodeID;
                }
            }
            else
            {
                $selected_node_ids_tmp[$SelectedNodeID] = $SelectedNodeID;
            }
        }
        $tpl->setVariable('selected_node_ids', array($GroupID => $selected_node_ids_tmp));
        $tpl->setVariable('groupData', $groupData);
    }
    else
    {
        $groupData = unserialize($http->postVariable('GroupData'));
        $errorIsSet = true;
        $error = ezpI18n::tr('extension/xrowgroupworkflow', 'Please select an object to add to group.');
    }
}
// copy a group
if($http->hasPostVariable('xrowGroupWorkflowCopyGroup') && isset($namedParameters['GroupID']) && $namedParameters['GroupID'] > 0)
{
    $groupID = $namedParameters['GroupID'];
    $copyGroupButton = $http->postVariable('xrowGroupWorkflowCopyGroup');
    if (isset($copyGroupButton[$groupID]))
    {
        $originGroupWorkflow = xrowGroupWorkflow::fetchByID($groupID);
        unset($originGroupWorkflow->id);
        $copyGroupWorkflow = $originGroupWorkflow;
        $groupData = unserialize($copyGroupWorkflow->data);
        $groupData['groupname'] = 'Copy of ' . $groupData['groupname'];
        $copyGroupWorkflow->data = serialize($groupData);
        $copyGroupWorkflow->store();
    }
}
// delete a group
if($http->hasPostVariable('xrowGroupWorkflowRemoveGroup') && isset($namedParameters['GroupID']) && $namedParameters['GroupID'] > 0)
{
    $groupID = $namedParameters['GroupID'];
    $removeGroupButton = $http->postVariable('xrowGroupWorkflowRemoveGroup');
    if (isset($removeGroupButton[$groupID]))
    {
        $xrowGroupWorkflowObject = new xrowGroupWorkflow(array('id' => $groupID));
        $xrowGroupWorkflowObject->remove();
    }
}
// delete an object from group
if($http->hasPostVariable('xrowGroupWorkflowRemoveObject') && isset($namedParameters['GroupID']) && $namedParameters['GroupID'] > 0)
{
    $groupID = $namedParameters['GroupID'];
    $removeObjectButton = $http->postVariable('xrowGroupWorkflowRemoveObject');
    if (isset($removeObjectButton[$groupID]))
    {
        $removeObject = key($removeObjectButton[$groupID]);
        $groupWorkflow = xrowGroupWorkflow::fetchByID($groupID);
        $groupData = unserialize($groupWorkflow->data);
        if(isset($groupData['children'][$removeObject]))
            unset($groupData['children'][$removeObject]);
        $groupWorkflow->data = serialize($groupData);
        $groupWorkflow->store();
    }
}

if($errorIsSet !== false)
{
    $tpl->setVariable('groupData', $groupData);
    $tpl->setVariable('error', $error);
}
$listTmp = eZPersistentObject::fetchObjectList(xrowGroupWorkflow::definition());
foreach($listTmp as $listTmpItem)
{
    $data = unserialize($listTmpItem->data);
    $listTmpItem->data = $data;
    $rows[$listTmpItem->id] = $listTmpItem;
}
$tpl->setVariable('groups', $rows);
$Result = array();
$Result['content'] = $tpl->fetch( 'design:workflow/mainpage.tpl' );

return $Result;

function setWorkflowData($groupData, $locale)
{
    if(isset($groupData['id']))
    {
        $row['id'] = $groupData['id'];
        unset($groupData['id']);
    }
    $dateArray = explode($locale['sign'], $groupData['date']);
    $row['date'] = mktime($groupData['hour'], $groupData['minute'], 0, $dateArray[$locale['dateOrder']['m']], $dateArray[$locale['dateOrder']['d']], $dateArray[$locale['dateOrder']['Y']]);
    if($groupData['status'] == xrowGroupWorkflow::DISABLED || $groupData['status'] == xrowGroupWorkflow::DONE || ($groupData['status'] > 0 && $row['date'] >= time()))
    {
        $row['status'] = (int)$groupData['status'];
        unset($groupData['date']);
        unset($groupData['hour']);
        unset($groupData['minute']);
        unset($groupData['status']);
        $row['data'] = serialize($groupData);
        $xrowGroupWorkflowObject = new xrowGroupWorkflow($row);
        $xrowGroupWorkflowObject->store();
        return array('status' => true);
    }
    else
    {
        $error = ezpI18n::tr('extension/xrowgroupworkflow', 'Please select a date in the future.');
        $errorIsSet = true;
        return array('status' => false, 'error' => ezpI18n::tr('extension/xrowgroupworkflow', 'Please select a date in the future.'));
    }
}