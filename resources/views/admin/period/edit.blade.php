@extends('adminlte::page')

@section('title', 'Edit Periode')

@section('content_header')
<h1>Edit Periode</h1>
@stop

@section('content')
<form action="{{route('period.update', $period->id)}}" method="post">
  @csrf
  @method("PUT")
  <x-adminlte-input name="start_date" value="{{old('start_date', $period->start_date)}}" required label="Tanggal Mulai" placeholder="tanggal mulai" type="date" igroup-size="md" />
  <x-adminlte-input name="end_date" value="{{old('end_date', $period->end_date)}}" required label="Tanggal Berakhir" placeholder="tanggal berakhir" type="date" igroup-size="md" />
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
