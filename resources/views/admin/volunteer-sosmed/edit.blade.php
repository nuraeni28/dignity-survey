@extends('adminlte::page')

@section('title', 'Edit Relawan Sosmed')

@section('content_header')
    <h1>Edit Relawan</h1>
@stop

@php
    $villages = new App\Http\Controllers\DependentDropdownController();

    if (Gate::check('admin')) {
        $villages = $villages->villagesData(Auth::user()->district->id);
    } else {
        $villages = $villages->villagesData($volunteer->indonesia_district_id);
    }
@endphp

@section('content')
    <form action="{{ route('relawan-sosmed.update', ['relawan_sosmed' => $volunteer->id, 'page' => $page]) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-adminlte-input name="name" value="{{ old('name', $volunteer->name) }}" label="Name" placeholder="name"
            type="text" igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input name="email" value="{{ old('email', $volunteer->email) }}" label="Email" placeholder="email"
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
            @if ($volunteer)
                <option value="{{ $volunteer->gender }}"
                    {{ old('gender', $volunteer->gender) == $volunteer->gender ? 'selected' : '' }}>
                    {{ $volunteer->gender }}
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
                        {{ old('indonesia_village_id', $volunteer->village ? $volunteer->village->id : '') == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @else
            {{-- @php
                dd($volunteer);
            @endphp --}}
            <x-adminlte-select disabled name="indonesia_province_id">
                <option value="{{ $volunteer->indonesia_province_id }}" disabled selected>{{ $volunteer->province->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select disabled name="indonesia_city_id">
                <option value="{{ $volunteer->indonesia_city_id }}" disabled selected>{{ $volunteer->city->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select disabled name="indonesia_district_id">
                <option value="{{ $volunteer->indonesia_district_id }}" disabled selected>{{ $volunteer->district->name }}
                </option>
            </x-adminlte-select>
            <x-adminlte-select name="indonesia_village_id">
                <option value="-" disabled selected>Pilih Desa</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}"
                        {{ old('indonesia_village_id', $volunteer->village ? $volunteer->village->id : '') == $village->id ? 'selected' : '' }}>
                        {{ $village->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        @endif
        <x-adminlte-input name="password" label="Password" placeholder="password" type="password" igroup-size="md" />
        <x-adminlte-input name="password_confirmation" label="Confirm Password" placeholder="confirm password"
            type="password" igroup-size="md" />
        <x-adminlte-input name="nik" value="{{ old('nik', $volunteer->nik) }}" label="NIK" placeholder="nik"
            type="number" igroup-size="md" />
        <x-adminlte-input name="phone" value="{{ old('phone', $volunteer->phone) }}" label="Phone" placeholder="phone"
            type="number" igroup-size="md" />
        <x-adminlte-textarea name="address" placeholder="address" label="Address">
            {{ old('address', $volunteer->address) }}
        </x-adminlte-textarea>
        <x-adminlte-input name="tps" value="{{ old('tps', $volunteer->tps) }}" label="tps" placeholder="Tps"
            type="number" igroup-size="md" />
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop
