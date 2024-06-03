@extends('adminlte::page')

@section('title', 'Dashboard')
<!-- Add DataTables CSS -->

<head>
    <!-- Other head elements -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .chart-container {
            display: flex;
            justify-content: space-between;
        }

        .chart-container>div {
            width: 48%;
        }

        .chart-scroll {
            overflow-x: auto;
            max-width: 100%;
            height: 400px;

            /* Set the height to limit the scroll area */
        }

        .chart-title {
            text-align: center;
            margin-bottom: 20px;
        }



        .chart-container-date {

            height: 400px;
            max-width: 100%;
        }
    </style>
</head>

@section('content_header')
    @if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('koordinator-area'))
        <h1>Jumlah Wawancara Per Kabupaten/Kota</h1>
    @else
        <h1>Dashboard Admin</h1>
    @endif
@stop
@php
    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(27);

    $districts = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_city_id')) {
        $districts = $districts->districtsData(old('indonesia_city_id'));
    } else {
        $districts = [];
    }
    $villages = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_district_id')) {
        $villages = $villages->villagesData(old('indonesia_district_id'));
    } elseif (Gate::check('admin')) {
        $villages = $villages->villagesData(Auth::user()->district->id);
    } else {
        $villages = [];
    }
@endphp
@section('content')
    <form action="{{ route('dashboard') }}" method="GET">
        @if (Auth::user()->hasRole('super-admin'))
            <div class="pb-3 pt-3 pr-2"
                style="background-color: white; border: 1px solid #f3f1f1; border-radius: 5px;margin-right:10px;margin-bottom:10px;margin-top:10px">
                <div class="ms-2 d-inline-block">
                    <label for="lokasi" style="margin-right: 10px;">Pilih Lokasi</label>
                    <select name="indonesia_cities_id" class="form-select-sm" id="city" style="width: 200px;">
                        <option value="">Pilih Kabupaten</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}|{{ $city->name }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        @endif
    </form>
    <div class="chart-container">
        <div>
            <h4 class="chart-title">Kecamatan</h4>
            <div id="chart"></div>
        </div>
        <div class="chart-scroll">
            <h4 class="chart-title">Kelurahan/Desa</h4>
            <div id="chartVillage"></div>
        </div>

    </div>
    <br>
    <div class="chart-scroll">
        <h4 class="chart-title">TPS</h4>
        <div id="chartTps"></div>
    </div>
    <br>
    <div class="chart-container">
        <div class="chart-scroll">
            <h4 class="chart-title">Jumlah Wawancara Per Pekerjaan</h4>
            <div id="chartOccupation"></div>
        </div>
        <div>
            <h4 class="chart-title">Jumlah Wawancara Per Kategori Usia</h4>
            <div id="chartAge"></div>
        </div>
    </div>
    <br>
    <div class="chart-container">
        <div>
            <h4 class="chart-title">Jumlah Wawancara Per Pendidikan</h4>
            <div id="chartEducation"></div>
        </div>
        <div>
            <h4 class="chart-title">Jumlah Wawancara Per Jumlah Anggota Keluarga</h4>
            <div id="chartFamilyElection"></div>
        </div>
    </div>
    <br>
    <div class="chart-container-date">
        <div class="scrollable">
            <h4 class="chart-title">Jumlah Wawancara Per Waktu</h4>
            <div id="chart-timeline"></div>
        </div>

    </div>
    <div class="btn-group">
        <button id="one_month">1 Month</button>
        <button id="six_months">6 Months</button>
        <button id="one_year">1 Year</button>
        <button id="ytd">YTD</button>
        <button id="all">All</button>
    </div>


@stop

@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var jq = jQuery.noConflict();
    </script>

    <script>
        var resetCssClasses = function(activeEl) {
            var els = document.querySelectorAll('button')
            Array.prototype.forEach.call(els, function(el) {
                el.classList.remove('active')
            })

            activeEl.target.classList.add('active')
        }

        document
            .querySelector('#one_month')
            .addEventListener('click', function(e) {
                resetCssClasses(e)

                chart.zoomX(
                    new Date('28 Jan 2013').getTime(),
                    new Date('27 Feb 2013').getTime()
                )
            })

        document
            .querySelector('#six_months')
            .addEventListener('click', function(e) {
                resetCssClasses(e)

                chart.zoomX(
                    new Date('27 Sep 2012').getTime(),
                    new Date('27 Feb 2013').getTime()
                )
            })

        document
            .querySelector('#one_year')
            .addEventListener('click', function(e) {
                resetCssClasses(e)
                chart.zoomX(
                    new Date('27 Feb 2012').getTime(),
                    new Date('27 Feb 2013').getTime()
                )
            })

        document.querySelector('#ytd').addEventListener('click', function(e) {
            resetCssClasses(e)

            chart.zoomX(
                new Date('01 Jan 2013').getTime(),
                new Date('27 Feb 2013').getTime()
            )
        })

        document.querySelector('#all').addEventListener('click', function(e) {
            resetCssClasses(e)

            chart.zoomX(
                new Date('23 Jan 2012').getTime(),
                new Date('27 Feb 2013').getTime()
            )
        })

        function getOccupationsData(cityId) {
            jq.ajax({
                url: "{{ route('occupations') }}",
                data: {
                    id: cityId
                },
                type: 'GET',
                success: function(occupationsData) {
                    console.log("Occupations Data:");
                    console.log(occupationsData);

                    jq.ajax({
                        url: "{{ route('interviewsByOccupation') }}",
                        data: {
                            id: cityId
                        },
                        type: 'GET',
                        success: function(interviewDataOccupations) {
                            console.log("Interview Data Occupations:");
                            console.log(interviewDataOccupations);
                            var interviewCounts = [];
                            var occupationsNames = [];
                            for (var id in interviewDataOccupations) {
                                if (interviewDataOccupations.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewDataOccupations[id]
                                        .interview_count); // Ambil nilai interview_count
                                    occupationsNames.push(interviewDataOccupations[id]
                                        .job); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];

                            var barHeight = 30;

                            // Menetapkan tinggi grafik dengan logika kondisional
                            var chartHeight = occupationsNames.length < 10 ? 350 : occupationsNames
                                .length * 50;
                            chartOccupation.updateOptions({
                                chart: {
                                    height: chartHeight,
                                },
                                plotOptions: {
                                    bar: {

                                        barHeight: barHeight,

                                    }
                                },
                                xaxis: {
                                    categories: occupationsNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });
                            chartOccupation.updateSeries(newData);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching interview data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('interviewsByAge') }}",
                        data: {
                            id: cityId
                        },
                        type: 'GET',
                        success: function(interviewDataAge) {
                            console.log("Interview Data Age:");
                            console.log(interviewDataAge);
                            var interviewCounts = [];
                            interviewDataAge.sort((a, b) => {
                                // Menggunakan fungsi bantuan untuk mendapatkan nilai awal rentang usia
                                function getAgeRangeStart(range) {
                                    return parseInt(range.split('-')[0]);
                                }

                                // Menggunakan fungsi bantuan untuk membandingkan nilai age_range
                                return getAgeRangeStart(a.age_range) - getAgeRangeStart(b
                                    .age_range);
                            });

                            var interviewCounts = [];

                            // Loop melalui data dan mengambil nilai interview_count untuk setiap item
                            for (var i = 0; i < interviewDataAge.length; i++) {
                                interviewCounts.push(interviewDataAge[i].interview_count);
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];


                            chartAge.updateSeries(newData);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching interview data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('interviewsByEducation') }}",
                        data: {
                            id: cityId
                        },
                        type: 'GET',
                        success: function(interviewDataEducation) {
                            console.log("Interview Data Education:");
                            console.log(interviewDataEducation);

                            var interviewCounts = [];
                            var educationsNames = [];
                            for (var id in interviewDataEducation) {
                                if (interviewDataEducation.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewDataEducation[id]
                                        .interview_count); // Ambil nilai interview_count
                                    educationsNames.push(interviewDataEducation[id]
                                        .education); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];

                            chartEducation.updateOptions({
                                plotOptions: {
                                    bar: {
                                        horizontal: true, // Grafik horizontal

                                        dataLabels: {
                                            position: 'top', // Menempatkan label di sebelah kanan batang

                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        colors: ['#333']
                                    },
                                },
                                xaxis: {
                                    categories: educationsNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });
                            chartEducation.updateSeries(newData);
                        },

                        error: function(xhr, status, error) {
                            console.error("Error fetching education interview data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('interviewsByFamilyElection') }}",
                        data: {
                            id: cityId
                        },
                        type: 'GET',
                        success: function(interviewDataFamilyElection) {
                            console.log("Interview Data FamilyElection:");
                            console.log(interviewDataFamilyElection);

                            var interviewCounts = [];
                            var familyElectionsNames = [];
                            for (var id in interviewDataFamilyElection) {
                                if (interviewDataFamilyElection.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewDataFamilyElection[id]
                                        .interview_count); // Ambil nilai interview_count
                                    familyElectionsNames.push(interviewDataFamilyElection[id]
                                        .family_election); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];

                            chartFamilyElection.updateOptions({
                                plotOptions: {
                                    bar: {
                                        horizontal: true, // Grafik horizontal

                                        dataLabels: {
                                            position: 'top', // Menempatkan label di sebelah kanan batang

                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        colors: ['#333']
                                    },
                                },
                                xaxis: {
                                    categories: familyElectionsNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });
                            chartFamilyElection.updateSeries(newData);
                        },

                        error: function(xhr, status, error) {
                            console.error("Error fetching family election interview data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('interviewsByTps') }}",
                        data: {
                            id: cityId
                        },
                        type: 'GET',
                        success: function(interviewDataTps) {
                            console.log("Interview Data Tps:");
                            console.log(interviewDataTps);

                            var interviewCounts = [];
                            var tpsNames = [];
                            for (var id in interviewDataTps) {
                                if (interviewDataTps.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewDataTps[id]
                                        .interview_count); // Ambil nilai interview_count
                                    tpsNames.push(interviewDataTps[id]
                                        .tps); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];
                            var chartHeight = tpsNames.length < 10 ? 350 : tpsNames
                                .length * 50;

                            chartTps.updateOptions({
                                chart: {
                                    height: chartHeight,
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: true, // Grafik horizontal
                                        barHeight: 30,
                                        dataLabels: {
                                            position: 'top', // Menempatkan label di sebelah kanan batang

                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        colors: ['#333']
                                    },
                                },
                                xaxis: {
                                    categories: tpsNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });
                            chartTps.updateSeries(newData);
                        },

                        error: function(xhr, status, error) {
                            console.error("Error fetching tps interview data:", error);
                        }
                    });

                },

                error: function(xhr, status, error) {
                    console.error("Error fetching occupation data:", error);
                }
            });
        }
        // Mendefinisikan fungsi untuk mengambil data district dari server
        function getDistrictData(cityId) {
            jq.ajax({
                url: "{{ route('districts') }}",
                type: 'GET',
                data: {
                    id: cityId
                },
                success: function(data) {
                    console.log("Data District:");
                    console.log(data);

                    var districtNames = Object.values(data);

                    // Gunakan nama-nama distrik untuk mengatur kategori di grafik
                    optionsDistrict.xaxis.categories = districtNames;

                    // Render kembali grafik dengan kategori baru
                    chart.updateOptions(optionsDistrict);
                    jq.ajax({
                        url: "{{ route('interviews') }}?id=" + encodeURIComponent(cityId),
                        type: 'GET',
                        success: function(interviewData) {
                            console.log("Interview Data:");
                            console.log(interviewData);
                            var interviewCounts = [];
                            var districtNames = [];

                            // Loop melalui interviewData untuk mendapatkan nilai interview_count dan nama distrik
                            for (var id in interviewData) {
                                if (interviewData.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewData[id]
                                        .interview_count); // Ambil nilai interview_count
                                    districtNames.push(interviewData[id]
                                        .district_name); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];

                            chart.updateOptions({
                                plotOptions: {
                                    bar: {
                                        horizontal: true, // Grafik horizontal

                                        dataLabels: {
                                            position: 'top', // Menempatkan label di sebelah kanan batang

                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        colors: ['#333']
                                    },
                                },

                                xaxis: {
                                    categories: districtNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });
                            chart.updateSeries(newData);

                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching interview data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('allVillages') }}",
                        type: 'GET',
                        data: {
                            id: cityId

                        },
                        success: function(villageData) {
                            console.log("Village Data:");
                            console.log(villageData);


                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching village data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('interviewByVillages') }}?id=" + encodeURIComponent(cityId),
                        type: 'GET',
                        success: function(interviewDataVillages) {
                            console.log("Interview Data Villages:");
                            console.log(interviewDataVillages);
                            var interviewCounts = [];
                            var villagesNames = [];
                            for (var id in interviewDataVillages) {
                                if (interviewDataVillages.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewDataVillages[id]
                                        .interview_count); // Ambil nilai interview_count
                                    villagesNames.push(interviewDataVillages[id]
                                        .village_name); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];

                            var barHeight = 30;

                            // Menetapkan tinggi grafik dengan logika kondisional
                            var chartHeight = villagesNames.length < 10 ? 350 : villagesNames
                                .length * 50;

                            chartVillage.updateOptions({
                                chart: {
                                    height: chartHeight,
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: true, // Grafik horizontal
                                        barHeight: barHeight,
                                        dataLabels: {
                                            position: 'top', // Menempatkan label di sebelah kanan batang

                                        }
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        colors: ['#333']
                                    },

                                },
                                xaxis: {
                                    categories: villagesNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });
                            chartVillage.updateSeries(newData);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching interview data:", error);
                        }
                    });
                    jq.ajax({
                        url: "{{ route('interviewsByDate') }}?id=" + encodeURIComponent(cityId),
                        type: 'GET',
                        success: function(interviewDataDate) {
                            console.log("Interview Data Date:");
                            console.log(interviewDataDate);
                            var interviewCounts = [];
                            var dateNames = [];
                            for (var id in interviewDataDate) {
                                if (interviewDataDate.hasOwnProperty(id)) {
                                    interviewCounts.push(interviewDataDate[id]
                                        .interview_count); // Ambil nilai interview_count
                                    dateNames.push(new Date(interviewDataDate[id].interview_date)
                                        .toLocaleDateString('id-ID')
                                        ); // Menggunakan fungsi toLocaleDateString() untuk mendapatkan format tanggal yang sesuai); // Ambil indonesia_district_id
                                }
                            }

                            // Gunakan interviewCounts sebagai data series untuk sumbu y
                            var newData = [{
                                data: interviewCounts
                            }];

                            var barHeight = 30;

                            // Menetapkan tinggi grafik dengan logika kondisional
                            var chartHeight = dateNames.length < 10 ? 350 : dateNames
                                .length * 50;


                            // Mengubah dateNames menjadi array dari timestamp Unix


                            chartDate.updateOptions({


                                xaxis: {
                                    tickAmount: dateNames.length,
                                    categories: dateNames // Atur districtNames sebagai kategori sumbu x
                                }
                            });

                            chartDate.updateSeries(newData);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching interview data:", error);
                        }
                    });

                },
                // Lakukan apa yang diperlukan dengan data district di sini

                error: function(xhr, status, error) {
                    console.error("Error fetching district data:", error);
                }
            });
        }


        jq(function() {

            // Tangkap perubahan pada elemen select untuk kota
            jq('#city').on('change', function() {
                var cityId = jq(this).val();
                console.log("Selected City ID:", cityId);
                getOccupationsData(cityId);
                // Panggil fungsi untuk mengambil data district
                getDistrictData(cityId);
            });
        });


        var optionsDistrict = {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4 // Menyesuaikan radius sudut
                }
            },
            dataLabels: {
                enabled: false
            },

        };

        var chart = new ApexCharts(document.querySelector("#chart"), optionsDistrict);
        chart.render();
        var optionsDate = {
            series: [{
                data: []
            }],
            chart: {
                id: 'area-datetime',
                type: 'area',
                height: 350,
                zoom: {
                    autoScaleYaxis: true
                }
            },
            annotations: {
                yaxis: [{
                    y: 30,
                    borderColor: '#999',
                    label: {
                        style: {
                            color: "#fff",
                            background: '#00E396'
                        }
                    }
                }],
                xaxis: [{
                    type: 'category',
                    labels: {
                        rotate: -90,
                        trim: false
                    },
                    borderColor: '#999',
                    yAxisIndex: 0,
                    label: {
                        show: true,
                        style: {
                            color: "#fff",
                            background: '#775DD0'
                        }
                    }
                }]
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 0,
                style: 'hollow',
            },
            xaxis: {
                type: 'category',
                labels: {
                    rotate: -90,
                    trim: false,
                    datetimeUTC: false,
                    formatter: function(value, timestamp) {
                        return new Date(value).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 100]
                }
            },
        };

        var chartDate = new ApexCharts(document.querySelector("#chart-timeline"), optionsDate);



        var chartDate = new ApexCharts(document.querySelector("#chart-timeline"), optionsDate);
        chartDate.render();
        var optionsVillage = {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: '100%', // Set tinggi ke 100% untuk menyesuaikan dengan tinggi container

            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4 // Menyesuaikan radius sudut
                }
            },
            dataLabels: {
                enabled: false
            },

        };

        var chartVillage = new ApexCharts(document.querySelector("#chartVillage"), optionsVillage);
        chartVillage.render();

        var optionsOccupation = {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: '100%', // Set tinggi ke 100% untuk menyesuaikan dengan tinggi container

            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top', // Menempatkan label di sebelah kanan batang

                    }
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#333']
                },
            },

        };

        var chartOccupation = new ApexCharts(document.querySelector("#chartOccupation"), optionsOccupation);
        chartOccupation.render();
        var optionsEducation = {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350, // Set tinggi ke 100% untuk menyesuaikan dengan tinggi container

            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4 // Menyesuaikan radius sudut

                }
            },
            dataLabels: {
                enabled: false
            },

        };

        var chartEducation = new ApexCharts(document.querySelector("#chartEducation"), optionsEducation);
        chartEducation.render();

        var optionsTps = {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: '100%', // Set tinggi ke 100% untuk menyesuaikan dengan tinggi container

            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4 // Menyesuaikan radius sudut
                }
            },
            dataLabels: {
                enabled: false
            },

        };

        var chartTps = new ApexCharts(document.querySelector("#chartTps"), optionsTps);
        chartTps.render();

        var optionsFamilyElection = {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350, // Set tinggi ke 100% untuk menyesuaikan dengan tinggi container

            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4 // Menyesuaikan radius sudut
                }
            },
            dataLabels: {
                enabled: false
            },

        };

        var chartFamilyElection = new ApexCharts(document.querySelector("#chartFamilyElection"),
            optionsFamilyElection);
        chartFamilyElection.render();

        var options = {
            series: [{
                data: []
            }],
            chart: {
                height: 350,
                type: 'bar',
                events: {
                    click: function(chart, w, e) {
                        // console.log(chart, w, e)
                    }
                }
            },

            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true,
                    dataLabels: {
                        position: 'top', // Menempatkan label di sebelah kanan batang

                    }

                },
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#333']
                },
            },


            legend: {
                show: false
            },
            xaxis: {
                categories: [
                    ['17'],
                    ['17-27'],
                    ['28-41'],
                    ['42-57'],
                    ['58-76'],
                    ['>77'],

                ],

            }
        };

        var chartAge = new ApexCharts(document.querySelector("#chartAge"), options);
        chartAge.render();
    </script>



@stop
{{-- @php
    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(27);

    $districts = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_city_id')) {
        $districts = $districts->districtsData(old('indonesia_city_id'));
    } else {
        $districts = [];
    }
    $villages = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_district_id')) {
        $villages = $villages->villagesData(old('indonesia_district_id'));
    } elseif (Gate::check('admin')) {
        $villages = $villages->villagesData(Auth::user()->district->id);
    } else {
        $villages = [];
    }
@endphp
@section('content')

    <form action="{{ route('dashboard') }}" method="GET">
        @if (Auth::user()->hasRole('super-admin'))
            <div class="pb-3 pt-3 pr-2"
                style="background-color: white; border: 1px solid #f3f1f1; border-radius: 5px;margin-right:10px;margin-bottom:10px;margin-top:10px">
                <div class="ms-2 d-inline-block">
                    <label for="lokasi" style="margin-right: 10px;">Pilih Lokasi</label>
                    <select name="indonesia_cities_id" class="form-select-sm" id="city" style="width: 200px;">
                        <option value="">Pilih Kabupaten</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}|{{ $city->name }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-primary btn-sm btn" type="submit"
                    style="width: 65px; color: black; font-weight: bold; background-color: #00A3FF;margin-left:30px">Filter</button>
            </div>
        @endif
    </form>
    @if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('koordinator-area'))
        <table id="volunteer-performa-table" class="display">
            <div class="btn-group" role="group" style="margin-left: 10px">
                <a href="{{ route('performa.export') }}?cities={{ Request::get('cities') }}&indonesia_cities_id={{ Request::get('indonesia_cities_id') }}"
                    class="btn btn-success" style="width: 100px">
                    Export
                    <i class="fas fa-save"></i>
                </a>
            </div>

            <thead>
                <tr>

                    <th>Email</th>
                    <th>Nama</th>
                    <th>Jumlah Wawancara</th>
                    <th>Frekuensi</th>
                    <th>Performa Rate</th>

                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>


                @foreach ($interviews as $interview)
                    <!-- Add your data rows here -->

                    <tr>
                        @php
                            $iv = App\Models\InterviewSchedule::with(['user', 'interview'])
                                ->has('user')
                                ->has('interview')
                                ->where('user_id', $interview->user_id)
                                ->select(\DB::raw('count(user_id) as interview_count')) // Use aggregate function
                                ->first();

                            $active_rate = ($interview->frequency / $interview->days_since_first_interview) * 100;
                            $qty_rate = ($iv->interview_count / 50) * 100;
                            $performance_rate = ($qty_rate + $active_rate) / 2;
                            // dd($iv);
                        @endphp

                        <td>{{ $interview->user ? $interview->user->email : '-' }}</td>
                        <td>{{ $interview->user ? $interview->user->name : '-' }}</td>
                        <td>{{ $iv->interview_count }}</td>
                        <td>{{ $interview->frequency }}</td>
                        <td>{{ number_format($performance_rate, 0) }}%</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@stop

@section('js')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></scrip>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#volunteer-performa-table').DataTable({
                "order": [
                    [4, 'desc']
                ],
                "lengthChange": false // Hide the "Show [number] entries" dropdow
            });

        });
    </script>
@stop --}}
