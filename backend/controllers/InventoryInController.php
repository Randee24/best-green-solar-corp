<?php

namespace backend\controllers;
use Yii;
use backend\models\InventoryIns;
use backend\models\InventoryInSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InventoryInController implements the CRUD actions for InventoryIns model.
 */
class InventoryInController extends Controller
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
     * Lists all InventoryIns models.
     *
     * @return string
     */
    public function actionIndex()
	{
		$searchModel = new InventoryInSearch();

		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams
		);

		return $this->render('index',[
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    /**
     * Displays a single InventoryIns model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
	{
		$model = InventoryIns::find()
			->with([
				'supplier',
				'user.department',
				'inventoryInItems.product.unit',
			])
			->where(['id' => $id])
			->one();

		if ($model === null)
		{
			throw new \yii\web\NotFoundHttpException(
				'Inventory IN transaction not found.'
			);
		}

		return $this->render('view',[
			'model' => $model,
			'history' => $model->getInventoryHistory(),
		]);
	}

    /**
     * Creates a new InventoryIns model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new InventoryIns();

		$model->transaction_date = date('Y-m-d');

		if (Yii::$app->request->isPost)
		{
			$model->load(Yii::$app->request->post());

			$items = Yii::$app->request->post('items',[]);

			if (is_array($items) && $model->createWithItems($items))
			{
				Yii::$app->session->setFlash('success','Inventory receipt created successfully.');

				return $this->redirect(['view','id' => $model->id]);
			}

			Yii::$app->session->setFlash(
				'error',
				implode(' ',$model->getFirstErrors()) ?: 'Unable to save inventory receipt.'
			);
		}
		return $this->renderInventoryInForm('create',$model);
    }

    /**
     * Updates an existing InventoryIns model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->status !== 'draft')
		{
			throw new \yii\web\ForbiddenHttpException(
				'Only draft Inventory IN transactions can be edited.'
			);
		}

		if ($model->load(Yii::$app->request->post()))
		{
			try
			{
				$items = Yii::$app->request->post('items',[]);

				if ($model->updateWithItems(
					$items,
					(int) Yii::$app->user->id
				))
				{
					Yii::$app->session->setFlash(
						'success',
						'Inventory IN updated successfully.'
					);

					return $this->redirect([
						'view',
						'id' => $model->id,
					]);
				}
			}
			catch (\Throwable $e)
			{
				Yii::$app->session->setFlash(
					'error',
					$e->getMessage()
				);
			}
		}

		return $this->renderInventoryInForm('update',$model);
	}

    /**
     * Deletes an existing InventoryIns model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
	 
	public function actionPost($id)
	{
		$request = Yii::$app->request;

		if (!$request->isPost)
		{
			throw new \yii\web\MethodNotAllowedHttpException(
				'Posting requires a POST request.'
			);
		}

		$model = $this->findModel($id);

		$pin = (string) $request->post('pin_code','');
		$userId = (int) Yii::$app->user->id;

		if (!$model->verifyPostingPin($pin,$userId))
		{
			Yii::$app->session->setFlash(
				'error',
				'Invalid PIN code. Inventory IN was not posted.'
			);

			return $this->redirect(['view','id' => $model->id]);
		}
		
		if (!Yii::$app->request->isPost) {
			throw new MethodNotAllowedHttpException(
				'Posting requires a POST request.'
			);
		}

		$model = InventoryIns::findOne($id);

		if ($model === null) {
			throw new NotFoundHttpException(
				'Inventory receipt not found.'
			);
		}

		if ($model->postInventory($userId)) {
			$model->logPostingChange($userId);
			Yii::$app->session->setFlash(
				'success',
				'Inventory receipt posted successfully.'
			);
		} else {
			Yii::$app->session->setFlash(
				'error',
				implode(' ', $model->getFirstErrors())
					?: 'Unable to post inventory receipt.'
			);
		}

		return $this->redirect([
			'view',
			'id' => $model->id,
		]);
	}
	
	private function renderInventoryInForm(string $view, InventoryIns $model)
	{
		$request = Yii::$app->request;

		$postedItems = $request->isPost
			? $request->post('items')
			: null;

		return $this->render($view,[
			'model' => $model,
			'products' => InventoryIns::getProductOptions(),
			'suppliers' => InventoryIns::getSupplierOptions(),
			'items' => $model->getFormItems(
				is_array($postedItems) ? $postedItems : null
			),
		]);
	}
    /**
     * Finds the InventoryIns model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return InventoryIns the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InventoryIns::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
