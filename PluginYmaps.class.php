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
            'component.user.search-form' => '_components/user/search-form.users.tpl' , // компонет в поиске
            'component.field.geo' => '_components/fields/field.geo.replace.tpl' ,
        )
    );
    
    public function __construct($param) {
        if(Config::Get('plugin.ymaps.geo.enable')){
            $this->aInherits['template']['component.field.geo'] = '_components/fields/field.geo.tpl';// новый гео компонент
        }
    }
    public function Init()
    {
        $this->Viewer_AssignJs('country_code', Config::Get('plugin.ymaps.geo.country_code'));
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