<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index" style="margin: 15px;">

    <?php $form = ActiveForm::begin();?>
        <input type="hidden" id="id" name="id">
        <div style="margin-top:20px;">
            <label for="date-range">日期间隔：</label>
            <select id="date-range" name="date-range" class="" style="width:150px;padding:2px 0;">
                <option value="7">7天</option>
                <option value="30">30天</option>
                <option value="60">60天</option>
                <option value="90">90天</option>
            </select>
            <?= Html::input('text','keywords',Yii::$app->request->get('k'),['id'=>'keywords', 'class' => '', 'placeholder'=>'商家编码/名称']);?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" id="is_show" name="is_show" <?=(Yii::$app->request->get('s')=='' || Yii::$app->request->get('s')=='1') ?'checked="checked"':''?>  >只看缺货</input>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" id="is_in_show" name="is_in_show" <?=(Yii::$app->request->get('is')=='' || Yii::$app->request->get('is')=='1') ?'checked="checked"':''?>  >只看未进货</input>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <input type="submit" value="查询" class="btn btn-success"> -->
            <a id="btn_query" href="javascript:;" class="btn btn-success" target="_blank">查询</a>
            <?= Html::a('日销量导入', ['import-stock'], ['class' => 'btn btn-success']) ?>
            <a id="btn_export" href="javascript:;" class="btn btn-success" target="_blank">导出</a>
            <p class="pull-right">
                <!-- 是否需要进货 = 库存数-日均出货量*(预计到货天数+1)，建议订货量 = 日均出货量*15             -->
            </p>
        </div>
    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive'],
        // 'layout' => "{summary}\n{items}\n{pager}",
        // 'summary' => '{begin}-{end}，共{totalCount}条数据，共{pageCount}页',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭分页
            'firstPageLabel'=>"«",
            'prevPageLabel'=>'‹',
            'nextPageLabel'=>'›',
            'lastPageLabel'=>'»',
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'header'=> '<a href="javascript:;">商家编码</a>',
                'value' => function ($data) {
                    return $data['code']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">商品名称</a>',
                'value' => function ($data) {
                    return $data['name']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">实际库存数</a>',
                'value' => function ($data) {
                    return $data['stock']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">到货天数</a>',
                'value' => function ($data) {
                    return $data['arrival_days']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">出货量</a>',
                'value' => function ($data) {
                    return $data['out_qty']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">日均销量</a>',
                'value' => function ($data) {
                    return round($data['out_qty_average'],1); 
                },
            ],
            [
                'header'=> '<a href="javascript:;">是否需要进货</a>',
                'format' => 'raw',
                'value' => function ($data) {
                    $col_is_in = '<span style="color:red;font-weight:bold;">缺</span>';
                    $is_in = ($data['stock'] - $data['out_qty_average'] * ($data['arrival_days']+1)) > 0 ? '' : $col_is_in;
                    return $is_in; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">建议进货量</a>',
                'value' => function ($data) {
                    return ($data['stock'] - $data['out_qty_average'] * ($data['arrival_days']+1)) > 0 ? '' : ceil($data['out_qty_average'] * 15);
                },
            ],
            [
                'header'=> '<a href="javascript:;">状态</a>',
                'format' => 'raw',
                'value' => function ($data) {
                    $express_status = $data['express_status'] == 1 ? '在途' : '未进货';
                    $col_is_in = '<a class="btn_stock_status" href="javascript:;" gid="' .$data['id'] . '" title="' . $express_status . '">' . $express_status . '</a>';
                    return ($data['stock'] - $data['out_qty_average'] * ($data['arrival_days']+1)) > 0 ? '' : $col_is_in;
                },
            ],
            // [
            //     'label'=>'操作',
            //     'format'=>'raw',
            //     'value' => function($data){
            //         $url = "http://www.baidu.com";
            //         return Html::a('已进货', $url, ['title' => '已进货']); 
            //     }
            // ]        
        ],
        
    ]); ?>
</div>

<div class="modal fade" id="edit_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>修改状态</h3>
			</div>
			<div class="modal-body">
                <?php $form = \yii\bootstrap\ActiveForm::begin(["id" => "stock-form", "class"=>"form-horizontal", "action"=> \yii\helpers\Url::toRoute("goods/express-status")]); ?>                      
                <input type="hidden" class="form-control" id="id" name="id" />
                <div id="express_status_div" class="form-group">
                    <label for="express_status" class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-10">
                    <label><input name="express_status" type="radio" value="0" />未进货 </label> 
                    <label><input name="express_status" type="radio" value="1" />在途 </label> 
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div id="remark_div" class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="remark" name="remark" placeholder="" />
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php \yii\bootstrap\ActiveForm::end(); ?>
            </div> 
            <div class="modal-footer">
				<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a> 
                <a id="edit_dialog_ok" href="#" class="btn btn-primary">确定</a>
			</div> 
        </div>   
    </div> 
</div>    
<script>
    function gotoUrl(isExport){
        //

        var url = location.href;
        var k = $.trim($('#keywords').val());
        var d = $('#date-range').val();
        var s = $("#is_show").prop('checked') ? "1" : "0";
        var is = $("#is_in_show").prop('checked') ? "1" : "0";

        
        if(url.indexOf("&k=") > 0){
            url = changeUrlArg(url, "k", k);
        }else{
            url += "&k=" + k;
        }

        if(url.indexOf("&d=") > 0){
            url = changeUrlArg(url, "d", d);
        }else{
            url += "&d=" + d;
        }

        if(url.indexOf("&s=") > 0){
            url = changeUrlArg(url, "s", s);
        }else{
            url += "&s=" + s;
        }

        if(url.indexOf("&is=") > 0){
            url = changeUrlArg(url, "is", is);
        }else{
            url += "&is=" + is;
        }

        if(isExport == true){
            if(url.indexOf("&export=") > 0){
                url = changeUrlArg(url, "export", "true");
            }else{
                url += "&export=true";
            }
        }
        
        location.href = url;
    }

    $(function(){    
        $('#date-range').val(<?= Yii::$app->request->get('d') ?>);

        $("#btn_query").on('click',function(){
            gotoUrl(false);
        });

        $("#btn_export").on('click',function(){
            gotoUrl(true);
        });

        $(".btn_stock_status").on('click',function(){
            var rlt = confirm('确定要变更状态吗？');
            if(rlt){
                $("#id").val($(this).attr('gid'));
                document.forms[0].action = '/backend/web/index.php?r=goods/update-express-status';
                document.forms[0].submit();
            }
            else{
                $('#edit_dialog').modal('show');
                // return false;
            }
        });

        var keywords = $('#keywords').val();
        if(keywords != ""){
            var regexp = new RegExp(keywords,"gim");
            var objs = $(".grid-view > table:contains('"+keywords+"')");
            objs.html(objs.html().replace(regexp,"<b style='background-color:yellow'>"+keywords+"</b>"));
        }
    });
</script>
