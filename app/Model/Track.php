<?php

App::uses('AppModel', 'Model');

class Track extends AppModel
{
    private $albumId;
    public $belongsTo = array('Album');
    public $validate = array(
        'title' => array(
            'rule' => array('maxLength', 255),
            'message' => 'The title of a track cannot exceed 255 characters.'
        ),
        'source_path' => array(
            'rule' => array('maxLength', 4096),
            'allowEmpty' => false,
            'required' => 'create',
            'message' => 'The source path of a file cannot be empty or exceed 4096 characters.'
        ),
        'playtime' => array(
            'rule' => array('maxLength', 9),
            'message' => 'The playtime cannot exceed 9 characters.'
        ),
        'track_number' => array(
            'rule' => array('naturalNumber', true),
            'message' => 'The track number must be a natural integer.'
        ),
        'max_track_number' => array(
            'rule' => array('naturalNumber', true),
            'message' => 'The max track number must be a natural integer.'
        ),
        'disc_number' => array(
            'rule' => array('naturalNumber', true),
            'message' => 'The disc number must be a natural integer.'
        ),
        'max_disc_number' => array(
            'rule' => array('naturalNumber', true),
            'message' => 'The max disc number must be a natural integer.'
        ),
        'year' => array(
            'rule' => array('date', 'y'),
            'allowEmpty' => true,
            'message' => 'Invalid track year.'

        ),
        'genre' => array(
            'rule' => array('maxLength', 255),
            'message' => 'The genre of a track cannot exceed 255 characters.'
        ),
        'artist' => array(
            'rule' => array('maxLength', 255),
            'message' => 'The artist value cannot exceed 255 characters.'
        )
    );

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
        $track = $this->find('first', array(
            'fields' => array('album_id'),
            'conditions' => array('id' => $this->id)
        ));

        if (isset($track)) {
            $this->albumId = $track[$this->alias]['album_id'];
        }
        return true;
    }

    public function afterDelete()
    {
        if (!empty($this->albumId)) {
            $neighbours = $this->find('count', array(
                'conditions' => array('album_id' => $this->albumId)
            ));

            if ($neighbours == 0) {
                $album = ClassRegistry::init('Album');
                $album->delete($this->albumId);
            }
        }
        return true;
    }
}