<?php
	//echo "<pre>";
	//print_r($outlets);die();
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Market and Outlet import from XLS uploader'); ?></h3>
			</div>	
			<div class="box-body">
				<div class="search-box col-md-6 col-md-offset-2">
                
                	<?php
                    if(isset($m_th)){
						echo 'missing_territory='. $m_te.'<br>';
						echo 'missing_thana='. $m_th.'<br>';
						echo 'create_market='. $c_m.'<br>';
						echo 'create_outlet='. $c_o.'<br>';
						echo 'thana_name='. $thana_name.'<br>';
						echo 'e_m='. $e_m.'<br>';
						echo 'e_o='. $e_o.'<br>';
						echo 'f_o='. $f_o.'<br>';
						echo 'f_o_n='. $f_o_n.'<br>';
						echo 'f_m_n='. $f_m_n.'<br>';
						
						//pr($market_ids);
						//echo 'outlet_ids=<br>';
						//pr($outlet_ids);
					}
					?>
                    
					<?php echo $this->Form->create('XlsMarketOutlet', array('role' => 'form', 'enctype'=>'multipart/form-data')); ?>
					<table class="search">
						<tr>
                        	<td class="text-center" style="width:40%;"><b>Upload File : </b></td>
							<td style="width:60%;">
								<input type="file" id="import" class="form-control" name="import">
							</td>
						</tr>					
											
						<tr>
                        	<td></td>
							<td>
								<?php echo $this->Form->button('Submit', array('type' => 'submit','class' => 'btn btn-large btn-primary','escape' => false)); ?>
							</td>						
						</tr>
					</table>	
					<?php echo $this->Form->end(); ?>
				</div>
			</div>			
		</div>
	</div>
</div>

