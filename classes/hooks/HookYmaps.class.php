<?php

class PluginYmaps_HookYmaps extends Hook
{

    public function RegisterHook()
    {
        $aLocationActions = Config::Get('plugin.ymaps.location.actions');
        $aSearchActions = Config::Get('plugin.ymaps.search.actions');
        $aActions = array_merge(array_keys($aLocationActions),array_keys($aSearchActions));
        
        if(!in_array(Router::GetAction(), $aActions)){
            return false;
        }
        
        $this->AddHook('template_html_head_end', 'AddYmapScript');        
    }
    
    public function AddYmapScript($aParams) { 
        return '<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>';
    }
    
}