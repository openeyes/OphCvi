<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "ophcocvi_clinicinfo_disorder_section".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property string $name
 * @property boolean $comments_allowed
 * @property string $comments_label
 * @property integer $display_order
 * @propety boolean $active
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */

class OphCoCvi_ClinicalInfo_Disorder_Section extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcocvi_clinicinfo_disorder_section';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'safe'),
            array('name', 'required'),
            array('id, name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'disorders' => array(self::HAS_MANY, 'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder', 'section_id', 'on' => 'active = 1', 'order' => 'display_order asc')
        );
    }

    /**
     * Add Lookup behaviour
     *
     * @return array
     */
    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * always order by display_order
     *
     * @return array
     */
    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order asc');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'active' => 'Active'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns all the active disorder sections
     */
    public function getAllDisorderSections($element) {
        $disorder_sections = OphCoCvi_ClinicalInfo_Disorder_Section::model()
            ->findAll('`active` = ?',array(1));
        $disorder_sections_with_comment = array();
        $i = 0;
        foreach($disorder_sections as $disorder_section) {
            $comments = Element_OphCoCvi_ClinicalInfo_Disorder_Section_Comments::model()
                ->getDisorderSectionComments($disorder_section->id, $element->id);
            $disorder_sections_with_comment[$i]['comment'] = $comments;
            $disorder_sections_with_comment[$i]['disorder'] = $disorder_section;
            $i++;
        }
        return($disorder_sections_with_comment);
    }
}
