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
class LocalTestController extends \yii\base\Controller
{
    public $layout = "lte_main";

    public function actionTest(){
        header("Content-Type:text/html;charset=utf-8");

        set_time_limit(0);

        $time_beging = 0;
        $time_end = 0;

        $file = 'uploads/201707179604.xls';

        $excelFile = Yii::getAlias('@backend/web/' . $file);//获取文件名  
        $phpexcel = new \PHPExcel();  

        /*

        if (strstr($file,'.xls')) {  
            $excelReader = \PHPExcel_IOFactory::createReader('Excel5');  
        } 
        else if(strstr($file,'.xlsx')){  
            $excelReader = \PHPExcel_IOFactory::createReader('Excel2007');   
        }  
        else if(strstr($file,'.csv')){  
            $excelReader = \PHPExcel_IOFactory::createReader('CSV');   
        }  
        else{
            echo 'file error';
        }
*/
        $time_beging = microtime(true);
        echo $time_beging . '<br />';

        $data = $this->readFromExcel($excelFile, $startRow = 2, $endRow = 10);

        // $objPHPExcel = \PHPExcel_IOFactory::load($excelFile);
        // $phpexcel = $objPHPExcel->getSheet(0);

        // $excelReader->setReadDataOnly(true); 
        // $phpexcel    = $excelReader->load($excelFile)->getSheet(0);//载入文件并获取第一个sheet  

        $time_end = microtime(true);
        echo $time_end . '<br />';

        $callTime = $time_beging - $time_end;
        echo sprintf('%.4f',$callTime) . '<br />';

        $tdata = [];
        foreach ($data as $key => $value) {
            array_push($tdata, [
                'code'=>$value[1], 
                'name'=>$value[2], 
                'category_name'=>$value[7], 
                'brand'=>$value[9], 
                'specification'=>$value[15], 
                'stock'=>$value[34], 
                'stock_position'=>$value[40],
                'status'=>1,
                'create_time'=>time(),
                'update_time'=>time()
            ]);
        }

        $time_beging = microtime(true);
        echo $time_beging . '<br />';
        Yii::$app->db->createCommand()->batchInsert(Goods::tableName(), ['code','name','category_name','brand','specification','stock','stock_position','status','create_time','update_time'], $tdata)->execute();
        $time_end = microtime(true);
        echo $time_end . '<br />';
        // var_dump($tdata);exit;
    }

    /**
     * 读取excel转换成数组
     *
     * @param string $excelFile 文件路径
     * @param int $startRow 开始读取的行数
     * @param int $endRow 结束读取的行数
     * @return array
     */
    private function readFromExcel($excelFile, $startRow = 1, $endRow = 100) {

        $excelType = \PHPExcel_IOFactory::identify($excelFile);
        $excelReader = \PHPExcel_IOFactory::createReader($excelType);

        if(strtoupper($excelType) == 'CSV') {
            $excelReader->setInputEncoding('GBK');
        }

        if ($startRow && $endRow) {
            $excelFilter           = new \common\utils\PHPExcelReadFilter();
            $excelFilter->startRow = $startRow;
            $excelFilter->endRow   = $endRow;
            $excelReader->setReadFilter($excelFilter);
        }

        $phpexcel    = $excelReader->load($excelFile);
        $activeSheet = $phpexcel->getActiveSheet();

        $highestRow         = $activeSheet->getHighestRow();//总行数 
        $highestColumn      = $activeSheet->getHighestColumn(); //最后列数所对应的字母，例如第1行就是A
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); //总列数

        $data = array();
        for ($row = $startRow; $row <= $endRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $data[$row][] = (string) $activeSheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            if(implode($data[$row], '') == '') {
                unset($data[$row]);
            }
        }
        return $data;
    }
}


