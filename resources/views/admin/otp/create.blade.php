@extends('adminlte::page')

@section('title', 'Create Testing OTP')

@section('content_header')
    <div class="row gap-3 justify-between">
        <h1>Create Testing OTP</h1>
    </div>
@stop


@section('content')
    <form action="{{ route('otp.store') }}" method="post">
        @csrf
        <x-adminlte-input value="{{ old('nama') }}" name="nama" required label="Nama" placeholder="Nama" type="text"
            igroup-size="md">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-dark">
                    <i class="fa fa-user"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input value="{{ old('number_phone') }}" name="number_phone" label="Nomor Hp" placeholder="Nomor Hp"
            type="number" igroup-size="md" />
        <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
    </form>

@stop
