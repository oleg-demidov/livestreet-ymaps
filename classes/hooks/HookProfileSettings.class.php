<?php

class PluginYmaps_HookProfileSettings extends Hook
{
    public $aLoadSettingsActions;
    public function RegisterHook()
    {
        $this->AddHook("action_event_settings_after", 'GeoLocationComponentAdd');
        $this->AddHook('settings_profile_save_before', 'SaveGeoUserBefore');
        
    }
    
    public function GeoLocationComponentAdd($aParams) {
        //$this->Component_Add('ymaps:geo-location-init');
    }
        
    public function SaveGeoUserBefore($aParams) {
        $oUser = $aParams['oUser'];
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('user');
        $aBehavior['validate_enable'] = true;
        $oUser->AttachBehavior('ymaps', $aBehavior);
        
        $oUser->ymaps->setParam('validate_enable', true);
        $oUser->ymaps->setParam('validate_from_request', true);
        
        $oUser->_setValidateScenario('none');
        if(!$oUser->_Validate()){
            $this->Message_AddError($oUser->_getValidateError());
        }
        $oUser->ymaps->CallbackAfterSave();
        
    }
}