<?php

namespace app\components;

use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;
use Yii;

/**
 * Class SluggableBehavior
 * @package app\components
 * @author  Artem Voitko <r3verser@gmail.com>
 *
 * Usage example:
 *
 * public function behaviors()
 * {
 * return [
 *      [
 *          'class' => \app\components\SluggableBehavior::className(),
 *          'attribute' => 'name',
 *          'slugAttribute' => 'slug',
 *          'transliterator' => 'Russian-Latin/BGN; NFKD',
 *          //Set this to true, if you want to update a slug when source attribute has been changed
 *          'forceUpdate' => false
 *      ],
 *  ];
 * }
 */
class SluggableBehavior extends \yii\behaviors\SluggableBehavior
{

    /**
     * Transliterator
     * @var string
     */
    public $transliterator;
    /**
     * Update slug attribute even it already exists
     * @var bool
     */
    public $forceUpdate = true;

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        $isNewSlug = true;

        if ($this->attribute !== null) {
            $attributes = (array)$this->attribute;
            /* @var $owner BaseActiveRecord */
            $owner = $this->owner;
            if (!$owner->getIsNewRecord() && !empty($owner->{$this->slugAttribute})) {
                $isNewSlug = false;
                foreach ($attributes as $attribute) {
                    if ($owner->isAttributeChanged($attribute) && $this->forceUpdate) {
                        $isNewSlug = true;
                        break;
                    }
                }
            }

            if ($isNewSlug) {
                $slugParts = [];
                foreach ($attributes as $attribute) {
                    $slugParts[] = $owner->{$attribute};
                }

                $oldTransliterator = Inflector::$transliterator;

                if (isset($this->transliterator)) {
                    Inflector::$transliterator = $this->transliterator;
                }

                $slug = Inflector::slug(implode('-', $slugParts));
                Inflector::$transliterator = $oldTransliterator;
            } else {
                $slug = $owner->{$this->slugAttribute};
            }
        } else {
            $slug = parent::getValue($event);
        }

        if ($this->ensureUnique && $isNewSlug) {
            $baseSlug = $slug;
            $iteration = 0;
            while (!$this->validateSlug($slug)) {
                $iteration++;
                $slug = $this->generateUniqueSlug($baseSlug, $iteration);
            }
        }
        return $slug;
    }

}
