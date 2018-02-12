<?php
/*
 * Хук показа карты в профиле
 */
class PluginYmaps_HookProfile extends Hook
{
    
    public function RegisterHook()
    {
        $this->AddHook('action_event_profile_after', 'AddComponent');
        $this->AddHook('template_user-info-group--items', 'AddMapProfile');
    }    
    
    public function AddComponent($param) {
        $this->Component_Add('ymaps:profile-map');
        $this->Viewer_AssignJs('ymapsOptions', Config::Get('plugin.ymaps.options.profile'));
    }
    
    public function getHTML($text, $oUser) {
        $aBehavior = $this->PluginYmaps_Geo_GetBehaviorFor('user');        
        $oUser->AttachBehavior('ymap', $aBehavior);
        
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oGeo', $oUser->ymap->getGeo(), true);
        $oViewer->Assign('text', $text, true);
        return $oViewer->Fetch('component@ymaps:modal-map');
    }
    
    public function AddMapProfile($aParams) {
        //print_r($aParams);
        foreach($aParams['items'] as &$aItem){
            if($aItem['label'] == 'Местоположение'){
                $aItem['content'] = $this->getHTML($aItem['content'], $aParams['user']);
                break;
            }
        }
        return $aParams['items'];
    }
}