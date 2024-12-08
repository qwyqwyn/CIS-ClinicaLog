<?php
session_start();
include('../database/config.php');
include('../php/user.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); 
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];

?>

<!DOCTYPE html> 
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>CIS:Clinicalog</title> 
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" /> 
    <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon"/>
 
    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({ 
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid", 
            "Font Awesome 5 Regular", 
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["../css/fonts.min.css"], 
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/plugins.min.css" />
    <link rel="stylesheet" href="../css/kaiadmin.min.css" />

    <!-- ICONS -->
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">

    <style>
      .sidebar {
          transition: background 0.3s ease;
          /* Initial background */
          background: linear-gradient(to bottom, #DB6079, #DA6F65, #E29AB4);
      }
      .logo-header {
          transition: background 0.3s ease;
      }
      .nav-item.active {
            background-color: rgba(0, 0, 0, 0.1); 
            color: #fff; 
        }

        .nav-item.active i {
            color: #fff;
        } 
  </style>
</head>
<body>
                <!--start card -->
                <div class="row">
                <div class="col-md-12">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Total for All Transactions</div>
                      <div class="card-tools">
                        <a
                          href="#"
                          class="btn btn-label-success btn-round btn-sm me-2"
                        >
                          <span class="btn-label">
                            <i class="fa fa-pencil"></i>
                          </span>
                          Export
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 375px">
                      <canvas id="statisticsChart"></canvas>
                    </div>
                    <div id="myChart"></div>
                  </div>
                </div>
              </div>                   
              </div>
 
    
    

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Dynamically load the sidebar
            $("#sidebar").load("sidebar.php", function(response, status, xhr) {
                if (status == "error") {
                    console.log("Error loading sidebar: " + xhr.status + " " + xhr.statusText);
                } else {
                    var currentPage = window.location.pathname.split('/').pop(); 
                    $('.nav-item').removeClass('active');
                    $('.nav-item').each(function() {
                        var href = $(this).find('a').attr('href');
                        if (href.indexOf(currentPage) !== -1) {
                            $(this).addClass('active');
                        }
                    });
                }
            });

            $("#header").load("header.php", function(response, status, xhr) {
                if (status == "error") {
                    console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
                }
            });
        });
    </script>

<script>
  // Chart initialization
  var ctx = document.getElementById('statisticsChart').getContext('2d');
  var statisticsChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Faculties",
        borderColor: '#f3545d',
        pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
        pointRadius: 0,
        backgroundColor: 'rgba(243, 84, 93, 0.4)',
        legendColor: '#f3545d',
        fill: true,
        borderWidth: 2,
        data: [15, 84, 15, 23, 20, 21, 20, 28, 22, 32, 30, 34]
      }, {
        label: "Students",
        borderColor: '#fdaf4b',
        pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
        pointRadius: 0,
        backgroundColor: 'rgba(253, 175, 75, 0.4)',
        legendColor: '#fdaf4b',
        fill: true,
        borderWidth: 2,
        data: [26, 20, 25, 87, 40, 50, 30, 95, 31, 41, 46, 51]
      }, {
        label: "Staffs",
        borderColor: '#177dff',
        pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
        pointRadius: 0,
        backgroundColor: 'rgba(23, 125, 255, 0.4)',
        legendColor: '#177dff',
        fill: true,
        borderWidth: 2,
        data: [52, 40, 40, 50, 30, 53, 80, 44, 68, 60, 70, 90]
      }, {
        label: "Extensions",
        borderColor: '#c7f2c4',
        pointBackgroundColor: 'rgba(199, 242, 196, 0.6)',
        pointRadius: 0,
        backgroundColor: 'rgba(199, 242, 196, 0.4)',
        legendColor: '#c7f2c4',
        fill: true,
        borderWidth: 2,
        data: [50, 40, 42, 54, 50, 53, 38, 44, 58, 10, 70, 90]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false
      },
      tooltips: {
        bodySpacing: 4,
        mode: "nearest",
        intersect: 0,
        position: "nearest",
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10
      },
      layout: {
        padding: { left: 5, right: 5, top: 15, bottom: 15 }
      },
      scales: {
        yAxes: [{
          ticks: {
            fontStyle: "500",
            beginAtZero: false,
            maxTicksLimit: 5,
            padding: 10
          },
          gridLines: {
            drawTicks: false,
            display: false
          }
        }],
        xAxes: [{
          gridLines: {
            zeroLineColor: "transparent"
          },
          ticks: {
            padding: 10,
            fontStyle: "500"
          }
        }]
      },
      legendCallback: function (chart) {
        var text = [];
        text.push('<ul class="' + chart.id + '-legend html-legend">');
        for (var i = 0; i < chart.data.datasets.length; i++) {
          text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>');
          if (chart.data.datasets[i].label) {
            text.push(chart.data.datasets[i].label);
          }
          text.push('</li>');
        }
        text.push('</ul>');
        return text.join('');
      }
    }
  });

  // Generate HTML legend
  var myLegendContainer = document.getElementById("myChart");
  myLegendContainer.innerHTML = statisticsChart.generateLegend();

  // Toggle visibility on legend item click
  function legendClickCallback(event) {
    const item = event.target.closest('li');
    const index = Array.from(legendItems).indexOf(item);
    const meta = statisticsChart.getDatasetMeta(index);

    // Check if we're showing only one dataset or all
    const currentlyOnlyOneVisible = statisticsChart.data.datasets.filter((ds, i) => !statisticsChart.getDatasetMeta(i).hidden).length === 1;

    if (currentlyOnlyOneVisible && !meta.hidden) {
      // All datasets were hidden except this one, so show all
      statisticsChart.data.datasets.forEach((dataset, i) => {
        statisticsChart.getDatasetMeta(i).hidden = false;
      });
    } else {
      // Show only the clicked dataset, hide others
      statisticsChart.data.datasets.forEach((dataset, i) => {
        statisticsChart.getDatasetMeta(i).hidden = i !== index;
      });
      meta.hidden = false;
    }

    statisticsChart.update(); // Refresh chart
  }

  // Bind click event to legend items
  var legendItems = myLegendContainer.getElementsByTagName('li');
  for (var i = 0; i < legendItems.length; i++) {
    legendItems[i].addEventListener("click", legendClickCallback, false);
  }
</script>




</body>
</html>