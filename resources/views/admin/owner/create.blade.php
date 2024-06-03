@extends('adminlte::page')

@section('title', 'Create Owner')

@section('content_header')
    <h1>Create Owner</h1>
@stop

@php
    $provinces = new App\Http\Controllers\DependentDropdownController();
    $provinces = $provinces->provinces();
@endphp

@section('content')
    <form action="{{ route('owner.store') }}" method="post">
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
        <x-adminlte-select name="indonesia_province_id">
            <option value="-" disabled selected>Pilih Provinsi</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->id }}" {{ old('indonesia_province_id') == $province->id ? 'selected' : '' }}>
                    {{ $province->name }}
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
