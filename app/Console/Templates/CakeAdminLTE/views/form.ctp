<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://cakephp.org CakePHP(tm) Project
 * @package Cake.Console.Templates.default.views
 * @since CakePHP(tm) v 1.2.0.5234
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-edit"></i>  <?php printf("<?php echo __('%s %s'); ?>", Inflector::humanize($action), $singularHumanName); ?></h3>
				<div class="box-tools pull-right">
					<?php echo "<?php echo \$this->Html->link(__('<i class=\"glyphicon glyphicon-th-large\"></i> ".$singularHumanName." List'), array('action' => 'index'), array('class' => 'btn btn-primary', 'escape' => false)); ?>\n"; ?>
				</div>
			</div>
			<div class="box-body">		
			<?php echo "<?php echo \$this->Form->create('{$modelClass}', array('role' => 'form')); ?>\n"; ?>
					<?php
						foreach ($fields as $field) {
							if (strpos($action, 'add') !== false && $field == $primaryKey) {
								continue;
							} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
								echo "\t\t\t\t\t<div class=\"form-group\">\n";
								echo "\t\t\t\t\t\t<?php echo \$this->Form->input('{$field}', array('class' => 'form-control')); ?>\n";
								echo "\t\t\t\t\t</div>\n";
							}
						}
						if (!empty($associations['hasAndBelongsToMany'])) {
							foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
								echo "\t\t\t\t\t<div class=\"form-group\">\n";
								echo "\t\t\t\t\t\t\t<?php echo \$this->Form->input('{$assocName}');?>\n";
								echo "\t\t\t\t\t</div>\n";
							}
						}
						echo "\n";
						echo "\t\t\t\t<?php echo \$this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>\n";
					?>
			<?php echo "<?php echo \$this->Form->end(); ?>\n";?>
			</div>
		</div>			
	</div>
</div>