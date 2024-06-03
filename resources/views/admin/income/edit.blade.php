@extends('adminlte::page')

@section('title', 'Edit Pendapatan')

@section('content_header')
<h1>Edit Pendapatan</h1>
@stop

@section('content')
<form action="{{route('income.update', $income->id)}}" method="post">
  @csrf
  @method("PUT")
  <x-adminlte-input name="name" value="{{old('name', $income->name)}}" required label="Opsi" placeholder="Opsi pendapatan" igroup-size="md" />
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
