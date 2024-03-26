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
            </div>	
            
            
            
            <div class="box-body">
            
                <?php echo $this->Form->create('OpeningBalance', array('role' => 'form')); ?>
                
                    <?php /*?><div class="form-group">
                    <?php echo $this->Form->input('fiscal_year_id', array('onChange' => 'getBalanceListByAreaOffice();', 'class' => 'form-control', 'selected' => $fiscal_year_id, 'id' => 'fiscal_year_id', 'empty'=>'---- Select ----','options'=>$fiscalYears)); ?>
                    </div><?php */?>
                    
                    <div class="form-group">
                    
                        <?php echo $this->Form->input('office_id', array('onChange' => 'getBalanceListByAreaOffice();', 'id' => 'office_id', 'selected' => $area_office_id, 'class' => 'form-control', 'label'=>'Area Office','empty'=>'---- Select ----')); ?>
                    
                    </div>
                
                
                
                    <table class="table table-bordered table-striped">
                    
                        <div class="box-header">
                            <div class="box-tools pull-right"></div>
                        </div>	
                        	
                        <thead>	
                            <tr style="font-size:14px;">
                                <th class="text-center"><?php echo 'Territory Name' ?></th>
                                <th class="text-center">Total Sales</th>
                                <th class="text-center">Total Outstanding</th>
                                <th style="display:none;" class="text-center">Total NCP Collection</th>
                                <th style="display:none;" class="text-center">Total Achivement</th>
                                
                            </tr>
                        </thead>
                        
                        <tbody id="data_table">
                        	<?php 
							if($opening_result){
								
								foreach($opening_result as $result)
								{ 
								$output = '';
							?>
                                <?php
								$output .= '<tr>';
								$output .= '<td class="text-center">
								'.$result['Territory']['name'].'
								<input type="hidden" value="'.$result['OpeningBalance']['territory_id'].'" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][territory_id]">
								</td>';
								
								$output .= '<td class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_sales'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_sales]"></td>';
								$output .= '<td class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_outstanding'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_outstanding]"></td>';
								
								$output .= '<td style="display:none;" class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_ncp_collection'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_ncp_collection]"></td>';
								
								$output .= '<td style="display:none;" class="text-right"><input type="number" value="'.$result['OpeningBalance']['total_achivement'].'" class="form-control sales quantity" name="data[OpeningBalance][territories]['.$result['OpeningBalance']['id'].'][total_achivement]"></td>';
								
								
								$output .= '</tr>';
										
								echo $output;
								?>
                                
                                <?php } ?>
                                
                            <?php } ?>
                        </tbody>
                        
                    </table>
                
                	<?php echo $this->Form->submit('Save', array('name'=>'save_button','value'=>'save_button','class' => 'btn btn-large btn-primary save','style'=>'margin-top:10px;margin-left:250px;')); ?>
                
                <?php echo $this->Form->end(); ?>	
                	
            </div>
            
            	
            	
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	//$("#fiscal_year_id").prop("selectedIndex", 0);
	//$("#office_id").prop("selectedIndex", 0);
});
</script>

<script>
function getBalanceListByAreaOffice()
{	
	//var fiscal_year_id = $("#fiscal_year_id option:selected").val(); 
	
	var office_id = $("#office_id option:selected").val(); 
	
	/*if(!fiscal_year_id){
		alert('Fiscal year is required!');
		return false;
	}*/
	
	fiscal_year_id = 0;
	
	
	if(office_id)
	{
		$.ajax({
			type: "POST",
			url: '<?= BASE_URL . 'opening_balances/BalanceListByAreaOffice'?>',
			data: 'office_id='+office_id+'&fiscal_year_id='+fiscal_year_id,
			cache: false, 
			success: function(response){
				//alert(response);
				$('#data_table').html(response);
			}
		});		
	}
}
</script>

