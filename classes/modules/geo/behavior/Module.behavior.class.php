<?php

class PluginYmaps_ModuleGeo_BehaviorModule extends Behavior
{
    
    /**
     * Дефолтные параметры
     *
     * @var array
     */
    protected $aParams = array(
        'target_type' => '',
    );
    /**
     * Список хуков
     *
     * @var array
     */
    protected $aHooks = array(
        /*'module_orm_GetItemsByFilter_after'  => array(
            'CallbackGetItemsByFilterAfter',
            1000
        ),*/
        'module_orm_GetItemsByFilter_before' => array(
            'CallbackGetItemsByFilterBefore',
            1000
        ),
        'module_orm_GetByFilter_before'      => array(
            'CallbackGetItemsByFilterBefore',
            1000
        ),
    );

    /**
     * Модифицирует фильтр в ORM запросе
     *
     * @param $aParams
     */
    public function CallbackGetItemsByFilterAfter($aParams)
    {
        $aEntities = $aParams['aEntities'];
        $aFilter = $aParams['aFilter'];
        $this->Category_RewriteGetItemsByFilter($aEntities, $aFilter, $this->getParam('target_type'));
    }

    /**
     * Модифицирует результат ORM запроса
     *
     * @param $aParams
     */
    public function CallbackGetItemsByFilterBefore($aParams)
    {
        $aFilter = $this->PluginYdirect_Geo_RewriteFilter($aParams['aFilter'], $aParams['sEntityFull'],
            $this->getParam('target_type'));
        $aParams['aFilter'] = $aFilter;
    }

    /**
     * Возвращает дерево категорий
     *
     * @return mixed
     */
    public function GetCategoriesTree()
    {
        return $this->Category_GetCategoriesTreeByTargetType($this->getParam('target_type'));
    }

    /**
     * Возвращает список ID объектов (элементов), которые принадлежат категории
     *
     * @param      $oCategory
     * @param      $iPage
     * @param      $iPerPage
     * @param bool $bIncludeChild
     *
     * @return mixed
     */
    public function GetTargetIdsByCategory($oCategory, $iPage, $iPerPage, $bIncludeChild = false)
    {
        return $this->Category_GetTargetIdsByCategory($oCategory, $this->getParam('target_type'), $iPage, $iPerPage,
            $bIncludeChild);
    }
}