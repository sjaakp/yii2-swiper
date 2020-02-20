Yii2-swiper
===========
#### Swiper widget for Yii2 ####

[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-swiper/v/stable)](https://packagist.org/packages/sjaakp/yii2-swiper)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-swiper/downloads)](https://packagist.org/packages/sjaakp/yii2-swiper)
[![License](https://poser.pugx.org/sjaakp/yii2-swiper/license)](https://packagist.org/packages/sjaakp/yii2-swiper)

This is a widget and associated behavior for [ActiveRecords](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord) 
in the [Yii 2.0](https://yiiframework.com/ "Yii") PHP Framework. It allows the user
to jump to neighbouring records by means of clicking, touch swiping, or using 
the left and right arrow keys.

**Yii2-swiper** assumes that your site is using a [Font Awesome](https://fontawesome.com/) 
icon font (v3 or higher).
[Bootstrap 4](https://getbootstrap.com/) is recommended.

A demonstration of **yii2-swiper** is [here](https://sjaakpriester.nl/software/swiper).

## Installation ##

The preferred way to install **yii2-swiper** is through [Composer](https://getcomposer.org/). 
Either add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-swiper": "*"` 

Or run:

`composer require sjaakp/yii2-swiper "*"` 

You can manually install **yii2-swiper** by
 [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-swiper/archive/master.zip).

## Using Yii2-swiper ##

**Yii2-swiper** consists of two classes: **PrevNextBehavior** and **Swiper** 
in namespace `sjaakp\swiper`.

#### PrevNextBehavior ####

This is a [`Behavior`](https://www.yiiframework.com/doc/api/2.0/yii-base-behavior)
for an [`ActiveRecord`](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord). 
It supplies the owning `ActiveRecord` with two virtual attributes:
- `prev` the previous record, or `null` if the current record is the first.
- `next` the next record, or `null` if the current record is the last.

**PrevNextBehavior** has two properties:
- **$attribute** `string` Name of the `ActiveRecord` attribute that 
defines the ordering. Has to be set.
- **$sort** `SORT_ASC|SORT_DESC` Sets whether the order is incrementing
or decrementing. Default: `SORT_ASC` (incrementing).

**PrevNextBehavior** can be added to an `ActiveRecord` like so:

    <?php
    use sjaakp\swiper\PrevNextBehavior;

    class Event extends \yii\db\ActiveRecord
    {
        // ...    
        public function behaviors()
        {
            return [
                [
                    'class' => PrevNextBehavior::class,
                    'attribute' => 'date',
                ]
            ];
        }
        // ...
    }


#### Swiper ####

This is a [`Widget`](https://www.yiiframework.com/doc/api/2.0/yii-base-widget),
rendering buttons to the previous and next page (if available). It also implements
the code to handle touch swiping and keyboard input. Its **$model** property should be set to an instance
of an `ActiveRecord` having **PrevNextBehavior**.

**Swiper**'s other properties are:

- **$labelAttribute** `string` Name of the attribute providing the label text for the
buttons. Must be set.
- **$shortLabelAttribute** `null|string` Name of the attribute providing the 
short label text for the buttons. Optional. The short label appears when the screen
width is smaller than the value indicated by **$breakpoint**. Default: `null` (no short labels).
- **$breakpoint** `string` The [Bootstrap 4 breakpoint](https://getbootstrap.com/docs/4.4/layout/overview/#responsive-breakpoints)
defining when short labels will appear. Default: `'sm'`.
- **$url** `string` The base URL of the button links. Default: `'view'`.
- **$swipeId** `null|string` The HTML-id of the element sensible to touch swiping.
If `null` (default) it is the `<body>` element.
- **$options** `array` HTML options for the surrounding `<nav>` element.
- **$registerLinkTags** `bool` Whether to register link tags in the HTML header 
for previous and next page. Default: `false`, to avoid conflicts with other widgets.

In a view file, say `event\view`, **Swiper** could be employed along these lines:

    <?php
        use sjaakp\swiper\Swiper
    ?>
    <h1>...</h1>
    ...
    <?= Swiper::widget([
        'model' => $model,
        'labelAttribute' => 'title',
        // 'url' => 'view',       // default 
        'registerLinkTags' => true
    ]) ?>
    ... other view code ...
