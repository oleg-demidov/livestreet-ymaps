<?php


class PluginYmaps_ModuleGeo_EntityGeo extends EntityORM
{
    protected $aValidateRules=array(
        array('long','number','allowEmpty' => false),
        array('lat','number','allowEmpty' => false),
        array('radius','number','allowEmpty' => false),
        array('target_id','number', 'allowEmpty' => false),
        array('target_type','string', 'min' => 1, 'allowEmpty' => false),
        array('zoom','number',  'allowEmpty' => false)
    );
    
    protected $aRelations = array(
        'user'=>array(self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id'),
    );
}