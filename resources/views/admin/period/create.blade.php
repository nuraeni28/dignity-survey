@extends('adminlte::page')

@section('title', 'Create Periode')

@section('content_header')
<h1>Create Periode</h1>
@stop

@section('content')
<form action="{{route('period.store')}}" method="post">
  @csrf
  <x-adminlte-input name="start_date" value="{{old('start_date')}}" required label="Tanggal Mulai" placeholder="tanggal mulai" type="date" igroup-size="md" />
  <x-adminlte-input name="end_date" value="{{old('end_date')}}" required label="Tanggal Berakhir" placeholder="tanggal berakhir" type="date" igroup-size="md" />
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
