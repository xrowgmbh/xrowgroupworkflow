<?php

//login as admin 
$user = eZUser::fetch( eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' ) );
$user->loginCurrent();

$stategroup = eZContentObjectStateGroup::fetchByIdentifier(xrowGroupWorkflow::STATE_GROUP);
die(var_dump($stategroup));

// get current groupworkflow
$condArray = array('date' => array('<=', time()));
$rows = eZPersistentObject::fetchObjectList(xrowGroupWorkflow::definition(),
                                            null,
                                            $condArray,
                                            null,
                                            null,
                                            true,
                                            false,
                                            null,
                                            null,
                                            ' status != 0 AND status != 100');

die(var_dump($rows));

if( is_array( $rows ) && count( $rows ) > 0 )
{
    foreach ( $rows as $row )
    {
        $data = unserialize($row['data']);
        $status = $row['status'];
        if(isset($data['children']) && count($data['children']) > 0)
        {
            foreach($data['children'] as $nodeID)
            {
                $object = eZContentObject::fetchByNodeID($nodeID);
                if( $node instanceof eZContentObjectTreeNode )
                {
                    switch ( $status )
                    {
                        case $online_state->ID:
                            $workflow->moveTo();
                            if ( ! $isQuiet )
                            {
                                $cli->output( "Move '" . $node->attribute( 'name' ) . "' (" . $node->NodeID . ")." );
                            }
                            break;
                        case $offline_state->ID:
                                $workflow->delete();
                                if ( ! $isQuiet )
                                {
                                    $cli->output( "Delete '" . $node->attribute( 'name' ) . "' (" . $node->NodeID . ")." );
                                }
                                break;
                            default:
                                $workflow->offline();
                                if ( ! $isQuiet )
                                {
                                    $cli->output( "Set offline '" . $node->attribute( 'name' ) . "' (" . $node->NodeID . ")." );
                                }
                                break;
                        }
                    }
                else
                {
                    eZDebug::writeError( array( $node, " is not instanceof eZContentObjectTreeNode" ), __METHOD__ );
                }
                echo ".";
            }
            $params["Offset"] = $params["Offset"] + count( $nodeArray );
            eZContentObject::clearCache();
        }
    }
    while ( is_array( $nodeArray ) and count( $nodeArray ) > 0 );
}

$params = array( 
    'Limitation' => array(),
    'IgnoreVisibility' => true,
    'ExtendedAttributeFilter' => array( 
        'id' => 'xrowworkflow_start', 
        'params' => array() 
    ) 
);
$nodeArrayCount = (int)eZContentObjectTreeNode::subTreeCountByNodeID( $params, $nodeID );
if ( $nodeArrayCount > 0 )
{
    if ( ! $isQuiet )
    {
        $cli->output( 'Publishing content of node START.' );
        $cli->output();
    }

    if ( ! $isQuiet )
    {
        $cli->output( "Publishing {$nodeArrayCount} node(s)." );
    }
    $params['Limit'] = 100;
    $params['Offset'] = 0;
    do
    {
        $nodeArray = eZContentObjectTreeNode::subTreeByNodeID( $params, $nodeID );
        if( is_array( $nodeArray ) && count( $nodeArray ) > 0 )
        {
            foreach ( $nodeArray as $node )
            {
                if( $node instanceof eZContentObjectTreeNode )
                {
                    $workflow = xrowworkflow::fetchByContentObjectID( $node->ContentObjectID );
                    if ( $workflow instanceof xrowworkflow )
                    {
                        $workflow->online();
                    }
                    
                    if ( ! $isQuiet )
                    {
                        $cli->output( 'Publishing node: "' . $node->attribute( 'name' ) . '" (' . $node->attribute( 'node_id' ) . ')' );
                    }
                }
                else
                {
                    eZDebug::writeError( array( $node, " is not instanceof eZContentObjectTreeNode" ), __METHOD__ );
                }
            }
            $params["Offset"] = $params["Offset"] + count( $nodeArray );
            eZContentObject::clearCache();
        }
    }
    while ( is_array( $nodeArray ) and count( $nodeArray ) > 0 );
}
