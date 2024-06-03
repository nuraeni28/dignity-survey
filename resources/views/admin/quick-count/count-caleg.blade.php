@extends('adminlte::page')

@section('title', 'Perolehan Suara Caleg')
<!-- Add DataTables CSS -->



@section('content_header')
    <h1>Jumlah TPS yang Telah Diinput</h1>
@stop

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;800;900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontt-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


</head>
@php
    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(Auth::user()->province->id);
    $districts = new App\Http\Controllers\DependentDropdownController();
    if (Auth::user()->city->id) {
        $districts = $districts->districtsData(Auth::user()->city->id);
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
    // dd($districts);
@endphp
@section('content')

    <table id="count-tps-table" class="display">


        <thead>
            <tr>
                <th>Kabupaten</th>
                <th>Jumlah TPS</th>
            </tr>
        </thead>
        <tbody>
              @php
                // dd($quickCounts);
                $city = \Laravolt\Indonesia\Models\City::whereIn('id', [413, 414, 416, 417, 418, 419, 415, 422, 412])->get();

            @endphp
            @foreach ($city as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    @php
                        $total_tps = 0;
                        foreach ($countTps as $count) {
                            if ($count['indonesia_city_id'] == $c->id) {
                                $total_tps = $count['total_tps'];
                                break;
                            }
                        }
                    @endphp
                    <td>{{ $total_tps }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <br>

    <h3>Perolehan Suara Caleg</h3>
    <br>
    <div class="card">
        <div class="card-body">
            <form id="filterForm" action="{{ route('quick-count.countCaleg') }}" method="GET">
                <div class="row">
                    <div class="col-6 form-group">
                        <x-adminlte-select id="district" name="indonesia_district_id" class="select2">
                            <option value="-" disabled selected>Pilih Kecamatan</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ old('indonesia_district_id') == $district->id || session('indonesia_district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                            <x-slot name="label">
                                <label for="district" class="required-label">Kecamatan</label>
                            </x-slot>
                        </x-adminlte-select>
                    </div>
                    <div class=" col-6 form-group">
                        <x-adminlte-select name="indonesia_village_id" id="village" class="select2">
                            <option value="-" disabled selected>Pilih Desa</option>
                            @foreach ($villages as $village)
                                <option value="{{ $village->id }}"
                                    {{ old('indonesia_village_id') == $village->id || session('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                                    {{ $village->name }}
                                </option>
                            @endforeach
                            <x-slot name="label">
                                <label for="village" class="required-label">Desa</label>
                            </x-slot>
                        </x-adminlte-select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>





    <table id="count-caleg-table" class="display">


        <thead>
            <tr>
                <th>TPS</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quickCounts as $q)
                <!-- Add your data rows here -->

                <tr>


                    <td>{{ $q->tps }}</td>
                    <td>
                        @php
                            // Cek apakah semua partai sudah ada untuk TPS tertentu
                            $partaiCount = \App\Models\QuickCount::where('tps', $q->tps)
                                ->where('indonesia_village_id', $q->indonesia_village_id)
                                ->count();
                            $isComplete = $partaiCount == $totalPartaiTersedia;
                            $partaiIds = \App\Models\QuickCount::where('tps', $q->tps)
                                ->where('indonesia_village_id', $q->indonesia_village_id)
                                ->pluck('partai_id');

                            $partaiBelumLengkap = \App\Models\Partai::whereNotIn('id', $partaiIds)->get();

                        @endphp
                        @if ($partaiBelumLengkap->isEmpty())
                            Lengkap
                        @else
                            Belum lengkap

                            @foreach ($partaiBelumLengkap as $index => $partai)
                                <li style="list-style-type: none;">{{ $index + 1 }}. {{ $partai->name }}</li>
                            @endforeach
                        @endif
                    </td>


                </tr>
            @endforeach

        </tbody>
    </table>

@stop

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <!-- Hapus pemanggilan jQuery sebelum memuat Select2 -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <!-- Load Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Initialize DataTable -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#count-caleg-table').DataTable({

                "lengthChange": false // Hide the "Show [number] entries" dropdow
            });

        });
        $(document).ready(function() {
            $('#count-tps-table').DataTable({

                "lengthChange": false // Hide the "Show [number] entries" dropdow
            });

        });
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        function onChangeSelect(url, id, name) {
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id: id
                },
                error: function(xhr, status, error) {
                    console.error(error); // Tampilkan pesan kesalahan pada konsol untuk debugging
                },
                success: function(data) {
                    var dropdown = $('#' + name);

                    console.log('Data dari ' + name + ':', data); // Tambahkan baris ini

                    dropdown.empty().removeAttr('disabled');



                    // if (name == 'district') {
                    //     dropdown.append('<option value="-" disabled selected>Pilih Kecamatan</option>');
                    // }
                    if (name === 'village') {
                        $('#village').empty();
                        $('#village').append('<option value="-" disabled selected>Pilih Desa</option>');
                    }

                    if (name === 'caleg') {
                        $('#caleg').empty();
                        $('#caleg').append('<option value="-" disabled selected>Pilih Caleg</option>');
                    }
                    if (name === 'partai') {
                        $('#partai').empty();
                        $('#partai').append('<option value="-" disabled selected>Pilih Partai</option>');
                    }


                    $.each(data, function(key, value) {
                        dropdown.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Set nilai 'old' untuk opsi kecamatan setelah perubahan
                    var oldCity = "{{ Auth::user()->city->id }}";
                    var oldDistrict = "{{ old('indonesia_district_id') }}";
                    var oldVillage = "{{ old('indonesia_village_id') }}";
                    var oldPartai = "{{ old('partai_id') }}";

                    if (name == 'district' && oldCity == id && oldDistrict) {
                        dropdown.val(oldDistrict);
                    }

                    if (name == 'district') {
                        dropdown.show();
                        console.log('Setting village value to old value:', oldDistrict);
                    }

                    if (name == 'village' && (oldVillage || "{{ session('indonesia_village_id') }}")) {
                        dropdown.val(oldVillage);
                    }
                    if (name == 'partai' && oldPartai) {
                        dropdown.val(oldPartai);
                    }
                    if (name == 'village') {
                        dropdown.show();

                    }



                    if (name == 'village') {
                        dropdown.show();
                    }
                    if (name == 'partai') {
                        dropdown.show();
                    }

                    // console.log(villageId);
                    // console.log(tps);
                    // if (!tps || !villageId) {
                    //     console.error('Nilai TPS atau ID Desa tidak valid');
                    //     return;
                    // }



                }

                // if (name == 'village' && oldCity == id && oldDistrict && oldVillage) {
                //     dropdown.val(oldVillage);
                //     console.log('Setting village value to old value:', oldVillage);
                // }



            });
        }

        $(function() {


            // Tambahkan event listener untuk perubahan kota
            $('#city').on('change', function() {
                // console.log('Nilai dari dropdown "Kota":', $(this).val()); // Tambahkan baris ini
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');

            });
            $('#district').on('change', function() {
                onChangeSelect("{{ route('villages') }}", $(this).val(), 'village');

            });



            $('#district').on('select2:select', function(e) {

            });
            $('#village').on('select2:select', function(e) {

            });
        });
    </script>
@stop
