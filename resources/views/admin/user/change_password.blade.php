@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
<h1>Change Password</h1>
@stop

@section('content')
<form action="{{route('admin.account.password.store')}}" method="post">
  @csrf
  <x-adminlte-input name="old_password" required label="Old Password" placeholder="old password" type="password" igroup-size="md" />
  <x-adminlte-input name="new_password" required label="New Password" placeholder="new password" type="password" igroup-size="md" />
  <x-adminlte-input name="confirm_password" required label="Confirm Password" placeholder="confirm password" type="password" igroup-size="md" />
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop
