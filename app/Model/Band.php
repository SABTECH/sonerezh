<?php

App::uses('AppModel', 'Model');

class Band extends AppModel
{
    public $hasMany = array('Album');
    public $validate = array(
        'name' => array(
            'rule' => array('maxLength', 255),
            'required' => 'create',
            'allowEmpty' => false,
            'message' => 'The band\'s name cannot be empty or exceed 255 characters.'
        )
    );

    public function beforeValidate($options = array())
    {
        if (isset($this->data[$this->alias]['id'])) {
            $this->validator()->getField('name')->getRule(0)->allowEmpty = true;
        }
    }

    public function beforeSave($options = array())
    {
        if (isset($this->data[$this->alias]['id'])) {
            $this->data[$this->alias]['updated'] = date('Y-m-d H:i:s');
        } else {
            unset($this->data[$this->alias]['updated']);
        }
        unset($this->data[$this->alias]['created']);
        return true;
    }
}