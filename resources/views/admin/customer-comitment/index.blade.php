@extends('adminlte::page')

@section('title', 'Pemantapan Data')
@php
    $page = request('page', 1);
@endphp

{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
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
<style>
    .select2-selection__choice {
        color: black !important;

        border: 1px solid black !important;
    }

    a.btn.btn-success {
        width: 100px;
        height: 40px;
    }

    #duplicate-button {
        width: 100px;
        height: 40px;
    }

    /* Target the Hapus button */
    form#deleteAllSelectedRecordForm button.btn.btn-danger {
        width: 100px;
        height: 40px;
    }

    .custom-button {
        width: 30px;
        height: 25px;
        display: inline-block;
        justify-content: center;
        align-items: center;
    }

    .select-container {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-top: 5px;
        /* Atur jarak dari select */
    }

    .select2-container--default .select2-selection--multiple {
        background-color: white;
        border: 1px solid black !important;
        border-radius: 0px !important;


    }

    .select2-container .select2-selection--multiple .select2-selection__rendered {
        color: blue !important;
        /* Warna teks placeholder menjadi hitam */
    }

    body {
        margin: 0;
        font-size: 0.87rem !important;
        font-weight: 400;
        line-height: 1.5;
        color: black !important;
        text-align: left;
        background-color: #fff;
    }
</style>

@section('content_header')
    <div class="col">
        <div style="display: flex; align-items: center;">
            <div class="d-flex justify-content-between"
                @if (Auth::user() && Auth::user()->hasRole('koordinator-area')) style="margin-left: 25px;" @else style="margin-left: 20px;" @endif>
                <form action="{{ route('pemantapan-data.index') }}" method="GET" class="form-inline d-flex">
                    <div class="input-group ml-auto ">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama/NIK/Email Relawan"
                            style="width: 250px">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                {{-- 
                <div class="btn-group" role="group" style="margin-left: 10px">
                    <a href="{{ route('pemantapan-data.export') }}?search={{ Request::get('search') }}&}}&duplicate={{ Request::get('duplicate') }}&tanggal_mulai={{ Request::get('tanggal_mulai') }}&tanggal_selesai={{ Request::get('tanggal_selesai') }}&cities={{ Request::get('cities') }}&indonesia_cities_id={{ Request::get('indonesia_cities_id') }}&indonesia_districts_id={{ Request::get('indonesia_districts_id[]') }}&indonesia_villages_id={{ Request::get('indonesia_villages_id[]') }}"
                        class="btn btn-success" style="width: 100px">
                        Export
                        <i class="fas fa-save"></i>
                    </a>
                    @if (Auth::user()->hasRole('super-admin'))
                        <form action="{{ route('pemantapan-data.deleteAll') }}" method="POST"
                            id="deleteAllSelectedRecordForm" style="padding-left: 10px">
                            @csrf
                            @method('DELETE')
                            <button type="button" id="deleteSelectedButton" class="btn btn-danger">Hapus</button>
                        </form>
                    @endif

                    <form action="{{ route('interview.index') }}" method="GET" style="padding-left: 20px">
                        <input type="hidden" name="duplicate" value="1">
                        <button type="submit" id="duplicate-button" class="btn btn-warning">Duplikat</button>
                    </form>
                </div> --}}
            </div>


        </div>
        <div class="col" style="margin-top:20px">
            <form action="{{ route('pemantapan-data.index') }}" method="GET">
                <div class="pb-3 pt-3 pr-2" style="  background-color: #EDEDED; padding:10px;width:max-content">
                    <div class="ms-2 d-inline-block">
                        <div class="row">
                            <div class="col mb-3"
                                style="margin-left:10px;background-color: white; border: 1px solid #f3f1f1; border-radius: 10px; box-shadow: 2px 2px 2px 0px rgba(53, 53, 53, 0.75);width:550px">
                                <label for="tanggal_mulai">Pilih Tanggal</label>
                                <br>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" style="margin-bottom: 10px;">
                                <label for="tanggal_selesai" style="padding-right: 10px; padding-left: 10px">sampai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            <div class="col">
                                <button class="btn-primary btn-sm btn" type="submit"
                                    style="width: 65px; color: black; font-weight: bold; background-color: #00A3FF;margin-left:30px">Filter</button>
                                <br>
                                <img src="{{ asset('public/assets/filter.png') }}" alt="Logo" class="logo"
                                    style="width: 65px; height: 40px;margin-left:30px">
                            </div>

                        </div>


                        @if (Auth::user())
                            @if (Auth::user()->hasRole('koordinator-area'))
                                <div class="pb-3 pt-3 pr-2"
                                    style="background-color: white; border: 1px solid #f3f1f1; border-radius: 5px; box-shadow: 2px 2px 2px 0px rgba(53, 53, 53, 0.75);margin-right:10px; width:530px">
                                    <label for="lokasi" style="padding-left: 10px">Pilih Lokasi</label>

                                    <input hidden type="text" name="cities" id="cities"
                                        value="{{ Auth::user()->indonesia_city_id }}">
                                    <div class="ms-2 d-inline-block custom-select2">
                                        <label for="district">Pilih Kecamatan</label>
                                        <div class="select-container">
                                            <select name="indonesia_districts_id[]" style="width:200px;"
                                                class="form-select form-select-sm" id="district" multiple>
                                                {{-- <option value="">Kecamatan</option> --}}
                                            </select>

                                        </div>
                                    </div>
                                    <div class="ms-2 d-inline-block custom-select2">
                                        <label for="district">Pilih Desa</label>
                                        <div class="select-container">
                                            <select name="indonesia_villages_id[]" style="width:200px;"
                                                class="form-select form-select-sm" id="village" multiple>
                                                {{-- <option value="">Desa</option> --}}

                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="pb-3 pt-3 pr-2"
                                        style="background-color: white; border: 1px solid #f3f1f1; border-radius: 5px; box-shadow: 2px 2px 2px 0px rgba(53, 53, 53, 0.75);margin-right:10px;">
                                        <div class="ms-2 d-inline-block">
                                            <label for="lokasi" style="margin-right: 10px;">Pilih Lokasi</label>
                                            <select name="indonesia_cities_id" class="form-select-sm" id="city"
                                                style="width: 200px;">
                                                <option value="">Pilih Kabupaten</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}|{{ $city->name }}">
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="ms-2 d-inline-block custom-select2">
                                            <select name="indonesia_districts_id[]" style="width:200px;"
                                                class="form-select form-select-sm " id="district" multiple>
                                                {{-- <option value="">Kecamatan</option> --}}
                                            </select>
                                        </div>
                                        <div class="ms-2 d-inline-block custom-select2">
                                            <select name="indonesia_villages_id[]" style="width:200px"
                                                class="form-select form-select-sm" id="village" multiple>
                                                {{-- <option value="">Desa</option> --}}

                                            </select>
                                        </div>

                            @endif
                        @endif
                    </div>
                </div>



            </form>
        </div>
    </div>
    </div>

@stop


@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="" id="select_all_ids"></th>
                        <th style="width: 5% !important;">{{ __('Foto') }}</th>

                        <th> {{ __('Tanggal') }}</th>
                        <th style="width: 50% !important;">{{ __('Lokasi') }}</th>
                        <th>{{ __('Relawan') }}</th>
                        <th>{{ __('Responden') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($interviews as $interview)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $interview->id }}" class="checkbox_ids">
                            </td>
                            <td>
                                <a href="{{ url('public/public/image_evidence/' . $interview->photo) }}" target="_blank">
                                    <img src="{{ url('public/public/image_evidence/' . $interview->photo) }}" alt="Image" height="100" width="100" />
                                </a>
                            </td>
                            {{-- <td>{{ $interview->interview->id }}</td> --}}
                            <td>

                                {{ date('d/m/Y', strtotime($interview->updated_at)) }}

                            </td>
                            <td>

                                {{ $interview->location }}

                            </td>
                            <td>
                                @if ($interview->user == null)
                                    -
                                @else
                                    {{ $interview->user->name }}
                                @endif
                            </td>
                            <td>
                                @if ($interview->customer == null)
                                    -
                                @else
                                    {{ $interview->customer->name }}
                                @endif
                            </td>


                            {{-- <td>
                                <nobr class="d-flex">
                                    @canany(['owner', 'admin', 'super-admin'])
                                        <a href="{{ route('interview.edit', $interview->id) }}"
                                            class="btn btn-xs btn-default text-primary mx-1 shadow custom-button"
                                            title="Edit">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        <form class="flex"
                                            action="{{ route('interview.destroy', ['interview' => $interview->id, 'page' => $interviews->currentPage()]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are you sure you want to delete?')"
                                                class="btn btn-xs btn-default text-danger mx-1 shadow custom-button"
                                                title="Delete">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                    <a href="{{ route('interview.show', $interview->id) }}"
                                        class="btn btn-xs btn-default text-teal mx-1 shadow custom-button"
                                        title="Details">
                                        <i class="fa fa-lg fa-fw fa-eye"></i>
                                    </a>
                                </nobr> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $interviews->appends(request()->query())->links() }}
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var jq = jQuery.noConflict();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selectAllCheckbox = document.getElementById('select_all_ids');
            var checkboxes = document.querySelectorAll('.checkbox_ids');
            var deleteSelectedButton = document.getElementById('deleteSelectedButton');

            deleteSelectedButton.addEventListener('click', function() {
                var selectedIds = [];

                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        selectedIds.push(checkbox.value);
                    }
                });

                if (selectedIds.length > 0) {
                    // Clear any previously added inputs
                    document.querySelectorAll('#deleteAllSelectedRecordForm input[name="selected_ids[]"]')
                        .forEach(function(input) {
                            input.remove();
                        });

                    // Add the selected IDs to the form data
                    selectedIds.forEach(function(id) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = id;
                        document.querySelector('#deleteAllSelectedRecordForm').appendChild(input);
                    });

                    // Submit the form
                    document.querySelector('#deleteAllSelectedRecordForm').submit();
                } else {
                    alert('No checkboxes selected.');
                }
            });

            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });
    </script>
    @if (Auth::user()->hasRole('koordinator-area'))
        <script>
            function onChangeSelect(url, id, name, selectedDistricts) {
                jq.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        id: id,
                        districts: selectedDistricts
                    },

                    success: function(data) {
                        console.log(data)
                        jq('#' + name).empty();
                        jq('#' + name).removeAttr('disabled');
                        jq.each(data, function(key, value) {
                            jq('#' + name).append('<option value="' + key + '">' + value + '</option>');
                        });

                        // Inisialisasi ulang Select2 setelah memperbarui opsi
                        jq('#' + name).select2();
                    }
                });

            }

            jq(function() {
                var selectedDistricts = [];
                var citiesValue = document.getElementById('cities').value;
                onChangeSelect("{{ route('districts') }}", citiesValue, 'district', selectedDistricts);
                jq('#district').select2({
                    multiple: true, // Aktifkan mode multiple select
                    placeholder: "Pilih Kecamatan"
                });

                jq('#district').on('change', function() {
                    var selectedDistricts = jq('#district').val();

                    console.log(selectedDistricts);
                    onChangeSelect("{{ route('villagesMultiSelect') }}", selectedDistricts, 'village');
                });

            });

            jq('#village').select2({
                multiple: true, // Aktifkan mode multiple select,

            });
        </script>
    @else
        <script>
            function onChangeSelect(url, id, name, selectedDistricts) {
                jq.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        id: id,
                        districts: selectedDistricts
                    },

                    success: function(data) {
                        console.log(data)
                        jq('#' + name).empty();
                        jq('#' + name).removeAttr('disabled');
                        jq.each(data, function(key, value) {
                            jq('#' + name).append('<option value="' + key + '">' + value + '</option>');
                        });

                        // Inisialisasi ulang Select2 setelah memperbarui opsi
                        jq('#' + name).select2();
                    }
                });

            }


            jq(function() {
                var selectedDistricts = [];
                jq('#city').on('change', function() {
                    console.log($(this).val());
                    onChangeSelect("{{ route('districts') }}", jq(this).val(), 'district', selectedDistricts);
                });


                jq('#district').select2({
                    multiple: true, // Aktifkan mode multiple select
                    placeholder: "Pilih Kecamatan"
                });


                jq('#district').on('change', function() {
                    var selectedDistricts = jq('#district').val();

                    console.log(selectedDistricts);
                    onChangeSelect("{{ route('villagesMultiSelect') }}", selectedDistricts, 'village');
                });

                jq('#village').select2({
                    multiple: true, // Aktifkan mode multiple select,
                    placeholder: "Pilih Desa"
                });
            });
        </script>
    @endif




@stop
