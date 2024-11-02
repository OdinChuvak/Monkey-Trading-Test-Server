<?php

namespace app\base;

use yii\base\InlineAction;
use yii\db\Exception;
use app\common\DomainException;
use app\enums\DomainErrors;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\BadRequestHttpException;
use yii\web\Controller as WebController;

abstract class Controller extends WebController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    /**
     * @param $action
     * @return bool
     * @throws DomainException
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function beforeAction($action): bool
    {
        $this->enableCsrfValidation = false;
        parent::beforeAction($action);

        if ($action->id === 'error') {
            return true;
        }

        $transaction = self::isSafeAction($action->id)
            ? Yii::$app->db->beginTransaction() : null;

        try {
            if (!Yii::$app->user->identity->wallet) {
                throw new DomainException(DomainErrors::WALLET_MISSING);
            }

            if ($action instanceof InlineAction) {
                Yii::$app->response->data = call_user_func([$action->controller, $action->actionMethod]);
            }

            if ($transaction) {
                $transaction->commit();
            }
        } catch (\Exception $exception) {
            if ($transaction) {
                $transaction->rollBack();
            }

             throw $exception;
        }

        return false;
    }

    protected function safeActions(): array
    {
        return [];
    }

    private function isSafeAction(string $action): bool
    {
        return in_array($action, static::safeActions());
    }
}