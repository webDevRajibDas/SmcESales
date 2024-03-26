
<style>
	.draft_size{
		padding: 0px 15px;
	}
</style>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('NCP Product Dashboard'); ?></h3>
			</div>	
			<div class="box-body">

				<table id="NCPDashborad" class="table table-bordered table-striped">
					<thead>
						<tr>
                            <th class="text-center">NCP Type</th>
                            <th class="text-center">Case</th>
                            <th class="text-center">Total Products in Cases</th>
							<th width="120" class="text-center"><?php echo __('Details'); ?></th>
						</tr>
					</thead>
					<tbody>
                        <?php foreach ($results as $val): ?>
                            <tr>
                                <td class='text-left'><?php echo $val[0]['NCP_TYPE']; ?></td>
                                <td class='text-center'><?php echo $val[0]['number_of_total_ncp']; ?></td>
                                <td class='text-center'><?php echo $val[0]['number_of_product']; ?></td>
                                <td class='text-center'>
                                    <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $val[0]['ncp_type_id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
					</tbody>
				</table>

			</div>			
		</div>
	</div>
</div>