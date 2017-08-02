<?php

namespace backend\controllers;

use Yii;
use backend\models\Goods;
use yii\data\ActiveDataProvider;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GoodsController implements the CRUD actions for Goods model.
 */
class GoodsController extends BaseController
{
    public $layout = "lte_main";
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Goods models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->orderBy('create_time desc,id desc'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Goods model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Goods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Goods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Goods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Goods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Goods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Goods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Goods::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImport()  
    {  
        $model = new \common\models\UploadForm();  
        $ok = "";
        // if ($model->load(Yii::$app->request->post())) {  
        if(Yii::$app->request->isPost){
            $model->file = \yii\web\UploadedFile::getInstance($model, 'file');
            $file = $model->upload();
            if ($file) {
                // 文件上传成功

                if(in_array($model->file->extension, array('xls','xlsx'))){

                    $data = $this->readFileFromGoods($file);

                    $count = Goods::find()->count();

                    if($count > 0){
                        foreach ($data as $key => $value) {
                            $goods = Goods::findOne(['code' => $value['code']]);
                            if($goods == null){
                                //insert
                                $goods = new Goods();
                                $goods->code = $value['code'];
                                $goods->status = 1;
                                $goods->create_time = time();
                            }

                            $goods->name = $value['name'];
                            $goods->category_name = $value['category_name'];
                            $goods->brand = $value['brand'];
                            $goods->specification = $value['specification'];
                            $goods->price = $value['price'];
                            $goods->stock = $value['stock'];
                            $goods->stock_position = $value['stock_position'];
                            $goods->update_time = time();

                            Yii::info($goods->name);
                            
                            if($goods){
                                $goods->save(false);
                            }
                        }
                    }
                    else{
                        Yii::$app->db->createCommand()->batchInsert(Goods::tableName(), ['code','name','category_name','brand','specification','stock','stock_position','status','create_time','update_time'], $data)->execute();
                    }

                    $this->redirect(array('index'));  
                } 
            }       
        } else { 
            return $this->render('import', [  
                'model' => $model,  
            ]);  
        }  
    }  

    private function readFileFromGoods($file){
        $time_beging = 0;
        $time_end = 0;

        // $file = 'uploads/201707179604.xls';

        $excelFile = Yii::getAlias('@backend/web/' . $file);//获取文件名  

        // $phpexcel = new \PHPExcel();  

        $time_beging = microtime(true);
        Yii::info($time_beging);

        $data = \common\utils\CommonFun::readFromExcel($excelFile, $startRow = 2, $endRow = 3000);

        $time_end = microtime(true);
        Yii::info($time_end);

        $callTime = $time_beging - $time_end;
        Yii::info(sprintf('%.4f',$callTime));

        $tdata = [];
        foreach ($data as $key => $value) {
            array_push($tdata, [
                'code'=>$value[1], 
                'name'=>$value[2], 
                'category_name'=>$value[7], 
                'brand'=>$value[9], 
                'specification'=>$value[15], 
                'price'=>$value[17], 
                'stock'=>$value[35], 
                'stock_position'=>$value[41],
                'status'=>1,
                'create_time'=>time(),
                'update_time'=>time()
            ]);
        }

        // var_dump($tdata);exit;
        return $tdata;
    }
}
