<?php

class PluginYmaps_HookSearch extends Hook
{
    
    public function RegisterHook()
    {
        $aLoadSearchActions = Config::Get('plugin.ymaps.search.actions');
        $aActions = array_keys($aLoadSearchActions);
        
        foreach( $aActions as $sAction){
            $this->AddHook("action_event_{$sAction}_after", 'ComponentSearchLoad');
        }
        //$this->AddHook('action_event_people_after', 'ComponentAdd');
    }    
    
    public function ComponentSearchLoad($aParams) {
        
        $this->Component_Add('ymaps:search-map');
        $this->Viewer_AssignJs('ymapsOptions', Config::Get('plugin.ymaps.search.actions.'.Router::GetAction()));
        $this->Viewer_AssignJs('ymapsInit', 0);
        
        if(Config::Get('plugin.ymaps.geo.enable')){
            $this->Lang_AddLangJs([
                'plugin.ymaps.field.defaultText'
            ]);
            $this->Component_Add('ymaps:fields');
            $this->Viewer_AssignJs('country_code', Config::Get('plugin.ymaps.geo.country_code'));
        }
    }

}