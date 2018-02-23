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
        
        $this->Lang_AddLangJs([
            'plugin.ymaps.search.layoutResult'
        ]);
        
        if(Config::Get('plugin.ymaps.geo.enable')){
            $this->Lang_AddLangJs([
                'plugin.ymaps.field.defaultText'
            ]);
            $this->Component_Add('ymaps:field');
            $this->Viewer_AssignJs('country_code', Config::Get('plugin.ymaps.geo.country_code'));
        }
    }

}