<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 18-02-2020.
 *
 * Swiper - Swiper widget for Yii 2.0 GridView or ListView
 */

namespace sjaakp\swiper;

use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Swiper
 * @package sjaakp\swiper
 */
class Swiper extends Widget
{
    /**
     * @var BaseActiveRecord | null
     */
    public $model;

    /**
     * @var BaseActiveRecord | null
     */
    public $previous;

    /**
     * @var BaseActiveRecord | null
     */
    public $next;

    /**
     * @var string (relative) URL base for links
     */
    public $url = 'view';

    /**
     * @var string name of attribute delivering label
     * Must be set
     */
    public $labelAttribute;

    /**
     * @var string | null name of attribute delivering short label
     * Optional
     */
    public $shortLabelAttribute;

    /**
     * @var string Bootstrap breakpoint below which short label is used
     */
    public $breakpoint = 'sm';

    /**
     * @var string | array | null URL for center button
     * If null: no center button
     */
    public $indexUrl;

    /**
     * @var string label for optional center button
     */
    public $indexLabel = 'Index';

    /**
     * @var string | null ID of HTML swipe element
     * If null: <body>
     */
    public $swipeId;

    /**
     * @var array HTML options for the Swiper.
     */
    public $options = [];

    /**
     * @var bool whether to register link tags for previous and next
     */
    public $registerLinkTags = false;

    /**
     * @inheritDoc
     * @throws  InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (is_null($this->labelAttribute))   {
            throw new InvalidConfigException('Swiper: property "labelAttribute" is not set.');
        }
        if ($this->model)   {
            if (! ($this->model->hasProperty('prev') && $this->model->hasProperty('next')))    {
                throw new InvalidConfigException(get_class($this->model) . ' doesn\'t have PrevNextBehavior.' );
            }
            $this->previous = $this->model->prev;
            $this->next = $this->model->next;
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    public function run()
    {
        $view = $this->getView();
        SwiperAsset::register($view);

        $id = $this->getId();
        $elmt = $this->swipeId ? "getElementById('$this->swipeId')" : 'body';

        $view->registerJs("function swipe(c){var lnk=document.getElementById('$id-'+c);if(lnk){location=lnk.href;}}
document.addEventListener('keydown',function(e){if(e.key=='ArrowLeft'){swipe('swiperight');}if(e.key=='ArrowRight'){swipe('swipeleft');}});
var $id= new Hammer(document.$elmt,{recognizers:[[Hammer.Swipe,{direction: Hammer.DIRECTION_HORIZONTAL}]]});
$id.on('swipeleft swiperight',function(ev){swipe(ev.type);});", $view::POS_READY, 'sjaakp\swiper');

        Html::addCssClass($this->options, 'swiper');
        $buttons = [ $this->renderButton($this->previous, $this->id . '-swiperight', 'prev') ];
        if (! is_null($this->indexUrl)) {
            Html::addCssClass($this->options, 'swiper-3');
            $buttons[] = Html::a(Html::tag('span', $this->indexLabel), $this->indexUrl);
        }
        $buttons[] = $this->renderButton($this->next, $this->id . '-swipeleft', 'next');
        return Html::tag('nav', implode("\n", $buttons), $this->options);
    }

    /**
     * @param $model BaseActiveRecord
     * @return string
     */
    protected function renderButton($model, $id, $rel)
    {
        if (! $model) return '<div class="swiper-empty"></div>';

        $url = [$this->url];
        $pkName = $model->primaryKey()[0];
        $url[$pkName] = $model->primaryKey;
        if ($this->registerLinkTags)    {
            $this->getView()->registerLinkTag([ 'rel' => $rel, 'href' => Url::to($url) ]);
        }

        $url['#'] = $this->id;
        $title = $model->{$this->labelAttribute};
        $text = $this->shortLabelAttribute
            ? (Html::tag('span', $model->{$this->shortLabelAttribute}, [ 'class' => "d-inline-block d-$this->breakpoint-none" ])
                . Html::tag('span', $title, [ 'class' => "d-none d-$this->breakpoint-inline-block" ]))
            : Html::tag('span', $title);

        return Html::a($text, $url, [ 'id' => $id, 'title' => $title ]);
    }
}
