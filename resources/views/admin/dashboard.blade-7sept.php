@extends('layout.default')
@section('mytitle', 'Dashboard')
<style>
    .widget-card{
        display: flex;
        justify-content: space-between;
    }
    .widget-heading-card h2 i{
        /* background: darkcyan;
        border-radius: 50%;
        padding: 10px; */
        background: darkcyan;
        border-radius: 50%;
        padding: 12px 8px;
        width: 45px;
        height: 45px;
        text-align: center;
        font-size: 22px;
    }
    .widget-content-card{
        padding: 10px !important;
        text-align: center;
    }
    .widget-content-card h3{
        /* font-weight: 600; */
        font-weight: 600;
        font-size: 20px;
        margin-top: 10px;
    }
    .widget-content-card h3 span{
        font-size: 14px;
    }
    .widget.widget-chart-two .widget-content {
      padding: 20px;
    }
</style>
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">

        <div class="col-12">
            <div class="row">
                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-5 layout-spacing"></div>
                <div class="col-xl-7 col-lg-7 col-md-7 col-sm-7 col-7 layout-spacing">
                    <div class="input-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Start Date</span>
                        </div>
                        <input id="basicFlatpickr" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Start Date..">
                        <div class="input-group-append">
                            <span class="input-group-text">End Date</span>
                        </div>
                        <input id="basicFlatpickr1" class="form-control flatpickr flatpickr-input active" type="text" placeholder="End Date..">
                        <div class="input-group-append" style="cursor: pointer;">
                            <span class="input-group-text" style="background-color: #5c1ac3;color: #fff;" onclick="filter()">Filter</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="form-group mb-0">
                <input id="basicFlatpickr" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Select Date..">
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="form-group mb-0">
                <input id="basicFlatpickr1" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Select Date..">
            </div>
        </div> -->

        @if(\Auth::user()->role != 3)
        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-two">
                <div class="widget-heading">
                    <h5 class="">{{(\Auth::user()->role == 1) ? 'Admin/ Reseller/ User' : ((\Auth::user()->role == 2) ? 'Reseller/ User' : 'User')}}</h5>
                </div>
                <div class="widget-content">
                    <div id="chart-2" class=""></div>
                </div>
            </div>
        </div>
        @endif

        <div class="@if(\Auth::user()->role == 3) {{'col-xl-12'}} @else {{'col-xl-8'}} @endif col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-two">
                <div class="widget-heading">
                    <h5 class="">Revenue</h5>
                </div>

                <div class="widget-content" style="padding: 16px;">
                    <div class="tabs tab-content">
                        <div id="content_1" class="tabcontent">
                            <div id="revenueMonthly"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($total_admin))
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fa fa-users"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{$total_admin}} <br><span>Total Admin</span></h3>
                </div>
            </div>
        </div>
        @endif
        @if(Auth::user()->role !=3)
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fa fa-users"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{$total_reseller}} <br><span>Total Reseller</span></h3>
                </div>
            </div>
        </div>
        @endif
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fa fa-users"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{$total_plan_sold}} <br><span>Total Plans Sold</span></h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fa fa-users"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{$total_user}} <br><span>Total User</span></h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fas fa-rupee-sign"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{$wallet ? $wallet : 0}} <br><span>Wallet Amount</span></h3>
                </div>
            </div>
        </div>


        @if(Auth::user()->role == 1)
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fas fa-rupee-sign"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{($monthly_profit > 0) ? $monthly_profit : $monthly_loss }} <br><span>{{($monthly_profit > 0) ? $monthly_profit_percentage .' % Monthly Profit in '. date('M') : $monthly_loss_percentage .' % Monthly Loss in '. date('M')}}</span></h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 layout-spacing">
            <div class="widget widget-card widget-chart-two">
                <div class="widget-heading widget-heading-card">
                    <h2><i class="fas fa-rupee-sign"></i></h2>
                </div>
                <div class="widget-content widget-content-card">
                    <h3>{{($yearly_profit > 0) ? $yearly_profit : $yearly_loss }} <br><span>{{($yearly_profit > 0) ? $yearly_profit_percentage .' % Yearly Profit in '. date('M') : $yearly_loss_percentage .' % Yearly Loss in '. date('M')}}</span></h3>
                </div>
            </div>
        </div>
        @endif

        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
          <div class="widget widget-table-two" style="padding: 16px;">
            <div class="widget-heading">
                <h5 class="">Recent Purchased Plan @if(Auth::user()->role == 2){{'By Users'}} @endif</h5>
            </div>
            <div class="widget-content">
              <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th><div class="th-content">Plan</div></th>
                            <th><div class="th-content th-heading">Price</div></th>
                            <th><div class="th-content">Total Sales</div></th>
                            <th><div class="th-content">Purchased Date</div></th>
                            <th><div class="th-content">Status</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($recently_purchase_plan)
                            @foreach($recently_purchase_plan as $row)
                            @php
                            $status = ($row->status == 1) ? '<span class="badge outline-badge-success">Active</span>' : '<span class="badge outline-badge-danger">De-Active</span>';
                            @endphp
                            <tr>
                                <td><div class="td-content customer-name">{{$row->title}}</div></td>
                                <td><div class="td-content product-brand">₹ {{$row->price}}</div></td>
                                <td><div class="td-content text-center">{{isset($row->total_plan_count) ? $row->total_plan_count: 1}}</div></td>
                                <td><div class="td-content pricing"><span class="">{{date('d M, Y', strtotime($row->purchase_at))}}</span></div></td>
                                <td><div class="td-content"><?= $status ?></div></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        @if(Auth::user()->role == 2)
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
          <div class="widget widget-table-two" style="padding: 16px;">
            <div class="widget-heading">
                <h5 class="">Recent Purchased Plan By Reseller</h5>
            </div>
            <div class="widget-content">
              <div class="table-responsive">
                <table class="table" id="dataTable1">
                    <thead>
                        <tr>
                            <th><div class="th-content">Plan</div></th>
                            <th><div class="th-content th-heading">Price</div></th>
                            <th><div class="th-content">Total Sales</div></th>
                            <th><div class="th-content">Purchased Date</div></th>
                            <th><div class="th-content">Status</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($recently_purchase_plan_reseller)
                            @foreach($recently_purchase_plan_reseller as $row)
                            @php
                            $status = ($row->status == 1) ? '<span class="badge outline-badge-success">Active</span>' : '<span class="badge outline-badge-danger">De-Active</span>';
                            @endphp
                            <tr>
                                <td><div class="td-content customer-name">{{$row->title}}</div></td>
                                <td><div class="td-content product-brand">₹ {{$row->price}}</div></td>
                                <td><div class="td-content text-center">{{isset($row->total_plan_count) ? $row->total_plan_count: 1}}</div></td>
                                <td><div class="td-content pricing"><span class="">{{date('d M, Y', strtotime($row->purchase_at))}}</span></div></td>
                                <td><div class="td-content"><?= $status ?></div></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        @endif

    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

    var startDate;
    var endDate;
    $(document).ready(function() {

      $('#dataTable').DataTable({

      })

      $('#dataTable1').DataTable({});

        startDate = document.getElementById('basicFlatpickr');
        endDate = document.getElementById('basicFlatpickr1');
        startDate.flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: ["<?= $startDate ?>"],
            minDate: '01-06-2022'
        });
        endDate.flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: "<?= $endDate ?>",
            minDate: '01-06-2022',
            maxDate: 'today',
        });
    });

    function filter(){
        startDate = startDate.value;
        endDate = endDate.value;
        var url = new URL(window.location.href).origin +"/dashboard-filter/"+startDate+"/"+endDate;
        window.location.href =url;
    }

</script>

<script>
    var monthly_revenue =  <?php print_r($monthly_revenue); ?>;
    var monthly_plan_sell =  <?php print_r($monthly_plan_sell); ?>;
    var monthly_profit =  <?php print_r($monthly_profit); ?>;
</script>

@if(\Auth::user()->role == 1)
<script>
var options = {
    chart: {
        type: 'donut',
        width: 380
    },
    colors: ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'],
    dataLabels: {
      enabled: false
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '14px',
        markers: {
          width: 10,
          height: 10,
        },
        itemMargin: {
          horizontal: 0,
          vertical: 8
        }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          background: 'transparent',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '29px',
              fontFamily: 'Nunito, sans-serif',
              color: undefined,
              offsetY: -10
            },
            value: {
              show: true,
              fontSize: '26px',
              fontFamily: 'Nunito, sans-serif',
              color: '#bfc9d4',
              offsetY: 16,
              formatter: function (val) {
                return Math.floor(val)
              }
            },
            total: {
              show: true,
              showAlways: true,
              label: 'Total',
              color: '#888ea8',
              formatter: function (w) {
                return w.globals.seriesTotals.reduce( function(a, b) {
                  return Math.floor(a + b)
                }, 0)
              }
            }
          }
        }
      }
    },
    stroke: {
      show: true,
      width: 25,
      colors: '#0e1726'
    },
    series: [<?= $total_admin; ?>, <?= $total_reseller; ?>, <?= $total_user; ?>],
    labels: ['Admin', 'Reseller', 'User'],
    responsive: [{
        breakpoint: 1599,
        options: {
            chart: {
                width: '350px',
                height: '400px'
            },
            legend: {
                position: 'bottom'
            }
        },

        breakpoint: 1439,
        options: {
            chart: {
                width: '250px',
                height: '390px'
            },
            legend: {
                position: 'bottom'
            },
            plotOptions: {
              pie: {
                donut: {
                  size: '65%',
                }
              }
            }
        },
    }]
}

//Revenue
var options1 = {
  chart: {
    fontFamily: 'Nunito, sans-serif',
    height: 365,
    type: 'area',
    zoom: {
        enabled: false
    },
    dropShadow: {
      enabled: true,
      opacity: 0.3,
      blur: 5,
      left: -7,
      top: 22
    },
    toolbar: {
      show: false
    },
    events: {
      mounted: function(ctx, config) {
        const highest1 = ctx.getHighestValueInSeries(0);
        const highest2 = ctx.getHighestValueInSeries(1);

        ctx.addPointAnnotation({
          x: new Date(ctx.w.globals.seriesX[0][ctx.w.globals.series[0].indexOf(highest1)]).getTime(),
          y: highest1,
          label: {
            style: {
              cssClass: 'd-none'
            }
          },
          customSVG: {
              SVG: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#1b55e2" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>',
              cssClass: undefined,
              offsetX: -8,
              offsetY: 5
          }
        })

        ctx.addPointAnnotation({
          x: new Date(ctx.w.globals.seriesX[1][ctx.w.globals.series[1].indexOf(highest2)]).getTime(),
          y: highest2,
          label: {
            style: {
              cssClass: 'd-none'
            }
          },
          customSVG: {
              SVG: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#e7515a" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>',
              cssClass: undefined,
              offsetX: -8,
              offsetY: 5
          }
        })
      },
    }
  },
  colors: ['#1b55e2', '#e7515a', '#e2a03f'],
  dataLabels: {
      enabled: false
  },
  markers: {
    discrete: [{
    seriesIndex: 0,
    dataPointIndex: 7,
    fillColor: '#000',
    strokeColor: '#000',
    size: 5
  }, {
    seriesIndex: 2,
    dataPointIndex: 11,
    fillColor: '#000',
    strokeColor: '#000',
    size: 4
  }]
  },
  subtitle: {
    text: 'Total Revenue',
    align: 'left',
    margin: 0,
    offsetX: 5,
    offsetY: 35,
    floating: false,
    style: {
      fontSize: '14px',
      color:  '#888ea8'
    }
  },
  title: {
    text: '<?= $total_revenue ?>',
    align: 'left',
    margin: 0,
    offsetX: 5,
    offsetY: 0,
    floating: false,
    style: {
      fontSize: '25px',
      color:  '#bfc9d4'
    },
  },
  stroke: {
      show: true,
      curve: 'smooth',
      width: 2,
      lineCap: 'square'
  },
  series: [{
      name: 'Revenue',
      data: Object.values(monthly_revenue)
  },{
    name: 'Selles',
    data: Object.values(monthly_plan_sell)
  }],
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  xaxis: {
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    crosshairs: {
      show: true
    },
    labels: {
      offsetX: 0,
      offsetY: 5,
      style: {
          fontSize: '12px',
          fontFamily: 'Nunito, sans-serif',
          cssClass: 'apexcharts-xaxis-title',
      },
    }
  },
  yaxis: {
    labels: {
      formatter: function(value, index) {
        return (value / 1000) + 'K'
      },
      offsetX: -22,
      offsetY: 0,
      style: {
          fontSize: '12px',
          fontFamily: 'Nunito, sans-serif',
          cssClass: 'apexcharts-yaxis-title',
      },
    }
  },
  grid: {
    borderColor: '#191e3a',
    strokeDashArray: 5,
    xaxis: {
        lines: {
            show: true
        }
    },
    yaxis: {
        lines: {
            show: false,
        }
    },
    padding: {
      top: 0,
      right: 0,
      bottom: 0,
      left: -10
    },
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    offsetY: -50,
    fontSize: '16px',
    fontFamily: 'Nunito, sans-serif',
    markers: {
      width: 10,
      height: 10,
      strokeWidth: 0,
      strokeColor: '#fff',
      fillColors: undefined,
      radius: 12,
      onClick: undefined,
      offsetX: 0,
      offsetY: 0
    },
    itemMargin: {
      horizontal: 0,
      vertical: 20
    }
  },
  tooltip: {
    theme: 'dark',
    marker: {
      show: true,
    },
    x: {
      show: false,
    }
  },
  fill: {
      type:"gradient",
      gradient: {
          type: "vertical",
          shadeIntensity: 1,
          inverseColors: !1,
          opacityFrom: .28,
          opacityTo: .05,
          stops: [45, 100]
      }
  },
  responsive: [{
    breakpoint: 575,
    options: {
      legend: {
          offsetY: -30,
      },
    },
  }]
}
</script>
@elseif(\Auth::user()->role == 2)
<script>
var options = {
    chart: {
        type: 'donut',
        width: 380
    },
    colors: ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'],
    dataLabels: {
      enabled: false
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '14px',
        markers: {
          width: 10,
          height: 10,
        },
        itemMargin: {
          horizontal: 0,
          vertical: 8
        }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          background: 'transparent',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '29px',
              fontFamily: 'Nunito, sans-serif',
              color: undefined,
              offsetY: -10
            },
            value: {
              show: true,
              fontSize: '26px',
              fontFamily: 'Nunito, sans-serif',
              color: '#bfc9d4',
              offsetY: 16,
              formatter: function (val) {
                return Math.floor(val)
              }
            },
            total: {
              show: true,
              showAlways: true,
              label: 'Total',
              color: '#888ea8',
              formatter: function (w) {
                return w.globals.seriesTotals.reduce( function(a, b) {
                  return Math.floor(a + b)
                }, 0)
              }
            }
          }
        }
      }
    },
    stroke: {
      show: true,
      width: 25,
      colors: '#0e1726'
    },
    series: [<?= $total_reseller; ?>, <?= $total_user; ?>],
    labels: ['Reseller', 'User'],
    responsive: [{
        breakpoint: 1599,
        options: {
            chart: {
                width: '350px',
                height: '400px'
            },
            legend: {
                position: 'bottom'
            }
        },

        breakpoint: 1439,
        options: {
            chart: {
                width: '250px',
                height: '390px'
            },
            legend: {
                position: 'bottom'
            },
            plotOptions: {
              pie: {
                donut: {
                  size: '65%',
                }
              }
            }
        },
    }]
}

//Revenue
var options1 = {
  chart: {
    fontFamily: 'Nunito, sans-serif',
    height: 365,
    type: 'area',
    zoom: {
        enabled: false
    },
    dropShadow: {
      enabled: true,
      opacity: 0.3,
      blur: 5,
      left: -7,
      top: 22
    },
    toolbar: {
      show: false
    },
    events: {
      mounted: function(ctx, config) {
        const highest1 = ctx.getHighestValueInSeries(0);
        const highest2 = ctx.getHighestValueInSeries(1);

        ctx.addPointAnnotation({
          x: new Date(ctx.w.globals.seriesX[0][ctx.w.globals.series[0].indexOf(highest1)]).getTime(),
          y: highest1,
          label: {
            style: {
              cssClass: 'd-none'
            }
          },
          customSVG: {
              SVG: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#1b55e2" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>',
              cssClass: undefined,
              offsetX: -8,
              offsetY: 5
          }
        })

        ctx.addPointAnnotation({
          x: new Date(ctx.w.globals.seriesX[1][ctx.w.globals.series[1].indexOf(highest2)]).getTime(),
          y: highest2,
          label: {
            style: {
              cssClass: 'd-none'
            }
          },
          customSVG: {
              SVG: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#e7515a" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>',
              cssClass: undefined,
              offsetX: -8,
              offsetY: 5
          }
        })
      },
    }
  },
  colors: ['#1b55e2', '#e7515a', '#e2a03f'],
  dataLabels: {
      enabled: false
  },
  markers: {
    discrete: [{
    seriesIndex: 0,
    dataPointIndex: 7,
    fillColor: '#000',
    strokeColor: '#000',
    size: 5
  }, {
    seriesIndex: 2,
    dataPointIndex: 11,
    fillColor: '#000',
    strokeColor: '#000',
    size: 4
  }]
  },
  subtitle: {
    text: 'Total Revenue',
    align: 'left',
    margin: 0,
    offsetX: 5,
    offsetY: 35,
    floating: false,
    style: {
      fontSize: '14px',
      color:  '#888ea8'
    }
  },
  title: {
    text: '<?= $total_revenue ?>',
    align: 'left',
    margin: 0,
    offsetX: 5,
    offsetY: 0,
    floating: false,
    style: {
      fontSize: '25px',
      color:  '#bfc9d4'
    },
  },
  stroke: {
      show: true,
      curve: 'smooth',
      width: 2,
      lineCap: 'square'
  },
  series: [{
      name: 'Revenue',
    //   data: [16800, 16800, 15500, 17800, 15500, 17000, 19000, 16000, 15000, 17000, 14000, 17000]
      data: Object.values(monthly_revenue)
  },{
    name: 'Selles',
    data: Object.values(monthly_plan_sell)
  },{
    name: 'Profit',
    data: Object.values(monthly_profit)
  }],
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  xaxis: {
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    crosshairs: {
      show: true
    },
    labels: {
      offsetX: 0,
      offsetY: 5,
      style: {
          fontSize: '12px',
          fontFamily: 'Nunito, sans-serif',
          cssClass: 'apexcharts-xaxis-title',
      },
    }
  },
  yaxis: {
    labels: {
      formatter: function(value, index) {
        return (value / 1000) + 'K'
      },
      offsetX: -22,
      offsetY: 0,
      style: {
          fontSize: '12px',
          fontFamily: 'Nunito, sans-serif',
          cssClass: 'apexcharts-yaxis-title',
      },
    }
  },
  grid: {
    borderColor: '#191e3a',
    strokeDashArray: 5,
    xaxis: {
        lines: {
            show: true
        }
    },
    yaxis: {
        lines: {
            show: false,
        }
    },
    padding: {
      top: 0,
      right: 0,
      bottom: 0,
      left: -10
    },
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    offsetY: -50,
    fontSize: '16px',
    fontFamily: 'Nunito, sans-serif',
    markers: {
      width: 10,
      height: 10,
      strokeWidth: 0,
      strokeColor: '#fff',
      fillColors: undefined,
      radius: 12,
      onClick: undefined,
      offsetX: 0,
      offsetY: 0
    },
    itemMargin: {
      horizontal: 0,
      vertical: 20
    }
  },
  tooltip: {
    theme: 'dark',
    marker: {
      show: true,
    },
    x: {
      show: false,
    }
  },
  fill: {
      type:"gradient",
      gradient: {
          type: "vertical",
          shadeIntensity: 1,
          inverseColors: !1,
          opacityFrom: .28,
          opacityTo: .05,
          stops: [45, 100]
      }
  },
  responsive: [{
    breakpoint: 575,
    options: {
      legend: {
          offsetY: -30,
      },
    },
  }]
}
</script>
@elseif(\Auth::user()->role == 3)
<script>
var options = {
    chart: {
        type: 'donut',
        width: 380
    },
    colors: ['#5c1ac3', '#e2a03f', '#e7515a', '#e2a03f'],
    dataLabels: {
      enabled: false
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '14px',
        markers: {
          width: 10,
          height: 10,
        },
        itemMargin: {
          horizontal: 0,
          vertical: 8
        }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '65%',
          background: 'transparent',
          labels: {
            show: true,
            name: {
              show: true,
              fontSize: '29px',
              fontFamily: 'Nunito, sans-serif',
              color: undefined,
              offsetY: -10
            },
            value: {
              show: true,
              fontSize: '26px',
              fontFamily: 'Nunito, sans-serif',
              color: '#bfc9d4',
              offsetY: 16,
              formatter: function (val) {
                return Math.floor(val)
              }
            },
            total: {
              show: true,
              showAlways: true,
              label: 'Total',
              color: '#888ea8',
              formatter: function (w) {
                return w.globals.seriesTotals.reduce( function(a, b) {
                  return Math.val(a + b)
                }, 0)
              }
            }
          }
        }
      }
    },
    stroke: {
      show: true,
      width: 25,
      colors: '#0e1726'
    },
    series: ['<?= $total_user; ?>'],
    labels: ['User'],
    responsive: [{
        breakpoint: 1599,
        options: {
            chart: {
                width: '350px',
                height: '400px'
            },
            legend: {
                position: 'bottom'
            }
        },

        breakpoint: 1439,
        options: {
            chart: {
                width: '250px',
                height: '390px'
            },
            legend: {
                position: 'bottom'
            },
            plotOptions: {
              pie: {
                donut: {
                  size: '65%',
                }
              }
            }
        },
    }]
}

//Revenue
var options1 = {
  chart: {
    fontFamily: 'Nunito, sans-serif',
    height: 365,
    type: 'area',
    zoom: {
        enabled: false
    },
    dropShadow: {
      enabled: true,
      opacity: 0.3,
      blur: 5,
      left: -7,
      top: 22
    },
    toolbar: {
      show: false
    },
    events: {
      mounted: function(ctx, config) {
        const highest1 = ctx.getHighestValueInSeries(0);
        const highest2 = ctx.getHighestValueInSeries(1);

        ctx.addPointAnnotation({
          x: new Date(ctx.w.globals.seriesX[0][ctx.w.globals.series[0].indexOf(highest1)]).getTime(),
          y: highest1,
          label: {
            style: {
              cssClass: 'd-none'
            }
          },
          customSVG: {
              SVG: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#1b55e2" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>',
              cssClass: undefined,
              offsetX: -8,
              offsetY: 5
          }
        })

        ctx.addPointAnnotation({
          x: new Date(ctx.w.globals.seriesX[1][ctx.w.globals.series[1].indexOf(highest2)]).getTime(),
          y: highest2,
          label: {
            style: {
              cssClass: 'd-none'
            }
          },
          customSVG: {
              SVG: '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#e7515a" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>',
              cssClass: undefined,
              offsetX: -8,
              offsetY: 5
          }
        })
      },
    }
  },
  colors: ['#1b55e2', '#e7515a', '#e2a03f'],
  dataLabels: {
      enabled: false
  },
  markers: {
    discrete: [{
    seriesIndex: 0,
    dataPointIndex: 7,
    fillColor: '#000',
    strokeColor: '#000',
    size: 5
  }, {
    seriesIndex: 2,
    dataPointIndex: 11,
    fillColor: '#000',
    strokeColor: '#000',
    size: 4
  }]
  },
  subtitle: {
    text: 'Total Revenue',
    align: 'left',
    margin: 0,
    offsetX: 5,
    offsetY: 35,
    floating: false,
    style: {
      fontSize: '14px',
      color:  '#888ea8'
    }
  },
  title: {
    text: '<?= $total_revenue ?>',
    align: 'left',
    margin: 0,
    offsetX: 5,
    offsetY: 0,
    floating: false,
    style: {
      fontSize: '25px',
      color:  '#bfc9d4'
    },
  },
  stroke: {
      show: true,
      curve: 'smooth',
      width: 2,
      lineCap: 'square'
  },
  series: [{
      name: 'Revenue',
    //   data: [16800, 16800, 15500, 17800, 15500, 17000, 19000, 16000, 15000, 17000, 14000, 17000]
      data: Object.values(monthly_revenue)
  },{
    name: 'Selles',
    data: Object.values(monthly_plan_sell)
  },{
    name: 'Profit',
    data: Object.values(monthly_profit)
  }],
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  xaxis: {
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    crosshairs: {
      show: true
    },
    labels: {
      offsetX: 0,
      offsetY: 5,
      style: {
          fontSize: '12px',
          fontFamily: 'Nunito, sans-serif',
          cssClass: 'apexcharts-xaxis-title',
      },
    }
  },
  yaxis: {
    labels: {
      formatter: function(value, index) {
        return (value / 1000) + 'K'
      },
      offsetX: -22,
      offsetY: 0,
      style: {
          fontSize: '12px',
          fontFamily: 'Nunito, sans-serif',
          cssClass: 'apexcharts-yaxis-title',
      },
    }
  },
  grid: {
    borderColor: '#191e3a',
    strokeDashArray: 5,
    xaxis: {
        lines: {
            show: true
        }
    },
    yaxis: {
        lines: {
            show: false,
        }
    },
    padding: {
      top: 0,
      right: 0,
      bottom: 0,
      left: -10
    },
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    offsetY: -50,
    fontSize: '16px',
    fontFamily: 'Nunito, sans-serif',
    markers: {
      width: 10,
      height: 10,
      strokeWidth: 0,
      strokeColor: '#fff',
      fillColors: undefined,
      radius: 12,
      onClick: undefined,
      offsetX: 0,
      offsetY: 0
    },
    itemMargin: {
      horizontal: 0,
      vertical: 20
    }
  },
  tooltip: {
    theme: 'dark',
    marker: {
      show: true,
    },
    x: {
      show: false,
    }
  },
  fill: {
      type:"gradient",
      gradient: {
          type: "vertical",
          shadeIntensity: 1,
          inverseColors: !1,
          opacityFrom: .28,
          opacityTo: .05,
          stops: [45, 100]
      }
  },
  responsive: [{
    breakpoint: 575,
    options: {
      legend: {
          offsetY: -30,
      },
    },
  }]
}
</script>
@endif

@endsection

@section('footer')
<!-- footer script if required -->
