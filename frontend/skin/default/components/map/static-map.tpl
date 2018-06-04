{**
 * Выбор местоположения
 *
 * @param string $targetType
 * @param object $place
 * @param array  $countries
 * @param array  $regions
 * @param array  $cities
 *}


{component_define_params params=['oLocation', 'classes', 'attributes', 'options']}

{function name="params_to_query" params = []}{strip}
        {foreach name="params2q" from=$params key='key' item='val'}
            {$key}={$val}
            {if !$smarty.foreach.params2q.last}&{/if}
        {/foreach}
    {/strip}
{/function}



{$mapQuery = []}
{$mapQuery.l = 'map'}
{$mapQuery.size = "{$options.width},{$options.height}"}
{$mapQuery.z = $options.zoom}
{$mapQuery.ll = $options.ll}

{if $oLocation}
    {$mapQuery.z = $oLocation->getZoom()}
    {$mapQuery.ll = "{$oLocation->getLat()},{$oLocation->getLong()}"}
    {$mapQuery.pt = "{$oLocation->getLat()},{$oLocation->getLong()},{""|join:$options.pt}"}
{/if}

{$src = "https://static-maps.yandex.ru/1.x/?{params_to_query params=$mapQuery}"}

<img class="{$classes}" {cattr list=$attributes} src="{$src}">
