<?php

class xrowGroupWorkflow extends eZPersistentObject
{
    const STATE_GROUP = 'groupworkflow';
    const ONLINE = 'online';
    const OFFLINE = 'offline';
    const DONE = '0';
    const DISABLED = '-1';

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
                'date' => 'desc',
                'status' => 'desc'
            ) , 
            'class_name' => 'xrowgroupworkflow',
            'name' => 'xrowgroupworkflow' 
        );
    }

    static public function fetchByID($ID)
    {
        return eZPersistentObject::fetchObject(xrowgroupworkflow::definition(), null, array( 
            'id' => $ID 
        ));
    }

    /**
     * Update a contentobject's state
     *
     * @param int $objectID
     * @param int $selectedStateIDList
     *
     * @return array An array with operation status, always true
     */
    static public function updateObjectState($object, $selectedStateIDList)
    {
        if($object instanceof eZContentObject)
        {
            // we don't need to re-assign states the object currently already has assigned
            $currentStateIDArray = $object->attribute( 'state_id_array' );
            $selectedStateIDList = array_diff( $selectedStateIDList, $currentStateIDArray );
            foreach ( $selectedStateIDList as $selectedStateID )
            {
                $state = eZContentObjectState::fetchById( $selectedStateID );
                $object->assignState( $state );
            }
            //call appropriate method from search engine
            eZSearch::updateObjectState($object->ID, $selectedStateIDList);
            eZContentCacheManager::clearContentCacheIfNeeded($object->ID);
        }
        else
        {
            eZDebug::writeDebug(array($object), __METHOD__);
        }
    }

    function online($object, $onlineStateID)
    {
        if ($this->attribute('date') > 0)
        {
            $object->setAttribute('published', $this->attribute('date'));
            $object->store();
        }
        self::updateObjectState($object, array($onlineStateID));
        eZContentCacheManager::clearContentCache($object->ID);
        $this->setAttribute('status', xrowGroupWorkflow::DONE);
        $this->store();
    }

    function offline($object, $offlineStateID)
    {
        self::updateObjectState($object, array($offlineStateID));
        $this->cleareZFlowBlocks();
        $this->setAttribute('status', xrowGroupWorkflow::DONE);
        $this->store();
    }

    function cleareZFlowBlocks($object)
    {
        $db = eZDB::instance();
        // Remove from the flow
        if ($object->ID > 0)
        {
            $db->begin();
            $db->query( 'DELETE FROM ezm_pool WHERE object_id = ' . (int)$object->ID);
            $db->commit();
            /*$rows = $db->arrayQuery('SELECT DISTINCT ezm_block.node_id FROM ezm_pool, ezm_block WHERE object_id = ' . (int)$object->ID . ' AND ezm_pool.block_id = ezm_block.id');
            if (isset($rows) && count($rows))
            {
                foreach ($rows as $row)
                {
                    $contentObject = eZContentObject::fetchByNodeID($row['node_id']);
                    if ($contentObject)
                        eZContentCacheManager::clearContentCache($contentObject->attribute('id'));
                }
            }*/
            eZContentCacheManager::clearContentCache($this->contentobject_id);
        }
    }
}
