<?php

namespace backend\controllers;

use Yii;
use backend\models\InventoryOuts;
use backend\models\InventoryOutSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InventoryOutController implements the CRUD actions for InventoryOuts model.
 */
class InventoryOutController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all InventoryOuts models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new InventoryOutSearch();

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new InventoryOuts();
        $model->date_and_time = date('Y-m-d H:i:s');
        $model->transaction_date = date('Y-m-d');

        $items = [];

        if ($model->load(Yii::$app->request->post()))
        {
            $items = Yii::$app->request->post('items',[]);

            if ($model->saveWithItems($items))
            {
                Yii::$app->session->setFlash('success','Inventory OUT created successfully.');

                return $this->redirect(['view','id' => $model->id]);
            }
        }

        return $this->render('create',[
            'model' => $model,
            'items' => $items,
        ]);
    }

    public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->status !== 'draft')
		{
			throw new BadRequestHttpException('Only draft transactions can be updated.');
		}

		$items = [];

		foreach ($model->inventoryOutItems as $item)
		{
			$items[] = [
				'product_id' => $item->product_id,
				'qty' => $item->qty,
				'remarks' => $item->remarks,
			];
		}

		if ($model->load(Yii::$app->request->post()))
		{
			$items = Yii::$app->request->post('items',[]);

			if ($model->saveWithItems($items))
			{
				Yii::$app->session->setFlash(
					'success',
					'Inventory OUT updated successfully.'
				);

				return $this->redirect(['view','id' => $model->id]);
			}
		}

		return $this->render('update',[
			'model' => $model,
			'items' => $items,
		]);
	}

    public function actionView($id)
	{
		$model = InventoryOuts::find()
			->with([
				'project',
				'inventoryPeriod',
				'inventoryOutItems.product',
				'inventoryOutItems.allocations.inventoryInItem',
				'inventoryOutChanges.user',
			])
			->where(['id' => $id])
			->one();

		if ($model === null)
		{
			throw new NotFoundHttpException('Inventory OUT not found.');
		}

		return $this->render('view',[
			'model' => $model,
		]);
	}

    public function actionPost($id)
    {
        if (!Yii::$app->request->isPost)
        {
            throw new BadRequestHttpException('POST request required.');
        }

        $model = $this->findModel($id);

        if ($model->postInventory())
        {
            Yii::$app->session->setFlash('success','Inventory OUT posted successfully.');
        }
        else
        {
            $errors = $model->getFirstErrors();

			Yii::$app->session->setFlash(
				'error',
				$errors ? implode(' ',$errors) : 'Unable to post Inventory OUT.'
			);
        }

        return $this->redirect(['view','id' => $model->id]);
    }

    protected function findModel($id)
    {
        $model = InventoryOuts::findOne($id);

        if ($model === null)
        {
            throw new NotFoundHttpException('Inventory OUT not found.');
        }

        return $model;
    }
}
