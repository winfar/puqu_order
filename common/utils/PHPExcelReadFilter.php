<?php

namespace common\utils;

/**
 * 读取excel过滤器类 单独文件
 */
class PHPExcelReadFilter implements \PHPExcel_Reader_IReadFilter {

    public $startRow = 1;
    public $endRow;

    public function readCell($column, $row, $worksheetName = '') {
        if (!$this->endRow) {
            return true;
        }
        
        if ($row >= $this->startRow && $row <= $this->endRow) {
            return true;
        }

        return false;
    }

}