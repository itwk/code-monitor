<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use app\models\Codes;
use DOMDocument;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ParseController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $records = Codes::find()->asArray()->all();
        foreach ($records as $record){
//            echo $record['code'] . PHP_EOL;
            $this->check_code($record);
        }

        return ExitCode::OK;
    }

    private function  sendEmail($message)
    {
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Error checking code')
            ->setTextBody($message)
//            ->setHtmlBody('<a href=' . \yii\helpers\Url::to(['site/confirm', 'token' => $user->activation_key ], true) .' ><b>Пройдите по ссылке чтобы подтвердить регистрацию</b></a>')
            ->send();
    }

    private function setData($id, $status, $dateTimeOfCheck, $comment)
    {
        Codes::setData($id, $status, $dateTimeOfCheck, $comment);
    }



    private function check_code($record)
    {
        $id = $record['id'];
        $url = $record['url'];
        $code = $record['code'];
        $codeFound = false;
        $dateTimeOfCheck = Yii::$app->formatter->asDatetime('now', 'dd-MM-yyyy H:i:s');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $res = curl_exec($ch);

        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        curl_close($ch);

        if ( ( $http_code != "200" ) && ( $http_code != "302" )) {
            $message = "Website {$url} unavailable. Code verification is not possible..";
            $this->sendEmail($message);
            $this->setData($id, 0, $dateTimeOfCheck, $message);
            return 0;
            }

        $dom = new DomDocument();
        @ $dom->loadHTML($res);
        $tags = $dom->getElementsByTagName('script');

        foreach ($tags as $tag){
            if (strpos($tag->textContent, $code) !== false){
                $codeFound = true;
                break;
            }
        }

        if ($codeFound === false){
            $message = "The code {$code} at {$url} was not found";
            $this->setData($id, 0, $dateTimeOfCheck, $message);
            $this->sendEmail($message);
        } else {
            $message = 'OK';
            $this->setData($id, 1, $dateTimeOfCheck, $message);
//            $this->sendEmail("OK!!! Код {$code} по адресу {$url} обнаружен" . PHP_EOL);
        }
    }
}
