<?php

namespace backend\controllers;
use Yii;
use backend\models\InventoryPeriods;
use backend\models\InventoryPeriodSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
/**
 * InventoryPeriodController implements the CRUD actions for InventoryPeriods model.
 */
class InventoryPeriodController extends Controller
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
     * Lists all InventoryPeriods models.
     *
     * @return string
     */
    public function actionIndex()
    {
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
        $model = InventoryPeriods::findOne($id);

        if ($model === null)
        {
            throw new NotFoundHttpException('Inventory period not found.');
        }

        $inventoryInProvider = new ActiveDataProvider([
            'query' => $model->getInventoryIns()
                ->with(['supplier','inventoryInItems'])
                ->orderBy(['transaction_date' => SORT_DESC,'id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
                'pageParam' => 'in-page',
            ],
            'sort' => false,
        ]);

        return $this->render('view',[
            'model' => $model,
            'inventoryInProvider' => $inventoryInProvider,
        ]);
    }

    /**
     * Creates a new InventoryPeriods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new InventoryPeriods();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InventoryPeriods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the InventoryPeriods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return InventoryPeriods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InventoryPeriods::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
