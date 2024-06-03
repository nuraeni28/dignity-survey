@extends('adminlte::page')

@section('title', 'Edit Koordinator Area')

@section('content_header')
    <h1>Edit Admin</h1>
@stop

@php
    $cities = new App\Http\Controllers\DependentDropdownController();

    if (!Gate::check('super-admin')) {
        $cities = $cities->citiesData(Auth::user()->indonesia_province_id);
    } elseif ($admin->indonesia_province_id == null) {
        $cities = $cities->citiesData(1);
    } else {
        $cities = $cities->citiesData($admin->indonesia_province_id);
    }
@endphp

@section('content')
    @php
        // dd($admin->id
    @endphp
    <form action="{{ route('kordinator.update', $coordinator->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-adminlte-input name="name" value="{{ old('name', $coordinator->name) }}" label="Name" placeholder="name"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input name="email" value="{{ old('email', $coordinator->email) }}" label="Email" placeholder="email"
            type="email" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-envelope"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        {{-- <x-adminlte-select name="gender" label='Jenis Kelamin'>
            <option value="-" disabled selected>Pilih Jenis Kelamin</option>
            <option value="Perempuan">Perempuan</option>
            <option value="Laki-Laki">Laki-Laki</option>
            @if ($coordinator)
                <option value="{{ $coordinator->gender }}"
                    {{ old('gender', $coordinator->gender) == $coordinator->gender ? 'selected' : '' }}>
                    {{ $coordinator->gender }}
                </option>
            @endif
        </x-adminlte-select> --}}
        <x-adminlte-input-file name="profile_image" label="Profile Picture" placeholder="Choose a file..." type="file"
            accept="image/*">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-lightblue">
                    <i class="fas fa-upload"></i>
                </div>
            </x-slot>
        </x-adminlte-input-file>

        <x-adminlte-select disabled name="indonesia_province_id">
            <option value="{{ $coordinator->indonesia_province_id }}" disabled selected>
                {{ $coordinator->province->name }}
            </option>
        </x-adminlte-select>

        {{-- 
        <x-adminlte-select disabled name="indonesia_province_id">
            <option value="{{ Auth::user()->province->id }}" disabled selected>{{ Auth::user()->province->name }}</option>
        </x-adminlte-select> --}}
        <x-adminlte-select id="city" name="indonesia_city_id">
            <option value="-" disabled selected>Pilih Kabupaten/Kota</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}"
                    {{ old('indonesia_city_id', $coordinator->indonesia_city_id) == $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </x-adminlte-select>

        <x-adminlte-input name="password" label="Password" placeholder="password" type="password" igroup-size="md" />
        <x-adminlte-input name="password_confirmation" label="Confirm Password" placeholder="confirm password"
            type="password" igroup-size="md" />
        <x-adminlte-input name="nik" value="{{ old('nik', $coordinator->nik) }}" label="NIK" placeholder="nik"
            type="number" igroup-size="md" />
        <x-adminlte-input name="phone" value="{{ old('phone', $coordinator->phone) }}" label="Phone" placeholder="phone"
            type="number" igroup-size="md" />
        <x-adminlte-textarea name="address" placeholder="address" label="Address">
            {{ old('address', $coordinator->address) }}
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
