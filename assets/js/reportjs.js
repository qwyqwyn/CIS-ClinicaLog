let transactionChart; 

function initializeYearDropdown() {
    const yearSelect = document.getElementById('yearSelect');
    const currentYear = new Date().getFullYear();
    const yearsToShow = 8; 

    for (let i = 0; i < yearsToShow; i++) {
        const year = currentYear - i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
    }

    yearSelect.value = currentYear;
    loadDataForYear(currentYear);
}

function loadDataForYear(year) {
  fetch(`../php-admin/reportscontrol.php?year=${year}`)
      .then(response => {
          if (!response.ok) {
              throw new Error('Network response was not ok');
          }
          return response.json();   
      })
      .then(data => {
          if (data.categoryData) {
              updateLineChart(data.categoryData); 
          }
          if (data.general_transactions) {
            updateChart(data.general_transactions);
          }
          if (data.medical_certificates) {
              updateMedicalCertificatesTable(data.medical_certificates);
          }
          if (data.consultation_treatments) {
              updateMedicalConsultationTable(data.consultation_treatments);
          }

          if (data.dental_checkups) {
            updateDentalCheckupTable(data.dental_checkups);
        }
      })
      .catch(error => {
          console.error('Error fetching data:', error);
      });
}

let lineChart;

function updateLineChart(categoryData) {
    const xValues = [];
    const yValues = [];

    for (const program in categoryData.students) {
        xValues.push(program);
        yValues.push(categoryData.students[program]); 
    }

    xValues.push('Faculty', 'Staff', 'Extension');
    yValues.push(categoryData.faculty, categoryData.staff, categoryData.extension);

    if (lineChart) {
        
        lineChart.data.labels = xValues;
        lineChart.data.datasets[0].data = yValues;
        lineChart.update(); 
    } else {
        lineChart = new Chart("lineChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    
                    fill: false,
                    lineTension: 0.1,
                    backgroundColor: "rgba(0, 0, 255, 0.8)",
                    borderColor: "rgba(0, 0, 255, 0.4)",
                    label: "Total of All Services",
                    data: yValues
                }]
            },
            options: {
                legend: { display: true },
                scales: {
                    x: {
                        ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    }
                }
            }
        });
    }
}



function createTranChart(data) {
    const ctx = document.getElementById('statisticsChart').getContext('2d');
    transactionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.months,
            datasets: [{
                label: "Faculties",
                borderColor: '#f3545d',
                pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
                pointRadius: 0,
                backgroundColor: 'rgba(243, 84, 93, 0.4)',
                legendColor: '#f3545d',
                fill: true,
                borderWidth: 2,
                data: data.faculty
            }, {
                label: "Students",
                borderColor: '#fdaf4b',
                pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
                pointRadius: 0,
                backgroundColor: 'rgba(253, 175, 75, 0.4)',
                legendColor: '#fdaf4b',
                fill: true,
                borderWidth: 2,
                data: data.students
            }, {
                label: "Staffs",
                borderColor: '#177dff',
                pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
                pointRadius: 0,
                backgroundColor: 'rgba(23, 125, 255, 0.4)',
                legendColor: '#177dff',
                fill: true,
                borderWidth: 2, 
                data: data.staff
            }, {
                label: "Extensions",
                borderColor: '#c7f2c4',
                pointBackgroundColor: 'rgba(199, 242, 196, 0.6)',
                pointRadius: 0,
                backgroundColor: 'rgba(199, 242, 196, 0.4)',
                legendColor: '#c7f2c4',
                fill: true,
                borderWidth: 2,
                data: data.extension
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

    var myLegendContainer = document.getElementById("myChart");
    myLegendContainer.innerHTML = transactionChart.generateLegend();

    var legendItems = myLegendContainer.getElementsByTagName('li');
    for (var i = 0; i < legendItems.length; i++) {
        legendItems[i].addEventListener("click", legendClickCallback, false);
    }
}

function updateChart(data) {
    if (transactionChart) {
        transactionChart.data.labels = data.months;
        transactionChart.data.datasets[0].data = data.faculty;
        transactionChart.data.datasets[1].data = data.students;
        transactionChart.data.datasets[2].data = data.staff;
        transactionChart.data.datasets[3].data = data.extensions;
        transactionChart.update();
    } else {
        createTranChart(data); 
    }
}

function legendClickCallback(event) {
    const item = event.target.closest('li');
    const index = Array.from(item.parentElement.children).indexOf(item);  
    const meta = transactionChart.getDatasetMeta(index);

    const currentlyOnlyOneVisible = transactionChart.data.datasets.filter((ds, i) => !transactionChart.getDatasetMeta(i).hidden).length === 1;

    if (currentlyOnlyOneVisible && !meta.hidden) {
        transactionChart.data.datasets.forEach((dataset, i) => {
            transactionChart.getDatasetMeta(i).hidden = false;
        });
    } else {
        transactionChart.data.datasets.forEach((dataset, i) => {
            transactionChart.getDatasetMeta(i).hidden = i !== index;
        });
        meta.hidden = false;
    }

    transactionChart.update();
} 

function updateMedicalCertificatesTable(medicalData) {
  const rows = document.querySelectorAll('#medcert tbody tr');
  
  rows.forEach((row, index) => {
      const month = row.querySelector('td').innerText.trim();

      const monthIndex = new Date(`${month} 1`).getMonth(); 

      const facultyCount = medicalData.medical_faculty[monthIndex] || 0;
      const staffCount = medicalData.medical_staff[monthIndex] || 0;
      const studentCount = medicalData.medical_students[monthIndex] || 0;
      const extensionCount = medicalData.medical_extension[monthIndex] || 0;

      row.cells[1].innerText = facultyCount;
      row.cells[2].innerText = staffCount;
      row.cells[3].innerText = studentCount;
      row.cells[4].innerText = extensionCount;

      const total = facultyCount + staffCount + studentCount + extensionCount;
      row.cells[5].innerText = total;
  });
}

function updateMedicalConsultationTable(consultData) {
  const rows = document.querySelectorAll('#consult tbody tr');
  
  rows.forEach((row, index) => {
      const month = row.querySelector('td').innerText.trim();

      const monthIndex = new Date(`${month} 1`).getMonth(); 

      const facultyCount = consultData.consultation_faculty[monthIndex] || 0;
      const staffCount = consultData.consultation_staff[monthIndex] || 0;
      const studentCount = consultData.consultation_students[monthIndex] || 0;
      const extensionCount = consultData.consultation_extension[monthIndex] || 0;

      row.cells[1].innerText = facultyCount;
      row.cells[2].innerText = staffCount;
      row.cells[3].innerText = studentCount;
      row.cells[4].innerText = extensionCount;

      const total = facultyCount + staffCount + studentCount + extensionCount;
      row.cells[5].innerText = total;
  });
}

function updateDentalCheckupTable(checkupData) {
  const rows = document.querySelectorAll('#checkup tbody tr');
  
  rows.forEach((row, index) => {
      const month = row.querySelector('td').innerText.trim();

      const monthIndex = new Date(`${month} 1`).getMonth(); 

      const facultyCount = checkupData.dental_faculty[monthIndex] || 0;
      const staffCount = checkupData.dental_staff[monthIndex] || 0;
      const studentCount = checkupData.dental_students[monthIndex] || 0;
      const extensionCount = checkupData.dental_extension[monthIndex] || 0;

      row.cells[1].innerText = facultyCount;
      row.cells[2].innerText = staffCount;
      row.cells[3].innerText = studentCount;
      row.cells[4].innerText = extensionCount;

      const total = facultyCount + staffCount + studentCount + extensionCount;
      row.cells[5].innerText = total;
  });
}

 
document.addEventListener('DOMContentLoaded', initializeYearDropdown);

async function exportToExcel() {
    const year = document.getElementById("yearSelect").value; 

    try {
        const response = await fetch(`reportexportcategory.php?year=${year}`);
        const data = await response.json();

        if (data.error) {
            console.error("Error fetching data:", data.error);
            return;
        }

        const months = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December'
        ];

        const programs = Object.keys(data[0] || {}).filter(key => key.endsWith('_count') && key !== 'monthly_total')
            .map(key => key.replace('_count', ''));

        const fullData = [];
        let totalRow = {
            month: 'Total',
            ...programs.reduce((totals, program) => {
                totals[program] = 0;
                return totals;
            }, {}),
            faculty: 0,
            staff: 0,
            extension: 0,
            monthly_total: 0
        };

        let dataFound = false;
        months.forEach(month => {
            const monthData = data.find(row => row.month === month);
        
            const row = { month: month };
        
            if (monthData) {
                dataFound = true;
        
                programs.forEach(program => {
                    const count = Number(monthData[`${program}_count`] || 0); 
                    row[program] = count;
                });

                row.faculty = Number(monthData.faculty_count || 0);
                row.staff = Number(monthData.staff_count || 0);
                row.extension = Number(monthData.extension_count || 0);
                row.monthly_total = Number(monthData.monthly_total || 0);

                fullData.push(row);
            } else {
                
                programs.forEach(program => {
                    row[program] = 0; 
                });
                row.faculty = 0;
                row.staff = 0;
                row.extension = 0;
                row.monthly_total = 0;
        
                fullData.push(row);
            }
        });

        programs.forEach(program => {
            totalRow[program] = fullData.reduce((sum, row) => sum + row[program], 0);
        });
        totalRow.faculty = fullData.reduce((sum, row) => sum + row.faculty, 0);
        totalRow.staff = fullData.reduce((sum, row) => sum + row.staff, 0);
        totalRow.extension = fullData.reduce((sum, row) => sum + row.extension, 0);
        totalRow.monthly_total = fullData.reduce((sum, row) => sum + row.monthly_total, 0);
        
        if (dataFound) {
            fullData.push(totalRow);
        }
        
        console.log("Computed Total Row:", totalRow);
        
        
        const worksheet = XLSX.utils.json_to_sheet(fullData);

        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, `Data ${year}`);

        XLSX.writeFile(workbook, `SummaryofAllServices${year}.xlsx`);

    } catch (error) {
        console.error("Error:", error);
    }
}

function reportToExportAllTable() {
    var year = document.getElementById("yearSelect").value || new Date().getFullYear();
    var url = 'reportexportalltable.php?year=' + year;

    function getMonthName(monthNumber) {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return monthNames[monthNumber - 1];
    }


    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                alert('No data available for the selected year.');
                return;
            }

            const headers = [
                "Month", 
                "Dental Check Up (Student)", "Dental Check Up (Faculty)", "Dental Check Up (Staff)", "Dental Check Up (Extension)", "Total Dental",
                "Consultation (Student)", "Consultation (Faculty)", "Consultation (Staff)", "Consultation (Extension)", "Total Consultation",
                "Medical Cert (Student)", "Medical Cert (Faculty)", "Medical Cert (Staff)", "Medical Cert (Extension)", "Total Medical Cert",
                "Grand Total"
            ];

            let sheetData = [headers];
            data.forEach(row => {
                const rowData = [
                    getMonthName(row.month),
                    row.dental_student, row.dental_faculty, row.dental_staff, row.dental_extension, row.dental_sum,
                    row.consult_student, row.consult_faculty, row.consult_staff, row.consult_extension, row.consult_sum,
                    row.medical_cert_student, row.medical_cert_faculty, row.medical_cert_staff, row.medical_cert_extension, row.medical_cert_sum,
                    row.total_sum
                ];
                sheetData.push(rowData);
            });

            const ws = XLSX.utils.aoa_to_sheet(sheetData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Summary");
            XLSX.writeFile(wb, `Summary_${year}.xlsx`);
        })
        .catch(error => {
            alert('Error fetching data for export: ' + error);
        });
}
