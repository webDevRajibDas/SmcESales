<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Session Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Session List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">                
				<table id="Sessions" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong>Session Name :</strong></td>
							<td><?php echo h($sessions['ProgramSession']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>SO Name :</strong></td>
							<td><?php echo h($sessions['SalesPerson']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Territory :</strong></td>
							<td><?php echo h($sessions['Territory']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Session Type :</strong></td>
							<td><?php echo h($sessions['SessionType']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Session Date :</strong></td>
							<td><?php echo $this->App->dateformat($sessions['ProgramSession']['session_date']); ?></td>
						</tr>
						<tr>
							<td><strong>Session Arranged Date :</strong></td>
							<td><?php echo $this->App->dateformat($sessions['ProgramSession']['session_arranged_date']); ?></td>
						</tr>
						<tr>
							<td><strong>Total Participants :</strong></td>
							<td><?php echo h($sessions['ProgramSession']['total_participant']); ?></td>
						</tr>
						<tr>
							<td><strong>Total Attend :</strong></td>
							<td><?php echo h($sessions['ProgramSession']['total_attend']); ?></td>
						</tr>						
					</tbody>
				</table>
			</div>
			
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<?php
						if(!empty($sessiondetails))
						{							
						?>
						<tr>		
							<th class="text-center">SL.</th>
							<th class="text-center">Product Name</th>														
							<th class="text-center">Quantity</th>						
						</tr>
						<?php						
						$sl = 1;
						foreach($sessiondetails as $val){
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['SessionDetail']['quantity']; ?></td>							
						</tr>
						<?php							
							$sl++;
							}							
						}
						?>							
				</table>
			</div>			
		</div>			
	</div>
</div>

