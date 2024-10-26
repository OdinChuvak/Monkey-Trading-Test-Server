<?php

namespace app\base;

use app\common\Response;
use app\common\DomainException;
use app\enums\DomainErrors;
use app\services\Moment\exceptions\MomentGettingException;
use app\services\Moment\exceptions\MomentSettingException;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller as WebController;

abstract class Controller extends WebController
{
    public function behaviors(): array
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
            ]
        ];
    }

    /**
     * @param $action
     * @return bool
     * @throws DomainException
     */
    public function beforeAction($action): bool
    {
        if ($action->id === 'error') {
            return true;
        }

        $transaction = self::isSafeAction($action->id)
            ? Yii::$app->db->beginTransaction() : null;

        try {
            if (!Yii::$app->user->wallet) {
                throw new DomainException(DomainErrors::WALLET_MISSING);
            }

            /**
             * TODO убедиться, что параметры уже инициализированы в экшне
             * Если нет, реализовать передачу параметров в экшен
             */
            call_user_func([$action, 'run']);

            if ($transaction) {
                $transaction->commit();
            }
        } catch (\Exception $exception) {
            if ($transaction) {
                $transaction->rollBack();
            }

            /**
             * @var int $code
             * @var string $message
             */
            extract($this->getExceptionError($exception));
            call_user_func([$this, 'errorAction'], $code, $message);
        }

        return false;
    }

    /**
     * @param $action
     * @param $result
     * @return Response
     */
    public function afterAction($action, $result): Response
    {
        return Response::getSuccessResponse(parent::afterAction($action, $result));
    }

    private function errorAction(int $code, string $message): Response
    {
        return Response::getErrorResponse([
            'code' => $code,
            'message' => $message,
        ]);
    }

    /**
     * @param \Exception $exception
     * @return array
     */
    private function getExceptionError(\Exception $exception): array
    {
        if (get_class($exception) === DomainException::class) {
            return $exception->getDomainError();
        }

        return [
            'code' => DomainErrors::CUSTOM_ERROR_CODE,
            'message' => $exception->getMessage(),
        ];
    }

    abstract protected function safeActions(): array;

    private function isSafeAction(string $action): bool
    {
        return in_array($action, static::safeActions());
    }
}