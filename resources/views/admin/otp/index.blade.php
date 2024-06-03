@extends('adminlte::page')
@section('title', 'Testing Otp')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1>Data Testing OTP
                <a href="{{ route('otp.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-lg fa-plus"></i>
                </a>

            </h1>
        </div>

    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10px">NO</th>
                        <th>NAMA</th>
                        <th>NO HP</th>
                        <th style="width: 40px">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = ($otps->currentPage() - 1) * $otps->perPage();
                    @endphp
                    @foreach ($otps as $otp)
                        @php
                            $i = $i + 1;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $otp->nama }}</td>
                            <td>{{ $otp->number_phone }}</td>
                            </td>
                            <td>
                                <nobr class="d-flex">
                                    <a href="{{ route('otp.edit', $otp->id) }}"
                                        class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                        <i class="fa fa-lg fa-fw fa-pen"></i>
                                    </a>
                                    <form class="flex" action="{{ route('otp.destroy', $otp->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure you want to delete?')"
                                            class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                        </button>
                                    </form>
                                </nobr>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $otps->links() }}
        </div>
    </div>
@stop
