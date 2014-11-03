yii2-SluggableBehavior
======================

Yii2 Upgraded SluggableBehavior with more options: 
* transliterator - You can change transliterator for Inflector 
* forceUpdate - Enable/Disable slug attribute update when model updates, if previous slug already exists.

Usage example:
--------------

```php
  public function behaviors()
  {
    return [
         [
             'class' => \app\components\SluggableBehavior::className(),
             'attribute' => 'name',
             'slugAttribute' => 'slug',
             'transliterator' => 'Russian-Latin/BGN; NFKD',
             //Set this to true, if you want to update a slug when source attribute has been changed
             'forceUpdate' => false
         ],
     ];
  }
```
