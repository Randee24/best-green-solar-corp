<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\InventoryPeriods;
use backend\models\InventoryPeriodSearch;

class InventoryPeriodController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'close' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        InventoryPeriods::ensureCurrentMonth();

        $searchModel = new InventoryPeriodSearch();

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

		return $this->render('view',[
			'model' => $model,
			'balancesProvider' => $model->getProductBalancesProvider(),
			'balanceSummary' => $model->getProductBalanceSummary(),
			'transactionSummary' => $model->getTransactionSummary(),
			'history' => $model->getPeriodHistory(),
		]);
    }

    public function actionClose($id)
    {
        $model = $this->findModel($id);

        // Add an RBAC permission check here if only certain
        // roles should be allowed to close inventory periods.

        $pin = Yii::$app->request->post('pin_code','');

        if (!is_string($pin) || $pin === '')
        {
            Yii::$app->session->setFlash(
                'error',
                'Please enter your PIN code.'
            );

            return $this->redirect(['view','id' => $id]);
        }

        if ($model->closePeriod((int) Yii::$app->user->id,$pin))
        {
            Yii::$app->session->setFlash(
                'success',
                'Inventory period closed successfully.'
            );
        }
        else
        {
            Yii::$app->session->setFlash(
                'error',
                implode(' ',array_values($model->getFirstErrors()))
            );
        }

        return $this->redirect(['view','id' => $id]);
    }

    protected function findModel($id)
    {
        $model = InventoryPeriods::findOne($id);

        if ($model === null)
        {
            throw new NotFoundHttpException(
                'The requested inventory period does not exist.'
            );
        }

        return $model;
    }
}
