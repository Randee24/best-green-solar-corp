<?php

namespace backend\controllers;
use Yii;
use backend\models\Suppliers;
use backend\models\SupplierSearch;
use backend\models\SupplierProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * SupplierController implements the CRUD actions for Suppliers model.
 */
class SupplierController extends Controller
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
     * Lists all Suppliers models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SupplierSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Suppliers model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */

	public function actionView($id)
	{
		$model = Suppliers::findOne($id);

		if ($model === null)
		{
			throw new NotFoundHttpException('Supplier not found.');
		}

		$searchModel = new SupplierProductSearch();

		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams,
			(int) $model->id
		);

		return $this->render('view',[
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    /**
     * Creates a new Suppliers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
	{
		$model = new Suppliers();

		if ($model->load(Yii::$app->request->post()) && $model->save())
		{
			Yii::$app->session->setFlash(
				'success',
				'Supplier "' . $model->name . '" has been created successfully.'
			);

			return $this->redirect(['view','id' => $model->id]);
		}

		return $this->render('create',[
			'model' => $model,
		]);
	}

	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save())
		{
			Yii::$app->session->setFlash(
				'success',
				'Supplier "' . $model->name . '" has been updated successfully.'
			);

			return $this->redirect(['view','id' => $model->id]);
		}

		return $this->render('update',[
			'model' => $model,
		]);
	}


    /**
     * Finds the Suppliers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Suppliers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Suppliers::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
