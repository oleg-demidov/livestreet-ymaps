<?php

class PluginYmaps_HookLocation extends Hook
{

    public function RegisterHook()
    {
        $aLoadLocationActions = Config::Get('plugin.ymaps.location.actions');
        $aActions = array_keys($aLoadLocationActions);
        
        foreach( $aActions as $sAction){
            $this->AddHook("action_event_{$sAction}_after", 'ComponentLocationLoad');
        }
        
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
    
    public function ComponentLocationLoad($param) { 
        $aLoadLocationActions = Config::Get('plugin.ymaps.location.actions');
        if(!in_array(Router::GetAction(), array_keys($aLoadLocationActions))){
            return false;
        }
        if(isset($aLoadLocationActions[Router::GetAction()]['events']) 
                and !isset($aLoadLocationActions[Router::GetAction()]['events'][$aParams['event']])){
            return false;
        }     
        
        $this->Component_Add('ymaps:fields'); 
        $this->Viewer_AppendScript($this->Component_GetWebPath('ymaps:fields').'/js/init.js');
        $this->Viewer_AssignJs('ymaps_options', Config::Get('plugin.ymaps.location.actions.'.Router::GetAction()));
        /*$this->Lang_AddLangJs([
            'plugin.ymaps.map.define_location'
        ]);*/
    }

}