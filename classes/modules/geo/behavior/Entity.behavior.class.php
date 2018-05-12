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
        'form_field'                     => 'location',
        'validate_from_request' => false,
        'multiple'                       => false,
        'validate_enable'                => false,
        'validate_field'                 => 'location',
        'validate_guest'                 => false,
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
        
        if ( $this->getParam('validate_enable')) {
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
        
        $oGeo = Engine::GetEntity('PluginYmaps_Geo_Geo', $mValue);
        
        if(ModuleRbac::ROLE_CODE_GUEST == $this->getPrimaryKeyValue() or (!$oGeo = $this->PluginYmaps_Geo_GetGeoByFilter([
                'target_id' => $this->getPrimaryKeyValue(),
                'target_type' => $this->getParam('target_type') ]))){
            $oGeo = Engine::GetEntity('PluginYmaps_Geo_Geo');
            $oGeo->setTargetId($this->getPrimaryKeyValue());
            $oGeo->setTargetType($this->getParam('target_type'));
        }
        $oGeo->_setData($mValue);
            
        if(!$this->getParam('validate_guest')){
            $oGeo->_setValidateScenario('target_id');
        }
            
        if(!$oGeo->_Validate()){
            return $this->Lang_Get('plugin.ymaps.notices.validate_require').'. '.$oGeo->_getValidateError();
        }     
        
        $this->oObject->_setData(array('_location_for_save' => $oGeo));
        return true;
    }

    /**
     * Коллбэк
     * Выполняется после сохранения сущности
     */
    public function CallbackAfterSave()
    {
        if(!$oGeo = $this->oObject->_getDataOne('_location_for_save')){
            return false;
        }
//        $oGeo->setTargetId($this->getPrimaryKeyValue());
        $oGeo->Save();
    }
    
    public function getPrimaryKeyValue() {
        if($value = $this->oObject->_getPrimaryKeyValue()){
            return $value;
        }
        if($value = $this->oObject->getId()){
            return $value;
        }
        return null;
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
            'target_id' => $this->oObject->_getPrimaryKeyValue(),
            
        ], $aFilter));
    }
    
}