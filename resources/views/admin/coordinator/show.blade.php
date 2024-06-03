@extends('adminlte::page')

@section('title', 'Detail Koordinator Area')

@section('content_header')
    <h1>Detail Koordinator Area</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <tr>
                    <td width="50%">Foto Profil</td>
                    <td width="50%">
                        @if ($coordinator->profile_image)
                            <img src="{{ asset('public/storage/' . $coordinator->profile_image) }}" height="80px"
                                width="80px" alt="{{ $coordinator->name }}" style="object-fit: cover; border-radius: 100%">
                        @else
                            <img src="{{ url('public/assets/default.jpeg') }}" height="80px" width="80px"
                                alt="{{ $coordinator->name }}" style="object-fit: cover">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Owner By</td>
                    <td width="50%">{{ $owner->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Nama</td>
                    <td width="50%">{{ $coordinator->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Email</td>
                    <td width="50%">{{ $coordinator->email }}</td>
                </tr>
                {{-- <td width="50%">Jenis Kelamin</td>
                <td width="50%">{{ $admin->gender }}</td> --}}
                </tr>
                <tr>
                    <td width="50%">NIK</td>
                    <td width="50%">{{ $coordinator->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Nomor Handphone</td>
                    <td width="50%">{{ $coordinator->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Alamat</td>
                    <td width="50%">{{ $coordinator->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Provinsi</td>
                    <td width="50%">
                        @if ($coordinator->province)
                            {{ $coordinator->province->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kabupaten/Kota</td>
                    <td width="50%">
                        @if ($coordinator->city)
                            {{ $coordinator->city->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <td width="50%">Terverifikasi</td>
                    <td width="50%">
                        {{ $coordinator->email_verified_at ? $coordinator->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">Tanggal dibuat</td>
                    <td width="50%">{{ $coordinator->created_at->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
