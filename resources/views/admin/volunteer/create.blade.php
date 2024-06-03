@extends('adminlte::page')

@section('title', 'Buat Surveyor')

@section('content_header')
    <div class="row gap-3 justify-between">
        <h1>Buat Surveyor</h1>
        <form class="form ml-auto" action="{{ route('relawan.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-adminlte-input value="{{ old('file') }}" name="file" required label="Import from file" placeholder="File"
                type="file" igroup-size="md">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fa fa-file"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
            <x-adminlte-button type="submit" label="Import" theme="success" icon="fas fa-lg fa-save" />
        </form>
    </div>
@stop

{{-- @php
    $villages = new App\Http\Controllers\DependentDropdownController();
    $villages = $villages->villagesData(Auth::user()->district->id);
@endphp --}}
@php
    $province = new App\Http\Controllers\DependentDropdownController();
    $province = $province->provinces();
    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(3);
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
    <form action="{{ route('relawan.store') }}" method="post">
        @csrf
        <x-adminlte-input value="{{ old('name') }}" name="name" required label="Name" placeholder="name"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('email') }}" name="email" required label="Email" placeholder="email"
            type="email" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-envelope"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <!--<x-adminlte-select name="gender" label='Jenis Kelamin'>-->
        <!--    <option value="-" disabled selected>Pilih Jenis Kelamin</option>-->
        <!--    <option value="Perempuan">Perempuan</option>-->
        <!--    <option value="Laki-Laki">Laki-Laki</option>-->
        <!--</x-adminlte-select>-->
        @if (Gate::check('admin'))
            <x-adminlte-select disabled name="indonesia_province_id">
                <option value="{{ Auth::user()->province->id }}" disabled selected>{{ Auth::user()->province->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select disabled name="indonesia_city_id">
                <option value="{{ Auth::user()->city->id }}" disabled selected>{{ Auth::user()->city->name }}</option>
            </x-adminlte-select>
            <x-adminlte-select disabled name="indonesia_district_id">
                <option value="{{ Auth::user()->district->id }}" disabled selected>{{ Auth::user()->district->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select name="indonesia_village_id">
                <option value="-" disabled selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @else
            <x-adminlte-select id="province" name="indonesia_province_id">
                <option value="-" selected>Pilih Provinsi</option>
                @foreach ($province as $province)
                    <option value="{{ $province->id }}"
                        {{ old('indonesia_province_id') == $province->id ? 'selected' : '' }}>
                        {{ $province->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="city" name="indonesia_city_id">
                <option value="-" selected>Pilih Kabupaten/Kota</option>
                {{-- @foreach ($cities as $city)
                    <option value="{{ $city->id }}" {{ old('indonesia_city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach --}}
            </x-adminlte-select>
            <x-adminlte-select id="district" name="indonesia_district_id">
                <option value="-" selected>Pilih Kecamatan</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}"
                        {{ old('indonesia_district_id') == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="village" name="indonesia_village_id">
                <option value="-" selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @endif
        <x-adminlte-input name="password" required label="Password" placeholder="password" type="password"
            igroup-size="md" />
        <x-adminlte-input name="password_confirmation" required label="Confirm Password" placeholder="confirm password"
            type="password" igroup-size="md" />
        <x-adminlte-input value="{{ old('nik') }}" name="nik" label="NIK" placeholder="nik" type="number"
            igroup-size="md" />
        <x-adminlte-input value="{{ old('phone') }}" name="phone" label="Phone" placeholder="phone" type="number"
            igroup-size="md" />
        <x-adminlte-textarea name="address" placeholder="address" label="Address">
            {{ old('address') }}
        </x-adminlte-textarea>
        <x-adminlte-input name="tps" placeholder="tps" label="Tps" type="number">
            {{ old('tps') }}
        </x-adminlte-input>
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var provinceSelect = document.getElementById("province");
            var citySelect = document.getElementById("city");
            var districtSelect = document.getElementById("district");
            var villageSelect = document.getElementById("village");

            provinceSelect.addEventListener("change", function() {
                var selectedProvinceId = provinceSelect.value;
                if (selectedProvinceId === '-') {
                    // Reset dropdown kota/kabupaten, kecamatan, dan desa
                    resetDropdown(citySelect);
                    resetDropdown(districtSelect);
                    resetDropdown(villageSelect);
                    return;
                }

                // Mengambil data kota/kabupaten berdasarkan provinsi yang dipilih
                fetchCities(selectedProvinceId);
            });

            citySelect.addEventListener("change", function() {
                var selectedCityId = citySelect.value;
                if (selectedCityId === '-') {
                    // Reset dropdown kecamatan dan desa
                    resetDropdown(districtSelect);
                    resetDropdown(villageSelect);
                    return;
                }

                // Mengambil data kecamatan berdasarkan kota/kabupaten yang dipilih
                fetchDistricts(selectedCityId);
            });

            districtSelect.addEventListener("change", function() {
                var selectedDistrictId = districtSelect.value;
                if (selectedDistrictId === '-') {
                    // Reset dropdown desa
                    resetDropdown(villageSelect);
                    return;
                }

                // Mengambil data desa berdasarkan kecamatan yang dipilih
                fetchVillages(selectedDistrictId);
            });

            // Fungsi untuk mengambil data kota/kabupaten berdasarkan provinsi yang dipilih
            function fetchCities(provinceId) {
                // Gantilah URL berikut dengan endpoint yang sesuai untuk mengambil data kota/kabupaten
                var url = '/get-cities/' + provinceId;

                fetch(url)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // Mengisi ulang dropdown kota/kabupaten dengan data yang diperoleh
                        populateDropdown(citySelect, data);
                    })
                    .catch(function(error) {
                        console.error('Error fetching cities:', error);
                    });
            }

            // Fungsi untuk mengambil data kecamatan berdasarkan kota/kabupaten yang dipilih
            function fetchDistricts(cityId) {
                // Gantilah URL berikut dengan endpoint yang sesuai untuk mengambil data kecamatan
                var url = '/get-districts/' + cityId;

                fetch(url)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // Mengisi ulang dropdown kecamatan dengan data yang diperoleh
                        populateDropdown(districtSelect, data);
                    })
                    .catch(function(error) {
                        console.error('Error fetching districts:', error);
                    });
            }

            // Fungsi untuk mengambil data desa berdasarkan kecamatan yang dipilih
            function fetchVillages(districtId) {
                // Gantilah URL berikut dengan endpoint yang sesuai untuk mengambil data desa
                var url = '/get-villages/' + districtId;

                fetch(url)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // Mengisi ulang dropdown desa dengan data yang diperoleh
                        populateDropdown(villageSelect, data);
                    })
                    .catch(function(error) {
                        console.error('Error fetching villages:', error);
                    });
            }

            // Fungsi untuk mengisi ulang dropdown dengan data
            function populateDropdown(selectElement, data) {
                selectElement.innerHTML = "<option value='-'>Pilih</option>";
                data.forEach(function(item) {
                    var option = document.createElement("option");
                    option.value = item.id;
                    option.textContent = item.name;
                    selectElement.appendChild(option);
                });
            }

            // Fungsi untuk mengembalikan dropdown ke opsi default
            function resetDropdown(selectElement) {
                selectElement.innerHTML = "<option value='-'>Pilih</option>";
            }
        });
    </script>
@stop
