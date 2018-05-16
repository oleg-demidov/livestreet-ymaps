<?php

class PluginYmaps_ActionYmaps extends ActionPlugin
{

    
    
    public function Init()
    {        
        $this->SetDefaultEvent('index');  
        $this->oUserCurrent = $this->User_GetUserCurrent();        
        
    }


    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^ajax-search-users$/i','EventAjaxSearchUsers');
        $this->AddEventPreg('/^ajax-search-ads/i','EventAjaxSearchAds');
        
        $this->RegisterEventExternal('Geo','PluginYmaps_ActionYmaps_EventGeo');
        $this->AddEventPreg('/^ajax-geo$/i','Geo::EventAjaxGeo');
        $this->AddEventPreg('/^ajax-cities$/i','Geo::EventAjaxCities');
        $this->AddEventPreg('/^ajax-regions$/i','Geo::EventAjaxRegions');
        $this->AddEventPreg('/^ajax-countries/i','Geo::EventAjaxCountries');
              
    }
    
    
    
    public function EventAjaxSearchUsers()
    {
         /**
         * Устанавливаем формат Ajax ответа
         */
        $this->Viewer_SetResponseAjax('json');
        $this->SetTemplate(false);
        /**
         * Формируем фильтр
         */
        $aFilter = array(
            'activate' => 1
        );
        $sOrderWay = in_array(getRequestStr('order'), array('desc', 'asc')) ? getRequestStr('order') : 'desc';
        $sOrderField = in_array(getRequestStr('sort_by'), array(
            'user_rating',
            'user_date_register',
            'user_login',
            'user_profile_name'
        )) ? getRequestStr('sort_by') : 'user_rating';
        if (is_numeric(getRequestStr('next_page')) and getRequestStr('next_page') > 0) {
            $iPage = getRequestStr('next_page');
        } else {
            $iPage = 1;
        }
        /**
         * Получаем из реквеста первые буквы для поиска пользователей по логину
         */
        $sTitle = getRequest('sText');
        if (is_string($sTitle) and mb_strlen($sTitle, 'utf-8')) {
            $sTitle = str_replace(array('_', '%'), array('\_', '\%'), $sTitle);
        } else {
            $sTitle = '';
        }
        /**
         * Как именно искать: совпадение в любой части логина, или только начало или конец логина
         */
        if ($sTitle) {
            if (getRequest('isPrefix')) {
                $sTitle .= '%';
            } elseif (getRequest('isPostfix')) {
                $sTitle = '%' . $sTitle;
            } else {
                $sTitle = '%' . $sTitle . '%';
            }
        }
        if ($sTitle) {
            $aFilter['name'] = $sTitle;
        }
        /**
         * Пол
         */
        if (in_array(getRequestStr('sex'), array('man', 'woman', 'other'))) {
            $aFilter['profile_sex'] = getRequestStr('sex');
        }
        /**
         * Онлайн
         * date_last
         */
        if (getRequest('is_online')) {
            $aFilter['date_last_more'] = date('Y-m-d H:i:s', time() - Config::Get('module.user.time_onlive'));
        }
        /**
         * Geo привязка
         */
        if (getRequestStr('city')) {
            $aFilter['geo_city'] = getRequestStr('city');
        } elseif (getRequestStr('region')) {
            $aFilter['geo_region'] = getRequestStr('region');
        } elseif (getRequestStr('country')) {
            $aFilter['geo_country'] = getRequestStr('country');
        }
        /**
         * Ищем пользователей
         */
        //TODO: Подгрузка порциями
        $aResult = $this->User_GetUsersByFilter($aFilter, array($sOrderField => $sOrderWay), 1,
            50000);
        $bHideMore = $iPage * Config::Get('module.user.per_page') >= $aResult['count'];
        /**
         * Формируем ответ
         */       
        
       
        $aUsers = $this->PluginYmaps_Geo_GetUsersWithLocation($aResult['collection']);
        
        /**
         * Для подгрузки
         */
        $this->Viewer_AssignAjax('objects', $aUsers);
        $this->Viewer_AssignAjax('count_loaded', count($aResult['collection']));
        $this->Viewer_AssignAjax('next_page', 2);
        $this->Viewer_AssignAjax('hide', 1);
        $this->Viewer_AssignAjax('searchCount', (int)$aResult['count']);
        $this->Viewer_AssignAjax('count_left', 1111);
        $this->Viewer_AssignAjax('textEmpty', $this->Lang_Get('search.alerts.empty'));
    }
    
    public function EventAjaxSearchAds()
    {
         /**
         * Устанавливаем формат Ajax ответа
         */
        $this->Viewer_SetResponseAjax('json');
        $this->SetTemplate(false);
        /**
         * Формируем фильтр
         */
        $aFilter = [
            '#select' => ['topic_id'],
            '#with'   => ['property'],
            '#page'   => [1, 10000]
        ];
        
        function _getCategoriesByUrlFull($sUrl) {
            if(!$sUrl){
                return false;
            }
            $aUrls = explode('/', trim($sUrl));
            $aUrlsField = [];
            foreach($aUrls as $sUrl){
                $aUrlsField[] = "'".$sUrl."'";
            }
            $aCategories = $this->Category_GetCategoryItemsByFilter([
                '#index-from' => 'id',
                'url in' => $aUrls,
                '#select' => ['id', 'title', 'url_full', 'url'],
                '#order' => ['field:url' => $aUrlsField]
            ]);  
            return $aCategories;        
        }

        if(getRequest('category_url_full')){
            $aCategories = _getCategoriesByUrlFull(getRequest('category_url_full'));
            if(sizeof($aCategories)){
                $aFilter['categories'] = $aCategories;
            }
        }elseif(getRequest('categories') ){
            $aCategories = $this->Category_GetCategoryItemsByFilter([
                '#index-from' => 'id',
                'id in' => getRequest('categories'),
                '#select' => [ 'url_full', 'id', 'title']
            ]);
            $aFilter['categories'] = $aCategories;
        }        
        
        if($oGeo = $this->_getGeoByRequest()){
            $aFilter['geo_object'] = $oGeo;
        }
        
        
        if($sOrderField == 'prop:price'){
            $aFilter['#prop:price !='] = -1;
        }
        
        if(($iPriceFrom = getRequest('price_from')) !== ''){
            $aFilter['#prop:price >'] = $iPriceFrom;
        }
        if(($iPriceTo = getRequest('price_to')) !== ''){
            $aFilter['#prop:price <'] = $iPriceTo;
        }
        
        $aBreadcrumbsHTML = null;
        if( isset($aFilter['categories']) ){
            $aBreadcrumbsHTML = $this->getBreadcrumbsHTML( $aFilter['categories'] );
        }
        
        $aAds = $this->Topic_GetAdsByFilter($aFilter);
        /**
         * Для подгрузки
         */
        //TODO: Сделать подгрузку долями
        $this->Viewer_AssignAjax('objects', $aAds['collection']);
        $this->Viewer_AssignAjax('count_loaded', $aAds['count']);
        $this->Viewer_AssignAjax('next_page', 2);
        $this->Viewer_AssignAjax('hide', 1);
        $this->Viewer_AssignAjax('searchCount', (int)$aResult['count']);
        $this->Viewer_AssignAjax('count_left', 1111);
        $this->Viewer_AssignAjax('textEmpty', $this->Lang_Get('search.alerts.empty'));
    }
}