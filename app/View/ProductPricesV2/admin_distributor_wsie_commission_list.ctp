<?php 
//pr($dist_commissions);die();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Distributor Commission Rate List'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Product Price List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>	
            <div class="box-body">
                <div class="search-box">
                        <?php echo $this->Form->create('DistDistributorWiseCommission', array('role' => 'form')); ?>
                        <div class="form-group">
                            <?php echo $this->Form->input('office_id', array('class' => 'form-control office_id','id'=>'office_id',  'empty' => '---- Select ----', 'options'=>$offices,'required' => TRUE)); ?>
                        </div>
                        <div class="form-group">
                            <?php echo $this->Form->input('product_id', array('type'=>'hidden', 'class' => 'form-control product_id', 'id'=>'product_id', 'required' => TRUE,'value'=>$product_id));?>

                            <?php echo $this->Form->input('dist_distributor_id', array('label'=>'Distributor :','class' => 'form-control dist_distributor_id','id'=>'dist_distributor_id','required' => TRUE, 'empty' => '---- Select ----')); ?>
                        </div>
                       <div class="form-group">
                            <?php echo $this->Form->input('effective_date', array('type'=>'text', 'class' => 'form-control datepicker1 effective_date', 'id'=>'effective_date','autocomplete'=>"off")); ?>
                        </div>
                        <div class="form-group">
                            <?php echo $this->Form->input('commission_rate', array('type'=>'number', 'class' => 'form-control commission_rate', 'id'=>'effective_date')); ?> %
                        </div>
                        <div class="form-group" align="center">
                            
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'name'=>'search',)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Add', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'name'=>'add',)); ?>
                                <?php //echo $this->Form->submit('Add', array('class' => 'btn btn-large btn-primary', 'name'=>'add')); ?>
                           
                        </div>
                        <?php //echo $this->Form->submit('Search', array('class' => 'btn btn-large btn-info', 'name'=>'search')); ?>
                        <?php //echo $this->Form->submit('Add', array('class' => 'btn btn-large btn-primary', 'name'=>'add')); ?>
                        <?php echo $this->Form->end(); ?>
                   
                </div>
                <table id="Territories" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">SL</th>
                            <th class="text-center">Distributor Name</th>
                            <th class="text-center">Effective Date</th>
                            <th class="text-center">Commission Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dist_commissions as $dist_commission): ?>
                            <tr>
                                <td class="text-center"><?php echo h($dist_commission['DistDistributorWiseCommission']['id']); ?></td>
                                <td class="text-center"><?php echo h($dist_commission['DistDistributor']['name']); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $effective_date =date('d-m-Y', strtotime($dist_commission['DistDistributorWiseCommission']['effective_date'])) ;
                                        echo h($effective_date); 
                                    ?></td>
                                <td class="text-center"> <?php echo h($dist_commission['DistDistributorWiseCommission']['commission_rate']); echo '%';?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>		
            </div>			
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDistributorWiseCommissions/get_distributor_list_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
});
</script>
<script>
    $(document).ready(function () {
        $('.datepicker1').datepicker({
            startDate: new Date(),
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true
        });
    });
</script>