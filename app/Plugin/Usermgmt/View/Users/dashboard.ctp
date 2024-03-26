<div class="row">
    <?php
	foreach($menu as $key=>$val)
	{
		if($this->App->menu_permission($val['controller'],$val['action'])){
	?>		
		<div class="col-md-2">
			<div class="dashboard_icon">
				<a href="<?=Router::url('/admin/'.$key);?>">
					<div style="text-align:center;font-size:30px;color:"><?php echo $val['icon']; ?></div>
					<div style="text-align:center;"><?php echo $val['title']; ?></div>	
				</a>
			</div>	
		</div>							
	<?php			
		}		
	}
	
	?>	
</div>
