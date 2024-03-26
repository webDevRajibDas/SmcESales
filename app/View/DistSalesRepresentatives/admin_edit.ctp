<?php
//$dis=($has_node)?"disabled":"";   
$dis="disabled";   
$office_id=$this->request->data['DistSalesRepresentative']['office_id'];
$dis_id=$this->request->data['DistSalesRepresentative']['dist_distributor_id'];
$code=$this->request->data['DistSalesRepresentative']['code'];
?>

<script>
var office_code='<?php echo json_encode($office_code);?>';
</script>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Edit Sales Representative'); ?></h3>
                <div class="box-tools pull-right">
                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Sales Representative List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body">		
                <?php echo $this->Form->create('DistSalesRepresentative', array('role' => 'form')); ?>
                <div class="form-group">
                    <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                </div>
                
                
                
                <div class="form-group">
                    <?php echo $this->Form->input('mobile_number', array('class' => 'form-control')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('office_id', array('label' => 'Area Office', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----',$dis)); ?>
                 <?php 
                       echo $this->Form->input('office_id', array('type'=>'hidden','class' => 'form-control','value'=>$office_id)); 
                     ?>
                
                </div>
                <div class="form-group">
                    <?php echo $this->Form->input('dist_distributor_id', array('label' => 'Distributor', 'id' => 'dist_distributor_id', 'class' => 'form-control', 'empty' => '---- Select ----',$dis)); ?>
                    
                     <?php 
                       echo $this->Form->input('dist_distributor_id', array('type'=>'hidden','class' => 'form-control','value'=>$dis_id)); 
                     ?>
                </div>
                
                <div class="form-group input-group">
                    <div class="input text required ">
                        <label for="DistSalesRepresentativeCode">Code :</label>
                        <div class="input-group-addon" id="code_prefix" style="width:60px;display: inline-block;float:left;"><?php echo $office_code[$office_id];?></div>
                        <input style="width:260px;display: inline-block;float:left;" name="data[DistSalesRepresentative][code]" class="form-control" maxlength="255" type="number" readonly value="<?php echo $code;?>" id="DistSalesRepresentativeCode" required="required">
                    </div>                
                </div>
                
                
                <div class="form-group">
                    <?php echo $this->Form->input('name', array('type' => 'hidden', 'name' => 'data[DistSalesRepresentative][pre_name]', 'id' => 'office_id', 'class' => 'form-control')); ?>
                    <?php echo $this->Form->input('code', array('type' => 'hidden', 'name' => 'data[DistSalesRepresentative][pre_code]', 'id' => 'code', 'class' => 'form-control')); ?>
                    <?php echo $this->Form->input('office_id', array('type' => 'hidden', 'name' => 'data[DistSalesRepresentative][pre_office_id]', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    <?php echo $this->Form->input('territory_id', array('type' => 'hidden', 'name' => 'data[DistSalesRepresentative][pre_territory_id]', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?>
                    <?php echo $this->Form->input('dist_distributor_id', array('type' => 'hidden', 'name' => 'data[DistSalesRepresentative][pre_dist_distributor_id]', 'id' => 'office_id', 'class' => 'form-control', 'empty' => '---- Select ----')); ?> 
                    <?php echo $this->Form->input('is_active', array('class' => 'form-control', 'type' => 'checkbox', 'label' => '<strong>Is Active :</strong>')); ?>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->input('remarks', array('label' => 'Remarks','id' => 'remarks', 'class' => 'form-control')); ?>
                </div>
                <?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>

            </div>
        </div>			
    </div>
</div>
<script>
    $(document).ready(function () {
        
        
        
                 $("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('hide');

		});
                
        $('#office_id').selectChain({
            target: $('#dist_distributor_id'),
            value: 'name',
            url: '<?= BASE_URL . 'DistDistributors/get_dist_distributor_list' ?>',
            type: 'post',
            data: {'office_id': 'office_id'}
        });
        
        $(document).on("change", "#office_id", function () {
           var office_id=$(this).val();
           
            if(office_id)
            {
                var parse_office_code=JSON.parse(office_code);
                var code=parse_office_code[office_id];
                $("#code_prefix").html(code);
                
                  $.ajax({
                    url: '<?= BASE_URL . 'DistSalesRepresentatives/get_sr_code' ?>',
                    data: {'office_id': office_id},
                    type: 'POST',
                    success: function (data)
                    {
                        $("#DistSalesRepresentativeCode").val(data);
                    }
                });
            }
            else 
            {
                $("#code_prefix").html("");
                 $("#DistSalesRepresentativeCode").val("");
            }
        });
    });
</script>