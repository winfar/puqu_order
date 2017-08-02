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
            <td>库存总额：<a href="#"><?= \backend\models\Goods::find()->sum('price') ?></a></td>
            <td>流通款：<a href="#"><?= \backend\models\Goods::find()->where(['clear'=>0])->sum('price') ?></a></td>
            <td>清库款：<a href="#"><?php $rlt=\backend\models\Goods::find()->where(['clear'=>1])->sum('price'); echo is_null($rlt)?0:$rlt ?></a></td>
          </tr>
          <tr>
            <td>缺货总数：<a href="#"><?=0000 ?></a></td>
            <td>清库款库库存金额占比：<a href="#"><?=00 ?></a></td>
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