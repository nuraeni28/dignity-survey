@extends('adminlte::page')

@section('title', 'Create Jadwal')

@section('content_header')
<h1>Create Jadwal</h1>
@stop

@section('content')
<form action="{{route('schedule.store')}}" method="post">
  @csrf
  <x-adminlte-select required name="period_id" label="Periode">
    <option value="-" disabled selected>Pilih Periode</option>
    @foreach ($periods as $period)
    <option value="{{ $period->id }}" {{ old('period_id') == $period->id ? 'selected' : '' }}>
      {{ $period->start_date->format('d/m/Y') . '-' . $period->end_date->format('d/m/Y') }}
    </option>
    @endforeach
  </x-adminlte-select>
  <x-adminlte-select required name="user_id" label="Relawan">
    <option value="-" disabled selected>Pilih Periode</option>
    @foreach ($users as $user)
    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
      {{ $user->name }}
    </option>
    @endforeach
  </x-adminlte-select>
  <x-adminlte-input name="target_interviews" value="{{old('target_interviews')}}" required label="Target Interview" placeholder="target interview" type="number" igroup-size="md" />
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Responden</h3>
      <div class="card-tools">
        <input type="checkbox" id="check_all" class="ml-2">
        <div class="card-title">Pilih Semua</div>
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover text-nowrap">
        <thead>
          <tr>
            <th>#</th>
            <th>NAMA</th>
            <th>NIK</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($customers as $customer)
          <tr>
            <td>
              <input type="checkbox" name="customer_id[]" value="{{ $customer->id }}">
            </td>
            <td>
              {{ $customer->name }}
            </td>
            <td>
              {{ $customer->nik }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <x-adminlte-button class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" />
</form>
@stop

@section('js')
<script>
  $(document).ready(function() {
    $('#check_all').click(function() {
      if ($(this).is(':checked')) {
        $('input[type=checkbox]').each(function() {
          $(this).prop('checked', true);
        });
      } else {
        $('input[type=checkbox]').each(function() {
          $(this).prop('checked', false);
        });
      }
    });
  });
</script>
@stop
