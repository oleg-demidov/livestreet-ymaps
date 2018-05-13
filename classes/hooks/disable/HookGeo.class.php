<?php

class PluginYmaps_HookGeo extends Hook
{

    public function RegisterHook()
    {
        $aLoadGeoActions = Config::Get('plugin.ymaps.load_geo_actions');
        foreach($aLoadGeoActions as $key => $value){
            if(is_array($value)){
                $sLoadGeoAction = $key;
            }else{
                $sLoadGeoAction = $value;
            }
            $this->AddHook("action_event_{$sLoadGeoAction}_after", 'ComponentLocationAdd');
        }        
           
    }
        
    public function ComponentLocationAdd($param) { 
        $aLoadGeoActions = Config::Get('plugin.ymaps.load_geo_actions');
        if(!in_array(Router::GetAction(), $aLoadGeoActions)){
            return false;
        }
        if(isset($aLoadGeoActions[Router::GetAction()]) 
                and !isset($aLoadGeoActions[Router::GetAction()][$aParams['event']])){
            return false;
        }        
        
        if(Config::Get('plugin.ymaps.geo.enable')){
            $this->Component_Add('ymaps:fields');
            $this->Viewer_AppendScript($this->Component_GetWebPath('ymaps:fields').'/js/init.js');
            $this->Lang_AddLangJs([
                'plugin.ymaps.map.define_location'
            ]);
        }
    }

}