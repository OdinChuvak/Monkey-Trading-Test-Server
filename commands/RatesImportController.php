<?php

namespace app\commands;

use app\models\Pair;
use app\models\Rate;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class RatesImportController extends Controller
{
    /**
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionRun()
    {
        $pairs = Pair::find()->where(['>=', 'id', 186])->all();
        $client = new Client(['baseUrl' => 'https://api.binance.com/api/v3/klines']);

        $startTimestamp = strtotime('2023-07-01 00:00:00');
        $endTimestamp = strtotime('2024-06-30 23:59:59');

        $step = 15 * 60 * 60;

        foreach ($pairs as $pair) {
            $lastPairRateTimestamp = Rate::find()
                ->select('created_at')
                ->where(['pair_id' => $pair->id])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();

            $t = $lastPairRateTimestamp ? $lastPairRateTimestamp + 60 : $startTimestamp;

            while ($t < $endTimestamp) {
                echo "Current: " . $t . "\n";
                echo "End Timestamp: " . $endTimestamp . "\n\n";

                $chunkLimit = $t + $step;

                $response = $client->createRequest()
                    ->setMethod('GET')
                    ->setData([
                        'symbol' => $pair->base_currency . $pair->quoted_currency,
                        'interval' => '1m',
                        'startTime' => $t * 1000,
                        'endTime' => $chunkLimit * 1000,
                        'limit' => 1000,
                    ])->send();

                if (!$response->isOk) {
                    throw new \Exception('Возникла ошибка');
                }

                $batchData = [];
                $responseData = $response->getData();

                foreach ($responseData as $rateItem) {
                    $moment = ((int) $rateItem[0]) / 1000;

                    if ($moment > $endTimestamp) {
                        break;
                    }

                    $batchData[] = [
                        'pair_id' => $pair->id,
                        'rate' => $rateItem[1],
                        'created_at' => $moment,
                        'updated_at' => $moment,
                    ];
                }

                \Yii::$app->db->createCommand()
                    ->batchInsert(Rate::tableName(), ['pair_id', 'rate', 'created_at', 'updated_at'], $batchData)
                    ->execute();

                $t = $chunkLimit + 60;
            }
        }
    }
}