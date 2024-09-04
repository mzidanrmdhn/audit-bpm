<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitId = getUnitIdFromURL();
        if (unitId) {
            fetchChartData(unitId);
        } else {
            console.error('Unit ID tidak ditemukan di URL.');
        }
    });

    function getUnitIdFromURL() {
        const pathSegments = window.location.pathname.split('/');
        return pathSegments[pathSegments.length - 1]; // Mendapatkan segmen terakhir dari path
    }

    async function fetchChartData(unitId) {
        try {
            const response = await axios.get(`/get-grafik-data?unit_id=${unitId}`);
            const data = response.data;

            const chartContainer = document.getElementById('chartContainer');
            const allDataContainer = document.getElementById('allDataChartContainer');

            // Clear previous content
            chartContainer.innerHTML = '';
            allDataContainer.innerHTML = '';

            // Handle criteria charts
            if (data.criteriaData.length === 0) {
                chartContainer.innerHTML = '<p class="text-center text-gray-500">Tidak ada data untuk grafik kriteria.</p>';
            } else {
                data.criteriaData.forEach((chartData, index) => {
                    const count = chartData.datasets[0].data.length;

                    // Skip charts with insufficient data
                    if (count <= 1) {
                        console.log(`Data terlalu sedikit untuk membuat grafik untuk criteria ${index + 1}.`);
                        return;
                    }

                    const canvasWrapper = document.createElement('div');
                    canvasWrapper.className = 'w-full bg-white p-4 mt-3 rounded-lg shadow justify-center';

                    const canvasName = document.createElement('p');
                    canvasName.className = 'font-bold text-md mb-4 items-center';
                    canvasName.innerText = chartData.criteria;

                    const canvasElement = document.createElement('canvas');
                    canvasElement.id = `criteriaChart${index}`;
                    canvasElement.style.height = '100px';

                    canvasWrapper.appendChild(canvasName);
                    canvasWrapper.appendChild(canvasElement);
                    chartContainer.appendChild(canvasWrapper);

                    const ctx = canvasElement.getContext('2d');

                    let chartType;
                    if (count === 2) {
                        chartType = 'bar';
                    } else if (count >= 3) {
                        chartType = 'radar';
                    }

                    new Chart(ctx, {
                        type: chartType,
                        data: chartData,
                        options: {
                            responsive: true,
                            scales: chartType === 'bar' ? {
                                y: {
                                    beginAtZero: true
                                }
                            } : {
                                r: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            }

            // Handle all data chart
            if (data.allData.length === 0 || data.allData[0].datasets[0].data.every(value => value === 0)) {
                allDataContainer.innerHTML = '<p class="text-center text-gray-500">Tidak ada data untuk grafik capaian mutu.</p>';
            } else {
                data.allData.forEach((chartData, index) => {
                    const canvasId = `allDataChart${index}`;
                    const canvasElement = document.createElement('canvas');
                    canvasElement.id = canvasId;
                    canvasElement.className = 'h-1/2';
                    allDataContainer.appendChild(canvasElement);

                    const ctx = canvasElement.getContext('2d');

                    new Chart(ctx, {
                        type: 'radar', // Tipe grafik yang sesuai
                        data: chartData,
                        options: {
                            responsive: true,
                            scales: {
                                r: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            }

        } catch (error) {
            console.error('Error fetching chart data:', error);
            // Optionally display an error message to the user
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.classList.remove('md:grid-cols-2');
            chartContainer.classList.remove('2xl:grid-cols-3');
            chartContainer.classList.add('grid-cols-1');
            chartContainer.innerHTML = '<p class="bg-white p-4 mt-3 rounded-lg shadow justify-center text-center w-full text-jet">Tidak ada data untuk memuat grafik</p>';

            const allDataContainer = document.getElementById('allDataChartContainer');
            allDataContainer.innerHTML = '<p class="text-center text-jet">Tidak ada data untuk membuat grafik</p>';
        }
    }


</script>
