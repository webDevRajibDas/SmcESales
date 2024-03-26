<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Visit Details'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Doctor Visit List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <table id="Doctors" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong>Doctor Name :</strong></td>
							<td><?php echo h($visits['Doctor']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Territory :</strong></td>
							<td><?php echo h($visits['Territory']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Market :</strong></td>
							<td><?php echo h($visits['Market']['name']); ?></td>
						</tr>
						<tr>
							<td><strong>Visit Date :</strong></td>
							<td><?php echo h($visits['DoctorVisit']['visit_date']); ?></td>
						</tr>
						<tr>
							<td><strong>Place of Visit :</strong></td>
							<td><?php echo h($visits['DoctorVisit']['place_of_visit']); ?></td>
						</tr>
						<tr>
							<td><strong>Night Halting :</strong></td>
							<td><?php echo h($visits['DoctorVisit']['night_halting']); ?></td>
						</tr>
						<tr>
							<td><strong>Clinic Name :</strong></td>
							<td><?php echo h($visits['DoctorVisit']['clinic_name']); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div class="box-body">
                <table class="table table-bordered">
					<tbody>
						<tr>		
							<th class="text-center">SL.</th>
							<th class="text-center">Product Name</th>
							<th class="text-center">Quantity</th>							
						</tr>
						<?php
						if(!empty($visitdetails))
						{
							$sl = 1;
							foreach($visitdetails as $val){
						?>
						<tr>		
							<td align="center"><?php echo $sl; ?></td>
							<td><?php echo $val['Product']['name']; ?></td>
							<td align="center"><?php echo $val['DoctorVisitDetail']['quantity']; ?></td>							
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

