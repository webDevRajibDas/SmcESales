<?php
$scroll = 0;
$this->html = '<ul class="sidebar-menu">';
$this->html .= '<li><a href="'.Router::url('/admin/dashboards').'">
								<i class="fa fa-dashboard"></i><span> Dashboard</span>
							</a></li>';
foreach($menu as $key=>$val)
{
	//if($this->App->menu_permission($val['controller'],$val['action'])){
	//if(!empty($val['child']))
	//{
		if(!empty($val['child']))
		{
			$this->child_html = '<ul class="treeview-menu">';
							$active_class = '';
							foreach($val['child'] as $ckey=>$cval){
								if($this->App->menu_permission($cval['controller'],$cval['action'])){
									if($this->request->params['controller'] == $cval['controller'])
									{
										$scroll = $val['scroll'];
										$active_class = 'active color';
										$active_subclass = 'active';
									}else{
										$active_subclass = '';
									}	
									$this->child_html .= '<li class="'.$active_subclass.'">
										<a href="'.Router::url('/admin/'.$ckey).'">
											<i class="fa fa-angle-double-right"></i><span>'.$cval['title'].'</span>
										</a>
									</li>';
								}
							}
			$this->child_html .= '</ul>';
			
			
			$this->html .= '<li class="treeview '.$active_class.'">
							<a href="'.Router::url('/admin/'.$key).'">
								'.$val['icon'].' <span>'.$val['title'].'</span> <i class="fa fa-angle-left pull-right"></i>
							</a>';
			$this->html .= 	$this->child_html;			
			$this->html .= '</li>';	
			unset($this->child_html);
		}
		else
		{
			
			if($this->request->params['controller'] == $key)
			{
				$active_class = 'active color';					
				$scroll = $val['scroll'];					
			}else{
				$active_class = '';
			}
			$this->html .= '<li class="'.$active_class.'">
						<a href="'.Router::url('/admin/'.$key).'">
							'.$val['icon'].' <span>'.$val['title'].'</span>
						</a>
					</li>';
					
		}	
	//}		
}

$this->html .= '<li><a href="'.Router::url('/changePassword').'">
								<i class="fa fa-bars"></i><span> Change Password</span>
							</a></li>';
$this->html .= '</ul>';

?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">                
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php
			echo $this->html;
		?>
    </section>
    <!-- /.sidebar -->
</aside>
<script>
$(document).ready(function(){
	$('.sidebar').slimscroll({scrollTo: <?php echo $scroll; ?>});	
});
</script>