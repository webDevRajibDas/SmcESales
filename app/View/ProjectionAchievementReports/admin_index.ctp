<?php
App::import('Controller', 'ProjectionAchievementReportsController');
$ProjectionAchievementController = new ProjectionAchievementReportsController;
?>


<style>
.search .radio label {
    width: auto;
  float:none;
  padding:0px 15px 0px 5px;
  margin:0px;
}
.search .radio legend {
    float: left;
    margin: 5px 20px 0 0;
    text-align: right;
    width: 25%;
  display: inline-block;
    font-weight: 700;
  font-size:14px;
  border-bottom:none;
}
#market_list .checkbox label{
  padding-left:0px;
  width:auto;
}
#market_list .checkbox{
  width:30%;
  float:left;
  margin:1px 0;
}
body .td_rank_list .checkbox{
  width:auto !important;
  padding-left:20px !important;
}
.radio input[type="radio"], .radio-inline input[type="radio"]{
    margin-left: 0px;
    position: relative;
  margin-top:8px;
}
.search label {
    width: 25%;
}
#market_list{
  padding-top:5px;
}
.market_list2 .checkbox{
  width:21% !important;
}
.market_list3 .checkbox{
  width:20% !important;
}




.outlet_category{
  float:right;
  width:85%;
  padding-left:10%;
  border:#c7c7c7 solid 1px;
  height:100px;
  overflow:auto;
  margin-right:5%;
  padding-top:5px;
}

.outlet_category2{
  padding-left:3%;
  height:118px;
}
.outlet_category3{
  width:92%;
  margin-right:3%;
  height:115px;
}
.outlet_category .checkbox{
  float:left;
  width:25% !important;
}
.outlet_category3 .checkbox{
  float:left;
  width:25% !important;
}
.label_title{
  float:right;
  width:85%;
  background:#c7c7c7;
  margin:0px;
  padding:1px 0px;
  text-align:center;
  color:#000;
  margin-right:5%;
  font-weight:bold;
  font-size:90%;
}
.pro_label_title{
  width:92%;
  margin-right:3%;
}
.outlet_category label{
  width:auto;
}


.outlet_category .form-control{
  width:55% !important;
}
.outlet_category2 .input{
  padding-bottom:3px;
  float:left;
  width:100%;
}
.outlet_category2 label{
  width:32%;
}
.selection_box{
  display:none;
}
.search .form-control {
     width: 65%; 
}
</style>


<div class="row">
    <div class="col-xs-12">
    <div class="box box-primary">
        
        
        
      <div class="box-header">
        <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Projection and Achievement Analysis Report'); ?></h3>
        <?php /*?><div class="box-tools pull-right">
          <?php if($this->App->menu_permission('nationalSalesReports', 'admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New ProjectionAchievement Setting'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } ?>
        </div><?php */?>
      </div>
            
              
      <div class="box-body">
        
                <div class="search-box">
          <?php echo $this->Form->create('ProjectionAchievementReports', array('role' => 'form', 'action'=>'index')); ?>
          <table class="search">
                       
                         <?php if($office_parent_id==0){ ?>
                         <tr>
              <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id', 'required'=>false, 'empty'=>'---- Head Office ----', 'options' => $region_offices,)); ?></td>
              <td></td>             
            </tr>
                        <?php } ?>
                        <?php if($office_parent_id==14){ ?>
                         <tr>
              <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id','class' => 'form-control region_office_id', 'required'=>false, 'options' => $region_offices,)); ?></td>
              <td></td>             
            </tr>
                        <?php } ?>
                        
                        
                         <?php if($office_parent_id==0 || $office_parent_id==14){ ?>
                         <tr>
              <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id','class' => 'form-control office_id','required'=>false,'empty'=>'---- All ----')); ?></td>
              <td></td>             
            </tr>
                        <?php }else{ ?>
                        <tr>
              <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id','class' => 'form-control office_id','required'=>false,)); ?></td>
              <td></td>             
            </tr>
                        <?php } ?>
            
            
             <tr>
                          <td>
              <?php echo $this->Form->input('territoty_selection', array('legend'=>'Type :', 'class' => 'territoty_selection', 'type' => 'radio', 'default' => '1', 'options' => $territoty_selection, 'required'=>true,'disabled'=>true));  ?></td>    
                            <td></td>
            </tr>
            
            
            
                                  
            <tr>
              <td><?php echo $this->Form->input('territory_id', array('id' => 'territory_id','class' => 'form-control territory_id', 'required'=>false, 'empty'=>'---- All ----')); ?></td>
              <td>
                            <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
              <?php echo $this->Form->input('qumulative', array('label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox',  'options' => $qumulatives, 'required'=>false));  ?>
                            </div>
                            </td>             
            </tr>
                        
                        <tr>
              <td width="50%"><?php echo $this->Form->input('fiscal_year_id', array('label' => 'Fiscal Year :', 'id' => 'fiscal_year_id', 'options' => $fiscal_years, 'class' => 'form-control fiscal_year_id','required'=>false,)); ?></td>
              <td></td>             
            </tr>
                        
                        <tr>
                        <td colspan="2">
                        <label style="float:left; width:12.5%;"></label>
                        <div id="market_list" class="input select market_list2" style="float:left; width:80%; padding-left:20px;">
                            
                            <div class="selection">       
                            <?php echo $this->Form->input('q_list', array('id' => 'q_list', 'label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options'=> $q_lists)); ?>
                            </div>
                        </div>
                        </td>
                      </tr>
                        
                        <tr>
                          <td>
              <?php echo $this->Form->input('columns', array('legend'=>'Columns :', 'class' => 'columns', 'type' => 'radio', 'default' => 'product', 'options' => $columns, 'required'=>true));  ?></td>    
                            <td></td>
            </tr>
                        
                        <tr>
                            <td colspan="2">
                            <div class="input select" style="float:left; width:100%; padding-bottom:20px;">
                              <div id="product" class="selection_box" style="float:left; width:100%; display:block;">
                                  
                                    <div style="margin:auto; width:90%;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall" />
                                    <label for="checkall" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label></div>
                                    
                                    <p class="label_title pro_label_title">Product Selection</p>
                                    <div id="market_list" class="product_selection outlet_category outlet_category3">
                                    <?php echo $this->Form->input('product_id', array('label'=>false, 'class' => 'checkbox product_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options'=> $product_list)); ?>
                                    </div>
                                </div>
                                
                                <div id="brand" class="selection_box" style="float:left; width:100%;">
                                  <div style="margin:auto; width:90%;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                    <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label></div>
                                    <p class="label_title pro_label_title">Brand Selection</p>
                                    <div id="market_list" class="brand_selection outlet_category outlet_category3">
                                    <?php echo $this->Form->input('brand_id', array('label'=>false, 'class' => 'checkbox brand_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options'=> $brands)); ?>
                                    </div>
                                </div>
                                
                                <div id="category" class="selection_box" style="float:left; width:100%;">
                                  <div style="margin:auto; width:90%;">
                                    <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall3" />
                                    <label for="checkall3" style="float:none; width:auto;  cursor:pointer;">Select All / Unselect All</label></div>
                                    <p class="label_title pro_label_title">Category Selection</p>
                                    <div id="market_list" class="category_selection outlet_category outlet_category3">
                                    <?php echo $this->Form->input('product_category_id', array('label'=>false, 'class' => 'checkbox product_category_id', 'fieldset' => false, 'multiple' => 'checkbox', 'options'=> $categories)); ?>
                                    </div>
                                </div>
                            </div>
                            
                            </td>
                        </tr>
                        
                        <tr>
                        <td colspan="2">
                        <label style="float:left; width:12.5%;">Indicator : </label>
                        <div id="market_list" class="input select market_list3" style="float:left; width:80%; padding-left:20px;">
                            
                            <div class="selection">       
                            <?php echo $this->Form->input('indicators', array('id' => 'indicators', 'label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options'=> $indicators)); ?>
                            </div>
                        </div>
                        </td>
                      </tr>

                      <tr>
                          <td>
              <?php echo $this->Form->input('indicator_unit', array('legend'=>'Indicator Unit :', 'class' => 'indicator_unit', 'type' => 'radio', 'default' => '1', 'options' => $indicator_unit, 'required'=>true));  ?></td>    
                            <td></td>
            </tr>


                        
                                                
            
            <tr align="center">
              <td colspan="2">
                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div'=>false, 'name'=>'submit')); ?>                                                            
                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                                
                               
                                
              </td>           
            </tr>
          </table>
                      
                    
          <?php echo $this->Form->end(); ?>
        </div>
                
                
                
                
                
                
                <?php if(!empty($request_data)){                   
                    $sub_col_num=count($sub_columns); 
                  ?>
                                
                 <div id="content" style="width:90%; margin:0 5%;">
                 
                    <style type="text/css">
          .table-responsive { color: #333; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; line-height: 1.42857; }
          .report_table{ font-size:12px; }
          .qty_val{ width:125px; margin:0; float:left; text-transform:capitalize;}
          .val{ border-right:none; }
          p{ margin:2px 0px; }
          .bottom_box{ float:left; width:33.3%; text-align:center; }
          td, th {padding: 5px;}
          table { border-collapse: collapse; border-spacing: 0; }
          .titlerow, .totalColumn{ background:#f1f1f1; }
          .report_table {margin-bottom: 18px; max-width: 100%; width: 100%;}
          .table-responsive {min-height: 0.01%;overflow-x: auto;}
          </style>
                    
                    
                <div class="table-responsive">
                    
                      <div class="pull-right csv_btn" style="padding-top:20px;">
                         <a class="btn btn-primary" id="download_xl">
                           Download XLS
                         </a>                            
                        </div>
                        
                        <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both;">
                            <h2 style="margin:2px 0;">Social Marketing Company</h2>
                            <h3 style="margin:2px 0;">Projection and Achievement Analysis Report</h3>
                            <h3 style="margin:2px 0;"><?php echo (isset($summery_area_name))?$summery_area_name:"";?></h3>
                            <p>
                                
                            </p>
                        </div>   
                         
                               <?php
                               if(!$details)
                               {
                                   ?>
                    
                    <div style="float:left; width:100%; height:400px; overflow:scroll;"> 
                                                       
                                <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                                                              
                                    <tr class="titlerow">
                                        <th rowspan="2">Products</th>
                                        <?php 
                                        foreach ($columns_list as $ck => $cv) {
                                         echo "<th colspan='".$sub_col_num."'>$cv</th>";
                                        }

                                        ?>
                                                                             
                                    </tr>

                                    <tr class="titlerow">
                                       
                                        <?php 
                                        foreach ($columns_list as $ck => $cv) {
                                          foreach ($sub_columns as $sck => $scv) {
                                           echo "<th>$scv</th> ";
                                          }
                                                                                
                                        }

                                        ?>
                                                                             
                                    </tr>
                                  
                                    <?php 
                                    echo $rows_data; 
                                                                          
                                    ?>
                                    
                                    
                                     
                              </table>
                            </div>
                    
                    
                    <?php 
                                   
                               }
                               else 
                               {
                                   ?>
                    
                    <div style="float:left; width:100%; height:400px; overflow:scroll;"> 
                        
                        <?php 
                        
                        foreach ($tables_data as $tv) {
                            ?>
                        <h3><?php echo $tv['area_name'];?></h3>
                         <table class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                                                              
                                    <tr class="titlerow">
                                        <th rowspan="2">Products</th>
                                        <?php 
                                        foreach ($columns_list as $ck => $cv) {
                                         echo "<th colspan='".$sub_col_num."'>$cv</th>";
                                        }

                                        ?>
                                                                             
                                    </tr>

                                    <tr class="titlerow">                                       
                                        <?php 
                                        foreach ($columns_list as $ck => $cv) {
                                          foreach ($sub_columns as $sck => $scv) {
                                           echo "<th>$scv</th> ";
                                          }
                                                                                
                                        }

                                        ?>
                                                                             
                                    </tr>
                                  
                                    <?php 
                                    echo $tv['data']; 
                                                                          
                                    ?>
                                    
                                    
                                     
                              </table>
                            
                         <?php 
                           }                        
                        ?>
                                                       
                               
                            </div>
                    
                    
                    
                               <?php 
                               }
                               ?>                                                                                                                                                    
                            
                                                   
                        <div style="float:left; width:100%; padding:100px 0 50px;">
                            <div class="bottom_box">
                                Prepared by:______________ 
                            </div>
                            <div class="bottom_box">
                                Checked by:______________ 
                            </div>
                            <div class="bottom_box">
                                Signed by:______________
                            </div>      
                        </div>
                        
                    </div>
                    
                </div>
                
                <?php } ?>
                                
                
                
      </div>      
    </div>
  </div>
</div>



<script>
$('.region_office_id').selectChain({
  target: $('.office_id'),
  value:'name',
  url: '<?= BASE_URL.'ProjectionAchievement_reports/get_office_list';?>',
  type: 'post',
  data:{'region_office_id': 'region_office_id' }
});
$('.region_office_id').change(function () {
  $('#territory_id').html('<option value="">---- All ----');
});
$('.office_id').selectChain({
  target: $('.territory_id'),
  value:'name',
  url: '<?= BASE_URL.'sales_people/get_territory_list'?>',
  type: 'post',
  data:{'office_id': 'office_id' }
});
</script>



<script>
$(document).ready(function() {
  $("input[type='checkbox']").iCheck('destroy');
  $("input[type='radio']").iCheck('destroy');
  
  $('#checkall').click(function() {
    var checked = $(this).prop('checked');
    $('.product_selection').find('input:checkbox').prop('checked', checked);
  });
  
  $('#checkall2').click(function() {
    var checked = $(this).prop('checked');
    $('.brand_selection').find('input:checkbox').prop('checked', checked);
  });
  
  $('#checkall3').click(function() {
    var checked = $(this).prop('checked');
    $('.category_selection').find('input:checkbox').prop('checked', checked);
  });
  
});


$(document).ready(function(){
  
  
  var columns = $('.columns:checked').val();
  $('.selection_box').hide();
  if (columns == 'brand') 
  {
    $('#brand').show();
  }
  else if (columns == 'category') 
  {
    $('#category').show();
  }
  else
  {
    $('#product').show();
  }
  
  $('.columns').change(function() {
    //alert(this.value);
    
    if (this.value == 'product' || this.value == 'category' || this.value == 'brand') {
      //$('.selection').prop("checked", false);
      //$('.selection').prop('checked', false);
      //alert(111);
      $('.product_selection').find('input:checkbox').prop('checked', false);
      $('.brand_selection').find('input:checkbox').prop('checked', false);
      $('.category_selection').find('input:checkbox').prop('checked', false);
      
      $('#checkall').prop('checked', false);
      $('#checkall2').prop('checked', false);
      $('#checkall3').prop('checked', false);
    }
    
    $('.selection_box').hide();
        if (this.value == 'brand') 
    {
            $('#brand').show();
        }
        else if (this.value == 'category') 
    {
            $('#category').show();
        }
    else
    {
      $('#product').show();
    }
    });

  /* onsubmit form check the required checkbox start */

  $( "#ProjectionAchievementReportsIndexForm" ).submit(function( event ) {   

  var checked_indicator = $("[name='data[ProjectionAchievementReports][indicators][]']:checked").length;    

  //var checked_indicator_unit=$("[name='data[ProjectionAchievementReports][indicator_unit][]']:checked").length;

      if(!checked_indicator)
      {
        alert("Please select Indicator");
        $("div#divLoading_default").removeClass('show');
        return false;
      }
      /*
      else if(!checked_indicator_unit)
      {
        alert("Please select Indicator Unit");
        $("div#divLoading_default").removeClass('show');
        return false;
      }
      */

    });

  /* onsubmit form check the required checkbox end */
  
});
</script>




<script>
  function PrintElem(elem)
  {
    var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

    //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('<html><head><title></title><style>.csv_btn{display:none;}</style>');
    mywindow.document.write('</head><body>');
    //mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    //mywindow.close();

    return true;
  }

              $("#download_xl").click(function(e){
              e.preventDefault();
              var html = $("#content").html();
                            
                            var blob = new Blob([html], { type: 'data:application/vnd.ms-excel' });
                            var downloadUrl = URL.createObjectURL(blob);
                            var a = document.createElement("a");
                            a.href = downloadUrl;
                            a.download = "downloadFile.xls";
                            document.body.appendChild(a);
                            a.click();
                          });
</script>