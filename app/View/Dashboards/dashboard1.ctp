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
  .fixed .content-wrapper,
  .fixed .right-side {
    padding-top: 0px !important;
  }

  .box {
    min-height: auto;
  }

  .box .datepicker-inline,
  .box .datepicker-inline .datepicker-days,
  .box .datepicker-inline>table,
  .box .datepicker-inline .datepicker-days>table {
    width: 100%;
  }

  .small-box h3 {
    font-size: 28px;
  }

  /*  .top-boxes .col-lg-2 {
    width: 19.99%;
  } */

  .details-box {
    padding: 5px 0 0;
  }

  .top-boxes .small-box {
    margin-bottom: 0px;
  }

  .top-boxes {
    margin-bottom: 20px;
  }

  .box-header h3 {
    margin-top: 0px;
    font-size: 20px;
  }

  .small-box .inner {
    text-align: center;
    margin-bottom: 2px;
    padding: 0px;
  }

  .small-box .inner small {
    color: #fff;
  }

  .small-box .icon {
    font-size: 70px;
    bottom: 10px;
  }

  .small-box .icon:hover {
    font-size: 75px;
  }

  .small-box .icon:hover i {
    font-size: 75px;
  }

  .small-box .inner .col-lg-2,
  .small-box .inner .col-lg-2 h3,
  .small-box .inner .col-lg-2 h4 {
    padding: 0px;
    margin: 0px;
    height: 60px;
    line-height: 60px;
  }

  .details-box {
    border-left: #e5e5e5 solid 1px;
  }

  .box_top_title .small-box {
    color: #333;
  }

  .small-box {
    box-shadow: none;
    border-bottom: #e5e5e5 solid 1px;
    border-left: #e5e5e5 solid 1px;
  }

  .box_top_title .small-box {
    border-left: #F9F9F9 solid 1px;
  }

  .box_top_title .details-box {
    border-top: 1px solid #e5e5e5;
  }

  .inner {
    min-height: 58px;
  }



  @media only screen and (max-width: 768px) {

    h2,
    span,
    h3,
    h4 {
      font-size: 8pt !important;
    }

    .details-box span {
      font-size: 7pt !important;
    }

    small {
      font-size: 8pt !important;
    }

    .box-header h3 {
      font-size: 8pt !important;
      font-weight: bold;
    }

    h3.box-title {
      font-weight: bold !important;
      padding: 5px !important;
    }

    .pull-right.select_box.a_office {
      margin-top: -4px;
    }

    .pull-right.select_box.a_office label {
      width: 55% !important;
      font-size: 8pt !important;
    }

    .chart-legend>li {
      width: 100%;
    }

    .data-sync .pull-right.select_box.a_office {
      margin-top: 2px;
    }

    .data-sync .pull-right.select_box.a_office label {
      width: 36% !important;
      margin-right: 5px;
    }

    .highcharts-legend {
      display: none;
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

    .daterange1 {
      margin-top: -5px;
    }

    .box-header>.box-tools {
      right: -2px;
    }
  }
</style>

<style>
  .loader {
    background-color: #666;
    height: 100%;
    width: 100%;
    z-index: 100;
    position: absolute;
    top: 0;
    bottom: 0;
    opacity: 0.4;
  }

  .loader_icon {
    position: absolute;
    top: 46%;
    left: 46%;
    z-index: 101;
  }

  .highcharts-legend {
    display: none;
  }

  .box-header div {
    display: none;
  }

  .box-header1 div {
    display: block;
  }

  #top_boxes_office_id,
  #region_office_id {
    width: 50%;
  }
</style>


<section class="content admin">


  <div class="box box-primary">
    <div class="loader top_box_loader hide">
      <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
    </div>
    <div class="box-header box-header1 with-border">

      <h3 class="">
        <div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;">
          <?php echo $this->Form->input('source', array('id' => 'source', 'onChange' => 'getTopBoxesData("source")', 'class' => 'form-control user_id office_id', 'options' => $sources, 'label' => 'Source', 'required' => true, 'empty' => '---- All ----')); ?>
        </div>

        <div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;display:<?php if (!$office_id) {
                                                                                                          echo 'block';
                                                                                                        } else {
                                                                                                          echo 'none';
                                                                                                        } ?>;">
          <?php echo $this->Form->input('top_boxes_office_id', array('id' => 'top_boxes_office_id', 'onChange' => 'getTopBoxesData("office")', 'class' => 'form-control user_id office_id', 'options' => $offices, 'label' => 'Area Office', 'required' => true, 'empty' => '---- All ----', 'default' => $office_id)); ?>
        </div>


        <div class="box-tools pull-right select_box a_office" style="width:30%; font-size:14px;display:<?php if (!$region) {
                                                                                                          echo 'block';
                                                                                                        } else {
                                                                                                          echo 'none';
                                                                                                        } ?>;">
          <?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'onChange' => 'getTopBoxesData("region")', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- Head Office ----', 'options' => $region_offices, 'default' => $region)); ?>
        </div>


        <!--<?php //if($region > 0){ 
            ?>
             <div style="display:none;">
            <?php //echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id','required'=>true, 'options' => $region_offices)); 
            ?>
            </div>
            <? php //} 
            ?> -->

      </h3>

    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <div class="row top-boxes">

        <div class="col-lg-12 col-xs-12 box_top_title">
          <div class="small-box" style="background:#ccc;">
            <div class="inner">
              <div class="col-lg-2 col-xs-2" style="background:#CCC;">
                <h4></h4>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h4>Today</h4>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h4>Yesterday</h4>
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
                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="today_Revenue"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="yesterday_Revenue"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="week_Revenue"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="month_Revenue"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><small style="color:#333;">Tk.</small> <span id="year_Revenue"></span></h3>
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
                <h3 style="background:#CCC; color:#333;"><span id="today_EC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="yesterday_EC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="week_EC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="month_EC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="year_EC"></span></h3>
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
                <h3 style="background:#CCC; color:#333;"><span id="today_OC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="yesterday_OC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="week_OC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="month_OC"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="year_OC"></span></h3>
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
                <h3 style="background:#CCC; color:#333;"><span id="today_CYP"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="yesterday_CYP"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="week_CYP"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="month_CYP"></span></h3>
              </div>
              <div class="col-lg-2 col-xs-2 details-box">
                <h3 style="background:#CCC; color:#333;"><span id="year_CYP"></span></h3>
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
    $(document).ready(function() {
      getTopBoxesData();
      /*setInterval(function () {
          getTopBoxesData();
        }, 500000);*/
    });
    $('#region_office_id').selectChain({
      target: $('#top_boxes_office_id'),
      value: 'name',
      url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
      type: 'post',
      data: {
        'region_office_id': 'region_office_id'
      }
    });

    function getTopBoxesData(type) {
      var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

      var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

      var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;

      var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source;

      allow = 1;

      if (allow == 1) {
        $.ajax({
          url: '<?= BASE_URL . 'admin/dashboards/topBoxesData' ?>',
          type: "POST",
          data: dataString,
          beforeSend: function() {
            $(".top_box_loader").removeClass('hide');
            $(".top_box_loader").addClass('show');
          },
          success: function(data) {
            var obj = jQuery.parseJSON(data);
            $('#today_Revenue').text(obj.today_Revenue);
            $('#yesterday_Revenue').text(obj.yesterday_Revenue);
            $('#week_Revenue').text(obj.week_Revenue);
            $('#month_Revenue').text(obj.month_Revenue);
            $('#year_Revenue').text(obj.year_Revenue);

            $('#today_EC').text(obj.today_EC);
            $('#yesterday_EC').text(obj.yesterday_EC);
            $('#week_EC').text(obj.week_EC);
            $('#month_EC').text(obj.month_EC);
            $('#year_EC').text(obj.year_EC);

            $('#today_OC').text(obj.today_OC);
            $('#yesterday_OC').text(obj.yesterday_OC);
            $('#week_OC').text(obj.week_OC);
            $('#month_OC').text(obj.month_OC);
            $('#year_OC').text(obj.year_OC);

            $('#today_CYP').text(obj.today_CYP);
            $('#yesterday_CYP').text(obj.yesterday_CYP);
            $('#week_CYP').text(obj.week_CYP);
            $('#month_CYP').text(obj.month_CYP);
            $('#year_CYP').text(obj.year_CYP);

            $(".top_box_loader").removeClass('show');
            $(".top_box_loader").addClass('hide');

          }
        });
      }
      getPieChartData();
      if (type == 'region') {
        $("#region_office_id2").val(region_office_id).change();
        $("#region_office_id3").val(region_office_id).change();
        $("#region_office_id4").val(region_office_id).change();
        $("#region_office_id5").val(region_office_id).change();
        $("#region_office_id6").val(region_office_id).change();
      } else if (type == 'office') {

        $(".office_id2").val(office_id).change();
        $(".office_id3").val(office_id).change();
        $(".office_id4").val(office_id).change();
        $(".office_id5").val(office_id).change();
        $(".office_id6").val(office_id).change();
      } else {

        $("#region_office_id2").val(region_office_id);
        $("#region_office_id3").val(region_office_id);
        $("#region_office_id4").val(region_office_id);
        $("#region_office_id5").val(region_office_id);
        $("#region_office_id6").val(region_office_id);

        $(".office_id2").val(office_id).change();
        $(".office_id3").val(office_id).change();
        $(".office_id4").val(office_id).change();
        $(".office_id5").val(office_id).change();
        $(".office_id6").val(office_id).change();
      }


      //$('#region_office_id2>option:eq(2)').prop("selected", true).change();



    }

    /*----------------- ajax pie chart data loading ------------------*/
    function getPieChartData() {
      var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

      var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

      var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;
      if (office_id) {
        $('.office_title').text('Current Month Revenue Comparison Between Territory/Selected Product/Selected Brand');
      } else {
        $('.office_title').text('Current Month Revenue Comparison Between Area Office/Selected Product/Selected Brand');
      }
      var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source;
      $.ajax({
        url: '<?= BASE_URL . 'dashboards/getPieChartData' ?>',
        type: "POST",
        data: dataString,
        beforeSend: function() {
          $(".piechart_loader").removeClass('hide');
          $(".piechart_loader").addClass('show');
        },
        success: function(data) {
          var obj = jQuery.parseJSON(data);
          // console.log();
          //obj.pie_data
          $("canvas#pieChart").remove();
          // $("div.pie_chart_div").append('<canvas id="pieChart" class="animated fadeIn"  height="250"></canvas>');

          $(".offices_pie .chart-legend").html(obj.chart_legend);
          $(".offices_pie  .ajax_pie_data").html(obj.pie_product_data);

          /*

          var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
          var pieChart = new Chart(pieChartCanvas);
          var PieData = [];
          var PieData = [];

          // create PieData dynamically
          obj.pie_data.forEach(function(e) {
            PieData.push({
              value: e.value,
              color: e.color,
              highlight: e.color,
              label: e.label
            });
          });
          var pieOptions = {
            // Boolean - Whether we should show a stroke on each segment
            segmentShowStroke: true,
            // String - The colour of each segment stroke
            segmentStrokeColor: '#fff',
            // Number - The width of each segment stroke
            segmentStrokeWidth: 1,
            // Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout: 50, // This is 0 for Pie charts
            // Number - Amount of animation steps
            animationSteps: 100,
            // String - Animation easing effect
            animationEasing: 'easeOutBounce',
            // Boolean - Whether we animate the rotation of the Doughnut
            animateRotate: true,
            // Boolean - Whether we animate scaling the Doughnut from the centre
            animateScale: false,
            // Boolean - whether to make the chart responsive to window resizing
            responsive: true,
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: false,
            // String - A legend template
            legendTemplate: '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
            // String - A tooltip template
            tooltipTemplate: '<%=value %>% (<%=label%>)',



          };


          // Create pie or douhnut chart
          // You can switch between pie and douhnut using the method below.
          pieChart.Doughnut(PieData, pieOptions);
          */

          var options = {
            chart: {
              renderTo: 'pieChart',
              type: 'pie',
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              margin: [0, 0, 0, 0],
              spacingTop: 0,
              spacingBottom: 0,
              spacingLeft: 0,
              spacingRight: 0
            },

            title: {
              text: ''
            },
            exporting: {
              enabled: false
            },
            tooltip: {
              pointFormat: ' <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
              point: {
                valueSuffix: '%'
              }
            },
            plotOptions: {
              pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                size: '100%',
                shadow: false,
                innerSize: '5%',
                slicedOffset: 0,
                dataLabels: {
                  enabled: true,
                  allowOverlap: true,
                  style: {
                    fontWeight: 0,
                    fontSize: 100
                  },
                  format: '<span style="font-size:10px">{point.label}:{point.percentage:.1f}</span>',

                },
              }
            },

            series: [{
              name: '',
              colorByPoint: true,
              data: []
            }]

          }
          var i = 1;

          obj.pie_data.forEach(function(e) {
            var distence = 0
            if (e.value > 0) {
              if (i % 2 == 0) {
                distence = -50;
              } else {
                distence = -30;
              }
              options.series[0].data.push({
                name: e.fullname,
                y: parseFloat(e.value),
                color: e.color,
                label: e.shortname,
                dataLabels: {
                  distance: distence
                }
              });
              i++;
            }

          });

          var allY, angle1, angle2, angle3,
            rotate = function() {
              $.each(options.series, function(i, p) {
                angle1 = 0;
                angle2 = 0;
                angle3 = 0;
                allY = 0;
                $.each(p.data, function(i, p) {
                  allY += p.y;
                });

                $.each(p.data, function(i, p) {
                  angle2 = angle1 + p.y * 360 / (allY);
                  angle3 = angle2 - p.y * 360 / (2 * allY);
                  if (angle3 >= 180) {
                    p.dataLabels.rotation = 90 + angle3;
                  } else {
                    p.dataLabels.rotation = -90 + angle3;
                  }
                  angle1 = angle2;
                });
              });

            };
          //rotate();

          var chart = new Highcharts.Chart(options);

          $(".piechart_loader").removeClass('show');
          $(".piechart_loader").addClass('hide');

        }
      });
    }
  </script>





  <div class="row" style="padding-bottom:20px;">

    <div class="col-md-6">

      <div class="box box-primary">
        <div class="loader loader_acheivement_target hide">
          <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
        </div>
        <div class="box-header with-border stock_status">
          <h3 class="">Yearly Achievement Vs Target</h3>
          <!--(Cal offices target from sales_target table for this year then divide by per month/12)-->

          <div style="width:100%; float:left;">
            <div class="pull-right select_box a_office" style="width:50%">
              <?php echo $this->Form->input('achievement_target_office_id', array('id' => 'achievement_target_office_id', 'onChange' => 'getAchievementTargetData()', 'class' => 'form-control user_id office_id2', 'label' => 'Area Office', 'options' => $offices, 'required' => true, 'empty' => '---- All ----')); ?>
            </div>
            <?php if (!$office_id) { ?>
              <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
                <?php echo $this->Form->input('region_office_id2', array('id' => 'region_office_id2', 'onChange' => 'getAchievementTargetData()', 'class' => 'form-control region_office_id2', 'label' => 'Region Office', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?>
              </div>
            <?php } ?>
          </div>

        </div>
        <div class="box-body">
          <div class="progress-group">

            <span class="progress-text">This Year Revenue</span>

            <span id="achievement_amount" class="progress-number"></span>

            <div id="achievement_and_total_penchant" class="progress sm">
              <div class="progress-bar progress-bar-aqua" style="width: %"></div>
            </div>

            <h3>( <span id="achievement_penchant"></span>% ) Achieved</h3>

          </div>
        </div>
      </div>

      <script>
        function getAchievementTargetData() {
          /*var office_id = $("#achievement_target_office_id option:selected").val()?$("#achievement_target_office_id option:selected").val():0;

          var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;*/
          var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

          var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

          var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;

          var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source;


          $.ajax({
            url: '<?= BASE_URL . 'admin/dashboards/achievementTargetData' ?>',
            type: "POST",
            data: dataString,
            beforeSend: function() {
              $(".loader_acheivement_target").removeClass('hide');
              $(".loader_acheivement_target").addClass('show');
            },
            success: function(data) {
              var obj = jQuery.parseJSON(data);
              //alert(obj.achievement_amount);

              $('#achievement_amount').html('<b>' + obj.achievement_amount + '</b>/' + obj.total_target);

              $('#achievement_and_total_penchant').html('<div class="progress-bar progress-bar-aqua" style="width: ' + obj.achievement_penchant + '%"></div>');

              $('#achievement_penchant').text(obj.achievement_penchant);

              $(".loader_acheivement_target").removeClass('show');
              $(".loader_acheivement_target").addClass('hide');
            }
          });
        }
      </script>


      <div class="box box-primary">
        <div class="loader piechart_loader hide">
          <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
        </div>
        <div class="box-header with-border">
          <h3 class="office_title">Area Office Sales</h3>
        </div>


        <!-- /.box-header -->
        <div id="all_offices_pie" class="offices_pie" style="display:block;">
          <div class="box-body">
            <div class="row">


              <div class="col-md-7">
                <div class="chart-responsive pie_chart_div">
                  <!--  <canvas id="pieChart" height="250"></canvas> -->
                  <div id="pieChart"></div>
                </div>
                <!-- ./chart-responsive -->
              </div>
              <!-- /.col -->
              <div class="col-md-5">
                <ul class="chart-legend clearfix ajax_chart_legend">
                </ul>
              </div>
              <!-- /.col -->

            </div>
            <!-- /.row -->
          </div>
          <!-- /.box-body -->

          <div class="box-footer no-padding">
            <ul class="nav nav-pills nav-stacked ajax_pie_data">
            </ul>
          </div>
        </div>





        <!-- /.footer -->
      </div>
    </div>

    <div class="col-md-6">

      <div class="box box-primary">
        <div class="loader yearly_target_loader hide">
          <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
        </div>
        <div class="box-header with-border stock_status">
          <h3>Yearly Products Sales Achievement Vs Target (Tk.)</h3>
          <!--(Cal offices Target from sale_target_months table using this month id)-->
          <div style="width:100%; float:left;">
            <div class="box-tools pull-right select_box a_office col-md-2" style="padding:0px; width:50%;">
              <?php echo $this->Form->input('product_achievement_target_office_id', array('id' => 'product_achievement_target_office_id', 'onChange' => 'ProductSalesAndTarget()', 'class' => 'form-control user_id office_id3', 'label' => 'Area Office', 'options' => $offices, 'required' => true, 'empty' => 'All')); ?>
            </div>
            <?php if (!$office_id) { ?>
              <div class="box-tools pull-right select_box a_office" style="width:50%; font-size:14px;">
                <?php echo $this->Form->input('region_office_id3', array('id' => 'region_office_id3', 'class' => 'form-control region_office_id3', 'onChange' => 'ProductSalesAndTarget()', 'label' => 'Region Office', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?>
              </div>
            <?php } ?>
          </div>
        </div>

        <div id="sales_target" class="box-body" style="min-height: 150px;">

        </div>
      </div>

    </div>

  </div>



  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="loader sales_trend_loader hide">
          <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
        </div>
        <div class="box-header with-border stock_status">
          <h3 class="">Monthly Sales Trend</h3>


          <div class="pull-right select_box a_office" style="width:30%">
            <?php echo $this->Form->input('sales_trend_office_id', array('id' => 'sales_trend_office_id', 'onChange' => 'getSalesTrendData()', 'class' => 'form-control user_id office_id4', 'label' => 'Area Office', 'options' => $offices, 'required' => true, 'empty' => '---- All ----')); ?>
          </div>

          <?php if (!$office_id) { ?>
            <div class="pull-right select_box a_office" style="width:auto; font-size:14px;">
              <?php echo $this->Form->input('region_office_id4', array('id' => 'region_office_id4', 'onChange' => 'getSalesTrendData()', 'class' => 'form-control region_office_id4', 'label' => 'Region Office', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?>
            </div>
          <?php } ?>


        </div>
        <div class="box-body" style="min-height: 150px;">

          <div id="container" style="width:100%; height: 400px; margin: 0 auto"></div>
        </div>
      </div>
    </div>
  </div>



  <div class="row">
    <div class="col-xs-11 col-md-11">
      <div class="box box-primary">
        <div class="loader stock_status_loader hide">
          <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
        </div>
        <div class="box-header with-border stock_status">
          <style>
            #stock_status_office_id {
              float: right;
            }

            .stock_status .a_office label {
              width: 60% !important;
            }
          </style>
          <h3 class="box-title" style="font-size:20px; font-weight:500; color:#4043A0;">National Stock Status</h3>

          <div style="float:left; width:100%;">
            <div class="box-tools pull-right select_box a_office" style="width:50%">
              <?php echo $this->Form->input('stock_status_office_id', array('id' => 'stock_status_office_id', 'onChange' => 'getStockStatusData()', 'class' => 'form-control user_id office_id5', 'label' => 'Area Office', 'options' => $offices, 'required' => true, 'empty' => '---- All ----')); ?>
            </div>
            <?php if (!$office_id) { ?>
              <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
                <?php echo $this->Form->input('region_office_id5', array('id' => 'region_office_id5', 'class' => 'form-control region_office_id5', 'onChange' => 'getStockStatusData()', 'label' => 'Region Office', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?>
              </div>
            <?php } ?>
          </div>

        </div>

        <div class="box-body" style="min-height: 150px;">

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

              </tbody>

            </table>
          </div>

        </div>


        <script>
          function getStockStatusData() {
            /*var office_id = $("#stock_status_office_id option:selected").val()?$("#stock_status_office_id option:selected").val():0;
            
            var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;*/
            var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

            var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;
            var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;

            var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source;
            //var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;

            $.ajax({
              url: '<?= BASE_URL . 'admin/dashboards/stockStatusData' ?>',
              type: "POST",
              data: dataString,
              beforeSend: function() {
                $(".stock_status_loader").removeClass('hide');
                $(".stock_status_loader").addClass('show');
              },
              success: function(data) {
                var obj = jQuery.parseJSON(data);
                //alert(obj.output);
                $('#stock_status_data').html(obj.output);
                $(".stock_status_loader").removeClass('show');
                $(".stock_status_loader").addClass('hide');
              }
            });
          }
        </script>

      </div>
    </div>




  </div>

  <!--
<div class="row">        
  <div class="col-md-11 col-xs-11">
    <div class="box box-primary data-sync">
      <div class="loader user_sync_loader hide">
        <img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
      </div>
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
            $sync_options = $offices;
            $sync_options['1'] = 'Details List';

            //echo $this->Form->input('office_id', array('id' => 'office_id', 'onChange' => 'getUserSyncData()', 'class' => 'form-control user_id office_id6', 'label'=>'Area Office', 'required'=>true, 'empty'=>'Pending Sync(Last 3 days)','options'=>$sync_options)); 
            ?>                      
          </div>
          
          <?php if (!$office_id) { ?>
          <div class="pull-right select_box a_office" style="width:50%; font-size:14px;">
            <?php //echo $this->Form->input('region_office_id6', array('id' => 'region_office_id6','class' => 'form-control region_office_id6', 'onChange' => 'getUserSyncData()', 'label'=>'Region Office', 'required'=>true,'empty'=>'---- All ----', 'options' => $region_offices,)); 
            ?>
          </div>
          <?php } ?>
        </div>
      </div>
      
      <div class="box-body" style="min-height: 150px;">

        <div class="table-responsive">
          <table class="table no-margin">

            <thead>
              <tr>
                <th>Name</th>
                <th>Territory</th>
                <?php /*?><th>Last Sync Date</th><?php */ ?>
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
-->
  <script>
    function getUserSyncData() {

      /*var office_id = $("#office_id option:selected").val()?$("#office_id option:selected").val():0;

      var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;*/
      var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

      var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

      var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id;

      $.ajax({
        url: '<?= BASE_URL . 'admin/dashboards/userSyncData' ?>',
        type: "POST",
        data: dataString,
        beforeSend: function() {
          $(".user_sync_loader").removeClass('hide');
          $(".user_sync_loader").addClass('show');
        },
        success: function(msg) {
          $('#syncTableList').html(msg);
          $(".user_sync_loader").removeClass('show');
          $(".user_sync_loader").addClass('hide');
        }
      });
    }
  </script>









</section>



<script>
  /*    $('.region_office_id2').selectChain({
     target: $('.office_id2'),
     value:'name',
     url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
     type: 'post',
     data:{'region_office_id': 'region_office_id2' }
   });
    $('.region_office_id3').selectChain({
     target: $('.office_id3'),
     value:'name',
     url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
     type: 'post',
     data:{'region_office_id': 'region_office_id3' }
   });
    $('.region_office_id4').selectChain({
     target: $('.office_id4'),
     value:'name',
     url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
     type: 'post',
     data:{'region_office_id': 'region_office_id4' }
   });
    $('.region_office_id5').selectChain({
     target: $('.office_id5'),
     value:'name',
     url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
     type: 'post',
     data:{'region_office_id': 'region_office_id5' }
   });
    $('.region_office_id6').selectChain({
     target: $('.office_id6'),
     value:'name',
     url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
     type: 'post',
     data:{'region_office_id': 'region_office_id6' }
   });*/
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
  function ProductSalesAndTarget() {
    //alert(office_id);

    /*var office_id = $("#product_achievement_target_office_id option:selected").val()?$("#product_achievement_target_office_id option:selected").val():0;
    
    var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;*/

    var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

    var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

    var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;

    var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source;

    $.ajax({
      url: '<?= BASE_URL . 'admin/dashboards/ajaxProductSalesAndTarget' ?>',
      type: "POST",
      data: dataString,
      beforeSend: function() {
        $(".yearly_target_loader").removeClass('hide');
        $(".yearly_target_loader").addClass('show');
      },
      //complete: function() {$("#confirmWait_"+pro_id).hide()},
      success: function(msg) {
        //alert(msg);
        $('#sales_target').html(msg);
        $(".yearly_target_loader").removeClass('show');
        $(".yearly_target_loader").addClass('hide');

      }
    });

  }
</script>


<script>
  function getSalesTrendData() {
    /*var office_id = $("#sales_trend_office_id option:selected").val()?$("#sales_trend_office_id option:selected").val():0;
    
    var region_office_id = $("#region_office_id option:selected").val()?$("#region_office_id option:selected").val():0;*/
    var office_id = $("#top_boxes_office_id option:selected").val() ? $("#top_boxes_office_id option:selected").val() : 0;

    var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

    var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;

    var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source;
    // var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id;

    $.ajax({
      url: '<?= BASE_URL . 'admin/dashboards/salesTrendData' ?>',
      type: "POST",
      data: dataString,
      beforeSend: function() {
        $(".sales_trend_loader").removeClass('hide');
        $(".sales_trend_loader").addClass('show');
      },
      success: function(data) {
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
            categories: ['<?= date('F', time()) ?>', '<?= date('F', strtotime('-1 month', strtotime(date('Y-m-01')))) ?>', '<?= date('F', strtotime('-2 month', strtotime(date('Y-m-01')))) ?>', '<?= date('F', strtotime('-3 month', strtotime(date('Y-m-01')))) ?>', '<?= date('F', strtotime('-4 month', strtotime(date('Y-m-01')))) ?>', '<?= date('F', strtotime('-5 month', strtotime(date('Y-m-01')))) ?>'],
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

        $(".sales_trend_loader").removeClass('show');
        $(".sales_trend_loader").addClass('hide');

      },
      cache: false
    });





  }
</script>