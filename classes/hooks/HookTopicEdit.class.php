<?php

class PluginYmaps_HookTopicEdit extends Hook
{
    public function RegisterHook()
    {
        $this->AddHook('topic_add_validate_before', 'ValidateLocationTopic');
        $this->AddHook('topic_edit_validate_before', 'ValidateLocationTopic');
        $this->AddHook('topic_edit_after', 'TopicEditAfter');
        $this->AddHook('topic_add_after', 'TopicEditAfter');
    }
    
        
    public function ValidateLocationTopic($aParams) {
        $oTopic = $aParams['oTopic'];
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('topic');
        $oTopic->AttachBehavior('ymaps', $aBehavior);
        
        $oTopic->ymaps->setParam('validate_enable', true);
        $oTopic->ymaps->setParam('validate_from_request', true);        
        
    }
    
    public function TopicEditAfter($aParams) {
        $oTopic = $aParams['oTopic'];
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('topic');
        $oTopic->AttachBehavior('ymaps', $aBehavior);
        
        $oTopic->ymaps->CallbackAfterSave();
        
    }
}