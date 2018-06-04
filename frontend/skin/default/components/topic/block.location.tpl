{**
 * Выбор местоположения
 *
 * @param string $targetType
 * @param object $place
 * @param array  $countries
 * @param array  $regions
 * @param array  $cities
 *}


{component_define_params params=['oLocation']}

{capture 'location'}
{$options = Config::Get("plugin.ymaps.map.actions.{Router::GetAction()}.staticMap")}
{component 'ymaps:map.static' 
    classes="topic-location-map"
    options=$options
    oLocation=$oLocation}
    
{/capture}

{component 'block'
    title=$aLang.plugin.ymaps.field.label
    content=$smarty.capture.location
}