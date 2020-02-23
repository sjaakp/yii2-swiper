<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 18-02-2020.
 *
 * Swiper - Swiper widget for Yii 2.0 GridView or ListView
 */

namespace sjaakp\swiper;

use yii\base\Behavior;
use yii\base\InvalidConfigException;

/**
 * Class PrevNextBehavior
 * @package sjaakp\swiper
 */
class PrevNextBehavior extends Behavior
{
    const SORT_DIFF = SORT_DESC ^ SORT_ASC;

    /**
     * @var string|array
     * If string: name of attribute along which records are selected
     * If [ $attributeName => function($model) ]: function returning attribute value
     */
    public $attribute;

    /**
     * @var int SORT_ASC | SORT_DESC sort direction
     */
    public $sort = SORT_ASC;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (is_null($this->attribute))   {
            throw new InvalidConfigException('PrevNextBehavior: property "attribute" is not set.');
        }
    }

    /**
     * @return \yii\db\ActiveQuery previous record
     */
    public function getPrev()   {
        return $this->pnQuery($this->sort ^ self::SORT_DIFF);
    }

    /**
     * @return \yii\db\ActiveQuery next record
     */
    public function getNext()   {
        return $this->pnQuery($this->sort);
    }

    protected function pnQuery($sort)
    {
        /* @var $owner \yii\db\ActiveRecord */
        $owner = $this->owner;
        $pkName = $owner->primaryKey()[0];
        $operator = $sort == SORT_DESC ? '<' : '>';
        return $owner->find()->andWhere([$operator, $this->attrName(), $this->attrVal()])
            ->orWhere([ 'and', [ $this->attrName() => $this->attrVal() ], [ $operator, $pkName, $owner->primaryKey ] ])
            ->orderBy([$this->attrName() => $sort, $pkName => $sort])->limit(1);
    }

    private $_attrName;
    private $_attrVal;

    protected function attrName()
    {
        if (is_null($this->_attrName))   {
            if (is_array($this->attribute)) {
                $this->_attrName = key($this->attribute);
            }
            else    {
                $this->_attrName = $this->attribute;
            }
        }
        return $this->_attrName;
    }

    protected function attrVal()
    {
        if (is_null($this->_attrVal))   {
            $owner = $this->owner;
            if (is_array($this->attribute)) {
                $fn = reset($this->attribute);
                $this->_attrVal = call_user_func($fn, $owner);
            }
            else    {
                $this->_attrVal = $owner->getAttribute($this->attribute);
            }
        }
        return $this->_attrVal;
    }
}
