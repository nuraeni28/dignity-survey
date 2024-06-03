@extends('adminlte::page')

@section('title', 'Buat Pemantapan Data')

@section('content_header')
    <h1>Buat Pemantapan Data</h1>
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
    if (old('indonesia_district_id')) {
        $villages = $villages->villagesData(old('indonesia_district_id'));
    } elseif (Gate::check('admin')) {
        $villages = $villages->villagesData(Auth::user()->district->id);
    } else {
        $villages = [];
    }
@endphp

@section('content')
    <form action="{{ route('responden.storeComitment') }}" method="post" enctype="multipart/form-data">
        @csrf
        <!--<x-adminlte-select id="volunteer" name="surveyor_id" igroup-size="lg" label="Relawan ">-->
        <!--    <x-slot name="label">-->
        <!--        <label for="name" class="required-label">Relawan <span class="required-asterisk">(Wajib)</span></label>-->
        <!--    </x-slot>-->
        <!--</x-adminlte-select>-->
          <x-adminlte-input value="{{ old('surveyor') }}" name="surveyor" required label="Nama Relawan"
            placeholder="nama relawan" type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
            <x-slot name="label">
                <label for="name" class="required-label">Nama Relawan <span
                        class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
          <x-adminlte-input value="{{old('nik_surveyor')}}" name="nik_surveyor" label="NIK Relawan" required
            placeholder="nik relawan" type="number" igroup-size="md" id="nik-relawan-input"> <x-slot name="label">
                <label for="name" class="required-label">NIK Relawan<span
                        class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('name') }}" name="name" required label="Nama Responden" placeholder="nama responden"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
            <x-slot name="label">
                <label for="name" class="required-label">Nama Responden <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('email') }}" name="email" label="Email (Tidak Wajib)" placeholder="email"
            type="email" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-envelope"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-select name="gender" label="Jenis Kelamin (Tidak Wajib)">
            <option value="-" disabled selected>Pilih Jenis Kelamin</option>
            <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            <option value="Laki-Laki" {{ old('gender') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('no_kk') }}" name="no_kk" label="NO KK" required placeholder="No KK"
            type="number" igroup-size="md" id="noKK-input"> <x-slot name="label">
                <label for="name" class="required-label">NO KK <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('nik') }}" name="nik" label="NIK Responden" required placeholder="nik responden"
            type="number" igroup-size="md" id="nik-input"> <x-slot name="label">
                <label for="name" class="required-label">NIK Responden <span class="required-asterisk">(Wajib)</span></label>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('phone') }}" name="phone" label="Phone (Tidak Wajib)" placeholder="phone"
            type="number" igroup-size="md" />
        <x-adminlte-input value="{{ old('dob') }}" name="dob" label="Tanggal Lahir (Tidak Wajib)"
            placeholder="tanggal lahir" type="date" igroup-size="md" />

        <x-adminlte-select disabled name="indonesia_province_id">
            <option value="{{ Auth::user()->province->id }}" disabled selected>{{ Auth::user()->province->name }}</option>

        </x-adminlte-select>
        @if (Gate::check('admin'))
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
        @elseif (Gate::check('koordinator-area'))
            <x-adminlte-select disabled name="indonesia_city_id" id="city" name="indonesia_city_id">
                <option value="{{ Auth::user()->city->id }}" disabled selected>{{ Auth::user()->city->name }}</option>

            </x-adminlte-select>
            <x-adminlte-select id="district" name="indonesia_district_id">
                <option value="-" disabled selected>Pilih Kecamatan <span class="required">(Wajib)</span></option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}"
                        {{ old('indonesia_district_id') == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
                <x-slot name="label">
                    <label for="name" class="required-label">Kecamatan <span
                            class="required-asterisk">(Wajib)</span></label>
                </x-slot>
            </x-adminlte-select>
            <x-adminlte-select name="indonesia_village_id" id="village">
                <option value="-" disabled selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
                <x-slot name="label">
                    <label for="name" class="required-label">Desa <span
                            class="required-asterisk">(Wajib)</span></label>
                </x-slot>
            </x-adminlte-select>
        @else
            <x-adminlte-select id="city" name="indonesia_city_id">
                <option value="-" disabled selected>Pilih Kabupaten/Kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" {{ old('indonesia_city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="district" disabled name="indonesia_district_id">
                <option value="-" disabled selected>Pilih Kecamatan</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}"
                        {{ old('indonesia_district_id') == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="village" disabled name="indonesia_village_id">
                <option value="-" disabled selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id') == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @endif
        <x-adminlte-textarea name="address" placeholder="alamat" label="Alamat (Tidak Wajib)">
            {{ old('address') }}
        </x-adminlte-textarea>
        {{-- <x-adminlte-input value="{{old('age')}}" name="age" label="Usia" placeholder="umur" type="number" igroup-size="md" /> --}}

        <x-adminlte-select name="religion" label='Agama (Tidak Wajib)'>
            <option value="-" disabled selected>Pilih Agama</option>
            <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
            <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
            <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
            <option value="Budha" {{ old('religion') == 'Budha' ? 'selected' : '' }}>Budha</option>
            <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
        </x-adminlte-select>
        <x-adminlte-select name="education" label='Pendidikan Terakhir (Tidak Wajib)'>
            <option value="-" disabled selected>Pilih Pendidikan Terakhir</option>
            <option value="Tidak Pernah Sekolah" {{ old('education') == 'Tidak Pernah Sekolah' ? 'selected' : '' }}>Tidak
                Pernah Sekolah
            </option>
            <option value="SD" {{ old('education') == 'SD' ? 'selected' : '' }}>SD</option>
            <option value="SMP"{{ old('education') == 'SMP' ? 'selected' : '' }}>SMP</option>
            <option value="SMA" {{ old('education') == 'SMA' ? 'selected' : '' }}>SMA</option>
            <option value="S1" {{ old('education') == 'S1' ? 'selected' : '' }}>S1</option>
            <option value="S2" {{ old('education') == 'S2' ? 'selected' : '' }}>S2</option>
            <option value="S3" {{ old('education') == 'S3' ? 'selected' : '' }}>S3</option>
        </x-adminlte-select>
        <x-adminlte-select name="job" label='Pekerjaan (Tidak Wajib)'>
            <option value="-" disabled selected>Pilih Pekerjaan</option>
            @foreach ($occupations as $occupation)
                <option value="{{ $occupation->name }}" {{ old('job') == $occupation->name ? 'selected' : '' }}>
                    {{ $occupation->name }}
                </option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('family_member') }}" name="family_member"
            label="Jumlah Anggota Keluarga (Tidak Wajib)" placeholder="jumlah anggota keluarga" type="number"
            igroup-size="md" />
        <x-adminlte-input value="{{ old('family_election') }}" name="family_election"
            label="Jumlah Pemilih Dalam KK (Tidak Wajib)" placeholder="jumlah pemilih dalam kk" type="number"
            igroup-size="md" />
        <x-adminlte-select name="marrital_status" label='Status Perkawinan (Tidak Wajib)'>
            <option value="-" disabled selected>Pilih Status Perkawinan</option>
            <option value="Lajang" {{ old('marrital_status') == 'Lajang' ? 'selected' : '' }}>Lajang</option>
            <option value="Menikah" {{ old('marrital_status') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
            <option value="Cerai Hidup" {{ old('marrital_status') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup
            </option>
            <option value="Cerai Mati" {{ old('marrital_status') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
        </x-adminlte-select>
        <x-adminlte-select name="monthly_income" label="Pendapatan Rata-rata Perbulan (Tidak Wajib)">
            <option value="-" disabled selected>Pilih Pendapatan Rata-rata Perbulan</option>
            @foreach ($incomes as $income)
                <option value="{{ $income->name }}" {{ old('monthly_income') == $income->name ? 'selected' : '' }}>
                    {{ $income->name }}
                </option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('tps') }}" name="tps" label="TPS (Tidak Wajib)" placeholder="TPS"
            type="number" igroup-size="md" />
        <div class="form-group">
            <label for="picture">Bukti Kunjungan</label>
            <input type="file" name="photo" class="form-control-file" id="photo" accept="image/*"
                onchange="previewImage(event)">
        </div>

        <!-- Image Preview -->
        <div id="image-preview" style="display: none;">
            <img id="preview" alt="Profile Preview" style="max-width: 100%; max-height: 200px;">
        </div>



        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>

@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
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
                preview.src = '';
                previewContainer.style.display = 'none';
            }
        }
    </script>
    <script>
        // function previewImage(event) {
        //     var input = event.target;
        //     var preview = document.getElementById('image-preview');
        //     var container = document.getElementById('image-preview-container');

        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();

        //         reader.onload = function(e) {
        //             preview.src = e.target.result;
        //             preview.style.display = 'block';
        //         };

        //         reader.readAsDataURL(input.files[0]);

        //         // Update the file name in the label
        //         var fileName = input.files[0].name;
        //         var label = document.querySelector('.custom-file-label');
        //         label.innerText = 'Profile Picture: ' + fileName;
        //     } else {
        //         preview.style.display = 'none';
        //     }
        // }
        $(document).ready(function() {
            $('#nik-input').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });
         $(document).ready(function() {
            $('#nik-relawan-input').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });
        $(document).ready(function() {
            $('#noKK-input').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });

        function onChangeSelect(url, id, name) {
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id: id
                },
                success: function(data) {
                    var dropdown = $('#' + name);
                    console.log('Data dari ' + name + ':', data); // Tambahkan baris ini

                    dropdown.empty().removeAttr('disabled');

                    if (name == 'district') {
                        dropdown.append('<option disabled selected>Pilih Kecamatan</option>');
                    }
                    if (name == 'village') {
                        $('#village').empty();
                        $('#village').append('<option disabled selected>Pilih Desa</option>');
                    }

                    $.each(data, function(key, value) {
                        dropdown.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Set nilai 'old' untuk opsi kecamatan setelah perubahan
                    var oldCity = "{{ Auth::user()->city->id }}";
                    var oldDistrict = "{{ old('indonesia_district_id') }}";
                    var oldVillage = "{{ old('indonesia_village_id') }}";

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

                    if (name == 'village') {
                        dropdown.show();

                    }

                    // if (name == 'village' && oldCity == id && oldDistrict && oldVillage) {
                    //     dropdown.val(oldVillage);
                    //     console.log('Setting village value to old value:', oldVillage);
                    // }

                }
            });
        }

        $(function() {
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

            var userCityId = "{{ Auth::user()->city->id }}";
            $('#volunteer').select2({
                placeholder: "Pilih Relawan",
                allowClear: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('relawan') }}?_=" + new Date().getTime(),
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            id: userCityId
                        };
                    },
                },
            });

            // Hapus opsi sebelum menambahkan yang baru
            $('#volunteer').on('select2:select', function(e) {
                var currentValue = e.params.data.id;
                console.log("ID user yang dipilih: " + e.params.data.id);
                var $this = $(this);
                $this.find('option').remove();
                $this.append('<option value="' + currentValue + '">' + e.params.data.text + '</option>');
            });

        });
    </script>
@stop
