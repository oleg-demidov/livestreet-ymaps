{$component = 'ymaps-modal-map'}
{component_define_params params=[ 'oGeo', 'url', 'icon', 'text',  'mods', 'classes', 'attributes' ]}

{$url = {$url|default:'#'}}

{if $oGeo}
    {$attributes['data-lat'] =  $oGeo->getLat()}
    {$attributes['data-long'] =  $oGeo->getLong()}
    {$attributes['data-zoom'] =  $oGeo->getZoom()}
{/if}

<a  href="{$url}" 
    class="{$component} {cmods name=$component mods=$mods} {$classes} ymaps-modal-toggle" 
    data-lsmodaltoggle-modal="ymaps_modal" 
    {cattr list=$attributes}>
        {component 'icon' icon={$icon|default:'map-marker'}} 
        {$text}</a>

{component 'modal'
    title='Местоположение'
    showFooter=false
    content='<i class="fa fa-spinner fa-spin fa-fw fa-3x  "></i>'
    id='ymaps_modal'
    classes='js-ymaps-modal'}