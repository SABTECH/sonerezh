<?php

App::uses('AppModel', 'Model');

class Album extends AppModel
{
    private $bandId;
    public $belongsTo = array('Band');
    public $hasMany = array('Track');
    public $validate = array(
        'name' => array(
            'rule' => array('maxLength', 255),
            'required' => 'create',
            'allowEmpty' => false,
            'message' => 'Album\'s name cannot be empty or exceed 255 characters.'
        ),
        'cover' => array(
            'rule' => array('maxLength', 37),
            'allowEmpty' => true,
            'message' => 'Album\'s cover cannot exceed 37 characters.'
        ),
        'year' => array(
            'rule' => array('date', 'y'),
            'allowEmpty' => true,
            'message' => 'Invalid album year.'
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

    public function beforeDelete($cascade = false)
    {
        $album = $this->find('first', array(
            'fields' => array('band_id'),
            'conditions' => array('id' => $this->id)
        ));

        if (isset($album)) {
            $this->bandId = $album[$this->alias]['band_id'];
        }
        return true;
    }

    public function afterDelete()
    {
        if (!empty($this->bandId)) {
            $neighbours = $this->find('count', array(
                'conditions' => array('band_id' => $this->bandId)
            ));

            if ($neighbours == 0) {
                $band = ClassRegistry::init('Band');
                $band->delete($this->bandId);
            }
        }
        return true;
    }
}