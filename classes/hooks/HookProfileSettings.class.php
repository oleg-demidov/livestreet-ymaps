<?php

class PluginYmaps_HookProfileSettings extends Hook
{
    
    public function RegisterHook()
    {
        $this->AddHook('template_ymap_settings', 'AssignGeo');
        $this->AddHook('action_event_settings_after', 'SettingsComponentAdd');
        $this->AddHook('settings_profile_save_before', 'SaveGeoUserBefore');
        
    }
    
    public function SettingsComponentAdd($aParams) {
        $oUser = $this->User_GetUserCurrent();
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('user');
        $oUser->AttachBehavior('ymap', $aBehavior);
        
        $oGeo = $oUser->ymap->getGeo();
                
        $aGeoData = $oGeo?$oGeo->_getData(['lat', 'long', 'radius','zoom']):[];
        
        $this->Viewer_Assign('ymapHook', 'settings');
        $this->Component_Add('ymaps:settings-map');
        $this->Lang_AddLangJs([
            'plugin.ymaps.field.mark_on_map'
        ]);
        
        if(Config::Get('plugin.ymaps.geo.enable')){
            $this->Lang_AddLangJs([
                'plugin.ymaps.field.defaultText'
            ]);
            $this->Component_Add('ymaps:field');
            $this->Viewer_AssignJs('country_code', Config::Get('plugin.ymaps.geo.country_code'));
        }
        
        $this->Viewer_AssignJs('profile_settings', Config::Get('plugin.ymaps.options.profile_settings'));
        $this->Viewer_AssignJs('ymapData', $aGeoData);
        
        
    }
    
    public function AssignGeo($aParams) {
        if(!isset($aParams['place']) or !$aParams['place']){
            return null;
        }
        $oGeo = $this->PluginYmaps_Geo_GetGeoByFilter([
            'target_type' => $aParams['place']->getTargetType(),
            'target_id' => $aParams['place']->getTargetId(),
            
        ]);
        return $oGeo->_getData();
    }
        
    public function SaveGeoUserBefore($aParams) {
        
        $oUser = $aParams['oUser'];
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('user');
        
        $oUser->AttachBehavior('ymap', $aBehavior);
        
        $oUser->_setValidateScenario('none');
        if(!$oUser->_Validate()){
            $this->Message_AddError($oUser->_getValidateError());
        }
        $oUser->ymap->CallbackAfterSave();
        
    }
}