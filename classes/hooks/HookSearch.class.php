<?php

class PluginYmaps_HookSearch extends Hook
{

    public function RegisterHook()
    {
        $this->AddHook('template_toggle_search_users_view', 'AddToMenuMap');
        
    }
    
    public function AddToMenuMap($param) {
        
        $param['items'][] = [
            'text' => $this->Lang_Get('plugin.ymaps.toggle.map'),
            'classes' => 'js-show-map',
            'icon' => 'map-o',
            'name' => 'map',
            'attributes' => ['title'=>$this->Lang_Get('plugin.ymaps.toggle.title') ]
        ];
        return $param['items'];
    }
    

}