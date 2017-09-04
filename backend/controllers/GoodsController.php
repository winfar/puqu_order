<?php

namespace backend\controllers;

use Yii;
use backend\models\Goods;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
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

        $export = trim(Yii::$app->request->get('export'));
        $condition = '';

        $keywords = trim(Yii::$app->request->get('k'));
        if(!empty($keywords)){
            $condition = ' and g.`name` like \'%'.$keywords.'%\' ';
        }

        $days = trim(Yii::$app->request->get('d'));
        if(empty($days)){
            $days = 7;
        }

        $having = ' having is_stock_in<=0';
        $is_show = trim(Yii::$app->request->get('s'));
        if($is_show=='0'){
            $having = '';
        }

        $is_in_show = trim(Yii::$app->request->get('is'));
        if($is_in_show == '' ||  $is_in_show == '1'){
            $condition .= ' and g.express_status=0';
        }

        $start_time = strtotime(date('Ymd')) - 60 * 60 * 24 * $days;

        $common_days = 0;

        $model_config = \backend\models\Config::findOne(['name'=>'GOODS_ARRIVAL_DAYS']);
        if($model_config){
            $common_days = $model_config->value;
        }

        $sql = 'select g.id,g.`code`,g.`name`,g.stock,if(g.arrival_days=0,' . $common_days . ',g.arrival_days) arrival_days, g.express_status, g.express_status_notes, sum(gsh.stock) out_qty,sum(gsh.stock)/'.$days.' out_qty_average, g.stock-sum(gsh.stock)/'.$days.'*' . $common_days . ' is_stock_in
                from goods g
                left join goods_stock_history gsh on g.`code`=gsh.`code` and gsh.stock_date >'.$start_time.'
                where g.clear=0 '.$condition.'
                GROUP BY g.`code`'.$having.'
                order by g.stock,out_qty desc';

        $rows = Goods::findBySql($sql)->all();
        $totalCount = count($rows);

        if($export == 'true'){
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
            ]);
            $this->ExportStock($dataProvider);
        }
        else{
            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                // 'params' => [':sex' => 1],
                'totalCount' => $totalCount,
                //'sort' =>false,//如果为假则删除排序
                // 'sort' => [
                //     'attributes' => [
                //         'username' => [
                //             'asc' => ['username' => SORT_ASC],
                //             'desc' => ['username' => SORT_DESC],
                //             'default' => SORT_DESC,
                //             'label' => '用户名',
                //         ],
                //         'sex' => [
                //             'asc' => ['sex' => SORT_ASC],
                //             'desc' => ['sex' => SORT_DESC],
                //             'default' => SORT_DESC,
                //             'label' => '性别',
                //         ],
                //         'created_on'
                //     ],
                // ],
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);

            return $this->render('stock', [
                'models' => $dataProvider->getModels(),
                'page' => $dataProvider->pagination,  
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionExpressStatus()
    {
        $id = Yii::$app->request->post('goods-status-id');
        $express_status = Yii::$app->request->post('express_status');
        $express_status_notes = Yii::$app->request->post('express_status_notes');
        
        $model = $this->findModel($id);

        if($model){
            $model->express_status = $express_status;
            $model->express_status_notes = $express_status_notes;

            if ($model->save()) {
                return $this->redirect(['stock']);
            } else {
                // return $this->render('stock', [
                //     'model' => $model,
                // ]);
                echo '物流状态更新错误';
            }
        } else {
            echo '物流状态更新错误';
        }
    }

    public function actionImport()  {  
        $model = new \common\models\UploadForm();  
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

    public function actionImportStock(){
        $model_upload = new \common\models\UploadForm();
        $error_msg = '';
        $stock_date = '';

        if(Yii::$app->request->isPost){
            $stock_date = Yii::$app->request->post('date');
            // echo strtotime($stock_date);exit;
            //添加日期判断
            if(empty($stock_date)){
                $error_msg = '日期不能为空';
            }
            else{
                $stock_date = strtotime($stock_date);
                
                $model_upload->file = \yii\web\UploadedFile::getInstance($model_upload, 'file');

                $file_name = 'goods_stock_' . date('Ymd',time()).rand(1000,9999);
                
                $file = $model_upload->uploadByFileName($file_name);
                if ($file) {
                    // 文件上传成功

                    if(in_array($model_upload->file->extension, array('xls','xlsx'))){

                        $data = $this->readFileFromGoodsStock($file);
                        // var_dump($data);exit;

                        foreach ($data as $key => $value) {
                            $goods_stock_history = \backend\models\GoodsStockHistory::findOne(['code' => $value['code'],'stock_date' => $stock_date]);
                            if($goods_stock_history == null){
                                //insert
                                $goods_stock_history = new \backend\models\GoodsStockHistory();
                                $goods_stock_history->create_time = time();
                            }

                            $goods_stock_history->stock_date = $stock_date;
                            $goods_stock_history->code = $value['code'];
                            $goods_stock_history->stock = $value['stock'];
                            $goods_stock_history->update_time = time();
                            
                            if($goods_stock_history){
                                $goods_stock_history->save(false);
                            }
                        }

                        $this->redirect(array('stock'));  
                    } 
                }   
            }    
        }
        else{
            $stock_date = strtotime(date('Ymd')) - 60 * 60 * 24;
            $goods_stock_history_count = \backend\models\GoodsStockHistory::find(['stock_date' => $stock_date])->count();

            if($goods_stock_history_count == 0){
                $error_msg = date('Y-m-d',strtotime('-1 day')) . ' 数据未导入';
            }
        }   

        // $condition = [];
        // $dataProvider = new ActiveDataProvider([
        //     'query' => Goods::find()->andWhere($condition)->orderBy('create_time desc,code,id desc'),
        // ]);

        // return $this->render('import-stock', [
        //     'dataProvider' => $dataProvider,
        // ]);

        return $this->render('import_stock', [  
            'model_upload' => $model_upload,  
            'error_msg' => $error_msg
        ]); 
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
                'stock'=>empty($value[7]) ? 0 : $value[7], 
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

    private function readFileFromGoodsStock($file){
        $time_beging = 0;
        $time_end = 0;

        // $file = 'uploads/201707179604.xls';

        $excelFile = Yii::getAlias('@backend/web/' . $file);//获取文件名  

        // $phpexcel = new \PHPExcel();  

        $data = \common\utils\CommonFun::readFromExcel($excelFile, $startRow = 2, $endRow = 3000);

        $tdata = [];
        foreach ($data as $key => $value) {
            array_push($tdata, [
                'code'=>$value[0], 
                'stock'=>$value[1]
            ]);
        }

        // var_dump($tdata);exit;
        return $tdata;
    }

    private function ExportStock($dataProvider){

        $objectPHPExcel = new \PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);

        //表格头的输出
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','商家编码');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','商品名称');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','实际库存数');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','到货天数');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','出货量');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','日均销量');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','是否需要进货');
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','建议进货量');
    
        $page_size = 52;
        // $model = new NewsSearch();
        // $dataProvider = $model->search();
        $dataProvider->setPagination(false);
        $data = $dataProvider->getModels();
        // $count = $dataProvider->getTotalItemCount();
        // $page_count = (int)($count/$page_size) +1;
        $current_page = 0;
        $n = 0;

        foreach ( $data as $product ){
            // if ( $n % $page_size === 0 )
            // {
            //     $current_page = $current_page +1;
    
            //     //报表头的输出
            //     $objectPHPExcel->getActiveSheet()->mergeCells('B1:G1');
            //     $objectPHPExcel->getActiveSheet()->setCellValue('B1','产品信息表');
    
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B2','产品信息表');
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B2','产品信息表');
            //     $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->getFont()->setSize(24);
            //     $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')
            //         ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B2','日期：'.date("Y年m月j日"));
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G2','第'.$current_page.'/'.$page_count.'页');
            //     $objectPHPExcel->setActiveSheetIndex(0)->getStyle('G2')
            //         ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    
            //     //表格头的输出
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B3','编号');
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6.5);
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C3','名称');
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D3','生产厂家');
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E3','单位');
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F3','单价');
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            //     $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G3','在库数');
            //     $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                    
            //     //设置居中
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3')
            //         ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
            //     //设置边框
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3' )
            //         ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3' )
            //         ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3' )
            //         ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3' )
            //         ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3' )
            //         ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
            //     //设置颜色
            //     $objectPHPExcel->getActiveSheet()->getStyle('B3:G3')->getFill()
            //         ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');
                    
            // }
            
            //设置边框
            // $currentRowNum = $n+4;
            // $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':G'.$currentRowNum )
            //         ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            // $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':G'.$currentRowNum )
            //         ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            // $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':G'.$currentRowNum )
            //         ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            // $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':G'.$currentRowNum )
            //         ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            // $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':G'.$currentRowNum )
            //         ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('A'.($n+2) ,$product['code']);
            $objectPHPExcel->getActiveSheet()->setCellValue('B'.($n+2) ,$product['name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('C'.($n+2) ,$product['stock']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D'.($n+2) ,$product['arrival_days']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E'.($n+2) ,$product['out_qty']);
            $objectPHPExcel->getActiveSheet()->setCellValue('F'.($n+2) ,$product['out_qty_average']);
            $objectPHPExcel->getActiveSheet()->setCellValue('G'.($n+2) ,($product['stock'] - $product['out_qty_average'] * ($product['arrival_days']+1)) > 0 ? '' : '缺');
            $objectPHPExcel->getActiveSheet()->setCellValue('H'.($n+2) ,($product['stock'] - $product['out_qty_average'] * ($product['arrival_days']+1)) > 0 ? '' : ceil($product['out_qty_average'] * 15));

            $n = $n + 1;    
        }
    
        //设置分页显示
        //$objectPHPExcel->getActiveSheet()->setBreak( 'I55' , PHPExcel_Worksheet::BREAK_ROW );
        //$objectPHPExcel->getActiveSheet()->setBreak( 'I10' , PHPExcel_Worksheet::BREAK_COLUMN );
        // $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        // $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
    
    
        ob_end_clean();
        ob_start();
    
        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.'日销量信息-'.date("YmdHis").'.xls"');
        $objWriter= \PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }
}
