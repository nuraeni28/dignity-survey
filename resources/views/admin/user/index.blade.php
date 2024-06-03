@extends('adminlte::page')

@section('title', 'Relawan')

@section('content_header')
<h1>Relawan</h1>
@stop

@section('content')
<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr>
          <th style="width: 10px">NO</th>
          <th>NAME</th>
          <th>EMAIL</th>
          <th>ROLE</th>
          <th>TARGET</th>
          <th>TERVIRIFIKASI</th>
          <th style="width: 40px">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @php
        $i=0;
        @endphp
        @foreach($users as $user)
        @php
        $i = $i+1;
        @endphp
        <tr>
          <td>{{$i}}</td>
          <td>{{$user->name}}</td>
          <td>{{$user->email}}</td>
          <td>
            @forelse($user->roles as $role)
            {{ $role->name }}
            @empty
            ----
            @endforelse
          </td>
          <td>
            @if ($user->targetInterview)
            {{count($user->doneInterviews)}} / {{ $user->targetInterview->target_interviews}}
            @else
            Belum ada target
            @endif
          </td>
          <td>{{$user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'Belum diverifikasi'}}</td>
          <td>
            <nobr>
              <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
              </button>
              <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                <i class="fa fa-lg fa-fw fa-trash"></i>
              </button>
              <button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
              </button>
            </nobr>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-footer clearfix">
    {{ $users->appends(request()->query())->links() }}
  </div>
</div>
@stop
