<?php

namespace app\commands;

use app\models\Moment;
use app\models\Pair;
use app\models\Rate;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\Query;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class RatesImportController extends Controller
{
    const START_DATE = "2023-07-01 00:00:00";

    const END_DATE = "2024-06-30 23:59:59";

    /**
     * @throws \yii\db\Exception
     */
    public function actionMoments()
    {
        /**
         * @var Moment $moment
         */
        $moment = Moment::find()
            ->orderBy(['timestamp' => SORT_DESC])
            ->one();

        $currentTimestamp = !empty($moment)
            ? $moment->timestamp + 60 : strtotime(self::START_DATE);

        $batchData = [];

        while ($currentTimestamp <= strtotime(self::END_DATE)) {
            $batchData[] = [
                'timestamp' => $currentTimestamp,
                'is_current' => false,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ];

            if (count($batchData) >= 1000) {
                Moment::batchInsert($batchData);
                $batchData = [];
            }

            $currentTimestamp += 60;
        }

        Moment::batchInsert($batchData);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionRun()
    {
        $pairs = Pair::find()->where(['>=', 'id', 251])->all();
        $client = new Client(['baseUrl' => 'https://api.binance.com/api/v3/klines']);

        /**
         * @var Pair $pair
         */
        foreach ($pairs as $pair) {

            $momentQuery = (new Query())
                ->from(Moment::tableName());

            /**
             * @var Rate $lastPairRateMoment
             */
            $lastPairRateMoment = Rate::find()
                ->where(['pair_id' => $pair->id])
                ->orderBy(['moment_id' => SORT_DESC])
                ->one();

            if ($lastPairRateMoment) {
                $momentQuery->where(['>', 'id', $lastPairRateMoment->moment_id]);
            }

            $batchCount = 0;

            foreach ($momentQuery->batch(900) as $moments) {
                $startTimestamp = $moments[0]['timestamp'];
                $endTimestamp = $moments[count($moments) - 1]['timestamp'];

                $ratesApiResponse = $client->createRequest()
                    ->setMethod('GET')
                    ->setData([
                        'symbol' => $pair->base_currency . $pair->quoted_currency,
                        'interval' => '1m',
                        'startTime' => $startTimestamp * 1000,
                        'endTime' => $endTimestamp * 1000,
                        'limit' => 1000,
                    ])->send();

                if (!$ratesApiResponse->isOk) {
                    throw new \Exception('Возникла ошибка');
                }

                $responseData = array_column($ratesApiResponse->getData(), null, 0);
                $batchData = [];

                foreach ($moments as $moment) {
                    $rateData = $responseData[$moment["timestamp"] * 1000] ?: null;
                    $batchData[] = [
                        'pair_id' => $pair->id,
                        'moment_id' => $moment["id"],
                        'rate' => !is_null($rateData) ? $rateData[1] : null,
                        'created_at' => $moment["timestamp"],
                        'updated_at' => $moment["timestamp"],
                    ];
                }

                $batchCount += count($batchData);

                echo count($batchData) . "\n";
                echo $batchCount . "\n\n";

                Rate::batchInsert($batchData);
            }
        }
    }
}