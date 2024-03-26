<?php
    App::import('Controller', 'OutletSalesReportsController');
    $EsalesController = new OutletSalesReportsController;
?>

<style>
    .search .radio label {
        width: auto;
        float: none;
        padding-left: 5px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 30%;
        float: left;
        margin: 1px 0;
    }

    body .td_rank_list .checkbox {
        width: auto !important;
        padding-left: 20px !important;
    }

    /*.td_rank_list #rank_list label{
	clear:right;
	width:50% !important;
}*/
    .so_list .checkbox {
        width: 50% !important;
        float: left;
        margin: 1px 0;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
        <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Outlet Sales Summary Report'); ?></h3>
                <?php /*?><div class="box-tools pull-right">
					<?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Esales Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div><?php */ ?>
            </div>
        </div>
        <div class="box-body">
    </div>
</div>        