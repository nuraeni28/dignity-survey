@extends('adminlte::page')

@section('title', 'Edit Pekerjaan')

@section('content_header')
<h1>Edit Pekerjaan</h1>
@stop

@section('content')
<form action="{{route('occupation.update', $occupation->id)}}" method="post">
  @csrf
  @method("PUT")
  <x-adminlte-input name="name" value="{{old('name', $occupation->name)}}" required label="Opsi" placeholder="Opsi pekerjaan" igroup-size="md" />
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
