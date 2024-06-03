@extends('adminlte::page')

@section('title', 'Edit Testing OTP')

@section('content_header')
    <h1>Edit Testing OTP</h1>
@stop

@section('content')
    @php
        // dd($admin->id
    @endphp
    <form action="{{ route('otp.update', $otp->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-adminlte-input name="nama" value="{{ old('nama', $otp->nama) }}" label="Nama" placeholder="Nama" type="text"
            igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>

        <x-adminlte-input name="number_phone" value="{{ old('number_phone', $otp->number_phone) }}" label="Nomor Hp"
            placeholder="Nomor Hp" type="number" igroup-size="md" />
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>
@stop
