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
        $keywords = trim(Yii::$app->request->get('k'));
        // var_dump($keywords);exit;
        $condition = [];
        if(!empty($keywords))
        {
            $condition = [
                'or',
                ['like','code',$keywords],
                ['like','name',$keywords],
                // ['like','mobile',$mobile],
            ];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->andWhere($condition)->orderBy('create_time desc,code,id desc'),
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

    public function actionSearch($code){

        $code = Yii::$app->request->post('code');
        $dataProvider = new ActiveDataProvider([
            'query' => Goods::find()->where(['code'=>$code])->orderBy('create_time desc,id desc'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
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
                    // var_dump($data);exit;

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
                            $goods->supplier = $value['supplier'];
                            $goods->specification = $value['specification'];
                            $goods->price = $value['price'];
                            $goods->stock = $value['stock'];
                            $goods->stock_position = $value['stock_position'];
                            $goods->clear = $value['clear'];
                            $goods->arrival_days = $value['arrival_days'];
                            $goods->update_time = time();

                            // Yii::info($goods->name);
                            
                            if($goods){
                                $goods->save(false);
                            }
                        }
                    }
                    else{
                        //字段顺序与读出的excel顺序一致
                        Yii::$app->db->createCommand()->batchInsert(Goods::tableName(), ['code','name','category_name','supplier','specification','brand','price','stock_position','stock','clear','arrival_days','status','create_time','update_time'], $data)->execute();
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
                'code'=>$value[0], 
                'name'=>$value[1], 
                'category_name'=>$value[2], 
                'supplier'=>$value[3],
                'specification'=>'',  
                'brand'=>$value[4], 
                'price'=>$value[5], 
                'stock_position'=>$value[6],
                'stock'=>$value[7], 
                'clear'=>empty($value[8]) ? 0 : $value[8]=='是' ? 1 : 0, 
                'arrival_days'=>empty($value[9]) ? 0 : $value[9], 
                'status'=>1,
                'create_time'=>time(),
                'update_time'=>time()
            ]);
        }

        // var_dump($tdata);exit;
        return $tdata;
    }
}
