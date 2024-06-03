@extends('adminlte::page')

@section('title', 'Detail Real Count Partai')

@section('content_header')
    <h1>Detail Quick Real Partai</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <tr>
                    <td width="50%">Nama Admin</td>
                    <td width="50%">{{ $data->admin }}</td>
                </tr>
                <tr>
                    <td width="50%">Partai</td>
                    <td width="50%">{{ $data->partai->name }}</td>
                </tr>
                 <tr>
                    <td width="50%">Total Suara Partai</td>
                    <td width="50%">{{ $data->jumlah_suara_partai }}</td>
                </tr>
                @foreach ($dataCaleg as $index => $caleg)
                    <tr>
                        <td width="50%">Jumlah Suara {{ $caleg->caleg->name }}</td>
                        <td width="50%">{{ $caleg->jumlah_suara_caleg }}</td>
                        </td>
                @endforeach
                </tr>
                <tr>
                    <td width="50%">Kabupaten/Kota</td>
                    <td width="50%">
                        @if ($data->city)
                            {{ $data->city->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kecamatan</td>
                    <td width="50%">
                        @if ($data->district)
                            {{ $data->district->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Desa/Kelurahan</td>
                    <td width="50%">
                        @if ($data->village)
                            {{ $data->village->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Tps</td>
                    <td width="50%">{{ $data->tps ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
