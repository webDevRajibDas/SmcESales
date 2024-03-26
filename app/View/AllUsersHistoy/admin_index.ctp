<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo $page_title; ?></h3>
				
			</div>	
			<div class="box-body">               
			<table class="table table-bordered table-striped text-center">
					<thead>
						<tr>												
							<th>Name</th>
							<th>Name</th>
							<th>UserName</th>
						</tr>
					</thead>
					<tbody>
                    <?php foreach($usersHistorys as $data): ?>
						<tr>
							<td><?php echo $data['AllUsersHistoy']['id'];?></td>
							<td><?php echo $data['AllUsersHistoy']['name'];?></td>
							<td><?php echo $data['AllUsersHistoy']['username'];?></td>
							
						</tr>
					<?php endforeach; ?>

					</tbody>
				</table>
               
                <div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
							<?php echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>
								<?php
								echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								echo $this->Paginator->numbers(array("separator" => "", "currentTag" => "a", "currentClass" => "active", "tag" => "li", "first" => 1));
								echo $this->Paginator->next(__("next"), array("tag" => "li", "currentClass" => "disabled"), null, array("tag" => "li", "class" => "disabled", "disabledTag" => "a"));
								?>
							</ul>
						</div>
					</div>
				</div>
                
				
               
				
			</div>			
		</div>
	</div>
</div>


<script>

</script>