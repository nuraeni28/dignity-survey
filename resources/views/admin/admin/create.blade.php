@extends('adminlte::page')

@section('title', 'Create Admin')

@section('content_header')
    <h1>Create Admin</h1>
@stop

@php
    $cities = new App\Http\Controllers\DependentDropdownController();
    $cities = $cities->citiesData(Auth::user()->province->id);
    $districts = new App\Http\Controllers\DependentDropdownController();
    if (old('indonesia_city_id')) {
        $districts = $districts->districtsData(old('indonesia_city_id'));
    } else {
        $districts = [];
    }
@endphp

@section('content')
    <form action="{{ route('admin.store') }}" method="post">
        @csrf
        <x-adminlte-input value="{{ old('name') }}" name="name" required label="Name" placeholder="name" type="text"
            igroup-size="md">
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
        <x-adminlte-select disabled name="indonesia_province_id">
            <option value="{{ Auth::user()->province->id }}" disabled selected>{{ Auth::user()->province->name }}</option>
        </x-adminlte-select>
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
                <option value="{{ $district->id }}" {{ old('indonesia_district_id') == $district->id ? 'selected' : '' }}>
                    {{ $district->name }}
                </option>
            @endforeach
        </x-adminlte-select>
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
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop

@section('js')
    <script>
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
                    $('#' + name).append('<option disabled>Pilih Kecamatan</option>');
                    $.each(data, function(key, value) {
                        $('#' + name).append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        }
        $(function() {
            $('#city').on('change', function() {
                onChangeSelect("{{ route('districts') }}", $(this).val(), 'district');
            })
        });
    </script>
@stop
