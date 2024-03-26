<?php
echo $this->Html->css("dist/css/AdminLTE.min");
//echo $this->Html->css("dist/css/skins/_all-skins.min");

echo $this->Html->css("bower_components/morris.js/morris");
echo $this->Html->css("bower_components/jvectormap/jquery-jvectormap");
echo $this->Html->css("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min");
echo $this->Html->css("bower_components/bootstrap-daterangepicker/daterangepicker");
echo $this->Html->css("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min");

echo $this->Html->css("bower_components/Ionicons/css/ionicons.min");
echo $this->Html->css("bower_components/jvectormap/jquery-jvectormap");


echo $this->fetch('css');
App::import('Controller', 'DashboardsController');
$DashboardsController = new DashboardsController;	

?>

<script language="javascript">
    setTimeout(function () {
        window.location.reload(1);
    }, 500000);
</script>
 
<style>
.fixed .content-wrapper, .fixed .right-side{
	padding-top:0px !important;
}
.box{min-height:auto;}
.box .datepicker-inline, .box .datepicker-inline .datepicker-days, .box .datepicker-inline > table, .box .datepicker-inline .datepicker-days > table {
    width: 100%;
}
.small-box h3{
	font-size:28px;
}
.top-boxes .col-lg-2{
	width: 19.99%;
}
.details-box{
	padding:5px 0 0;
}
.top-boxes .small-box{
	margin-bottom:0px;
}
.top-boxes{
	margin-bottom:20px;
}
.box-header h3{
	margin-top:0px;
	font-size:20px;
}

.small-box .inner{
	text-align:center;
	margin-bottom:2px;
	padding:0px;
}
.small-box .inner small{
	color:#fff;
}
.small-box .icon{
	font-size:70px;
	bottom:10px;
}
.small-box .icon:hover{
	font-size:75px;
}
.small-box .icon:hover i{
	font-size:75px;
}

.small-box .inner .col-lg-2, .small-box .inner .col-lg-2 h3, .small-box .inner .col-lg-2 h4{
	padding:0px;
	margin:0px;
	height:60px;
    line-height:60px;
}
.details-box{
	border-left:#e5e5e5 solid 1px;
}
.box_top_title .small-box{
	color:#333;
}
.small-box{
	box-shadow:none;
	border-bottom:#e5e5e5 solid 1px;
	border-left:#e5e5e5 solid 1px;
}
.box_top_title .small-box{
	border-left:#F9F9F9 solid 1px;
}
.box_top_title .details-box {
	border-top: 1px solid #e5e5e5;
}
.inner{
	min-height:58px;
}



@media only screen and (max-width: 768px) {
  h2, span, h3, h4 {
    font-size: 8pt !important;
  }
  .details-box span {
    font-size: 7pt !important;
  }
  small{
	   font-size: 8pt !important;
  }
  .box-header h3{
	  font-size: 8pt !important;
	  font-weight: bold;
  }
  h3.box-title{
	  font-weight: bold !important;
      padding: 5px !important;
  }
  .pull-right.select_box.a_office{
	  margin-top:-4px;
  }
  .pull-right.select_box.a_office label{
	  width: 55% !important;
	  font-size: 8pt !important;
  }
  .chart-legend > li{
	  width:100%;
  }
  .data-sync .pull-right.select_box.a_office{
	  margin-top:2px;
  }
  .data-sync .pull-right.select_box.a_office label{
	  width: 36% !important;
	  margin-right:5px;
  }
  .highcharts-legend{
	  display:none;
  }
  .form-control {
    width: 32%;
  }
  #product_achievement_target_office_id {
    float: right;
    margin-right: 38px;
    margin-top: 4px;
    width: 17% !important;
  }
  .daterange1{
	  margin-top:-5px;
  }
  .box-header > .box-tools {
    right: -2px;
  }
}
</style>

<style>
#divLoading {
	display : none;
}
#divLoading.show {
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
.highcharts-legend{
	display:none;
}
.box-header div{
	display:none;
}
.box-header1 div{
	display:block;
}
#top_boxes_office_id, #region_office_id{
	width:50%;
}
</style>

<div id="divLoading" class=""> </div>

<section class="content admin">	
    
    
    <div class="box box-primary">
        <div class="box-header box-header1 with-border">
        
          <h3 class="">          	
            
          	<div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;">
               <?php echo $this->Form->input('top_boxes_office_id', array('id' => 'top_boxes_office_id', 'onChange' => 'getTopBoxesData("office")', 'class' => 'form-control user_id office_id', 'options' => $offices, 'label'=>'Area Office', 'required'=>true, 'empty'=>'---- All ----')); ?>
            </div>
            
            <?php if(!$office_id){ ?>
            <div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;">
            <?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'onChange' => 'getTopBoxesData("region")', 'class' => 'form-control region_office_id','required'=>true,'empty'=>'---- Head Office ----', 'options' => $region_offices,)); ?>
            </div>
            <?php } ?>
            
          </h3>
          
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <div class="row top-boxes">
                
                <div class="col-lg-12 col-xs-12 box_top_title">
                    <div class="small-box" style="background:#ccc;">
                        <div class="inner">
                              <div class="col-lg-2 col-xs-2" style="background:#CCC;"><h4></h4></div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h4>Today</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h4>This Week</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h4>This Month</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box" style="border-right: 1px solid #e5e5e5;">
                                <h4>This Year</h4>
                              </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-lg-12 col-xs-12">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                              <div class="col-lg-2 col-xs-2">
                                <h4 style="background:#CCC; color:#333;">Revenue</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="today_Revenue"><?=$today_Revenue?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="week_Revenue"><?=$week_Revenue?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="month_Revenue"><?=$month_Revenue?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="year_Revenue"><?=$year_Revenue?></span></h3>
                              </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xs-12">
                    <div class="small-box bg-green">
                        <div class="inner">
                              <div class="col-lg-2 col-xs-2">
                                <h4 style="background:#CCC; color:#333;">EC</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="today_EC"><?=$today_EC?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="week_EC"><?=$week_EC?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="month_EC"><?=$month_EC?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="year_EC"><?=$year_EC?></span></h3>
                              </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xs-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                              <div class="col-lg-2 col-xs-2">
                                <h4 style="background:#CCC; color:#333;">OC</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="today_OC"><?=$today_OC?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="week_OC"><?=$week_OC?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="month_OC"><?=$month_OC?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="year_OC"><?=$year_OC?></span></h3>
                              </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xs-12">
                    <div class="small-box bg-red">
                        <div class="inner">
                              <div class="col-lg-2 col-xs-2">
                                <h4 style="background:#CCC; color:#333;">CYP</h4>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="today_CYP"><?=$today_CYP?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="week_CYP"><?=$week_CYP?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="month_CYP"><?=$month_CYP?></span></h3>
                              </div>
                              <div class="col-lg-2 col-xs-2 details-box">
                                <h3 style="background:#CCC; color:#333;"><span id="year_CYP"><?=$year_CYP?></span></h3>
                              </div>
                        </div>
                    </div>
                </div>
                                      
            </div>

        </div>
    </div>
    
    
    <style>
	/*#region_office_id2, #region_office_id3, #region_office_id4, #region_office_id5, #region_office_id6{
		display:none;
	}
	.office_id2, .office_id3, .office_id4, .office_id5, .office_id6{
		display:none;
	}*/
	</style>
    
    
    
    
    <script>
	$('#region_office_id').selectChain({
		target: $('#top_boxes_office_id'),
		value:'name',
		url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
		type: 'post',
		data:{'region_office_id': 'region_office_id' }
	});
	
    function getTopBoxesData(type)
	{		
        var office_id = $("#top_boxes_office_id option:selected").val()?$("#top_boxes_office_id option:selected").val():0;
		
		var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;
		
		
		//$('#region_office_id2');
		//$('#region_office_id2>option:eq(2)').prop('selected', true);
		//$('#region_office_id2').trigger("change");
		
		
		if(type=='region')
		{
			$("#region_office_id2").val(region_office_id).change();
			$("#region_office_id3").val(region_office_id).change();
			$("#region_office_id4").val(region_office_id).change();
			$("#region_office_id5").val(region_office_id).change();
			$("#region_office_id6").val(region_office_id).change();
			
			//for pie chart
			$('.offices_pie').hide();
			/*if(region_office_id==38){
				$('#region_dhaka_offices_pie').show();
			}else if(region_office_id==20){
				$('#region_east_offices_pie').show();
			}else if(region_office_id==21){
				$('#region_north_offices_pie').show();
			}else if(region_office_id==39){
				$('#region_south_offices_pie').show();
			}else{
				$('#all_offices_pie').show();
			}*/
			
			if(region_office_id){
				$('#region_'+region_office_id+'_offices_pie').show();
			}else{
				$('#all_offices_pie').show();
			}
			
			$('.office_title').text('Area Office Sales');
			//end for pie chart
			
		}
		else
		{
			//for pie chart
			$('.offices_pie').hide();
			if(office_id){
				$('#office_'+office_id+'_offices_pie').show();
			}else{
				$('#all_offices_pie').show();
			}
			$('.office_title').text('Area Territory Office Sales');
			//end for pie chart
			
			$(".office_id2").val(office_id).change();
			$(".office_id3").val(office_id).change();
			$(".office_id4").val(office_id).change();
			$(".office_id5").val(office_id).change();
			$(".office_id6").val(office_id).change();
		}
		//$('#region_office_id2>option:eq(2)').prop("selected", true).change();
		
		
        var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;
        
		allow = 1;
		
		if(allow==1){
			$.ajax 
			({
				url: '<?= BASE_URL.'admin/dashboards/topBoxesData'?>',
				type: "POST",
				data: dataString,
				beforeSend: function() {$("div#divLoading").addClass('show');},
				success: function(data)
				{	
					var obj = jQuery.parseJSON(data);
					$('#today_Revenue').text(obj.today_Revenue);
					$('#week_Revenue').text(obj.week_Revenue);
					$('#month_Revenue').text(obj.month_Revenue);
					$('#year_Revenue').text(obj.year_Revenue);
					
					$('#today_EC').text(obj.today_EC);
					$('#week_EC').text(obj.week_EC);
					$('#month_EC').text(obj.month_EC);
					$('#year_EC').text(obj.year_EC);
					
					$('#today_OC').text(obj.today_OC);
					$('#week_OC').text(obj.week_OC);
					$('#month_OC').text(obj.month_OC);
					$('#year_OC').text(obj.year_OC);
					
					$('#today_CYP').text(obj.today_CYP);
					$('#week_CYP').text(obj.week_CYP);
					$('#month_CYP').text(obj.month_CYP);
					$('#year_CYP').text(obj.year_CYP);
					
					$("div#divLoading").removeClass('show');
					
				}
			});
		}
    }
    </script>
    
      


    
    <div class="row" style="padding-bottom:20px;">
    
    	<div class="col-md-6">
       		
              <div class="box box-primary">
                <div class="box-header with-border stock_status">
                   	<h3 class="">Yearly Achievement Vs Target</h3>
                    <!--(Cal offices target from sales_target table for this year then divide by per month/12)-->
                    
                    <div style="width:100%; float:left;">
                        <div class="pull-right select_box a_office" style="width:50%">
                          <?php echo $this->Form->input('achievement_target_office_id', array('id' => 'achievement_target_office_id', 'onChange' => 'getAchievementTargetData()', 'class' => 'form-control user_id office_id2', 'label'=>'Area Office', 'options' => $offices, 'required'=>true, 'empty'=>'---- All ----')); ?>
                        </div>
                        <?php if(!$office_id){ ?>
                        <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
                        <?php echo $this->Form->input('region_office_id2', array('id' => 'region_office_id2', 'onChange' => 'getAchievementTargetData()', 'class' => 'form-control region_office_id2', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); ?>
                        </div>
                        <?php } ?>
                  	</div>
                  
                </div>
                <div class="box-body">
                      <div class="progress-group">
                      
                        <span class="progress-text">This Year Revenue</span>
                        
                        <span id="achievement_amount" class="progress-number"><b><?=$achievement_amount?></b>/<?=$total_target?></span>
                    
                        <div id="achievement_and_total_penchant" class="progress sm">
                          <div class="progress-bar progress-bar-aqua" style="width: <?=$achievement_penchant?>%"></div>
                        </div>
                        
                        <h3>( <span id="achievement_penchant"><?=$achievement_penchant?></span>% ) Achieved</h3>
                        
                      </div>
                </div>
              </div>
              
              <script>
			  function getAchievementTargetData()
			  {
				var office_id = $("#achievement_target_office_id option:selected").val()?$("#achievement_target_office_id option:selected").val():0;
				var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;
				var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;

				
				$.ajax 
				({
					url: '<?= BASE_URL.'admin/dashboards/achievementTargetData'?>',
					type: "POST",
					data: dataString,
					beforeSend: function() {$("div#divLoading").addClass('show');},
					success: function(data)
					{	
						var obj = jQuery.parseJSON(data);
						//alert(obj.achievement_amount);
						
						$('#achievement_amount').html('<b>'+obj.achievement_amount+'</b>/'+obj.total_target);
						
						$('#achievement_and_total_penchant').html('<div class="progress-bar progress-bar-aqua" style="width: '+obj.achievement_penchant+'%"></div>');
						
						$('#achievement_penchant').text(obj.achievement_penchant);
						
						//$("div#divLoading").removeClass('show');
					}
				});
			  }
			  </script>
          
         	
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="office_title">Area Office Sales</h3>
                </div>
                
                
                <!-- /.box-header -->
                <div id="all_offices_pie" class="offices_pie" style="display:block;">
                    <div class="box-body">
                      <div class="row">
                      
                        
                            <div class="col-md-7">
                              <div class="chart-responsive">
                                <canvas id="pieChart" height="250"></canvas>
                              </div>
                              <!-- ./chart-responsive -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-5">
                                 <?php
                                 //pr($offices);
                                 ?>	
                                 <ul class="chart-legend clearfix">
                                    <?php 
                                    $i=0;
                                    foreach($offices as $key => $value)
                                    { 
                                        if($key!=14)
                                        {
                                        if($i==0){$color='f56954';}
                                        if($i==1){$color='f39c12';}
                                        if($i==2){$color='00c0ef';}
                                        if($i==3){$color='0073b7';}
                                        if($i==4){$color='222222';}
                                        if($i==5){$color='4043A0';}
                                        if($i==6){$color='00a65a';}
                                        
                                        if($i==7){$color='001f3f';}
                                        if($i==8){$color='39cccc';}
                                        if($i==9){$color='3d9970';}
                                        if($i==10){$color='01ff70';}
                                        if($i==11){$color='ff851b';}
                                        if($i==12){$color='f012be';}
                                        if($i==13){$color='932ab6';}
                                        if($i==14){$color='85144b';}
                                        if($i==15){$color='888';}
                                        if($i==16){$color='d2d6de';}
                                    ?>
                                        <li><i style="color:#<?=$color?>;" class="fa fa-circle-o"></i> <?=$value?></li>
                                    <?php
                                        $i++; 
                                        }
                                    } 
                                    ?>
                                    
                                  </ul>
                            </div>
                            <!-- /.col -->
                        
                      </div>
                      <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                    
                    <div class="box-footer no-padding">
                      <ul class="nav nav-pills nav-stacked">
                        <?php /*?><?php 
                        foreach($pie_products as $pie_product){ ?>
                        <li><a><?=$pie_product['Product']['name']?> <span class="pull-right text-red"> <?=$DashboardsController->getAreaOfficeSales(0, $pie_product['PieProductSetting']['product_id']);?>%</span></a></li>
                        <?php } ?><?php */?>
                        
                        <?=$pie_data?>
                        
                      </ul>
                    </div>
                </div>
                
                
                <?php if(!$office_id){ ?>
                
                    <!--For Area Offices-->
                    <?php foreach($all_offices_list as $region_id => $r_o_list){ ?>
                    <div id="region_<?=$region_id?>_offices_pie" class="offices_pie">
                        <div class="box-body">
                          <div class="row">
                                                  
                            <div class="col-md-7">
                              <div class="chart-responsive">
                                <canvas id="pieChart_region_<?=$region_id?>" height="250"></canvas>
                              </div>
                              <!-- ./chart-responsive -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-5">
                             <ul class="chart-legend clearfix">
                                <?php 
                                $i=0;
                                foreach($r_o_list as $key => $value)
                                { 
                                    if($key!=14)
                                    {
                                    if($i==0){$color='f56954';}
                                    if($i==1){$color='f39c12';}
                                    if($i==2){$color='00c0ef';}
                                    if($i==3){$color='0073b7';}
                                    if($i==4){$color='222222';}
                                    if($i==5){$color='4043A0';}
                                    if($i==6){$color='00a65a';}
                                    
                                    if($i==7){$color='001f3f';}
                                    if($i==8){$color='39cccc';}
                                    if($i==9){$color='3d9970';}
                                    if($i==10){$color='01ff70';}
                                    if($i==11){$color='ff851b';}
                                    if($i==12){$color='f012be';}
                                    if($i==13){$color='932ab6';}
                                    if($i==14){$color='85144b';}
                                    if($i==15){$color='888';}
                                    if($i==16){$color='d2d6de';}
                                ?>
                                    <li><i style="color:#<?=$color?>;" class="fa fa-circle-o"></i> <?=$value?></li>
                                <?php
                                    $i++; 
                                    }
                                } 
                                ?>
                              </ul>
                            </div>
                            <!-- /.col -->
                                                    
                          </div>
                          <!-- /.row -->
                        </div>
                        <!-- /.box-body -->
                        
                        
                        <div class="box-footer no-padding">
                          <ul class="nav nav-pills nav-stacked">
                            <?php 
                            foreach($pie_products as $pie_product){ ?>
                            <li><a><?=$pie_product['Product']['name']?> <span class="pull-right text-red"> <?=$DashboardsController->getAreaOfficeSales(0, $pie_product['PieProductSetting']['product_id'], $region_id);?>%</span></a></li>
                            <?php } ?>
                          </ul>
                        </div>
                        
                    </div>
                    <?php } ?>
                                                
                <?php } ?>
                
                
                <!--For Area Territories-->
                <?php foreach($all_territories_list as $t_office_id => $o_t_list){ ?>
                <div id="office_<?=$t_office_id?>_offices_pie" class="offices_pie">
                    <div class="box-body">
                      <div class="row">
                                              
                        <div class="col-md-7">
                          <div class="chart-responsive">
                            <canvas id="pieChart_office_<?=$t_office_id?>" height="250"></canvas>
                          </div>
                          <!-- ./chart-responsive -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-5">
                         <ul class="chart-legend clearfix">
                            <?php 
                            $i=0;
                            foreach($o_t_list as $key => $value)
                            { 
                                if($key!=14)
                                {
                                if($i==0){$color='f56954';}
								if($i==1){$color='f39c12';}
								if($i==2){$color='00c0ef';}
								if($i==3){$color='0073b7';}
								if($i==4){$color='222222';}
								if($i==5){$color='4043A0';}
								if($i==6){$color='00a65a';}
								
								if($i==7){$color='001f3f';}
								if($i==8){$color='39cccc';}
								if($i==9){$color='3d9970';}
								if($i==10){$color='01ff70';}
								if($i==11){$color='ff851b';}
								if($i==12){$color='f012be';}
								if($i==13){$color='932ab6';}
								if($i==14){$color='85144b';}
								if($i==15){$color='888';}
								if($i==16){$color='d2d6de';}
								
								if($i==17){$color='773095';}
								if($i==18){$color='c568eb';}
								if($i==19){$color='e19d32';}
								if($i==20){$color='31dd92';}
								if($i==21){$color='4df90a';}
								if($i==22){$color='c9bb44';}
                            ?>
                                <li><i style="color:#<?=$color?>;" class="fa fa-circle-o"></i> <?=$value?></li>
                            <?php
                                $i++; 
                                }
                            } 
                            ?>
                          </ul>
                        </div>
                        <!-- /.col -->
                                                
                      </div>
                      <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                    
                    <div class="box-footer no-padding">
                      <ul class="nav nav-pills nav-stacked">
                        <?php 
                        foreach($pie_products as $pie_product){ ?>
                        <li><a><?=$pie_product['Product']['name']?> <span class="pull-right text-red"> <?=$DashboardsController->getAreaOfficeTerritorySales(0, $pie_product['PieProductSetting']['product_id'], $t_office_id);?>%</span></a></li>
                        <?php } ?>
                      </ul>
                    </div>
                    
                </div>
                <?php } ?>
                
                
                <!-- /.footer -->
              </div>
              
              
          	
         </div>
        
        
        
         <div class="col-md-6">
        	
            <style>
			/*#product_achievement_target_office_id{
				width:80% !important;
			}*/
			</style>
            
        	<div class="box box-primary">
                <div class="box-header with-border stock_status">
                      <h3>Yearly Products Sales Achievement Vs Target (Tk.)</h3>
                      <!--(Cal offices Target from sale_target_months table using this month id)-->
                     <div style="width:100%; float:left;">
                       <div class="box-tools pull-right select_box a_office col-md-2" style="padding:0px; width:50%;">
						  <?php echo $this->Form->input('product_achievement_target_office_id', array('id' => 'product_achievement_target_office_id', 'onChange' => 'ProductSalesAndTarget()', 'class' => 'form-control user_id office_id3', 'label'=>'Area Office', 'options' => $offices, 'required'=>true, 'empty'=>'All')); ?>
                       </div>
                        <?php if(!$office_id){ ?>
                        <div class="box-tools pull-right select_box a_office" style="width:50%; font-size:14px;">
                        <?php echo $this->Form->input('region_office_id3', array('id' => 'region_office_id3','class' => 'form-control region_office_id3', 'onChange' => 'ProductSalesAndTarget()', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); ?> 
                        </div>
                        <?php } ?>
                      </div>
                      <!-- tools box -->
                      <?php /*?><div class="pull-right box-tools">
                        <button data-toggle="tooltip" class="btn btn-primary btn-sm daterange1 pull-right" type="button" title="Date range">
                          <i class="fa fa-calendar"></i></button>
                      </div><?php */?>
                      <!-- /. tools -->                   
                </div>
                
                <div id="sales_target" class="box-body">
                      <?php 					  
					  foreach($product_settings as $settings){
						  $sales_data = $DashboardsController->getProductSalesAndTarget($settings['ProductSetting']['product_id'], $this->params['url'], $office_id); 
					  ?>
                      <div class="progress-group">
                        <span class="progress-text"><?=$settings['Product']['name']?> (<?=$sales_data['penchant']?>%)</span>
                        <span class="progress-number"><b><?=number_format($sales_data['achive'], 0, '.', ',');?></b>/<?=number_format($sales_data['target'])?></span>
                        <div class="progress sm">
                          <div class="progress-bar" style="background-color:<?=$settings['ProductSetting']['colour']?>; width: <?=$sales_data['penchant']?>%"></div>
                        </div>
                      </div>
                      <?php } ?>
                      
          		</div>
            </div>
            
        </div>
        
    </div>
    
    
    
    <div class="row">
    	<div class="col-md-12">
        	<div class="box box-primary">
            	<div class="box-header with-border stock_status">
                  <h3 class="">Monthly Sales Trend</h3>
                    
                      
                      <div class="pull-right select_box a_office" style="width:30%">
                          <?php echo $this->Form->input('sales_trend_office_id', array('id' => 'sales_trend_office_id', 'onChange' => 'getSalesTrendData()', 'class' => 'form-control user_id office_id4', 'label'=>'Area Office', 'options' => $offices, 'required'=>true, 'empty'=>'---- All ----')); ?>
                      </div>
                  
						<?php if(!$office_id){ ?>
                        <div class="pull-right select_box a_office" style="width:auto; font-size:14px;">
                        <?php echo $this->Form->input('region_office_id4', array('id' => 'region_office_id4', 'onChange' => 'getSalesTrendData()', 'class' => 'form-control region_office_id4', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); ?>
                        </div>
                        <?php } ?>
                    
                  
                </div>
                <div class="box-body">
					<div id="container" style="width:100%; height: 400px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
    </div>
    
    
	
    <div class="row">
    	<div class="col-xs-11 col-md-11">
            <div class="box box-primary">
            
                <div class="box-header with-border stock_status">
                  <style>
				  #stock_status_office_id{
					  float:right;
				  }
				  .stock_status .a_office label{
					  width:60% !important;
				  }
				  </style>
                  <h3 class="box-title" style="font-size:20px; font-weight:500; color:#4043A0;">National Stock Status</h3>
                  
                  <div style="float:left; width:100%;">
                  <div class="box-tools pull-right select_box a_office" style="width:50%">
                      <?php echo $this->Form->input('stock_status_office_id', array('id' => 'stock_status_office_id', 'onChange' => 'getStockStatusData()', 'class' => 'form-control user_id office_id5', 'label'=>'Area Office', 'options' => $offices, 'required'=>true, 'empty'=>'---- All ----')); ?>
                  </div>
                  <?php if(!$office_id){ ?>
                    <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
                    <?php echo $this->Form->input('region_office_id5', array('id' => 'region_office_id5','class' => 'form-control region_office_id5', 'onChange' => 'getStockStatusData()', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); ?>
                    </div>
                    <?php } ?>
                  </div>
                
                </div>
             
                <div class="box-body">
                
                    <div class="table-responsive">
                        <table class="table no-margin">
                          <thead style="background:#ccc;">
                            <tr>
                                <th>Product Name</th>
                                <th class="text-right">SO Stock</th>
                                <th class="text-right">ASO Stock</th>
                                <th class="text-right">CWH Stock</th>
                                <th class="text-right">National Stock</th>
                            </tr>
                          </thead>
                          
                          <tbody id="stock_status_data">
                          	<?php  
							foreach($report_products as $report_product){
							$data = $DashboardsController->getStocks($report_product['ReportProductSetting']['product_id'], $office_id);
							?>
                            <tr>
                                <td><?=$report_product['Product']['name']?></td>
                                <td class="text-right"><?=number_format($data['so_stock'])?></td>
                                <td class="text-right"><?=number_format($data['aso_stock'])?></td>
                                <td class="text-right"><?=number_format($data['cwh_stock'])?></td>
                                <td class="text-right"><?=number_format($data['so_stock']+$data['aso_stock']+$data['cwh_stock'])?></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                          
                        </table>
                    </div>
                  
                </div>
                
                
                <script>
				function getStockStatusData()
				{
					var office_id = $("#stock_status_office_id option:selected").val()?$("#stock_status_office_id option:selected").val():0;
					
					var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;
		
					var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;
					
					$.ajax 
					({
						url: '<?= BASE_URL.'admin/dashboards/stockStatusData'?>',
						type: "POST",
						data: dataString,
						beforeSend: function() {$("div#divLoading").addClass('show');},
						success: function(data)
						{	
							var obj = jQuery.parseJSON(data);
							//alert(obj.output);
							$('#stock_status_data').html(obj.output);
							//$("div#divLoading").removeClass('show');
						}
					});
				}
				</script>
                            
            </div>
        </div>
        
		
        
        
	</div>
    
    <div class="row">        
        <div class="col-md-11 col-xs-11">
            <div class="box box-primary data-sync">
                <style>
				.a_office{margin-top:5px;}
                .a_office label{width:43% !important;}
				.a_office select#office_id{width:50% !important;}
                </style>
                <div class="box-header with-border">
                  <h3 class="box-title" style="font-size:20px; font-weight:500;">Data Sync History</h3>
                  
                  <div style="float:left; width:100%;">
                  <div class="pull-right select_box a_office" style="width:50%">
<?php
					  $sync_options=$offices;					  
					  $sync_options['1'] = 'Details List';
					  
					  echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'getUserSyncData()', 'class' => 'form-control user_id office_id6', 'label'=>'Area Office', 'required'=>true, 'empty'=>'Pending Sync(Last 3 days)','options'=>$sync_options)); ?>                      
                  </div>
                  
                  <?php if(!$office_id){ ?>
                    <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
                    <?php echo $this->Form->input('region_office_id6', array('id' => 'region_office_id6','class' => 'form-control region_office_id6', 'onChange' => 'getUserSyncData()', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); ?>
                    </div>
                    <?php } ?>
                  </div>
                </div>
             
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                          
                          <thead>
                            <tr>
                                <th>Name</th>
                                <th>Territory</th>
                                <?php /*?><th>Last Sync Date</th><?php */?>
                                <th>Last Memo Sync</th>
                                <?php /*?><th class="text-center">No. of Memos</th>
                                <th class="text-center">Sync Memos</th>
                                <th class="text-center">Total Amount</th>
                                <th width="15%" class="text-center">Waiting Sync</th><?php */?>
                            </tr>
                          </thead>
                          
                            <tbody id="syncTableList">
                                <?php
								//pr($data_array); 
								foreach($data_array as $so)
								{ 
								//$label = ($so['hours'] > 24 ? 'danger' : 'success');
								$label = (isset($so['last_memo_sync']) && date('Y-m-d') == date('Y-m-d', strtotime($so['last_memo_sync'])) ? 'success' : 'danger');
								
								/* checking the so that not synced last previous two days */
								
								$three_days_pre_date = date('Y-m-d', strtotime('-3 days'));								
								$need_shown = (isset($so['last_memo_sync']) && ($three_days_pre_date > date('Y-m-d', strtotime($so['last_memo_sync']))));
								if($need_shown)
								{
								?>
                                    <tr>
                                        <td><span class="label label-<?=$label?>"><?=$so['name']; ?></span></td>
                                        <td><?=$so['territory']; ?></td>
                                        <?php /*?><td><?=$this->App->datetimeformat($so['time']); ?></td><?php */?>
                                        <td><?=isset($so['last_memo_sync']) ? $this->App->datetimeformat($so['last_memo_sync']) : ''?></td>
                                        
                                        <?php /*?><td class="text-center"><?=$so['total_memo']; ?></td>
                                        <td class="text-center"><?=$so['total_sync_memo']; ?></td>
                                        <td class="text-center"><?=sprintf("%01.2f", $so['total_memo_value']); ?></td>
                                        <td class="text-center"><?=$so['total_waiting_sync_memo']; ?></td><?php */?>
                                    </tr>
								<?php } } ?>
                            </tbody>
                          
                        </table>
                    </div>
                </div>
            </div>
        </div>
	</div>

	<script>
    function getUserSyncData(){
        
        //alert(111);
        
        var office_id = $("#office_id option:selected").val()?$("#office_id option:selected").val():0;
        
        var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;
		
		var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;
        
        $.ajax 
        ({
            url: '<?= BASE_URL.'admin/dashboards/userSyncData'?>',
            type: "POST",
            data: dataString,
            beforeSend: function() {$("div#divLoading").addClass('show');},
            //complete: function() {$("#confirmWait_"+pro_id).hide()},
            success: function(msg)
            {	
                //alert(msg);
                $('#syncTableList').html(msg);
                //$("div#divLoading").removeClass('show');
            }
        });
    }
    </script>
    
    
    
    
    


   

</section>



<script>
$('.region_office_id2').selectChain({
	target: $('.office_id2'),
	value:'name',
	url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
	type: 'post',
	data:{'region_office_id': 'region_office_id2' }
});
$('.region_office_id3').selectChain({
	target: $('.office_id3'),
	value:'name',
	url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
	type: 'post',
	data:{'region_office_id': 'region_office_id3' }
});
$('.region_office_id4').selectChain({
	target: $('.office_id4'),
	value:'name',
	url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
	type: 'post',
	data:{'region_office_id': 'region_office_id4' }
});
$('.region_office_id5').selectChain({
	target: $('.office_id5'),
	value:'name',
	url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
	type: 'post',
	data:{'region_office_id': 'region_office_id5' }
});
$('.region_office_id6').selectChain({
	target: $('.office_id6'),
	value:'name',
	url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
	type: 'post',
	data:{'region_office_id': 'region_office_id6' }
});
</script>




<?php
echo $this->Html->script("bower_components/jquery-ui/jquery-ui.min"); 
echo $this->Html->script("bower_components/raphael/raphael.min");
//echo $this->Html->script("bower_components/morris.js/morris.min"); 

echo $this->Html->script("bower_components/jquery-sparkline/dist/jquery.sparkline.min");

echo $this->Html->script("plugins/jvectormap/jquery-jvectormap-1.2.2.min");
echo $this->Html->script("plugins/jvectormap/jquery-jvectormap-world-mill-en");

echo $this->Html->script("bower_components/jquery-knob/dist/jquery.knob.min");

echo $this->Html->script("bower_components/moment/min/moment.min");
echo $this->Html->script("bower_components/bootstrap-daterangepicker/daterangepicker");

echo $this->Html->script("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js");


echo $this->Html->script("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min");
echo $this->Html->script("bower_components/jquery-slimscroll/jquery.slimscroll.min");

echo $this->Html->script("bower_components/fastclick/lib/fastclick");
//echo $this->Html->script("adminlte.min");
echo $this->Html->script("dist/js/adminlte.min");
echo $this->Html->script("dist/js/pages/dashboard");
echo $this->Html->script("dist/js/demo");

//test dashboard 1
echo $this->Html->script("bower_components/Chart.js/Chart");
//echo $this->Html->script("dist/js/pages/dashboard2");


echo $this->Html->script("highcharts");
echo $this->Html->script("exporting");

echo $this->fetch('script');
?>

<script type="text/javascript">
	$('.daterange1').daterangepicker({
		
		//var start = moment().subtract(29, 'days');
   		//var end = moment();

		//alert(start);
		
		/*ranges   : {
		  'Today'       : [moment(), moment()],
		  'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		  'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
		  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		  'This Month'  : [moment().startOf('month'), moment().endOf('month')],
		  'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},*/
		
		locale: {
		  format: 'DD-MM-YYYY'
		},
		//"singleDatePicker": true,
		//minDate: '<?=date('01/07/Y')?>',
		//maxDate: '<?=date('d/m/Y')?>',
		/*datepickerOptions: {
			minDate: '2017-07-01',
			maxDate: '2018-06-30',
			numberOfMonths : 3,
			changeMonth: true,
			changeYear: false
		}*/
		<?php if($this->params['url'] && $this->params['url']['date_start']){ ?>
			startDate: '<?=date('d/m/Y', strtotime($this->params['url']['date_start']))?>',
			endDate  : '<?=date('d/m/Y', strtotime($this->params['url']['date_end']))?>'
		<?php }else{ ?>
			startDate: moment().startOf('month'),
			endDate  : moment().endOf('month')
		<?php } ?>
		
	  }, function (start, end) {
		//window.alert('You chose: ' + start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		
		//alert(startDate);
		
		
		var office_id = $("#product_achievement_target_office_id option:selected").val();
		
		//alert(office_id);
		
		var dataString = 'date_start='+ start.format('YYYY-MM-DD') + '&date_end=' + end.format('YYYY-MM-DD') + '&office_id=' + office_id;
		
		$.ajax 
		({
			url: '<?= BASE_URL.'admin/dashboards/ajaxProductSalesAndTarget'?>',
			type: "POST",
			data: dataString,
			//beforeSend: function() {$("#message").html("<img id='checkmark' src='images/loading.gif' />")},
			//complete: function() {$("#confirmWait_"+pro_id).hide()},
			success: function(msg)
			{	
				//alert(msg);
				$('#sales_target').html(msg);
				
			}
		});
		
		//window.location.href = '<?=BASE_URL?>admin/dashboards/?date_start=' + start.format('YYYY-MM-DD') + '&date_end=' + end.format('YYYY-MM-DD');
	});
	
	
	function ProductSalesAndTarget()
	{
		//alert(office_id);
		
		var office_id = $("#product_achievement_target_office_id option:selected").val()?$("#product_achievement_target_office_id option:selected").val():0;
		
		var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;
		
		var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;
				
		$.ajax 
		({
			url: '<?= BASE_URL.'admin/dashboards/ajaxProductSalesAndTarget'?>',
			type: "POST",
			data: dataString,
			beforeSend: function() {$("div#divLoading").addClass('show');},
			//complete: function() {$("#confirmWait_"+pro_id).hide()},
			success: function(msg)
			{	
				//alert(msg);
				$('#sales_target').html(msg);
				//$("div#divLoading").removeClass('show');
				
			}
		});
		
	}
	
</script>


<script>

$( document ).ready(function() 
{
      
	  
	    
	  // Get context with jQuery - using jQuery's .get() method.
	  var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
	  var pieChart       = new Chart(pieChartCanvas);
	  var PieData        = [
    
	
	  <?php 
	  $i=0;
	  foreach($offices as $key => $value)
	  {
		$value = str_replace('Sales ', '', $value);
		if($key!=14)
		{
			if($i==0){$color='f56954';}
			if($i==1){$color='f39c12';}
			if($i==2){$color='00c0ef';}
			if($i==3){$color='0073b7';}
			if($i==4){$color='222222';}
			if($i==5){$color='4043A0';}
			if($i==6){$color='00a65a';}
			
			if($i==7){$color='001f3f';}
			if($i==8){$color='39cccc';}
			if($i==9){$color='3d9970';}
			if($i==10){$color='01ff70';}
			if($i==11){$color='ff851b';}
			if($i==12){$color='f012be';}
			if($i==13){$color='932ab6';}
			if($i==14){$color='85144b';}
			if($i==15){$color='888';}
			if($i==16){$color='d2d6de';}
	  ?>
	  {
      value    : <?=$DashboardsController->getAreaOfficeSales($key, 0, $region);?>,
      color    : '#<?=$color?>',
      highlight: '#<?=$color?>',
      label    : '<?=$value?>'
      },
	  <?php 
	  	$i++; 
		}
	  } 
	  ?>
    
  ];
  var pieOptions     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio  : false,
    // String - A legend template
    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %>% (<%=label%>)'
  };
  // Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  // -----------------
  // - END PIE CHART -
  // -----------------
  
  
  	<?php if(!$office_id){ ?>
  
  	  //Area Office Pie
  	  <?php foreach($all_offices_list as $region_id => $r_o_list){ ?>
  	  //region_dhaka_offices pie
	  var pieChartCanvas = $('#pieChart_region_<?=$region_id?>').get(0).getContext('2d');
	  var pieChart       = new Chart(pieChartCanvas);
	  var PieData        = [
	
	  <?php 
	  $i=0;
	  foreach($r_o_list as $key => $value)
	  {
		$value = str_replace('Sales ', '', $value);
		if($key!=14)
		{
			if($i==0){$color='f56954';}
			if($i==1){$color='f39c12';}
			if($i==2){$color='00c0ef';}
			if($i==3){$color='0073b7';}
			if($i==4){$color='222222';}
			if($i==5){$color='4043A0';}
			if($i==6){$color='00a65a';}
	  ?>
	  {
      value    : <?=$DashboardsController->getAreaOfficeSales($key, 0, 38);?>,
      color    : '#<?=$color?>',
      highlight: '#<?=$color?>',
      label    : '<?=$value?>'
      },
	  <?php 
	  	$i++; 
		}
	  } 
	  ?>
    
	  ];
	  var pieOptions     = {
		// Boolean - Whether we should show a stroke on each segment
		segmentShowStroke    : true,
		// String - The colour of each segment stroke
		segmentStrokeColor   : '#fff',
		// Number - The width of each segment stroke
		segmentStrokeWidth   : 1,
		// Number - The percentage of the chart that we cut out of the middle
		percentageInnerCutout: 50, // This is 0 for Pie charts
		// Number - Amount of animation steps
		animationSteps       : 100,
		// String - Animation easing effect
		animationEasing      : 'easeOutBounce',
		// Boolean - Whether we animate the rotation of the Doughnut
		animateRotate        : true,
		// Boolean - Whether we animate scaling the Doughnut from the centre
		animateScale         : false,
		// Boolean - whether to make the chart responsive to window resizing
		responsive           : true,
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio  : false,
		// String - A legend template
		legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
		// String - A tooltip template
		tooltipTemplate      : '<%=value %>% (<%=label%>)'
	  };
	  // Create pie or douhnut chart
	  // You can switch between pie and douhnut using the method below.
	  pieChart.Doughnut(PieData, pieOptions);
  	  
	  <?php } ?>	  
	  
  	<?php } ?>  
	  
	  
	  //Area Territory Pie
	  <?php foreach($all_territories_list as $tt_office_id => $a_t_list){ ?>
  	  
	  var pieChartCanvas = $('#pieChart_office_<?=$tt_office_id?>').get(0).getContext('2d');
	  var pieChart       = new Chart(pieChartCanvas);
	  var PieData        = [
	
	  <?php 
	  $i=0;
	  foreach($a_t_list as $key => $value)
	  {
		$value = str_replace('Sales ', '', $value);
		if($key!=14)
		{
			if($i==0){$color='f56954';}
			if($i==1){$color='f39c12';}
			if($i==2){$color='00c0ef';}
			if($i==3){$color='0073b7';}
			if($i==4){$color='222222';}
			if($i==5){$color='4043A0';}
			if($i==6){$color='00a65a';}
			
			if($i==7){$color='001f3f';}
			if($i==8){$color='39cccc';}
			if($i==9){$color='3d9970';}
			if($i==10){$color='01ff70';}
			if($i==11){$color='ff851b';}
			if($i==12){$color='f012be';}
			if($i==13){$color='932ab6';}
			if($i==14){$color='85144b';}
			if($i==15){$color='888';}
			if($i==16){$color='d2d6de';}
	  ?>
	  {
      value    : <?=$DashboardsController->getAreaOfficeTerritorySales($key, 0, $tt_office_id);?>,
      color    : '#<?=$color?>',
      highlight: '#<?=$color?>',
      label    : "<?=$value?>"
      },
	  <?php 
	  	$i++; 
		}
	  } 
	  ?>
    
	  ];
	  var pieOptions     = {
		// Boolean - Whether we should show a stroke on each segment
		segmentShowStroke    : true,
		// String - The colour of each segment stroke
		segmentStrokeColor   : '#fff',
		// Number - The width of each segment stroke
		segmentStrokeWidth   : 1,
		// Number - The percentage of the chart that we cut out of the middle
		percentageInnerCutout: 50, // This is 0 for Pie charts
		// Number - Amount of animation steps
		animationSteps       : 100,
		// String - Animation easing effect
		animationEasing      : 'easeOutBounce',
		// Boolean - Whether we animate the rotation of the Doughnut
		animateRotate        : true,
		// Boolean - Whether we animate scaling the Doughnut from the centre
		animateScale         : false,
		// Boolean - whether to make the chart responsive to window resizing
		responsive           : true,
		// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		maintainAspectRatio  : false,
		// String - A legend template
		legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
		// String - A tooltip template
		tooltipTemplate      : '<%=value %>% (<%=label%>)'
	  };
	  // Create pie or douhnut chart
	  // You can switch between pie and douhnut using the method below.
	  pieChart.Doughnut(PieData, pieOptions);
  	  
	  <?php } ?>
	  
  $('.offices_pie').hide();
  $('#all_offices_pie').show();
  
});




</script>


<script type="text/javascript">
var chart;
$(document).ready(function() {

	chart = new Highcharts.Chart({
	
	//Highcharts.chart('container', {
		chart: {
			renderTo: 'container',
			type: 'column'
		},
		title: {
			text: ''
		},
		xAxis: {
			categories: ['<?=date('F', time())?>', '<?=date('F', strtotime('-1 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-2 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-3 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-4 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-5 month', strtotime(date('Y-m-01'))))?>'],
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Total sales Revenue (Million)'
			},
			stackLabels: {
				enabled: true,
				style: {
					fontWeight: 'bold',
					color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				}
			},
			labels: {
				format: '{value} M',
				style: {
					color: Highcharts.getOptions().colors[1]
				}
			},
		},
		legend: {
			align: 'right',
			x: -30,
			verticalAlign: 'top',
			y: 25,
			floating: true,
			backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
			borderColor: '#CCC',
			borderWidth: 1,
			shadow: false
		},
		tooltip: {
			headerFormat: '<b>{point.x}</b><br/>',
			//pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
			pointFormat: '{series.name}: Tk. {point.y}',
			valueSuffix: '°C'
		},
		plotOptions: {
			column: {
				stacking: 'normal',
			   /* dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				}*/
			}
		},
		series: [{
			name: 'Total Sales',
			data: [<?=$bar_series?>],
			tooltip: {
				valueSuffix: 'M'
			}
		}, 
		/*{
			name: 'Jane',
			data: [2, 2, 3, 2, 1]
		}, 
		{
			name: 'Joe',
			data: [3, 4, 4, 2, 5]
		}*/
		
		]
	});

})
</script>


<script>
function getSalesTrendData()
{
	var office_id = $("#sales_trend_office_id option:selected").val()?$("#sales_trend_office_id option:selected").val():0;
	
	var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;
		
	var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;
	
	$.ajax 
	({
		url: '<?= BASE_URL.'admin/dashboards/salesTrendData'?>',
		type: "POST",
		data: dataString,
		beforeSend: function() {$("div#divLoading").addClass('show');},
		success: function(data)
		{	
			var obj = jQuery.parseJSON(data);

			var options = {
				chart: {
					renderTo: 'container',
					type: 'column'
				},
				title: {
					text: ''
				},
				xAxis: {
					categories: ['<?=date('F', time())?>', '<?=date('F', strtotime('-1 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-2 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-3 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-4 month', strtotime(date('Y-m-01'))))?>', '<?=date('F', strtotime('-5 month', strtotime(date('Y-m-01'))))?>'],
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Total sales Revenue (Million)'
					},
					stackLabels: {
						enabled: true,
						style: {
							fontWeight: 'bold',
							color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
						}
					},
					labels: {
						format: '{value} M',
						style: {
							color: Highcharts.getOptions().colors[1]
						}
					},
				},
				legend: {
					align: 'right',
					x: -30,
					verticalAlign: 'top',
					y: 25,
					floating: true,
					backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
					borderColor: '#CCC',
					borderWidth: 1,
					shadow: false
				},
				tooltip: {
					headerFormat: '<b>{point.x}</b><br/>',
					pointFormat: '{series.name}: Tk. {point.y}',
					valueSuffix: '°C'
				},
				plotOptions: {
					column: {
						stacking: 'normal',
					}
				},
				series: []
				
			}

			
			var series = { 
							data: []
						};
			
			series.name = 'Total Sales';
			
			series.data.push(parseFloat(obj.m_1_total_amount));
			series.data.push(parseFloat(obj.m_2_total_amount));
			series.data.push(parseFloat(obj.m_3_total_amount));
			series.data.push(parseFloat(obj.m_4_total_amount));
			series.data.push(parseFloat(obj.m_5_total_amount));
			series.data.push(parseFloat(obj.m_6_total_amount));
			
			options.series.push(series);
			
			var chart = new Highcharts.Chart(options);
			
			//$("div#divLoading").removeClass('show');
						
		},
		cache: false
	});
}
</script>





