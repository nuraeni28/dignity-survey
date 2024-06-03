@extends('adminlte::page')

@section('title', 'Detail Pendukung')

@section('content_header')
    <h1>Detail Pendukung</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">

                <tr>
                    <td width="50%">Nama</td>
                    <td width="50%">{{ $supporter->name }}</td>
                </tr>
                <tr>
                    <td width="50%">NIK</td>
                    <td width="50%">{{ $supporter->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Nomor Handphone</td>
                    <td width="50%">{{ $supporter->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Provinsi</td>
                    <td width="50%">
                        @if ($supporter->province)
                            {{ $supporter->province->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kabupaten/Kota</td>
                    <td width="50%">
                        @if ($supporter->city)
                            {{ $supporter->city->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kecamatan</td>
                    <td width="50%">
                        @if ($supporter->district)
                            {{ $supporter->district->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Desa/Kelurahan</td>
                    <td width="50%">
                        @if ($supporter->village)
                            {{ $supporter->village->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>

            </table>
        </div>
    </div>
@stop
