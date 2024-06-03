@extends('adminlte::page')

@section('title', 'Detail Admin')

@section('content_header')
    <h1>Detail Admin</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <tr>
                    <td width="50%">Foto Profil</td>
                    <td width="50%">
                        @if ($admin->profile_image)
                            <img src="{{ asset('public/storage/' . $admin->profile_image) }}" height="80px" width="80px"
                                alt="{{ $admin->name }}" style="object-fit: cover; border-radius: 100%">
                        @else
                            <img src="{{ url('public/assets/default.jpeg') }}" height="80px" width="80px"
                                alt="{{ $admin->name }}" style="object-fit: cover">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Owner By</td>
                    <td width="50%">{{ $owner->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Nama</td>
                    <td width="50%">{{ $admin->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Email</td>
                    <td width="50%">{{ $admin->email }}</td>
                </tr>
                {{-- <td width="50%">Jenis Kelamin</td>
                <td width="50%">{{ $admin->gender }}</td> --}}
                </tr>
                <tr>
                    <td width="50%">NIK</td>
                    <td width="50%">{{ $admin->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Nomor Handphone</td>
                    <td width="50%">{{ $admin->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Alamat</td>
                    <td width="50%">{{ $admin->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Provinsi</td>
                    <td width="50%">
                        @if ($admin->province)
                            {{ $admin->province->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kabupaten/Kota</td>
                    <td width="50%">
                        @if ($admin->city)
                            {{ $admin->city->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kecamatan</td>
                    <td width="50%">
                        @if ($admin->district)
                            {{ $admin->district->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Terverifikasi</td>
                    <td width="50%">
                        {{ $admin->email_verified_at ? $admin->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">Tanggal dibuat</td>
                    <td width="50%">{{ $admin->created_at->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
