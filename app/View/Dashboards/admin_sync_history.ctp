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
    /*setTimeout(function () {
        getTopBoxesData();
      }, 500000);*/
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

 .loader
 {
    background-color: #666;
    height: 100%;
    width:100%;
    z-index: 100;
    position: absolute;
    top: 0;
    bottom: 0;
    opacity: 0.4;
 }
 .loader_icon{
  position: absolute;
  top:46%;
  left: 46%;
  z-index: 101;
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


<section class="content admin">	
  



<div class="row">        
  <div class="col-md-12 col-xs-12">
    <div class="box box-primary data-sync">
      <div class="loader user_sync_loader hide">
        <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
      </div>
      <style>
        .a_office{margin-top:5px;}
        .a_office label{width:43% !important;}
        .a_office select#office_id{width:50% !important;}
      </style>
      <div class="box-header box-header1 with-border">
        <!--h3 class="box-title" style="font-size:20px; font-weight:500;">Data Sync History</h3-->

        <h3 class="">          	
        Data Sync History 
       
        <div class="pull-right csv_btn">
				<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'escape' => false, 'id' => 'download_xl')); ?>
			</div>

       
        <div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;display:<?php if(!$office_id){ echo 'block';} else{ echo 'none'; }?>;">
          <?php echo $this->Form->input('top_boxes_office_id', array('id' => 'top_boxes_office_id', 'onChange' => 'getUserSyncData()', 'class' => 'form-control user_id office_id', 'options' => $offices, 'label'=>'Area Office', 'required'=>true, 'empty'=> '---- All ----' ,'default'=>$office_id)); ?>
        </div>
        
        
        <div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;display:<?php if(!$region){ echo 'block';} else{ echo 'none'; }?>;">
         <?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'onChange' => 'getUserSyncData()', 'class' => 'form-control region_office_id','required'=>true,'empty'=>'---- Head Office ----', 'options' => $region_offices,'default'=>$region)); ?>
       </div>
       
     
             
           </h3>

			
    
        <div style="float:left; width:100%;">
          <div class="pull-right select_box a_office" style="width:50%">
            <?php
            $sync_options=$offices;					  
            $sync_options['1'] = 'Details List';
            
            //echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'getUserSyncData()', 'class' => 'form-control user_id office_id6', 'label'=>'Area Office', 'required'=>true, 'empty'=>'Pending Sync(Last 3 days)','options'=>$sync_options)); ?>                      
          </div>
          
          <?php if(!$office_id){ ?>
          <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
            <?php //echo $this->Form->input('region_office_id6', array('id' => 'region_office_id6','class' => 'form-control region_office_id6', 'onChange' => 'getUserSyncData()', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); ?>
          </div>
          <?php } ?>
        </div>
      </div>
      
      <div class="box-body" style="min-height: 150px;">

        <div class="table-responsive" id="synclist">
          <table class="table no-margin">

            <thead>
              <tr>
                <th>Region</th>
                <th>Office</th>
                <th>Territory</th>
                <th>Sales Officer</th>
                 <th>Last Memo Sync</th>
                <th>Last Sync Time</th>
              </tr>
            </thead>

            <tbody id="syncTableList">

            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function(){
      getUserSyncData();
      /*setInterval(function () {
          getTopBoxesData();
        }, 500000);*/
    });
    $('#region_office_id').selectChain({
      target: $('#top_boxes_office_id'),
      value:'name',
      url: '<?= BASE_URL.'esales_reports/get_office_list';?>',
      type: 'post',
      data:{'region_office_id': 'region_office_id' }
    });
  
</script>
  <script>
    function getUserSyncData(){

      /*var office_id = $("#office_id option:selected").val()?$("#office_id option:selected").val():0;

      var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;*/
      var office_id = $("#top_boxes_office_id option:selected").val()?$("#top_boxes_office_id option:selected").val():0;

      var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;

      var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;

      $.ajax 
      ({
        url: '<?= BASE_URL.'admin/dashboards/userSyncData'?>',
        type: "POST",
        data: dataString,
        beforeSend: function() {
          $(".user_sync_loader").removeClass('hide');
          $(".user_sync_loader").addClass('show');
        },
        success: function(msg)
        {	
          $('#syncTableList').html(msg);
          $(".user_sync_loader").removeClass('show');
          $(".user_sync_loader").addClass('hide');
        }
      });
    }

    $("#download_xl").click(function(e) {
			e.preventDefault();
			var html = $("#synclist").html();
			var blob = new Blob([html], {
				type: 'data:application/vnd.ms-excel'
			});
			var downloadUrl = URL.createObjectURL(blob);
			var a = document.createElement("a");
			a.href = downloadUrl;
			a.download = "user_sync.xls";
			document.body.appendChild(a);
			a.click();
		});

  </script>
    
    


  </section>




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



