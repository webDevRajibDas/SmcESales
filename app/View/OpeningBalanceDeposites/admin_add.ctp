<style>
    .sales{
        width:60%;
    }
</style>

<?php
$parent_office_id = $this->Session->read('Office.parent_office_id');
$office_id = $this->Session->read('Office.id');
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __($page_title); ?></h3>
                <div class="box-tools pull-right">
					<?php if($this->App->menu_permission('OpeningBalanceDeposites', 'admin_index')){ echo $this->Html->link(__('Deposite List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
				</div>
            </div>	
            
            
            
            <div class="box-body">
            
                <?php echo $this->Form->create('OpeningBalanceDeposite', array('role' => 'form')); ?>
                
                    <?php /*?><div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('onChange' => 'getBalanceListByAreaOffice();', 'class' => 'form-control', 'selected' => $fiscal_year_id, 'id' => 'fiscal_year_id', 'empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
                    </div><?php */?>
                    
                    <div class="form-group">
                        <?php echo $this->Form->input('office_id', array('onChange' => 'getBalanceListByAreaOffice();', 'id' => 'office_id', 'selected' => $area_office_id, 'class' => 'form-control', 'label'=>'Area Office','empty'=>'---- Select ----')); ?>
                    </div>
                    
                    
                    <div class="form-group">
                        <?php echo $this->Form->input('opening_balance_id', array('onChange' => 'getCollectionByOpeningBalanceID();', 'id' => 'opening_balance_id', 'selected' => $area_office_id, 'label'=>'Opening Balance Territory', 'class' => 'form-control', 'empty'=>'---- Select ----')); ?>
                    </div>
                    
                    
                    <div class="form-group">
						<?php echo $this->Form->input('due_collection', array('label'=>'Market Collection', 'readonly'=>'true', 'id' => 'due_collection', 'type' => 'text', 'class' => 'form-control', 'required' => TRUE)); ?>
                    </div>
                    
                    <div class="form-group">
						<?php echo $this->Form->input('amount', array('type' => 'text', 'class' => 'form-control', 'required' => TRUE)); ?>
                    </div>
                	
                    <div class="form-group">
						<?php echo $this->Form->input('date_added', array('id' => 'date_added', 'label'=>'Entry Date', 'class' => 'form-control datepicker', 'required' => TRUE)); ?>
                    </div>
                    
                    
                
                
                	<div class="form-group">
						<?php echo $this->Form->submit('Save', array('name'=>'save_button','value'=>'save_button','class' => 'btn btn-large btn-primary save','style'=>'margin-top:10px;')); ?>
                    </div>
                    
                
                	
                
                <?php echo $this->Form->end(); ?>	
                	
            </div>
            
            	
            	
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	$("#fiscal_year_id").prop("selectedIndex", 0);
	$("#office_id").prop("selectedIndex", 0);
});


function getBalanceListByAreaOffice()
{	
	//var fiscal_year_id = $("#fiscal_year_id option:selected").val();
	
	fiscal_year_id = 0; 
	
	var office_id = $("#office_id option:selected").val(); 
	
	/*if(!fiscal_year_id)
	{
		alert('Fiscal year is required!');
		return false;
	}*/
		
	if(office_id)
	{
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL . 'opening_balance_collections/get_balance_list'?>',
			data: 'office_id='+office_id+'&fiscal_year_id='+fiscal_year_id,
			cache: false, 
			success: function(response){
				//alert(response);
				$('#opening_balance_id').html(response);
			}
		});		
	}
	else
	{
		var output = '<option value="">---- Select ----</option>';
		$('#opening_balance_id').html(output);
	}
}

function getCollectionByOpeningBalanceID()
{	
	var opening_balance_id = $("#opening_balance_id option:selected").val();
	
	if(!opening_balance_id){
		$('#due_collection').val(0);
	}

	//var fiscal_year_id = $("#fiscal_year_id option:selected").val(); 
	
	fiscal_year_id = 0;
	
	var office_id = $("#office_id option:selected").val(); 
	
	$('#due_collection').val(0)
	
	/*if(!fiscal_year_id)
	{
		alert('Fiscal year is required!');
		return false;
	}*/
	
	if(!office_id)
	{
		alert('Office is required!');
		return false;
	}
		
	if(office_id)
	{
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL . 'opening_balance_deposites/get_collection_by_opening_balance_id'?>',
			data: 'opening_balance_id='+opening_balance_id+'&office_id='+office_id+'&fiscal_year_id='+fiscal_year_id,
			cache: false, 
			success: function(response){
				//alert(response);
				$('#due_collection').val(response);
			}
		});		
	}
	else
	{
		var output = '<option value="">---- Select ----</option>';
		$('#opening_balance_id').html(output);
	}
}

</script>

