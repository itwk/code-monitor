<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "codes".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $code
 * @property int $status
 * @property datetime last_success_check
 * @property string comment
 */
class Codes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'codes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url', 'code'], 'required'],
            [['status'], 'integer'],
            [['name', 'url', 'code'], 'string', 'max' => 255],
            [['url'], 'unique'],
            ['last_success_check', 'trim'],
            ['comment', 'trim']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
//            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'code' => 'Code',
            'status' => 'State',
            'last_success_check' => 'Last check',
            'comment' => 'Comment'
        ];
    }

    public static function setData($id, $status, $dateTimeOfCheck, $comment)
    {
        $record = self::findOne($id);
        $record->status = $status;
        $record->last_success_check = $dateTimeOfCheck;
        $record->comment = $comment;
        $record->save();
    }

}
