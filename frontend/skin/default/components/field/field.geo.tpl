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
    {component_define_params params=[ 'place', 'classes', 'countries', 'regions', 'cities', 'targetType', 'dafaultValue', 'dafaultText']}

    {if $targetType}
        {$attributes = array_merge( $attributes|default:[], [ 'data-type' => $targetType ] )}
    {/if}
   
    {$find = 0}
    
    {$countryCode = {Config::Get('plugin.ymaps.geo.country_code')|strtoupper}}
    
    {if $cities and $place and $place->getCityId()}
        {foreach $cities as $city}
            {if $city->getId() == $place->getCityId()}
                {$find = 1}
                {$choosenGeo = $city}
            {/if}
        {/foreach}
    {/if}
    
    {if !$find and $regions and $place and $place->getRegionId()}
        {foreach $regions as $region}
            {if $region->getId() == $place->getRegionId()}
                {$find = 1}
                {$choosenGeo = $region}
            {/if}
        {/foreach}
    {/if}
    
    {if !$find and $countries and $place and $place->getCountryId()}
        {foreach $countries as $country}
            {if $country->getId() == $place->getCountryId()}
                {$find = 1}
                {$choosenGeo = $country}
            {else}
                {if $countryCode == $country->getCode()}
                    {$choosenGeo = $country->getName()}
                {/if}
            {/if}
        {/foreach}
        
    {/if}
    
    {$chooseName = {lang 'plugin.ymaps.field.defaultText'}}
    {$default = 1}
    
    {if $choosenGeo}
        {$default = 0}
        {$chooseName = $choosenGeo->getName()}
        {$id = $choosenGeo->getId()}
        {$type = $choosenGeo->getId()}
    {/if}
    
    {if $default}
        {$classes = "{$classes} default-text"}
    {/if}
    {$mods = "$mods geo"}
    
    
{/block}

{block 'field_input'}
    
    <div class="{$component}-icon">{component 'icon' icon="map-marker"}</div>
    <div class="input-close-but">X</div>
    <input type="text" class="{$component}-input {$classes} {$component}-geo js-field-geo" value="{$chooseName}" name="geo_text">
    
    {if $id}
        <input type="hidden" class="appended-geo" value="{$id}" name="geo[{$type}]">
    {/if}
    
    {$aItems = [["text" => {lang 'plugin.ymaps.loading'}, "classes" => 'ls-loading']]} 
    
    {component 'dropdown.menu' text='Выбрать' items=$aItems isSubnav=true}

{/block}