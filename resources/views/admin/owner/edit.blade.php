@extends('adminlte::page')

@section('title', 'Edit Owner')

@section('content_header')
    <h1>Edit Owner</h1>
@stop

@php
    $provinces = new App\Http\Controllers\DependentDropdownController();
    $provinces = $provinces->provinces();
@endphp

@section('content')
    <form action="{{ route('owner.update', $owner->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-adminlte-input name="name" value="{{ old('name', $owner->name) }}" label="Name" placeholder="name" type="text"
            igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input name="email" value="{{ old('email', $owner->email) }}" label="Email" placeholder="email"
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
        <!--    @if ($owner)-->
        <!--        <option value="{{ $owner->gender }}"-->
        <!--            {{ old('gender', $owner->gender) == $owner->gender ? 'selected' : '' }}>-->
        <!--            {{ $owner->gender }}-->
        <!--        </option>-->
        <!--    @endif-->
        <!--</x-adminlte-select>-->
        <x-adminlte-input-file name="profile_image" label="Profile Picture" placeholder="Choose a file..." type="file"
            accept="image/*">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-lightblue">
                    <i class="fas fa-upload"></i>
                </div>
            </x-slot>
        </x-adminlte-input-file>
        <x-adminlte-select name="indonesia_province_id">
            <option value="-" disabled selected>Pilih Provinsi</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->id }}"
                    {{ old('indonesia_province_id', $owner->indonesia_province_id) == $province->id ? 'selected' : '' }}>
                    {{ $province->name }}
                </option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-input name="password" label="Password" placeholder="password" type="password" igroup-size="md" />
        <x-adminlte-input name="password_confirmation" label="Confirm Password" placeholder="confirm password"
            type="password" igroup-size="md" />
        <x-adminlte-input name="nik" value="{{ old('nik', $owner->nik) }}" label="NIK" placeholder="nik"
            type="number" igroup-size="md" />
        <x-adminlte-input name="phone" value="{{ old('phone', $owner->phone) }}" label="Phone" placeholder="phone"
            type="number" igroup-size="md" />
        <x-adminlte-textarea name="address" placeholder="address" label="Address">
            {{ old('address', $owner->address) }}
        </x-adminlte-textarea>
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop
