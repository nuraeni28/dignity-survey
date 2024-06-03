@extends('adminlte::page')

@section('title', 'Import Interview')

@section('content_header')
    <div class="row gap-3 justify-between">
        <h1>Import Interview</h1>
        <form class="form ml-auto" action="{{ route('interview.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-adminlte-input value="{{ old('file') }}" name="file" required label="Import from file" placeholder="File"
                type="file" igroup-size="md">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-dark">
                        <i class="fa fa-file"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
            <x-adminlte-button type="submit" label="Import" theme="success" icon="fas fa-lg fa-save" />
        </form>
    </div>
@stop
