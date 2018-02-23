<?php

class PluginYmaps_ModuleGeo_BehaviorEntity extends Behavior
{
    /**
     * Дефолтные параметры
     *
     * @var array
     */
    protected $aParams = array(
        'target_type'                    => '',
        'form_field'                     => 'ymap',
        'validate_from_request' => true,
        'multiple'                       => false,
        'validate_enable'                => true,
        'validate_field'                 => 'ymap',
        'validate_min'                   => 1,
        'validate_max'                   => 5,
    );
    /**
     * Список хуков
     *
     * @var array
     */
    protected $aHooks = array(
        'validate_after' => 'CallbackValidateAfter',
        'after_save'     => 'CallbackAfterSave',
        'after_delete'   => 'CallbackAfterDelete',
    );

    /**
     * Инициализация
     */
    protected function Init()
    {
        parent::Init();
    }

    /**
     * Коллбэк
     * Выполняется при инициализации сущности
     *
     * @param $aParams
     */
    public function CallbackValidateAfter($aParams)
    {
       
       if ($aParams['bResult'] and $this->getParam('validate_enable')) {
            $aFields = $aParams['aFields'];    
            if (is_null($aFields) or in_array($this->getParam('validate_field'), $aFields)) {
                $oValidator = $this->Validate_CreateValidator('ygeo_check', $this,
                    $this->getParam('validate_field'));           
                $oValidator->validateEntity($this->oObject, $aFields);
                $aParams['bResult'] = !$this->oObject->_hasValidateErrors();
            }
        }
    }
    
    public function ValidateYgeoCheck($mValue) {
        
        if ($this->getParam('validate_from_request')) {
            $mValue = getRequest($this->getParam('form_field'));
        }
        
        /**
         * Значение может  массивом
         */
        if (!is_array($mValue)) {
            return $this->Lang_Get('plugin.ymaps.notices.validate_require');
        }
        /**
         * Значение должно быть полным Сохраняем в БД
         */
        
        $iTargetId = $this->getPrimaryKeyValueFromEntity($this->oObject);
        
        if(!$oGeo = $this->PluginYmaps_Geo_GetGeoByFilter(['target_id' => $iTargetId,
                'target_type' => $this->getParam('target_type') ])){
            $oGeo = Engine::GetEntity('PluginYmaps_Geo_Geo');
            $oGeo->setTargetId($iTargetId);
            $oGeo->setTargetType($this->getParam('target_type'));
        }
        
        
        $oGeo->_setData($mValue);
            
        
        if(!$oGeo->_Validate()){
            return $this->Lang_Get('plugin.ymaps.notices.validate_require').'. '.$oGeo->_getValidateError();
        }      
        
        $this->oObject->_setData(array('_ygeo_for_save' => $oGeo));
        return true;
    }
    
    public function getPrimaryKeyValueFromEntity($oEntity) {
        if($value = $oEntity->_getPrimaryKeyValue()){
            return $value;
        }
        $sKey = $oEntity->_getPrimaryKey();
        return call_user_func(array($oEntity, 'get'. ucfirst($sKey)));
    }

    /**
     * Коллбэк
     * Выполняется после сохранения сущности
     */
    public function CallbackAfterSave()
    {
        if(!$oGeo = $this->oObject->_getDataOne('_ygeo_for_save')){
            return false;
        }        
        $oGeo->Save();
    }

    /**
     * Коллбэк
     * Выполняется после удаления сущности
     */
    public function CallbackAfterDelete()
    {
        $oGeo = $this->getGeo();
        if($oGeo){
            $oGeo->Delete();
        }
    }

    public function getGeo($aFilter = ['#index-from-primary'])
    {
        return $this->PluginYmaps_Geo_GetGeoByFilter(array_merge([
            'target_type' => $this->getParam('target_type'),
            'target_id' => $this->getPrimaryKeyValueFromEntity($this->oObject),
            
        ], $aFilter));
    }
    
}