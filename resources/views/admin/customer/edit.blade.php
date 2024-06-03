@extends('adminlte::page')

@section('title', 'Edit Responden')

@section('content_header')
    <h1>Edit Responden</h1>
@stop

@php
    $cities = new App\Http\Controllers\DependentDropdownController();
    if (!Gate::check('super-admin')) {
        $cities = $cities->citiesData(Auth::user()->province->id);
    } else {
        $cities = $cities->citiesData($customer->province->id);
    }
    
    $districts = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_city_id', $customer->indonesia_city_id)) {
        $districts = $districts->districtsData(old('indonesia_city_id', $customer->indonesia_city_id));
    } else {
        $districts = [];
    }
    $villages = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_district_id', $customer->indonesia_district_id)) {
        $villages = $villages->villagesData(old('indonesia_district_id', $customer->indonesia_district_id));
    } else {
        $villages = [];
    }
@endphp

@section('content')
    <form action="{{ route('responden.update', ['responden' => $customer->id, 'page' => $page]) }}" method="post">
        @csrf
        @method('PUT')
       
        <x-adminlte-input value="{{ old('name', $customer->name) }}" name="name" required label="Nama" placeholder="nama"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('email', $customer->email) }}" name="email" required label="Email"
            placeholder="email" type="email" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-envelope"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-select name="gender" label='Jenis Kelamin'>
            <option value="-" disabled selected>Pilih Jenis Kelamin</option>
            <option value="Perempuan">Perempuan</option>
            <option value="Laki-Laki">Laki-Laki</option>
            @if ($customer)
                <option value="{{ $customer->jenis_kelamin }}"
                    {{ old('gender', $customer->jenis_kelamin) == $customer->jenis_kelamin ? 'selected' : '' }}>
                    {{ $customer->jenis_kelamin }}
                </option>
            @endif
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('nik', $customer->nik) }}" name="nik" label="NIK Responden" placeholder="nik responden"
            type="number" igroup-size="md" />
             <x-adminlte-input value="{{ old('no_kk', $customer->no_kk) }}" name="no_kk" label="NO KK" placeholder="no kk"
            id="no_kk" required type="number" igroup-size="md" />
        <x-adminlte-input value="{{ old('phone', $customer->phone) }}" name="phone" label="Phone" placeholder="phone"
            type="number" igroup-size="md" />
        <x-adminlte-input value="{{ old('dob', $customer->dob) }}" name="dob" label="Tanggal Lahir"
            placeholder="tanggal lahir" type="date" igroup-size="md" />

        @if (!Gate::check('super-admin'))
            <x-adminlte-select disabled name="indonesia_province_id">
                <option value="{{ Auth::user()->province->id }}" disabled selected>{{ Auth::user()->province->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select id="city" name="indonesia_city_id">
                <option value="-" disabled selected>Pilih Kabupaten/Kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}"
                        {{ old('indonesia_city_id', $customer->indonesia_city_id) == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="district" disabled name="indonesia_district_id">
                <option value="-" disabled selected>Pilih Kecamatan</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}"
                        {{ old('indonesia_district_id', $customer->indonesia_district_id) == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="village" disabled name="indonesia_village_id">
                <option value="-" disabled selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id', $customer->indonesia_village_id) == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @else
            <x-adminlte-select disabled name="indonesia_province_id">
                <option value="{{ $customer->province->id }}" disabled selected>{{ $customer->province->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select id="city" name="indonesia_city_id">
                <option value="-" disabled selected>Pilih Kabupaten/Kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}"
                        {{ old('indonesia_city_id', $customer->indonesia_city_id) == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="district" disabled name="indonesia_district_id">
                <option value="-" disabled selected>Pilih Kecamatan</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}"
                        {{ old('indonesia_district_id', $customer->indonesia_district_id) == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="village" disabled name="indonesia_village_id">
                <option value="-" disabled selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id', $customer->indonesia_village_id) == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @endif
        <x-adminlte-textarea name="address" placeholder="alamat" label="Alamat">
            {{ old('address', $customer->address) }}
        </x-adminlte-textarea>
        <x-adminlte-select name="religion" label='Agama'>
            <option value="-" disabled selected>Pilih Agama</option>
            <option value="Islam">Islam</option>
            <option value="Kristen">Kristen</option>
            <option value="Hindu">Hindu</option>
            <option value="Budha">Budha</option>
            <option value="Katolik">Katolik</option>
            @if ($customer)
                <option value="{{ $customer->religion }}"
                    {{ old('religion', $customer->religion) == $customer->religion ? 'selected' : '' }}>
                    {{ $customer->religion }}
                </option>
            @endif
        </x-adminlte-select>
        <x-adminlte-select name="education" label='Pendidikan Terakhir'>
            <option value="-" disabled selected>Pilih Pendidikan Terakhir</option>
            <option value="Tidak Pernah Sekolah">Tidak Pernah Sekolah</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
            @if ($customer)
                <option value="{{ $customer->education }}"
                    {{ old('education', $customer->education) == $customer->education ? 'selected' : '' }}>
                    {{ $customer->education }}
                </option>
            @endif
        </x-adminlte-select>
        <x-adminlte-select name="job" label='Pekerjaan'>
            <option value="-" disabled selected>Pilih Pekerjaan</option>
            @if ($customer && $customer->job)
                <option value="{{ $customer->job }}" {{ old('job', $customer->job) == $customer->job ? 'selected' : '' }}>
                    {{ $customer->job }}
                </option>
            @else
                @foreach ($occupations as $occupation)
                    <option value="{{ $occupation->name }}">{{ $occupation->name }}</option>
                @endforeach
            @endif
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('family_member', $customer->family_member) }}" name="family_member"
            label="Jumlah Anggota Keluarga" placeholder="jumlah anggota keluarga" type="number" igroup-size="md" />
        <x-adminlte-input value="{{ old('family_election', $customer->family_election) }}" name="family_election"
            label="Jumlah Pemilih Dalam KK" placeholder="jumlah pemilih dalam kk" type="number" igroup-size="md" />
        <x-adminlte-select name="marrital_status" label='Status Perkawinan'>
            <option value="-" disabled selected>Pilih Status Perkawinan</option>
            <option value="Lajang">Lajang</option>
            <option value="Menikah">Menikah</option>
            <option value="Cerai Hidup">Cerai Hidup</option>
            <option value="Cerai Mati">Cerai Mati</option>
            @if ($customer)
                <option value="{{ $customer->marrital_status }}"
                    {{ old('marrital_status', $customer->marrital_status) == $customer->marrital_status ? 'selected' : '' }}>
                    {{ $customer->marrital_status }}
                </option>
            @endif
        </x-adminlte-select>
        <x-adminlte-select name="monthly_income" label='Pendapatan Rata-rata Perbulan'>
            <option value="-" disabled selected>Pilih Pendapatan Rata-rata Perbulan</option>
            @foreach ($incomes as $income)
                <option value="{{ $income->name }}">{{ $income->name }}</option>
            @endforeach
            @if ($customer)
                <option value="{{ $customer->monthly_income }}"
                    {{ old('monthly_income', $customer->monthly_income) == $customer->monthly_income ? 'selected' : '' }}>
                    {{ $customer->monthly_income }}
                </option>
            @endif
        </x-adminlte-select>
        <x-adminlte-input value="{{ old('tps', $customer->tps) }}" name="tps" label="Tps" placeholder="tps"
            type="number" igroup-size="md" />
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop
@section('js')
    <script>
      $(document).ready(function() {
            $('#nik').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });
        $(document).ready(function() {
            $('#no_kk').on('input', function() {
                if ($(this).val().length > 16) {
                    $(this).val($(this).val().slice(0, 16));
                }
            });
        });
           $(document).ready(function() {
            $('#nik-relawan').on('input', function() {
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
                    $('#' + name).empty();
                    $('#' + name).removeAttr('disabled');
                    if (name == 'district') {
                        $('#' + name).append('<option disabled selected>Pilih Kecamatan</option>');
                    }
                    if (name == 'village') {
                        $('#village').empty();
                        $('#village').append('<option disabled selected>Pilih Desa</option>');
                    }
                    $.each(data, function(key, value) {
                        $('#' + name).append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        }
        $(function() {
            $('#city').on('change', function() {
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');
            });
            $('#district').on('change', function() {
                onChangeSelect("{{ route('villages') }}", $(this).val(), 'village');
            });
        });
    </script>
@stop
