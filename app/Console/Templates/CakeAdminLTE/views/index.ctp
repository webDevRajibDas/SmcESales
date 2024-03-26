<?php
/**
 * @copyright     Arena Phone BD Ltd.
 * @developed by  Md. Imrul Hasan <imrul.hasan@arena.com.bd>
 */
?>
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo "<?php echo __('{$pluralHumanName}'); ?>"; ?></h3>
				<div class="box-tools pull-right">
					<?php 
						echo "<?php if(\$this->App->menu_permission('$pluralVar','admin_add')){ echo \$this->Html->link(__('<i class=\"glyphicon glyphicon-plus\"></i> New ".$singularHumanName."'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>\n"; 
					?>
				</div>
			</div>	
			<div class="box-body">
                <table id="<?php echo str_replace(' ', '', $pluralHumanName); ?>" class="table table-bordered table-striped">
					<thead>
						<tr>
						<?php foreach ($fields as $field): ?>
	<th class="text-center"><?php echo "<?php echo \$this->Paginator->sort('{$field}'); ?>"; ?></th>
						<?php endforeach; ?>
	<th width="120" class="text-center"><?php echo "<?php echo __('Actions'); ?>"; ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
						echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";
						echo "\t\t\t\t\t<tr>\n";
						foreach ($fields as $field) {
							$isKey = false;
							if (!empty($associations['belongsTo'])) {
								foreach ($associations['belongsTo'] as $alias => $details) {
									if ($field === $details['foreignKey']) {
										$isKey = true;
										echo "\t\t\t\t\t\t<td class=\"text-center\">\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
										break;
									}
								}
							}
							if ($isKey !== true) {
								echo "\t\t\t\t\t\t<td class=\"text-center\"><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?></td>\n";
							}
						}

						echo "\t\t\t\t\t\t<td class=\"text-center\">\n";
						echo "\t\t\t\t\t\t\t<?php if(\$this->App->menu_permission('$pluralVar','admin_view')){ echo \$this->Html->link(__('<i class=\"glyphicon glyphicon-eye-open\"></i>'), array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); } ?>\n";
						echo "\t\t\t\t\t\t\t<?php if(\$this->App->menu_permission('$pluralVar','admin_edit')){ echo \$this->Html->link(__('<i class=\"glyphicon glyphicon-pencil\"></i>'), array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'edit')); } ?>\n";
						echo "\t\t\t\t\t\t\t<?php if(\$this->App->menu_permission('$pluralVar','admin_delete')){ echo \$this->Form->postLink(__('<i class=\"glyphicon glyphicon-trash\"></i>'), array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'delete'), __('Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); } ?>\n";
						echo "\t\t\t\t\t\t</td>\n";
						echo "\t\t\t\t\t</tr>\n";
						echo "\t\t\t\t\t<?php endforeach; ?>\n";
					?>
					</tbody>
				</table>
				<div class='row'>
					<div class='col-xs-6'>
						<div id='Users_info' class='dataTables_info'>
						<?php
						echo '<?php	echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); ?>';
						?>	
						</div>
					</div>
					<div class='col-xs-6'>
						<div class='dataTables_paginate paging_bootstrap'>
							<ul class='pagination'>									
								<?php
								echo '<?php
									echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
									echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
									echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
								?>';
								?>
								<?php echo "\n"; ?>
							</ul>
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>
</div>