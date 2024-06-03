@extends('adminlte::page')

@section('title', 'Detail Responden')

@section('content_header')
    <h1>Detail Responden</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                  <tr>
                    <td width="50%">Nama Relawan</td>
                    <td width="50%">{{ $customer->surveyor}}</td>
                </tr>
                 <tr>
                    <td width="50%">NIK Relawan</td>
                    <td width="50%">{{ $customer->nik_surveyor}}</td>
                </tr>
                <tr>
                    <td width="50%">Nama Responden</td>
                    <td width="50%">{{ $customer->name }}</td>
                </tr>
               
                 <tr>
                    <td width="50%">Metode</td>
                    <td width="50%">{{ $customer->metode}}</td>
                </tr>
                <tr>
                    <td width="50%">Email</td>
                    <td width="50%">{{ $customer->email }}</td>
                </tr>
                <tr>
                    <td width="50%">Jenis Kelamin</td>
                    <td width="50%">{{ $customer->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td width="50%">NIK Responden</td>
                    <td width="50%">{{ $customer->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">No KK</td>
                    <td width="50%">{{ $customer->no_kk?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Nomor Handphone</td>
                    <td width="50%">{{ $customer->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Tanggal Lahir</td>
                    <td width="50%">
                        @if ($customer->dob)
                            {{ \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Provinsi</td>
                    <td width="50%">
                        @if ($customer->province)
                            {{ $customer->province->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kabupaten/Kota</td>
                    <td width="50%">
                        @if ($customer->city)
                            {{ $customer->city->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kecamatan</td>
                    <td width="50%">
                        @if ($customer->district)
                            {{ $customer->district->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Desa/Kelurahan</td>
                    <td width="50%">
                        @if ($customer->village)
                            {{ $customer->village->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Alamat</td>
                    <td width="50%">{{ $customer->address ?? '-' }}</td>
                </tr>


                <tr>
                    <td width="50%">Agama</td>
                    <td width="50%">{{ $customer->religion ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Pendidikan Terakhir</td>
                    <td width="50%">{{ $customer->education ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Pekerjaan</td>
                    <td width="50%">{{ $customer->job ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Jumlah Anggota Keluarga</td>
                    <td width="50%">{{ $customer->family_member ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Jumlah Pemilih Dalam KK</td>
                    <td width="50%">{{ $customer->family_election ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Status Pernikahan</td>
                    <td width="50%">{{ $customer->marrital_status ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Pendapatan Perbulan</td>
                    <td width="50%">{{ $customer->monthly_income ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Tps</td>
                    <td width="50%">{{ $customer->tps ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
