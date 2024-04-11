let requestPending = false;
$(document).ready(function(){
  // blokUI('Sedang Mengambil Data . . .');
  getData();
  trafficInfo();
  setInterval(getData, timeout);
  $('#traffic-title').html(`Traffic ${ sumberInternet }`);
});

const getData = () => {
  if (!requestPending) {
    requestPending = true;
    getClock();
    getResource();
    getInterface();
    getLog();
    requestPending = false; 
  }
}

const getClock = () => {
  const response = (resp) => {
    const tanggal = new Date(resp.data.date);

    $('#clock-day').html(`| ${ hari[tanggal.getDay()] }`);
    $('#clock-time').html(resp.data.time);
    $('#clock-date').html(`${ tanggal.getDate() } ${ bulan[tanggal.getMonth()] } ${ tanggal.getFullYear() }`);

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }
  requestData('post', `${baseL}/api/get-clock`, null, response, 'logout', ()=>{
    requestPending = false;         
    $.unblockUI();
  });
}

const getResource = () => {
  const response = (resp) => {
    $('#router-info-title').html(`${ resp.data.platform } <span>| ${ resp.data['board-name']}</span>`);
    $('#router-info-model').html(resp.data.model);
    $('#router-info-version').html(resp.data.version);
    $('#cpu-info').html(`| ${ resp.data.cpu }`);
    $('#cpu-load').html(`Load : ${ resp.data['cpu-load'] } %`);
    $('#cpu-frequency').html(`${ resp.data['cpu-frequency'] } MHz`);

    $('#memory_dashboard_title').html(`Memory (${ (resp.data['total-memory'] / 1049000).toFixed(0) } MiB)`);
    memoryInfo(resp.data);

    $('#hdd_dashboard_title').html(`HDD (${ (resp.data['total-hdd-space'] /1049000).toFixed(0) } MiB)`);
    hddInfo(resp.data)

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }
  requestData('post', `${baseL}/api/get-resource`, null, response, 'logout', ()=>{
    requestPending = false;         
    $.unblockUI();
  });
}

const getInterface = () => {
  const response = (resp) => {
    $('#int_list').empty();
    resp.data.forEach(val => {
      let html = '';
      html += `<tr>`;
      html += `<td>${ val.name }</td>`;
      html += `<td>${ val.type }</td>`;
      html += `<td>${ val.running === 'true' ? 'Connected' : '' }</td>`;
      html += `</td>`;
      $('#int_list').append(html);
    });

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }

  requestData('post', `${baseL}/api/interfaces`, null, response, 'logout', ()=>{
    requestPending = false;         
    $.unblockUI();
  });
}

const getLog = () => {
  const response = (resp) => {
    let data = resp.data;
    $('#log_dashboard').empty();
    for(var i = data.length-1; i > 0; i--){
      if (data.length-i === 5) {
        return;
      }

      let html = '';
      html += `<tr class="${ data[i]['status']==='error' ? 'text-danger fst-italic' : ''}">`;
      const logDate = new Date(data[i]['created_at']);
      html += `<td>${logDate.getHours()}:${logDate.getMinutes()} ${logDate.getDate()} ${sBulan[logDate.getMonth()]}</td>`
      html += `<td>${ data[i]['deskripsi'] }</td>`
      html += `<td>${ data[i]['status'] }</td>`
      html += `</tr>`;
      $('#log_dashboard').append(html);
    }

    if (resp.status === 'fail') {
      notif('error', resp.message);
    }
  }
  requestData('post', `${baseL}/api/get-log`, null, response, 'logout', ()=>{
    requestPending = false;         
    $.unblockUI();
  });
}

const memoryInfo = (data) => {
  echarts.init(document.querySelector("#memory_stat")).setOption({
    tooltip: {
      trigger: 'item'
    },
    legend: {
      top: '0%',
      left: 'center'
    },
    series: [{
      name: 'Memory (MiB)',
      type: 'pie',
      radius: ['40%', '80%'],
      avoidLabelOverlap: false,
      label: {
          show: false,
          position: 'center'
      },
      emphasis: {
          label: {
              show: true,
              fontSize: '18',
              fontWeight: 'bold'
          }
      },
      labelLine: {
          show: false
      },
      data: [{
              value: data["total-memory"] - data["free-memory"],
              name: 'Used'
          },
          {
              value: data["free-memory"],
              name: 'Free'
          },
      ]
    }]
  });
}

const hddInfo = (data) => {
  echarts.init(document.querySelector("#hdd_stat")).setOption({
    tooltip: {
      trigger: 'item'
    },
    legend: {
      top: '0%',
      left: 'center'
    },
    series: [{
      name: 'HDD (MiB)',
      type: 'pie',
      radius: ['40%', '80%'],
      avoidLabelOverlap: false,
      label: {
        show: false,
        position: 'center'
      },
      emphasis: {
        label: {
          show: true,
          fontSize: '18',
          fontWeight: 'bold'
        }
      },
      labelLine: {
        show: false
      },
      data: [
        {
          value: data["total-hdd-space"] - data["free-hdd-space"],
          name: 'Used'
        },
        {
            value: data["free-hdd-space"],
            name: 'Free'
        },
      ]
    }]
  });
}

const trafficInfo = () => {
  var ctx = document.getElementById('traffic_dashboard').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00'],
      datasets: [
        {
          label: 'rx rate',
          data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          backgroundColor: [
            'rgba(255, 99, 132, 1)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)'
          ]
        }, {
          label: 'tx rate',
          data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
          backgroundColor: [
            'rgba(75, 192, 192, 1)'
          ],
          borderColor: [
            'rgba(75, 192, 192, 1)'
          ]
        }
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      },
      borderWidth: 1,
      showLine: true,
      spanGaps: true,
      animation: false,
    }
  });

  //get data traffic
  setInterval(()=>{
    const response = (resp) => {
      myChart.data.labels.push(resp.data.time);
      myChart.data.datasets[0].data.push(resp.data.rx_byte);
      myChart.data.datasets[1].data.push(resp.data.tx_byte);
  
      //remove first
      if (myChart.data.labels.length > 10) {
         myChart.data.labels.shift();
         myChart.data.datasets.forEach((dataset) => {
            dataset.data.shift();
         });
      }
      myChart.update();
  
      if (resp.status === 'fail') {
        notif('error', resp.message);
      }
    }
    let post_data = postData;
    post_data.interface = sumberInternet;
    requestData('post', `${baseL}/api/get-traffic`, post_data, response, 'logout', ()=>{
      requestPending = false;         
      $.unblockUI();
    });
  }, 3000)
}