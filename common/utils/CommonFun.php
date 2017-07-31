<?php
namespace common\utils;
use yii\base\Object;
use Yii;
class CommonFun extends Object{
    /*
    * 二维数组按照指定的键值进行排序
    */
   public  static function arraySort($arr,$keys,$type='asc'){ 
       $keysvalue = $new_array = array();
       foreach ($arr as $k=>$v){
               $keysvalue[$k] = $v[$keys];
       }
       if(strtolower($type) == 'asc'){
               asort($keysvalue);
       }else{
               arsort($keysvalue);
       }
       reset($keysvalue);
       foreach ($keysvalue as $k=>$v){
               $new_array[$k] = $arr[$k];
       }
       return $new_array; 
   }    
   
   
   //单位转换
   public  static function sizecount($filesize) {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize . ' Bytes';
        }
        return $filesize;
    }
    
    /**
     * 获取客户端IP
     * @return string 返回ip地址,如127.0.0.1
     */
    public static function getClientIp()
    {
        $onlineip = 'Unknown';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $real_ip = $ips['0'];
            if ($_SERVER['HTTP_X_FORWARDED_FOR'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $real_ip))
            {
                $onlineip = $real_ip;
            }
            elseif ($_SERVER['HTTP_CLIENT_IP'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP']))
            {
                $onlineip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        if ($onlineip == 'Unknown' && isset($_SERVER['HTTP_CDN_SRC_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CDN_SRC_IP']))
        {
            $onlineip = $_SERVER['HTTP_CDN_SRC_IP'];
            $c_agentip = 0;
        }
        if ($onlineip == 'Unknown' && isset($_SERVER['HTTP_NS_IP']) && preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_NS_IP'] ))
        {
            $onlineip = $_SERVER ['HTTP_NS_IP'];
            $c_agentip = 0;
        }
        if ($onlineip == 'Unknown' && isset($_SERVER['REMOTE_ADDR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['REMOTE_ADDR']))
        {
            $onlineip = $_SERVER['REMOTE_ADDR'];
            $c_agentip = 0;
        }
        return $onlineip;
    }
    
    /**
     * 读取文本末尾n行
     * @param string $fp
     * @param int $n
     * @param number $base
     * @return multitype:
     */
    public static function tail($fileName, $n, $base = 5) {
        $fp = fopen($fileName, "r+");
        $pos = $n + 1;
        $lines = array ();
        while ( count ( $lines ) <= $n ) {
            try {
                fseek ( $fp, - $pos, SEEK_END );
            } catch ( Exception $e ) {
                fseek ( 0 );
                break;
            }
            $pos *= $base;
            while ( ! feof ( $fp ) ) {
                array_unshift ( $lines, fgets ( $fp ) );
            }
        }
        //echo implode ( "", array_reverse ( $lines ) );
        return array_reverse ( array_slice ( $lines, 0, $n ) );
    }
   
    
    public static function sortClass($orderby, $key){
        $data = explode(' ', $orderby);
        $sortClass = 'class="sorting"';
        if(count($data) > 0){
            if(empty($data[0]) == false && $data[0] == $key){
                if(empty($data[1]) == false && $data[1] == 'desc'){
                    $sortClass = 'class="sorting_desc"';
                    
                }
                else{
                    $sortClass = 'class="sorting_asc"';
                }
            }
        }
        return $sortClass;
    }

    /**
     * 读取excel转换成数组
     *
     * @param string $excelFile 文件路径
     * @param int $startRow 开始读取的行数
     * @param int $endRow 结束读取的行数
     * @return array
     */
    public static function readFromExcel($excelFile, $startRow = 1, $endRow = 100) {

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
