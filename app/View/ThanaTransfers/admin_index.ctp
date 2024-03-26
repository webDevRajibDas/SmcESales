
<style>
.form-control{
	width:50%;
}
#market_list .checkbox label{
	padding-left:10px;
	width:auto;
}
#market_list .checkbox{
	width:30%;
	float:left;
	margin:1px 0;
}
</style>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php echo __('Thana Transfer'); ?></h3>
				<div class="box-tools pull-right">
					<?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Thana List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo $this->Form->create('Thana', array('role' => 'form')); ?>
				
                <div class="col-md-12 col-sm-12">			
                    <div class="col-md-6 col-sm-6">		
                        <div class="form-group required">
                            <?php echo $this->Form->input('office_id', array('id'=>'office_id', 'class' => 'form-control','required' => 'true', 'empty'=>'---- Select ----')); ?>
                        </div>
                        <div class="form-group">
                            <?php echo $this->Form->input('territory_id', array('id'=>'territory_id', 'onChange'=>'getThanaList()', 'class' => 'form-control', 'empty'=>'---- Select ----')); ?>
                        </div>
                        <?php /*?><div class="form-group">
                            <?php echo $this->Form->input('thana_id', array('id'=>'thana_id', 'onChange'=>'getThanaList()', 'class' => 'form-control', 'empty'=>'---- Select ----')); ?>
                        </div><?php */?>
                    </div>
                
                    <div class="col-md-6 col-sm-6">		
                        <div class="form-group required">
                            <?php echo $this->Form->input('to_office_id', array('id'=>'to_office_id', 'class' => 'form-control', 'required' => 'true', 'empty'=>'---- Select ----', 'options' => $offices)); ?>
                        </div>
                        <div class="form-group required">
                            <?php echo $this->Form->input('to_territory_id', array('id'=>'to_territory_id', 'class' => 'form-control', 'required' => 'true', 'empty'=>'---- Select ----')); ?>
                        </div>
                        <?php /*?><div class="form-group required">
                            <?php echo $this->Form->input('to_thana_id', array('id'=>'to_thana_id', 'class' => 'form-control', 'required' => 'true', 'empty'=>'---- Select ----')); ?>
                        </div><?php */?>
                    </div>
                </div>
                
                <div class="col-md-12 col-sm-12">
                	<div class="form-group">
                            <label style="width:auto; margin-left:6%;">Thanas : </label>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                            <?php //echo $this->Form->input('thana_id', array('label'=>false,'multiple' => 'checkbox', 'options' => $markets,'required'=>true)); ?>
                            </div>
                        </div>
                </div>
				
                <div class="col-md-12 col-sm-12">
				<?php echo $this->Form->submit('Save', array('class' => 'btn btn-large btn-primary')); ?>
                </div>
			<?php echo $this->Form->end(); ?>
			</div>
		</div>			
	</div>
</div>
<script>
$(document).ready(function(){
	$('.chosen').chosen();
	
	$('#office_id').selectChain({
		target: $('#territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'office_id' }
	});
	
	$('#to_office_id').selectChain({
		target: $('#to_territory_id'),
		value:'name',
		url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
		type: 'post',
		data:{'office_id': 'to_office_id' }
	});
	
});
$(document).ready(function(){
	/*$('#territory_id').selectChain({
		target: $('#thana_id'),
		value:'name',
		url: '<?= BASE_URL.'ThanaTransfers/get_thana_list'?>',
		type: 'post',
		data:{'territory_id': 'territory_id' }
	});*/
	
	/*$('#to_territory_id').selectChain({
		target: $('#to_thana_id'),
		value:'name',
		url: '<?= BASE_URL.'ThanaTransfers/get_thana_list'?>',
		type: 'post',
		data:{'territory_id': 'to_territory_id' }
	});*/
});
</script>
<script>
function getThanaList(){
	var territory_id = $('#territory_id').val();	
	$.ajax({
		type: "POST",
		url: '<?php echo BASE_URL;?>ThanaTransfers/get_thana_list',
		data: 'territory_id='+territory_id,
		cache: false, 
		success: function(response){
			//alert(response);						
			$('#market_list').html(response);				
		}
	});		
};
</script>