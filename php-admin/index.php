<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/dashboard.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];

$dashboard = new Dashboard($conn); 

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
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar"></div>
        <!-- End Sidebar -->
        <div class="main-panel">
            <!-- Header --> 
            <div class="main-header" id="header"></div>
            <!-- Main Content -->
            <div class="container" id="content">
            <div class="page-inner">
            <div
              class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4"
            >
              <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-primary bubble-shadow-small"
                        >
                          <i class="fas fa-users"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                          <div class="numbers">
                              <p class="card-category">Active Patients</p>
                              <h4 class="card-title"><?php echo $dashboard->countActivePatients();; ?></h4>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-info bubble-shadow-small"
                        >
                          <i class="fas fa-user-check"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Active Staff Users</p>
                          <h4 class="card-title"><?php echo $dashboard->countActiveadminusers(); ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-success bubble-shadow-small"
                        >
                          <i class="fas fa-luggage-cart"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Medicines</p>
                          <h4 class="card-title"><?php echo $dashboard->countAvailableMedstocks();; ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-secondary bubble-shadow-small"
                        >
                          <i class="far fa-check-circle"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Transactions</p>
                          <h4 class="card-title"><?php echo $dashboard->countTransactions(); ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-8">
              <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">No. of Classified Active Patients</div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="chart-container" style="min-height: 375px">
                      <canvas id="statisticsChart"></canvas>
                    </div>
                    <div id="myChart"></div>
                    <?php
// Initialize an array to store data for Chart.js
$chartData = [
    'labels' => [],
    'Faculties' => [],
    'Students' => [],
    'Staffs' => [],
    'Extensions' => []
];

// Generate a list of the last 12 months in ascending order
$allMonths = [];
$currentDate = new DateTime();
$currentDate->modify("-11 months"); // Start from 11 months ago
for ($i = 0; $i < 12; $i++) {
    $month = $currentDate->format("Y-m");
    $allMonths[] = $month;
    $currentDate->modify("+1 month");
}


// Populate chart data with monthly counts
foreach ($allMonths as $month) {
    // Extract start and end dates for the month
    $start_date = $month . '-01';
    $end_date = (new DateTime($start_date))->modify('last day of this month')->format('Y-m-d');

    $chartData['labels'][] = $month;
    $chartData['Faculties'][] = array_sum($dashboard->countFacultyConsultationsPerMonth($start_date, $end_date));
    $chartData['Students'][] = array_sum($dashboard->countStudentConsultationsPerMonth($start_date, $end_date));
    $chartData['Staffs'][] = array_sum($dashboard->countStaffConsultationsPerMonth($start_date, $end_date));
    $chartData['Extensions'][] = array_sum($dashboard->countExtensionConsultationsPerMonth($start_date, $end_date));
}

// Pass the chart data as JSON to the frontend
echo '<script>var chartData = ' . json_encode($chartData) . ';</script>';
?>



                  </div>
                </div>
              </div>
              <div class="col-md-4">
              <div class="card card-equal-height">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Short-dated Medicines</h4>
                </div>
            </div>
                <div class="card-body">
  
                </div>

                <div class="table-responsive">
                    <table id="medcert" class="display table table-striped table-hover">
                    <?php
                    $daysThreshold = 30;
                    $medstocks = $dashboard->getAlmostExpiredMedstocks($daysThreshold);
                    ?>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Expiration Date</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Expiration Date</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php if (!empty($medstocks)): ?>
                                <?php foreach ($medstocks as $medstock): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($medstock['medstock_id']); ?></td>
                                        <td><?= htmlspecialchars($medstock['medicine_name']); ?></td>
                                        <td><?= htmlspecialchars($medstock['medstock_expirationdt']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No almost expired medicines found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>


                </div>
            </div>
        </div>
              </div>
            </div>

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
  // Access the chart data from PHP
  const labels = chartData.labels; // Months
  const datasets = [
    {
      label: "Faculties",
      borderColor: '#f3545d',
      pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
      pointRadius: 0,
      backgroundColor: 'rgba(243, 84, 93, 0.4)',
      legendColor: '#f3545d',
      fill: true,
      borderWidth: 2,
      data: chartData.Faculties,
    },
    {
      label: "Students",
      borderColor: '#fdaf4b',
      pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
      pointRadius: 0,
      backgroundColor: 'rgba(253, 175, 75, 0.4)',
      legendColor: '#fdaf4b',
      fill: true,
      borderWidth: 2,
      data: chartData.Students,
    },
    {
      label: "Staffs",
      borderColor: '#177dff',
      pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
      pointRadius: 0,
      backgroundColor: 'rgba(23, 125, 255, 0.4)',
      legendColor: '#177dff',
      fill: true,
      borderWidth: 2,
      data: chartData.Staffs,
    },
    {
      label: "Extensions",
      borderColor: '#c7f2c4',
      pointBackgroundColor: 'rgba(199, 242, 196, 0.6)',
      pointRadius: 0,
      backgroundColor: 'rgba(199, 242, 196, 0.4)',
      legendColor: '#c7f2c4',
      fill: true,
      borderWidth: 2,
      data: chartData.Extensions,
    },
  ];

  // Chart initialization
  var ctx = document.getElementById('statisticsChart').getContext('2d');
  var statisticsChart = new Chart(ctx, {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false, // Disable default legend
      },
      tooltips: {
        bodySpacing: 4,
        mode: "nearest",
        intersect: 0,
        position: "nearest",
        xPadding: 10,
        yPadding: 10,
        caretPadding: 10,
      },
      layout: {
        padding: { left: 5, right: 5, top: 15, bottom: 15 },
      },
      scales: {
        yAxes: [{
          ticks: {
            fontStyle: "500",
            beginAtZero: false,
            maxTicksLimit: 5,
            padding: 10,
          },
          gridLines: {
            drawTicks: false,
            display: false,
          },
        }],
        xAxes: [{
          gridLines: {
            zeroLineColor: "transparent",
          },
          ticks: {
            padding: 10,
            fontStyle: "500",
          },
        }],
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
      },
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
