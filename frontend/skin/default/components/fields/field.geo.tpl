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
    {component_define_params params=['choosenGeo', 'place', 'classes', 'countries', 'regions', 'cities', 'targetType', 'oLocation',
'dafaultValue', 'dafaultText']}

    
    {if $targetType}
        {$attributes = array_merge( $attributes|default:[], [ 'data-type' => $targetType ] )}
    {/if}
    
    {$sInputVal = ''}
    
    {$countryCode = {Config::Get('plugin.ymaps.geo.country_code')|strtoupper}}
    
    {if $cities and $place and $place->getCityId()}
        {foreach $cities as $city}
            {if $city->getId() == $place->getCityId()}
                {if !$choosenGeo}
                    {$choosenGeo = $city}
                {/if}
                {$sInputVal = $city->getName()}
            {/if}
        {/foreach}
    {/if}
    
    {if  $regions and $place and $place->getRegionId()}
        {foreach $regions as $region} 
            {if $region->getId() == $place->getRegionId()}
                {if !$choosenGeo}
                    {$choosenGeo = $region}
                {/if}
                {$sInputVal = "{if $sInputVal}{$sInputVal}, {/if}{$region->getName()}"}                
            {/if}
        {/foreach}
    {/if}
    
    {if  $countries and $place and $place->getCountryId()}
        {foreach $countries as $country}
            {if $country->getId() == $place->getCountryId()}
                {if !$choosenGeo}
                    {$choosenGeo = $country}
                {/if}
                {$sInputVal = "{if $sInputVal}{$sInputVal}, {/if}{$country->getName()}"}   
            {/if}
        {/foreach}        
    {/if}
    
    {if $place }
        {$idCountry = $place->getCountryId()}
        {$idRegion = $place->getRegionId()}
        {$idCity = $place->getCityId()}
    {/if}
    
    {$mods = "$mods geo"}
    
    {$showDropdown = Config::Get('plugin.ymaps.geo.showDropdown')}
    
    {$classes = "$classes ymaps-ajax-geo"}
    
{/block}

{block 'field_input'}
    
    <div class="{$component}-icon">{component 'icon' icon="map-marker"}</div>
    <div class="input-close-but">X</div>
    <input type="text" class="{$component}-input {$classes} {$component}-geo js-field-geo" 
           placeholder="{lang 'plugin.ymaps.field.defaultText'}"
           value="{$sInputVal}" data-show-dropdown="{$showDropdown}" autocomplete="off" >
    
    {*if $choosenGeo}
        <input type="hidden" class="appended-geo" value="{$choosenGeo->getId()}" name="{$name|default:'geo'}[{$choosenGeo->getType()}]">
        <input type="hidden" class="appended-geo" value="{$choosenGeo->getId()}" name="{$choosenGeo->getType()}">
    {/if*}
    
    
    <input type="hidden" class="js-field-geo-country" value="{$idCountry}" name="{$name|default:'geo'}[country]">
    <input type="hidden" class="js-field-geo-region" value="{$idRegion}" name="{$name|default:'geo'}[region]">
    <input type="hidden" class="js-field-geo-city" value="{$idCity}" name="{$name|default:'geo'}[city]"> 
    
    
    {$aItems = [["text" => {lang 'plugin.ymaps.loading'}, "classes" => 'ls-loading']]} 
    
    {component 'dropdown.menu' text='Выбрать' items=$aItems isSubnav=true}

    {hook run='field_geo_end' target=$place oLocation=$oLocation}
{/block}