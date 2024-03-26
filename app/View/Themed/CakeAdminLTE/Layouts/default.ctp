<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<?php echo $this->Html->docType('html5'); ?> 
<html>
	<head>
	
	<!-- using for loader  start -->
<style>
#divLoading_default {
	display : none;
}
#divLoading_default.show {
	display : block;
	position : fixed;
	z-index: 100;
	background-image : url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
	background-color: #666;   
	opacity : 0.4;
	background-repeat : no-repeat;
	background-position : center;
	left : 0;
	bottom : 0;
	right : 0;
	top : 0;
}
#loadinggif.show {
	left : 50%;
	top : 50%;
	position : absolute;
	z-index : 101;
	width : 32px;
	height : 32px;
	margin-left : -16px;
	margin-top : -16px;
}
</style>
 <!-- using for loader  end -->
	
		<?php echo $this->Html->charset(); ?>

		<title>
			<?php echo $page_title; ?>
		</title>
		<?php 
			echo $this->Html->meta('icon');
			echo $this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no']);
			echo $this->fetch('meta');

			echo $this->Html->css('bootstrap.min.css');
			echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css');
			echo $this->Html->css('ionicons.min.css');
			echo $this->Html->css('//fonts.googleapis.com/css?family=Droid+Serif:400,700,700italic,400italic');
			echo $this->Html->css('CakeAdminLTE');
			echo $this->Html->css('datatables/dataTables.bootstrap'); 
			echo $this->Html->css('datepicker/datepicker3'); 
			echo $this->Html->css('chosen'); 
			
			echo $this->Html->css('timepicker/jquery.timepicker'); 
			
			echo $this->fetch('css');
			//echo $this->Html->script('libs/jquery-1.10.2.min');
			//echo $this->Html->script('libs/bootstrap.min');
			
			
			
			echo $this->fetch('script');
		?>
		<?php
			echo $this->Html->script('jquery.min');
			echo $this->Html->script('bootstrap.min');
			echo $this->Html->script('CakeAdminLTE/app');
			echo $this->Html->script('plugins/datepicker/bootstrap-datepicker');
			echo $this->Html->script('plugins/slimScroll/jquery.slimscroll');
			echo $this->Html->script('select-chain');
			echo $this->Html->script('chosen');
			
			echo $this->Html->script('jquery.timepicker.min');
			
			echo $this->fetch('script');
		?>
		<script>
		$(document).ready(function (){
			
			/* using for loader  start */

           /*$("input[type='submit']").on("click", function(){
   				$("div#divLoading_default").addClass('show');

			});*/

           $("input[type='submit']").on("click", function(){
           		var required_field_fillup_okay=1;

           		$(this).closest('form').find('*[required]').each(function(){
           			if(!$(this).val())
           			{
           				required_field_fillup_okay=0;
           				return false;
           			}
           		})
           		if(required_field_fillup_okay)
           		{
	   				$("div#divLoading_default").addClass('show');
           		}

			});
           
           /* using for loader end */
			
			
			$('.datepicker').datepicker({
				format: "dd-mm-yyyy",
				autoclose: true,
				todayHighlight: true
			});	
			
			$('.expire_datepicker').datepicker({
				format: "mm-yy",
				startView: "year", 
				minViewMode: "months",
				autoclose: true
			});
			$('.datepicker,.expire_datepicker').on('click', function(e) {
			   e.preventDefault();
			   $(this).attr("autocomplete", "off");  
			});			
		});
		</script>
	</head>

	<body class="skin-blue fixed">
<div id="divLoading_default" class=""> </div>
		<?php echo $this->element('menu/top_menu'); ?>
		<div class="wrapper row-offcanvas row-offcanvas-left">
			<?php echo $this->element('menu/left_sidebar'); ?>
		
			<!-- Right side column. Contains the navbar and content of the page -->
		    <aside class="right-side">		    	
				<section class="content"> 
				<?php echo $this->Session->flash(); ?>
				<?php echo $this->fetch('content'); ?>				
				</section>
				<!--
				<p align="center">Developed By <a href="http://www.arena.com.bd" target="_blank">Arena Phone BD Ltd.</a></p>
				-->
				<?php echo $this->element('sql_dump'); ?>
			</aside><!-- /.right-side -->	
			
		</div><!-- ./wrapper -->				
	</body>
	
</html>