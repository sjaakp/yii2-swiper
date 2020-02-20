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

/**
 * Class PrevNextBehavior
 * @package sjaakp\swiper
 */
class PrevNextBehavior extends Behavior
{
    const SORT_DIFF = SORT_DESC ^ SORT_ASC;

    /**
     * @var string name of attribute along which records are selected
     */
    public $attribute;

    /**
     * @var int SORT_ASC | SORT_DESC sort direction
     */
    public $sort = SORT_ASC;

    /**
     * @return \yii\db\ActiveRecord|null previous record
     */
    public function getPrev()   {
        return $this->pnQuery($this->sort ^ self::SORT_DIFF);
    }

    /**
     * @return \yii\db\ActiveRecord|null next record
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
        $attrVal = $owner->getAttribute($this->attribute);
        return $owner->find()->where([$operator, $this->attribute, $attrVal])
            ->orWhere([ 'and', [ $this->attribute => $attrVal ], [ $operator, $pkName, $owner->primaryKey ] ])
            ->orderBy([$this->attribute => $sort, $pkName => $sort])->limit(1)->one();
    }
}
