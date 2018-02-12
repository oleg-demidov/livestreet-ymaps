<?php

class PluginYmaps_HookSearch extends Hook
{
    
    public function RegisterHook()
    {
        $this->AddHook('action_event_people_after', 'ComponentAdd');
    }
    
    public function ComponentAdd($aParams) {
        $this->Component_Add('ymaps:search-map');
        $this->Viewer_AssignJs('ymapsOptions', Config::Get('plugin.ymaps.options.search'));
        $this->Viewer_AssignJs('ymapsInit', 0);
    }

}