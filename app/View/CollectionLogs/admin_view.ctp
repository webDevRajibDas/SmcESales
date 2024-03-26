<?php
	// pr($current_deposit);exit;
	App::import('Controller', 'CollectionLogsController');
	$CollectionLogsController = new CollectionLogsController;	

	//pr($collection_log);exit();
	//pr($current_collection);exit();
?>
<div class="row">
    <div class="col-xs-12">		
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> <?php  echo __('Collection'); ?></h3>
				<div class="box-tools pull-right">
	                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-th-large"></i> Collection List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
				</div>
			</div>			
			<div class="box-body">
                <?php foreach($collection_log as $data){ ?>
                <table class="table table-bordered">
                	<tbody>
                		<tr>
                			<td><b>Log Status</b></td>
                			<td>
                				<?php if($data['CollectionLog']['is_deleted']==0) {echo '<span class="btn btn-warning btn-xs">Edited</span>';}else{echo '<span class="btn btn-danger btn-xs">Deleted</span>';} ?>
                			</td>
                			<td>
                				<b><?php if($data['CollectionLog']['is_deleted']==0) {echo '<span>Edited At</span>';}else{echo '<span>Deleted At</span>';} ?></b>
                			</td>
                			<td>
                				<?php echo $this->App->datetimeformat($data['CollectionLog']['deleted_at']); ?>
                			</td>
                		</tr>

                		<tr>
                			<td><b>Memo No</b></td>
                			<td><?=$data['CollectionLog']['memo_no'];?></td>
                			<td><b>Memo Value</b></td>
                			<td><?=$data['CollectionLog']['memo_value'];?></td>
                		</tr>


                		<tr>
                			<td><b>Instrument Ref.No</b></td>
                			<td><?=$data['CollectionLog']['instrumentRefNo'];?></td>
                			<td><b>Outlet</b></td>
                			<td><?=$data['Outlet']['name'];?></td>
                		</tr>
                		<tr>
                			<td><b>Type</b></td>
                			<td>
                				<?php 
                					if($data['CollectionLog']['type'] == 1){
                						echo "Cash";
                					}else{
                						echo "Instrument";
                					}
                				?>	
                			</td>
                			<td><b>Instrument Type</b></td>
                			<td><?=$data['InstrumentType']['name'];?></td>
                		</tr>

                		<tr>
                			<td><b>Instrument No</b></td>
                			<td><?=$data['CollectionLog']['instrument_no'];?></td>
                			<td><b>Collection Date 	</b></td>
                			<td><?=$data['CollectionLog']['collectionDate'];?></td>
                		</tr>
                		<tr>
                			<td><b>Collection Amount</b></td>
                			<td><?=$data['CollectionLog']['collectionAmount'];?></td>
                			<td><b>	</b></td>
                			<td></td>
                		</tr>
                		
                	</tbody>
                </table>
                <br>
                <?php } ?>

                <?php if($current_collection) {?>
	                 <?php foreach($current_collection as $data){ ?>
	                <table class="table table-bordered">
	                	<tbody>
	                		<tr>
	                			<td><b>Log Status</b></td>
	                			<td>
	                				<span class="btn btn-info btn-xs">Current Collection</span>
	                			</td>
	                			<td>
	                				
	                			</td>
	                			<td>
	                				
	                			</td>
	                		</tr>
	                		<tr>
                			<td><b>Memo No</b></td>
                			<td><?=$data['Collection']['memo_no'];?></td>
                			<td><b>Memo Value</b></td>
                			<td><?=$data['Collection']['memo_value'];?></td>
                		</tr>


                		<tr>
                			<td><b>Instrument Ref.No</b></td>
                			<td><?=$data['Collection']['instrumentRefNo'];?></td>
                			<td><b>Outlet</b></td>
                			<td><?=$data['Outlet']['name'];?></td>
                		</tr>
                		<tr>
                			<td><b>Type</b></td>
                			<td>
                				<?php 
                					if($data['Collection']['type'] == 1){
                						echo "Cash";
                					}else{
                						echo "Instrument";
                					}
                				?>	
                			</td>
                			<td><b>Instrument Type</b></td>
                			<td><?=$data['InstrumentType']['name'];?></td>
                		</tr>

                		<tr>
                			<td><b>Instrument No</b></td>
                			<td><?=$data['Collection']['instrument_no'];?></td>
                			<td><b>Collection Date 	</b></td>
                			<td><?=$data['Collection']['collectionDate'];?></td>
                		</tr>
                		<tr>
                			<td><b>Collection Amount</b></td>
                			<td><?=$data['Collection']['collectionAmount'];?></td>
                			<td><b>	</b></td>
                			<td></td>
                		</tr>
	                		
	                	</tbody>
	                </table>
	                <?php } ?>
                <?php } ?>
			</div>			
		</div>
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

