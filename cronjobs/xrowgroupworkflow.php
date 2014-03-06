<?php

//login as admin 
$user = eZUser::fetch( eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' ) );
$user->loginCurrent();

$onlineStateID = eZContentObjectState::fetchByIdentifier(xrowGroupWorkflow::ONLINE, eZContentObjectStateGroup::fetchByIdentifier(xrowGroupWorkflow::STATE_GROUP)->ID)->ID;
$offlineStateID = eZContentObjectState::fetchByIdentifier(xrowGroupWorkflow::OFFLINE, eZContentObjectStateGroup::fetchByIdentifier(xrowGroupWorkflow::STATE_GROUP)->ID)->ID;

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
                                            ' AND status > 0 AND status < 100');

if( is_array( $rows ) && count( $rows ) > 0 )
{
    foreach ( $rows as $groupworkflow )
    {
        $data = unserialize($groupworkflow->data);
        $status = $groupworkflow->status;
        if(isset($data['children']) && count($data['children']) > 0)
        {
            foreach($data['children'] as $nodeID)
            {
                $object = eZContentObject::fetchByNodeID($nodeID);
                if($object instanceof eZContentObject)
                {
                    switch ( $status )
                    {
                        case $onlineStateID:
                            $groupworkflow->online($object, $onlineStateID);
                            if ( ! $isQuiet )
                            {
                                $cli->output( "Set online '" . $object->attribute( 'name' ) . "' (" . $object->ID . ")." );
                            }
                            break;
                        case $offlineStateID:
                            $groupworkflow->offline($object, $offlineStateID);
                            if ( ! $isQuiet )
                            {
                                $cli->output( "Set offline '" . $object->attribute( 'name' ) . "' (" . $object->ID . ")." );
                            }
                            break;
                        }
                    }
                else
                {
                    eZDebug::writeError( array( $object, " is not instanceof eZContentObject" ), __METHOD__ );
                }
                echo ".";
            }
        }
    }
}
if( !$isQuiet )
    $cli->output( 'Finished.' );