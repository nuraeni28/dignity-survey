@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
<h1>Profile</h1>
@stop

@section('content')
<form action="{{route('admin.account.info.store')}}" method="post">
  @csrf
  <x-adminlte-input name="name" value="{{$user->name}}" required label="Name" placeholder="name" type="text" igroup-size="md">
    <x-slot name="prependSlot">
      <div class="input-group-text bg-dark">
        <i class="fa fa-user"></i>
      </div>
    </x-slot>
  </x-adminlte-input>
  <x-adminlte-input name="email" value="{{$user->email}}" required label="Email" placeholder="email" type="email" igroup-size="md">
    <x-slot name="prependSlot">
      <div class="input-group-text bg-dark">
        <i class="fa fa-envelope"></i>
      </div>
    </x-slot>
  </x-adminlte-input>
  <x-adminlte-input name="nik" required value="{{old('nik', $user->nik)}}" label="NIK" placeholder="nik" type="number" igroup-size="md" />
  <x-adminlte-input name="phone" required value="{{old('phone', $user->phone)}}" label="Phone" placeholder="phone" type="number" igroup-size="md" />
  <x-adminlte-textarea name="address" required label="Address" placeholder="address">
    {{old('address', $user->address)}}
  </x-adminlte-textarea>
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
