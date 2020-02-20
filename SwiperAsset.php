<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 18-02-2020.
 *
 * Swiper - Swiper widget for Yii 2.0 GridView or ListView
 */

namespace sjaakp\swiper;

use yii\web\AssetBundle;

/**
 * Class SwiperAsset
 * @package sjaakp\swiper
 */
class SwiperAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
    public $css = [
        'swiper.css'
    ];
    public $js = [
        '//ajax.googleapis.com/ajax/libs/hammerjs/2.0.8/hammer.min.js'
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG
    ];
}
