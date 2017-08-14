<?php
use yii\helpers\Url;
?>

<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->
	<div class="row">
		<div class="col-md-12">
		<div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">商品信息</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table table-bordered">
          <!-- <tr>
            <th style="width: 200px">名称</th>
            <th>信息</th>
            <th style="width: 200px">说明</th>
          </tr> -->
          <tr>
            <td>商品总数：<a href="<?=Url::toRoute('goods/index')?>"><?= \backend\models\Goods::find()->count() ?></a></td>
            <td>流通款：<a href="#"><?= \backend\models\Goods::find()->where(['clear'=>0])->count() ?></a></td>
            <td>清库款：<a href="#"><?= \backend\models\Goods::find()->where(['clear'=>1])->count() ?></a></td>
          </tr>
          <tr>
            <?php $stock_amount = \backend\models\Goods::find()->sum('price*stock') ?>
            <td>库存总额：<a href="#"><?= $stock_amount ?></a></td>
            <td>流通款：<a href="#"><?= \backend\models\Goods::find()->where(['clear'=>0])->sum('price*stock') ?></a></td>
            <td>清库款：<a href="#"><?php $rlt=\backend\models\Goods::find()->where(['clear'=>1])->sum('price*stock'); echo is_null($rlt)?0:$rlt ?></a></td>
          </tr>
          <tr>
            <?php
              static $days = 7;
              $start_time = strtotime(date('Ymd')) - 60 * 60 * 24 * $days;

              $common_days = 0;

              $model_config = \backend\models\Config::findOne(['name'=>'GOODS_ARRIVAL_DAYS']);
              if($model_config){
                  $common_days = $model_config->value;
              }

              $sql = 'select g.id,g.`code`,g.`name`,g.stock,if(g.arrival_days=0,' . $common_days . ',g.arrival_days) arrival_days,sum(gsh.stock) out_qty,sum(gsh.stock)/'.$days.' out_qty_average, g.stock-sum(gsh.stock)/'.$days.'*' . $common_days . ' is_stock_in
                      from goods g
                      left join goods_stock_history gsh on g.`code`=gsh.`code`
                      where gsh.stock_date <= UNIX_TIMESTAMP()
                      and gsh.stock_date >'.$start_time.'
                      and g.clear=0
                      GROUP BY g.`code` having is_stock_in<=0
                      order by gsh.stock_date desc,g.stock,out_qty';

              // $rows = Goods::findBySql($sql)->all();
              $goods_stock_count = \backend\models\Goods::findBySql($sql)->count();
              // $goods_stock_count=0;
            ?>
            <td>缺货总数：<a href="<?=Url::toRoute('goods/stock')?>"><?=$goods_stock_count ?></a></td>
            <td>清库款库存金额占比：<a href="#"><?=$stock_amount==0 ? 0 : round($rlt/$stock_amount*100,2) ?>%</a></td>
            <td></td>
          </tr>
        </table>
      </div>
      <!-- <div class="box-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 10px">#</th>
            <th style="width: 200px">名称</th>
            <th>信息</th>
            <th style="width: 200px">说明</th>
          </tr>
          <?php 
              $count = 1;
              foreach($sysInfo as $info){
            echo '<tr>';
            echo '  <td>'. $count .'</td>';
            echo '  <td>'.$info['name'].'</td>';
            echo '  <td>'.$info['value'].'</td>';
            echo '  <td></td>';
            echo '</tr>';
            $count++;
          }
          ?>
          </table>
      </div> -->
      <!-- /.box-body -->
      <div class="box-footer clearfix">
        
      </div>
    </div>
    <!-- /.box -->
	</div>
		
		
	</div>
	<!-- /.row -->
	<!-- Main row -->
	<div class="row">
		
	</div>
	<!-- /.row (main row) -->

</section>
<!-- /.content -->