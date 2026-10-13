<?php

namespace backend\controllers;
use Yii;
use backend\models\Products;
use backend\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Products model.
 */
class ProductController extends Controller
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
     * Lists all Products models.
     *
     * @return string
     */
	public function actionIndex()
	{
		$searchModel = new ProductSearch();

		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams
		);

		return $this->render('index',[
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'productTypeOptions' => Products::getProductTypeOptions(),
		]);
	}

	public function actionCreate()
	{
		$model = new Products();

		if ($model->load(Yii::$app->request->post()))
		{
			if ($model->save())
			{
				Yii::$app->session->setFlash(
					'success',
					'Product "' . $model->name . '" has been created successfully.'
				);

				return $this->redirect(['view','id' => $model->id]);
			}

			Yii::$app->session->setFlash(
				'error',
				'Unable to create product. Please check the form and try again.'
			);
		}

		return $this->render('create',[
			'model' => $model,
		]);
	}

	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()))
		{
			if ($model->save())
			{
				Yii::$app->session->setFlash(
					'success',
					'Product "' . $model->name . '" has been updated successfully.'
				);

				return $this->redirect(['view','id' => $model->id]);
			}

			Yii::$app->session->setFlash(
				'error',
				'Unable to update product. Please check the form and try again.'
			);
		}

		return $this->render('update',[
			'model' => $model,
		]);
	}

	
	public function actionView($id)
	{
		$model = Products::findOne($id);

		if ($model === null)
		{
			throw new \yii\web\NotFoundHttpException(
				'Product not found.'
			);
		}

		$priceData = $model->getSupplierPriceData();

		return $this->render('view',[
			'model' => $model,
			'qty' => $model->getAvailableQty(),
			'supplierPrices' => $priceData['supplierPrices'],
			'historyProvider' => $priceData['historyProvider'],
			'priceChangeCount' => $priceData['priceChangeCount'],
		]);
	}

	protected function findModel($id)
	{
		$model = Products::find()
			->with(['productType','unit'])
			->where(['id' => $id])
			->one();

		if ($model === null)
		{
			throw new NotFoundHttpException('Product not found.');
		}

		return $model;
	}
}
