<?php

class xrowGroupWorkflow extends eZPersistentObject
{
    const STATE_GROUP = 'groupworkflow';
    const ONLINE = 'online';
    const OFFLINE = 'offline';
    const DONE = 100;
    const DISABLED = 0;

    function __construct( $row )
    {
        parent::__construct( $row );
    }

    static function definition()
    {
        return array( 
            'fields' => array( 
                'id' => array( 
                    'name' => 'id',
                    'datatype' => 'integer',
                    'default' => 0,
                    'required' => true
                ) , 
                'status' => array( 
                    'name' => 'status',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => true
                ) , 
                'date' => array( 
                    'name' => 'date',
                    'datatype' => 'integer',
                    'default' => 0,
                    'required' => true
                ) , 
                'data' => array( 
                    'name' => 'data',
                    'datatype' => 'text',
                    'default' => '',
                    'required' => true 
                ) 
            ) , 
            'keys' => array( 
                'id' 
            ) , 
            'sort' => array( 
                'date' => 'desc' 
            ) , 
            'class_name' => 'xrowgroupworkflow',
            'name' => 'xrowgroupworkflow' 
        );
    }

    static public function fetchByID( $ID )
    {
        return eZPersistentObject::fetchObject( xrowgroupworkflow::definition(), null, array( 
            'id' => $ID 
        ) );
    }

    /**
     * Update a contentobject's state
     *
     * @param int $objectID
     * @param int $selectedStateIDList
     *
     * @return array An array with operation status, always true
     */
    static public function updateObjectState( $objectID, $selectedStateIDList )
    {
        $object = eZContentObject::fetch( $objectID );
        // we don't need to re-assign states the object currently already has assigned
        $currentStateIDArray = $object->attribute( 'state_id_array' );
        $selectedStateIDList = array_diff( $selectedStateIDList, $currentStateIDArray );
        foreach ( $selectedStateIDList as $selectedStateID )
        {
            $state = eZContentObjectState::fetchById( $selectedStateID );
            $object->assignState( $state );
        }
        //call appropriate method from search engine
        eZSearch::updateObjectState( $objectID, $selectedStateIDList );
        eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
    }

    function online()
    {
        $object = eZContentObject::fetch( $this->contentobject_id );
        if ( $this->attribute( 'start' ) > 0 && $object->attribute( 'class_identifier' ) != 'event' )
        {
            $object->setAttribute( 'published', $this->attribute( 'start' ) );
            $object->store();
        }
        self::updateObjectState( $this->contentobject_id, array( 
            eZContentObjectState::fetchByIdentifier( xrowworkflow::ONLINE, eZContentObjectStateGroup::fetchByIdentifier( xrowworkflow::STATE_GROUP )->ID )->ID 
        ) );
        if( $this->attribute( 'end' ) !== NULL && $this->attribute( 'end' ) > 0 )
        {
            $this->setAttribute( 'start', 0 );
            $this->store();
        }
        else
        {
            $this->remove();
        }
        eZContentCacheManager::clearContentCache( $this->contentobject_id );
        eZDebug::writeDebug( __METHOD__ );
    }

    function offline($contentobject_id)
    {
        self::updateObjectState( $this->contentobject_id, array( 
            eZContentObjectState::fetchByIdentifier( xrowgroupworkflow::OFFLINE, eZContentObjectStateGroup::fetchByIdentifier( xrowworkflow::STATE_GROUP )->ID )->ID 
        ) );
        $this->cleareZFlowBlocks();
        $this->setAttribute('status', 100);
        $this->store();
        eZDebug::writeDebug( __METHOD__ );
    }

    function cleareZFlowBlocks()
    {
        $db = eZDB::instance();
        // Remove from the flow
        if ($this->contentobject_id > 0)
        {
            $db->begin();
            $db->query( 'DELETE FROM ezm_pool WHERE object_id = ' . (int) $this->contentobject_id );
            $db->commit();
            $rows = $db->arrayQuery( 'SELECT DISTINCT ezm_block.node_id FROM ezm_pool, ezm_block WHERE object_id = ' . (int) $this->contentobject_id . ' AND ezm_pool.block_id = ezm_block.id' );
            if (isset($rows) && count($rows))
            {
                foreach ($rows as $row)
                {
                    $contentObject = eZContentObject::fetchByNodeID($row['node_id']);
                    if ($contentObject)
                        eZContentCacheManager::clearContentCache($contentObject->attribute('id'));
                }
            }
            eZContentCacheManager::clearContentCache($this->contentobject_id);
        }
    }
}
