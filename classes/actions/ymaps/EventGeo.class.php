<?php


/**
 * Description of EventTopic
 *
 * @author oleg
 */

class PluginYmaps_ActionYmaps_EventGeo extends Event {
    
    public function Init()
    {
         $this->Viewer_SetResponseAjax('json');
    }
    
    public function EventAjaxCountries()
    {
        /**
         * Получаем список регионов
         */
        if ($sTargetType = getRequestStr('target_type') and $this->Geo_IsAllowTargetType($sTargetType)) {
            $aCountries = $this->Geo_GetCountriesUsedByTargetType($oCountry->getId(), $sTargetType);
        } else {
            $aCountries = $this->Geo_GetCountries([], array('name_ru' => 'asc', 'name_en' => 'asc'), 1, 1000);
            $aCountries = $aCountries['collection'];
        }
        /**
         * Формируем ответ
         */
        $this->Viewer_AssignAjax('html', $this->getHtmlDropdownMenu($aCountries));
        
        
    }
    
    public function EventAjaxRegions()
    {
        /**
         * Находим страну
         */
        if($sCountryCode = getRequest('country_code')){
            $oCountry = $this->Geo_GetCountries(['code' =>$sCountryCode], [], 1, 1);
            if($oCountry['count']){
                $oCountry = array_shift($oCountry['collection']);
                $aFilter['country_id'] = $oCountry->getId();
            }
        }elseif($oCountry = $this->Geo_GetGeoObject('country', getRequest('id'))){
            $aFilter['country_id'] = $oCountry->getId();
        }
        
        if (!isset($aFilter['country_id'])) {
            return $this->EventErrorDebug();
        }
        
        /**
         * Получаем список регионов
         */
        if ($sTargetType = getRequestStr('target_type') and $this->Geo_IsAllowTargetType($sTargetType)) {
            $aRegions = $this->Geo_GetRegionsUsedByTargetType($oCountry->getId(), $sTargetType);
        } else {
            $aRegions = $this->Geo_GetRegions($aFilter, array('name_ru' => 'asc', 'name_en' => 'asc'), 1, 1000);
            $aRegions = $aRegions['collection'];
        }
        /**
         * Формируем ответ
         */
        $this->Viewer_AssignAjax('html', $this->getHtmlDropdownMenu($aRegions, $oCountry));
    }
    
    public function EventAjaxCities() {
        
        /**
         * Находим регион
         */
        if (!($oRegion = $this->Geo_GetGeoObject('region', getRequest('id')))) {
            
            return $this->EventErrorDebug();
        }
        /**
         * Получаем города
         */
        if ($sTargetType = getRequestStr('target_type') and $this->Geo_IsAllowTargetType($sTargetType)) {
            $aCities = $this->Geo_GetCitiesUsedByTargetType($oRegion->getId(), $sTargetType);
        } else {
            $aCities = $this->Geo_GetCities(array('region_id' => $oRegion->getId()), array('sort' => 'asc'), 1,
                1000);
            $aCities = $aCities['collection'];
        }
        /**
         * Формируем ответ
         */
        $this->Viewer_AssignAjax('html', $this->getHtmlDropdownMenu($aCities, $oRegion, false));
    }
    
    public function getHtmlDropdownMenu($oObjects, $oParent = null, $appendMenu = true) {
        $aReturn = [];
        
        $nameParent='';
        if($oParent){
            $nameParent = $oParent->getName();
            $type = strtolower( Engine::GetEntityName($oParent) );
            $aReturn = [
                [
                    'text' => "<b>".$this->Lang_Get('plugin.ymaps.field.'.$type, ['name'=>$oParent->getName()])."</b> ",
                    'attributes'   => [ 
                        'data-parent-name' => '',
                        'data-id' => $oParent->getId(),
                        'data-text' => $nameParent,
                        'data-type' => strtolower( Engine::GetEntityName($oParent) )
                    ]
                ],
                ['name' => '-']
            ];
        }
        
        foreach ($oObjects as $oObject) {
            $name = $oObject->getRegionName()?"<b>".$oObject->getName()."</b> ".$oObject->getRegionName():$oObject->getName();
            $appendArr = array(
                'attributes'   => [ 
                    'data-parent-name' => $nameParent,
                    'data-id' => $oObject->getId(),
                    'data-text' => $oObject->getName(),
                    'data-type' => strtolower( Engine::GetEntityName($oObject) )
                ],
                'text' => $name
            );
            if($appendMenu){
                $appendArr['menu'] = ['items' => [["text" => '&nbsp;', "classes" => 'ls-loading']]];
            }
            $aReturn[] = $appendArr;
        }
        
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('items', $aReturn, true);
        $oViewer->Assign('isSubnav', 1, true);
        return $oViewer->Fetch("component@dropdown.menu");
    }
    
    public function EventAjaxGeo() {
        
        $aFilter = [];
        if($sCountryCode = Config::Get('plugin.ymaps.geo.country_code')){
            $oCountry = $this->Geo_GetCountries(['code' => strtoupper($sCountryCode)], [], 1, 1);
            if($oCountry['count']){
                $aFilter['country_id'] = $oCountry['collection'][0]->getId();
            }            
        }
        
        $sLang = $this->Lang_GetLang();
        if($sLang != 'ru'){
            $sLang = 'en';
        }
        
        $aFilter['name_'.$sLang.'_like'] = '%'.getRequest('query', '').'%';
        
        $aCities = $this->Geo_GetCities($aFilter, array('name_'.$sLang => 'asc'), 1, Config::Get('plugin.ymaps.geo.results'));
        
        foreach($aCities['collection'] as &$mCity){
            if($oRegion = $this->Geo_GetRegionById($mCity->getRegionId())){
                $mCity->setRegionName($oRegion->_getDataOne('name_'.$sLang));
            }            
        }
        $aCities = $aCities['collection'];
        
        $this->Viewer_AssignAjax('lang', $this->Lang_GetLang());
        $this->Viewer_AssignAjax('html', $this->getHtmlDropdownMenu($aCities, null, false));
    }

    
}