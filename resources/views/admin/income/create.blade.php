@extends('adminlte::page')

@section('title', 'Create Pendapatan')

@section('content_header')
<h1>Create Pendapatan</h1>
@stop

@section('content')
<form action="{{route('income.store')}}" method="post">
  @csrf
  <x-adminlte-input name="name" value="{{old('name')}}" required label="Opsi" placeholder="Opsi pendapatan" igroup-size="md" />
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
