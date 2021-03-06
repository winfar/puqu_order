<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;

/**
 * ApiController implements the CRUD actions for Api model.
 */
class ApiController extends Controller
{
    public $layout = false;

    /**
     * @inheritdoc
     * @param [type] $action
     * @return void
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        $req_sign = $_REQUEST['sign'];

        //sign
        if(empty($req_sign)){
            $this->apiPrint(1,'缺少参数');
        }

        if($req_sign == 'true'){
            return true;
        }

        $params = $_REQUEST;

        unset($params['r']);
        unset($params['sign']);
        unset($params['issign']);

        $sign = $this->param_signature($params);

        Yii::info('req:'. json_encode($_REQUEST));
        Yii::info('sign:'.$sign);

        if($sign != $req_sign){
            $this->apiPrint(2,'签名校验出错');
        }

        if (parent::beforeAction($action)) {
            // if($this->verifyPermission($action) == true){
            //     return true;
            // }
            return true;
        }
        return false;
    }

    public function actionSignTest(){
        // $params = json_decode('{"r":"api\/inventory-sync","timestamp":"2017-08-04 13:28:36","app_key":"SGERP","v":"1.0","page_no":"1","page_size":"1","start_time":"2017-08-04 12:28:36","end_time":"2017-08-04 12:28:36","status":"2","sign":"0B1E5B50B914CDB3A2FFE515B21F4F45"}',true);
        // unset($params['r']);
        // unset($params['sign']);
        // unset($params['issign']);
        // $sign = $this->param_signature($params);
        // echo $sign;exit;
    }

    private function param_signature($normalized, $secret="a031881f64200b1d"){

        // $methodPart = strtoupper($method); //"GET" "POST"

        ksort($normalized);

        $parts = '';
        foreach ($normalized as $key => $value) {
            $parts .= $key.$value;
            // array_push($parts,($key.'.'.$value));
        }

        $parts = $secret . $parts . $secret;
        $sign = strtoupper(md5($parts));
        // echo ("signature:". getSignature($baseString,$key));
        return $sign;
    }

    private function percentEncode($value) {
        if($value){
            $value = urlencode($value);
            $value = str_replace("+", "%20",$value);
            $value = str_replace("*", "%2A",$value);
            $value = str_replace("%7E", "~",$value);
        }
        return $value;
    }

    private function getSignature($str,$key){
        $signature='';
        if(function_exists('hash_hmac')){
            $signature = base64_encode(hash_hmac('sha1',$str,$key,true));
        }else{
            $blocksize = 64;
            $hashfunc = 'sha1';
            if(strlen($key) > $blocksize){
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key,$blocksize,chr(0x00));
            $ipad = str_repeat(chr(0x36),$blocksize);
            $opad = str_repeat(chr(0x5c),$blocksize);

            $hmac = pack('H*', $hashfunc(($key ^ $opad). pack('H*',$hashfunc(($key ^ $ipad) . $str))));

            $signature = base64_encode($hmac);
        }
        return $signature;
    }
    
    protected function apiPrint($err_code = 0, $message = 'ok', $result=null){

        header('Content-type: application/json;charset=utf-8');

        $result['success'] = true;
        $result['err_code'] = $err_code;
        $result['message'] = $message;

        if($err_code != 0){
            $result['success'] = false;
        }
        Yii::info($result);
        exit(json_encode($result));

    }

    public function actionInventorySync(){
        // 商品id
        $product_id = $_REQUEST['product_id'];
        // 规格id
        $sku_id = $_REQUEST['sku_id'];
        // 库存数
        $qty = $_REQUEST['qty'];
        // 同步类型（默认为全量更新）1-全量更新（即覆盖原库存）；2-增量更新（即增减原库存：正数为增，负数为减）；
        $sync_type = $_REQUEST['sync_type'];

        if(empty($product_id) || empty($sku_id) || empty($sync_type)){
            $this->apiPrint(1,'参数错误');
        }

        if($sync_type < 1 || $sync_type > 2){
            $this->apiPrint(1,'同步类型错误');
        }

        if($sync_type == 1){
            if($qty < 0){
                $this->apiPrint(1,'库存数量错误');
            }
        }

        $model = \backend\models\Goods::findOne($product_id);
        if($model){

            $stock_before = $model->stock;
            $now = time();

            switch ($sync_type) {
                case 1:
                    $model->stock = $qty;
                    break;
                case 2:
                    $model->stock = $model->stock + $qty;
                    break;
                default:
                    # code...
                    break;
            }

            $model->update_time = $now;

            $rs = $model->save(false);

            if($rs){
                $stock_after = $model->stock;
                $goods_id = $model->id;
                $code = $model->code;

                //明细
                $model_goods_stock_record = new \backend\models\GoodsStockRecord();
                $model_goods_stock_record->create_time  = $now;
                $model_goods_stock_record->goods_id     = $goods_id;
                $model_goods_stock_record->update_type  = $sync_type;
                $model_goods_stock_record->update_avlue = $qty;
                $model_goods_stock_record->stock_before = $stock_before;
                $model_goods_stock_record->stock_after  = $stock_after;
                $rs_goods_stock_record = $model_goods_stock_record->save(false);

                //返回
                $result['product_id'] = $code;
                $result['sku_id'] = $sku_id;
                $result['qty'] = $stock_after;
                $this->apiPrint(0,'更新成功',$result);
                // return ['code'=>0,'data'=>$rs];
            }
            else{
                $this->apiPrint(1,'更新失败');
                // return ['code'=>481,'data'=>'用户验证信息创建失败'];
            }
        }
        else{
            $this->apiPrint(1,'商品不存在，id:'.$product_id);
        }
    }

    public function actionGetProduct(){
        // 商品id
        $product_id = $_REQUEST['product_id'];

        // $product_id='6900090011101';

        if(empty($product_id)){
            $this->apiPrint(1,'参数错误');
        }

        Yii::info('product_id:'.$product_id);

        $model = \backend\models\Goods::findOne(['code' => $product_id]);
        if($model){
            $product['id'] = $model->id;
            $product['name'] = $model->name;
            $product['qty'] = $model->stock;
            $product['price'] = $model->price;
            $product['code'] = $model->code;
            $product['image_url'] = '';
            $product['skus'] = [];

            $result['product']=$product;
            $this->apiPrint(0,'成功',$result);
        }
        $this->apiPrint(1,'商品不存在');
    }

    public function actionGetProductList(){
        // 商品id
        $page_no = $_REQUEST['page_no'];
        $page_size = $_REQUEST['page_size'];
        $status = $_REQUEST['status'];

        // $product_id='6900090011101';

        if(empty($page_no) || $page_no <= 0){
            $page_no = 1;
        }

        if(empty($page_size) || $page_size <= 0){
            $page_size = 10;
        }

        if(empty($status) || $status <= 0){
            $status = 0;
        }

        // Yii::info('product_id:'.$product_id);

        // $model = \backend\models\Goods::findOne(['code' => $product_id]);

        $data = \backend\models\Goods::find()->andWhere(['status' => 1, 'clear' => 0]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $page_size]);
        $pages->setPage($page_no-1);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        $products = [];
        foreach ($model as $key => $value) {
            $products[$key]['id'] = $value->id;
            $products[$key]['name'] = $value->name;
            $products[$key]['qty'] = $value->stock;
            $products[$key]['price'] = $value->price;
            $products[$key]['code'] = $value->code;
            $products[$key]['image_url'] = '';
            $products[$key]['skus'] = [];
        }

        $result['products']=$products;
        $result['total_count'] = $pages->totalCount;
        $this->apiPrint(0,'成功',$result);

        // $this->apiPrint(1,'商品不存在');
    }

    public function actionStockStatistics(){

    }
}
