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

    public function actionStock(){

        static $common_days = 0;
        $list = [];

        $sql = 'select g.id,g.`code`,g.`name`,g.stock,g.arrival_days,sum(stock_after-stock_before) out_qty
                from goods g
                left join goods_stock_record sr on g.id=sr.goods_id
                where sr.create_time <=1502380800 
                GROUP BY goods_id
                order by stock,out_qty';

        $dataProvider = new ActiveDataProvider([
            'query' => Goods::findBySql($sql),
        ]);

        return $this->render('stock', [
            'dataProvider' => $dataProvider,
        ]);
        
        /*
        $goods = Goods::find()->orderBy('create_time desc,id desc')->all();
        // $data = \backend\models\GoodsStockHistory::find()->orderBy('create_date,id')->all();
        

        foreach ($goods as $key => $value) {
            $condition=['goods_id'=>$value->id];
            $data = \backend\models\GoodsStockRecord::find()->andWhere($condition)->orderBy('create_time,id')->all();

            $list[$key]['id'] = $value->id;
            $list[$key]['code'] = $value->code;
            $list[$key]['name'] = $value->name;
            $list[$key]['stock'] = $value->stock;

            $arrival_days = $value->arrival_days;

            if($arrival_days == 0){
                
                if($common_days == 0){
                    $model_config = \backend\models\Config::findOne(['name'=>'GOODS_ARRIVAL_DAYS']);
                    if($model_config){
                        $common_days = $model_config->value;
                    }
                }

                $arrival_days = $common_days;
            }

            $list[$key]['arrival_days'] = $arrival_days;

            $stock_curr = $value->stock;
            $stock_in=0;
            $stock_out=0;
            $stock_tmp=0;
            foreach ($data as $k => $v) {

                if($v->stock_after > $stock_tmp){
                    $stock_out += $v->stock_after - $stock_tmp;
                }

                // if($day_stock < $stock_tmp){
                //     $stock_out += $stock_tmp - $day_stock;
                // }

                $stock_tmp = $v->stock_after;
            }
            
        }
*/
        /*
        foreach ($goods as $key => $value) {

            // echo '$value->id:'.$value->id . '<br>';

            $list[$key]['id'] = $value->id;
            $list[$key]['code'] = $value->code;
            $list[$key]['name'] = $value->name;
            $list[$key]['stock'] = $value->stock;
            $days = [];
            $stock_average=0;
            $stock_sum=0;
            $stock_first=0;
            $stock_last=0;
            $stock_in=0;
            $stock_out=0;
            $stock_tmp=0;
            foreach ($data as $k => $v) {
                // echo '$v->goods_id_stocks:',$v->goods_id_stocks . '<br>';
                
                $id_in_str = strstr($v->goods_id_stocks,$value->id.'|');

                // echo '$id_in_str:'.$id_in_str. '<br>';

                $day_stock = 0;
                if(strlen($id_in_str) > 0){
                    if(strpos($id_in_str, ',')){
                        $day_stock = explode('|',strstr($id_in_str,',',true))[1];
                    }
                    else{
                        $day_stock = explode('|',$id_in_str)[1];
                    }

                    // echo '$day_stock:'.$day_stock. '<br>';
                }
                array_push($days,intval($day_stock));
                // $list[$key][$v->create_date] = intval($day_stock);

                // $stock_sum = $stock_sum + $day_stock;

                if($day_stock > $stock_tmp){
                    $stock_in += $day_stock - $stock_tmp;
                }

                if($day_stock < $stock_tmp){
                    $stock_out += $stock_tmp - $day_stock;
                }

                $stock_tmp = $day_stock;

                if($k==0){
                    $stock_first = $day_stock;
                }

                if($k==count($data)-1){
                    $stock_last = $day_stock;
                }
            }
            $list[$key]['days'] = $days;

            // $list[$key]['stock_sum'] = $stock_sum;

            //也可以时间内最大值减去最后一天的值为当前进货数
            $list[$key]['stock_in'] = $stock_in;
            $list[$key]['stock_out'] = $stock_out;
            //平均存货量
            $stock_average = $stock_first + $stock_last / 2;
            $stock_average = $stock_average == 0 ? $stock_in / count($days) : $stock_average;
            $list[$key]['stock_average'] = $stock_average;
            //库存周转率
            $list[$key]['stock_turnover'] = $stock_average == 0 ? 0 : $stock_out / $stock_average;
            //库存周转天数
            $list[$key]['stock_turnover_days'] = $list[$key]['stock_turnover'] == 0 ? 120 : count($days) / $list[$key]['stock_turnover']; 
        }*/
        var_dump($list);
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
