<?php

class PluginYmaps_HookMain extends Hook
{
    
    public function RegisterHook()
    {
        //$this->AddHook('template_html_head_end', 'AddYmapScript');
    }
    
    public function AddYmapScript($aParams) {
        /*
         * Нужно ли загружать скрипты Яндекс карт
         */
        $aLoadYmapActions = Config::Get('plugin.ymaps.load_ymaps_actions');        
        if( in_array(Router::GetAction(), $aLoadYmapActions)){
            return '<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>';
        }
    }

}