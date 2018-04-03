<?php


class PluginYmaps_ModuleGeo_EntityGeo extends EntityORM
{
    protected $aValidateRules=array(
        array('long','number','allowEmpty' => false, 'on' => array('')),
        array('lat','number','allowEmpty' => false, 'on' => array('')),
        array('radius','number','allowEmpty' => false, 'on' => array('')),
        array('target_id','number', 'allowEmpty' => false, 'on' => ['target_id']),
        array('target_type','string', 'min' => 1, 'allowEmpty' => false, 'on' => array('')),
        array('zoom','number',  'allowEmpty' => false, 'on' => array(''))
    );
    
    protected $aRelations = array(
        'user'=>array(self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id'),
    );
}