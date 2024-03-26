<?php
//pr(BASE_URL);die();
//print_r($product_name);die();

//pr($this->Session->read('Office.company_id'));die();
//pr($this->Session->read('Office')['group_id']);die();
//pr($this->Session->read('Office.group_id'));die();
//pr($this->App->menu_permission('manages','admin_view'));die();
?>
<style>
    table, th, td {
        /*border: 1px solid black;*/
        border-collapse: collapse;
    }
    #content { display: none; }
    @media print
    {
        #non-printable { display: none; }
        #content { display: block; }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    }
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Product Issue'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Add New'), array('action' => 'create_order'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('Order', array('role' => 'form','action'=>'filter')); ?>
                    <?php echo $this->Form->input('confirm_status', array('class' => 'form-control', 'value' => '1', 'type' => 'hidden')); ?>
                    <table class="search">


                        <tr>
                            <td>
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker1','required'=>false,'value'=>(isset($this->request->data['Order']['date_from'])=='' ? date('Y-m-d') : $this->request->data['Order']['date_from']),)); ?>
                            </td>

                            <td>
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker1','required'=>false,'value'=>(isset($this->request->data['Order']['date_to'])=='' ? date('Y-m-d') : $this->request->data['Order']['date_to']))); ?>
                            </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit','id'=>'search_button','class' => 'btn btn-large btn-primary','escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>
                <div class="table-responsive">
                    <table id="Order" class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">SL.</th>
                            <th class="text-center" rowspan="2">Office</th>
                            <th class="text-center" rowspan="2">Product</th>
                            <th class="text-center" colspan="2">QTY</th>
                            <th class="text-center" rowspan="2">Total</th>
                        </tr>
                        <tr>
                            <th>Sales Unit</th>
                            <th>Bonus Unit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $i=1;
                        //echo '<pre>';print_r($sales_array);exit;
                        foreach ($sales_array as $v)
                        {
                            ?><tr>
                            <td> <?=$i;?> </td>
                            <td> <?=$v['office'];?> </td>
                            <td> <?=$v['name'];?> </td>
                            <td> <?=$v['s_qty'] . ' ' . $v['s_uom'];?> </td>
                            <td> <?=$v['b_qty'] . ' ' . $v['b_uom'];?> </td>
                            <td> <?=$v['s_qty'] + $v['b_s_qty'];?>  <?=$v['s_uom'];?> </td>
                            </tr>
                            <?php $i++; }
                        ?>

                        <?php

                        $i=$i+1;
                        //echo '<pre>';print_r($sales_array);exit;
                        foreach ($bonus_array as $v)
                        {
                            ?><tr>
                            <td> <?=$i;?> </td>
                            <td> <?=$v['office'];?> </td>
                            <td> <?=$v['name'];?> </td>
                            <td> 0 </td>
                            <td> <?=$v['b_qty'] . ' ' . $v['b_uom'];?> </td>
                            <td> <?=$v['s_qty'];?>  <?=$v['s_uom'];?> </td>
                            </tr>
                            <?php $i++; }
                        ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.datepicker1').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true,
        });
    });
</script>





