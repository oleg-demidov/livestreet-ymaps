{**
 * Выбор местоположения
 *
 * @param string $targetType
 * @param object $place
 * @param array  $countries
 * @param array  $regions
 * @param array  $cities
 *}

{extends 'component@field.field'}


{block 'field_options' append}
    {component_define_params params=['oLocation']}

    {function name="params_to_query" params = []}{strip}
            {foreach name="params2q" from=$params key='key' item='val'}
                {$key}={$val}
                {if !$smarty.foreach.params2q.last}&{/if}
            {/foreach}
        {/strip}
    {/function}

    {$classes = "{$classes} js-ymaps-field-location"} 
    
    {$fieldName = Config::Get('plugin.ymaps.map.actions.{Router::GetAction()}.field_name')}
    
    {$optStatMap = Config::Get("plugin.ymaps.map.actions.{Router::GetAction()}.staticMap")}
    
    {$mapQuery = []}
    {$mapQuery.l = 'map'}
    {$mapQuery.size = "{$optStatMap.width},{$optStatMap.height}"}
    {$mapQuery.z = $optStatMap.zoom}
    {$mapQuery.ll = $optStatMap.ll}
    
    {if $oLocation}
        {$mapQuery.z = $oLocation->getZoom()}
        {$mapQuery.ll = "{$oLocation->getLat()},{$oLocation->getLong()}"}
        {$mapQuery.pt = "{$oLocation->getLat()},{$oLocation->getLong()},{""|join:$optStatMap.pt}"}
    {/if}
    
    {$src = "https://static-maps.yandex.ru/1.x/?{params_to_query params=$mapQuery}"}
    
{/block}

{block 'field_input'}
    
    <div class="static_map" href=".inline_map">
            <img src="{$src}" width="{$optStatMap.width}" height="{$optStatMap.height}">
    </div>
    <div id="map" class="inline_map" ></div>
    <div class="fields">
        {if $oLocation}
            <input type="hidden" name="{$fieldName}[lat]" value="{$oLocation->getLat()}" class="field-location">
            <input type="hidden" name="{$fieldName}[long]" value="{$oLocation->getLong()}" class="field-location">
            <input type="hidden" name="{$fieldName}[radius]" value="{$oLocation->getRadius()}" class="field-location">
            <input type="hidden" name="{$fieldName}[zoom]" value="{$oLocation->getZoom()}" class="field-location">
        {/if}
    </div>    

{/block}