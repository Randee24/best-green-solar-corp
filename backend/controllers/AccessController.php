<?php

namespace backend\controllers;

use backend\models\Accesses;
use backend\models\AccessSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AccessController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ]);
    }

    
	public function actionIndex()
	{
		$searchModel = new AccessSearch();

		$dataProvider = $searchModel->search(
			Yii::$app->request->queryParams
		);

		return $this->render('index',[
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'roleOptions' => Accesses::getRoleOptions(),
			'departmentOptions' => Accesses::getDepartmentOptions(),
			'pageOptions' => Accesses::getPageOptions(),
		]);
	}


    public function actionView($id)
    {
        return $this->render('view',array_merge(
            ['model' => $this->findModel($id)],
            Accesses::getFormOptions()
        ));
    }

    public function actionCreate()
    {
        $model = new Accesses();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success','Access rule created successfully.');
            return $this->redirect(['view','id' => $model->id]);
        }

        return $this->render('create',array_merge(
            ['model' => $model],
            Accesses::getFormOptions()
        ));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success','Access rule updated successfully.');
            return $this->redirect(['view','id' => $model->id]);
        }

        return $this->render('update',array_merge(
            ['model' => $model],
            Accesses::getFormOptions()
        ));
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success','Access rule deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel($id): Accesses
    {
        $model = Accesses::findOne($id);
        if ($model === null)
        {
            throw new NotFoundHttpException('The requested access rule does not exist.');
        }
        return $model;
    }
}
