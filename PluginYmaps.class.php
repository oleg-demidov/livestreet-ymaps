<?php
/**
 * @author Oleg Demidov
 *
 */

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attempt!');
}

class PluginYmaps extends Plugin
{
    protected $aInherits = array(
        'template' => array(
            'component.user.info-group' => '_components/user/info-group.tpl', // компонет профиля
//            'component.user.search-form' => '_components/user/search-form.users.tpl' , // компонет в поиске
            'component.field.geo' => '_components/fields/field.geo.replace.tpl' ,
        )
    );
    
    protected $bGeoEnable;

    public function __construct() {
        if(Config::Get('plugin.ymaps.geo.enable')){
            $this->aInherits['template']['component.field.geo'] = '_components/fields/field.geo.tpl';// новый гео компонент
            $this->bGeoEnable = true;
        }
    }
    public function Init()
    {
        $this->Viewer_AssignJs('country_code', Config::Get('plugin.ymaps.geo.country_code'));
        
        if($this->bGeoEnable){
            $this->Viewer_AppendScript($this->Component_GetWebPath('ymaps:fields').'/js/init-geo.js');
            $this->Lang_AddLangJs([
                'plugin.ymaps.map.define_location'
            ]);
        }
        
        $this->Component_Add('ymaps:search-map');
        $this->Component_Add('ymaps:fields');
        $this->Viewer_AssignJs('ymaps_options', Config::Get('plugin.ymaps.location.actions.'.Router::GetAction()));
        $this->Viewer_AssignJs('ymapsOptions', Config::Get('plugin.ymaps.search.actions.'.Router::GetAction()));

        $this->Viewer_AppendScript($sPath = Plugin::GetTemplateWebPath('ymaps').'assets/js/init.js');
    }

    public function Activate()
    {
        return true;
    }

    public function Deactivate()
    {
        return true;
    }
}