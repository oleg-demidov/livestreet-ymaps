<?php

class PluginYmaps_BlockTopicLocation extends Block
{

    public function Exec()
    {
        
        $oTopic = $this->GetParam('topic');
        
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('topic');
        $oTopic->AttachBehavior('ymaps', $aBehavior);
        
        $this->Viewer_Assign('oLocation', $oTopic->ymaps->getGeo(), true);
        $this->SetTemplate('component@ymaps:topic.block.location');
    }
    
}