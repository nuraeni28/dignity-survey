@extends('adminlte::page')

@section('title', 'Edit Real Count')

@section('content_header')
    <h1>Edit Real Count</h1>
@stop

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;800;900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontt-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<style>
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {

        background-color: white;
        height: calc(2.875rem + 2px);

        padding-top: 7px;
        /* Adjust the value to your preference */
    }


    .required-asterisk {
        color: red;
        /* Color for the asterisk */
        font-weight: bold;
        /* Optionally make the asterisk bold */
    }

    /* Style for the dropdown arrow to match the rounded corners */
    .select2-container--default .select2-selection--single .select2-selection__arrow {

        background-color: #D9D9D9;
        height: calc(2.875rem + 2px);
        padding-top: 7px;
        /* Adjust the value to match the border-radius above */
    }
</style>
@php

    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(Auth::user()->province->id);
    $districts = new App\Http\Controllers\DependentDropdownController();
    if (Auth::user()->city->id) {
        $districts = $districts->districtsData(Auth::user()->city->id);
    } else {
        $districts = [];
    }
    // dd($districts);
    $villages = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_district_id', $quickCount->indonesia_district_id)) {
        $villages = $villages->villagesData(old('indonesia_district_id', $quickCount->indonesia_district_id));
    } elseif (Gate::check('admin')) {
        $villages = $villages->villagesData(Auth::user()->district->id);
    } else {
        $villages = [];
    }

    $caleg = new App\Http\Controllers\DependentDropdownController();
    $partai = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_district_id') || session('indonesia_district_id')) {
        $partai = $partai->partaiData(session('tps'), session('indonesia_village_id'));
    } else {
        $partai = $partai->partaiData($quickCount->tps, $quickCount->indonesia_district_id);
    }

@endphp

@section('content')
    <form action="{{ route('real-count.update', $quickCount->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-adminlte-select disabled name="indonesia_province_id" label="Provinsi">
            <option value="{{ Auth::user()->province->id }}" disabled selected>{{ Auth::user()->province->name }}</option>

        </x-adminlte-select>


        <x-adminlte-select disabled name="indonesia_city_id" id="city" name="indonesia_city_id" label="Kabupaten/Kota">
            <option value="{{ Auth::user()->city->id }}" disabled selected>{{ Auth::user()->city->name }}</option>

        </x-adminlte-select>
        <x-adminlte-select id="district" name="indonesia_district_id" required>
            <option value="-" disabled selected>Pilih Kecamatan</option>
            @foreach ($districts as $district)
                <option value="{{ $district->id }}"
                    {{ old('indonesia_district_id', $quickCount->indonesia_district_id) == $district->id ? 'selected' : '' }}>
                    {{ $district->name }}
                </option>
            @endforeach
            <x-slot name="label">
                <label for="name" class="required-label">Kecamatan </label>
            </x-slot>
        </x-adminlte-select>
        <x-adminlte-select name="indonesia_village_id" id="village" required>
            <option value="-" disabled selected>Pilih Desa</option>
            @foreach ($villages as $village)
                <option value="{{ $village->id }}"
                    {{ old('indonesia_village_id,', $quickCount->indonesia_village_id) == $village->id || session('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                    {{ $village->name }}
                </option>
            @endforeach
            <x-slot name="label">
                <label for="name" class="required-label">Desa</span></label>
            </x-slot>
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('tps', $quickCount->tps) }}" name="tps" label="TPS" required
            placeholder="tps" type="number" igroup-size="md" id="tps"> <x-slot name="label">
                <label for="tps" class="required-label">TPS</label>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-select disabled name="partai_id" label='Partai' id="partai" required>
            <option value="{{ $quickCount->partai_id }}" selected>{{ $quickCount->partai->name }}</option>

        </x-adminlte-select>
        <x-adminlte-input value="{{ old('jumlah_suara_partai', $quickCount->jumlah_suara_partai) }}"
            name="jumlah_suara_partai" label="Total Suara Partai" required placeholder="total suara partai" type="number"
            igroup-size="md"> <x-slot name="label">
                <label for="jumlah_suara_partai" class="required-label">Jumlah Suara Partai</label>
            </x-slot>
        </x-adminlte-input>
         @if ($quickCount->partai_id == 2)
            <div id="caleg_forms">
                @foreach ($dataCaleg as $index => $caleg)
                    <div class="form-group">
                        @if ($caleg->caleg)
                            <!-- Pastikan $caleg->caleg tidak null sebelum mengakses propertinya -->
                            <label for="jumlah_suara_caleg_{{ $caleg->caleg->id }}">Jumlah Suara Caleg
                                {{ $caleg->caleg->name }}</label>
                        @else
                            <label for="jumlah_suara_caleg_{{ $caleg->caleg->id }}">Jumlah Suara Caleg -</label>
                        @endif
                        <input type="number" name="jumlah_suara_caleg[]" class="form-control" required
                            id="jumlah_suara_caleg_{{ $caleg->caleg->id }}" value="{{ $caleg->jumlah_suara_caleg }}">
                        <!-- Inisialisasi nilai input jumlah suara caleg -->
                        <input type="hidden" value="{{ $caleg->caleg->id }}" name="id_caleg[]" class="form-control"
                            required id="id_caleg_{{ $caleg->caleg->id }}">
                    </div>
                @endforeach
            </div>
        @endif





        <div class="form-group">
            <label for="picture">Bukti (Foto)</label>
            <input type="file" name="picture" class="form-control-file" id="picture" accept="image/*"
                onchange="previewImage(event)">
        </div>

        <div id="image-preview">
            <img id="preview" alt="Profile Preview" style="max-width: 100%; max-height: 200px;"
                src="{{ url('public/public/quick_count/' . str_replace(' ', '%20', $quickCount->foto)) }}">
        </div>





        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>

@stop

@section('js')

    <script>
        $(document).ready(function() {
            $('#submit_button').click(function(event) {
                // Check if the select element has a value
                if ($('#village').val() === '') {
                    // If not, prevent form submission
                    event.preventDefault();
                    // Display a custom message or perform any other action
                    alert('Silakan pilih desa.');
                }
            });
        });
        $(document).ready(function() {
            $('#tps').on('input', function() {
                if ($(this).val().length > 2) {
                    $(this).val($(this).val().slice(0, 2));
                }
            });
        });

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('image-preview');


            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                const imageUrl = "{{ url('public/image/' . str_replace(' ', '%20', $quickCount->foto)) }}";
                preview.src = imageUrl;
                previewContainer.style.display = 'none';
            }
        }


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
                    // if (name === 'partai') {
                    //     $('#partai').empty();
                    //     $('#partai').append('<option value="-" disabled selected>Pilih Partai</option>');
                    // }


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

                    if (name == 'village' && oldVillage) {
                        dropdown.val(oldVillage);
                    }
                    // if (name == 'partai' && oldPartai) {
                    //     dropdown.val(oldPartai);
                    // }
                    if (name == 'village') {
                        dropdown.show();

                    }
                    // if (name == 'partai') {
                    //     dropdown.show();

                    // }


                    // $.each(data, function(key, value) {


                    //     $('#caleg_forms').append('<div class="form-group">' +
                    //         '<label for="jumlah_suara_caleg_' + key + '">Jumlah Suara Caleg ' +
                    //         $quickCount.caleg.name + '</label>' +
                    //         '<input type="number" name="jumlah_suara_caleg[]" class="form-control" required id="jumlah_suara_caleg_' +
                    //         key + '" value="' + jumlahSuaraCaleg + '">' +
                    //         // Inisialisasi nilai input jumlah suara caleg
                    //         '<input type="text" hidden value="' + key +
                    //         '" name="id_caleg[]" class="form-control" required id="id_caleg_' +
                    //         key + '">' +
                    //         '</div>');
                    // });

                    if (name == 'village') {
                        dropdown.show();
                    }
                    if (name == 'partai') {
                        dropdown.show();
                    }





                }

                // if (name == 'village' && oldCity == id && oldDistrict && oldVillage) {
                //     dropdown.val(oldVillage);
                //     console.log('Setting village value to old value:', oldVillage);
                // }



            });
        }

        $(function() {

            console.log("Session indonesia_village_id:", "{{ session('indonesia_village_id') }}");
            console.log("Session disctrict", "{{ session('indonesia_district_id') }}");

            var oldCity = "{{ Auth::user()->city->id }}";
            var oldDistrict = "{{ old('indonesia_district_id') }}";
            var oldVillage = "{{ old('indonesia_village_id') }}";


            if (oldCity && oldDistrict) {
                onChangeSelect("{{ route('districts') }}", oldCity, 'district');

            }

            if (oldCity && oldDistrict && oldVillage) {

                onChangeSelect("{{ route('villages') }}", oldDistrict, 'village');


            }

            // Tambahkan event listener untuk perubahan kota
            $('#city').on('change', function() {
                // console.log('Nilai dari dropdown "Kota":', $(this).val()); // Tambahkan baris ini
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');

            });
            $('#district').on('change', function() {
                onChangeSelect("{{ route('villages') }}", $(this).val(), 'village');

            });




        });
    </script>
@stop
