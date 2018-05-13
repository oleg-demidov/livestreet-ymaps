<?php

class PluginYmaps_HookLocation extends Hook
{

    public function RegisterHook()
    {
        $aLoadLocationActions = Config::Get('plugin.ymaps.location.actions');
        $aActions = array_keys($aLoadLocationActions);
        
        if(in_array(Router::GetAction(), $aActions)){
            $this->AddHook('template_field_geo_end', 'ComponentLocationAdd');
        }
        
    }
    
    public function ComponentLocationAdd($param) {
        
        if(isset($param['oLocation']) and $param['oLocation']){
            $oLocation = $param['oLocation'];
        }
        
        if(!isset($oLocation) and isset($param['target']) and $param['target']){
            $oLocation = $this->PluginYmaps_Geo_GetGeoByFilter($param['target']->_getData(['target_type','target_id']));
        }
        
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oLocation', $oLocation, true);
        $oViewer->Assign('label', $this->Lang_Get('plugin.ymaps.map.label'), true);
        return $oViewer->Fetch('component@ymaps:fields.location');
    }
    

}