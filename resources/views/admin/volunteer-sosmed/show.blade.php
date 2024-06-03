@extends('adminlte::page')

@section('title', 'Detail Relawan Sosmed')

@section('content_header')
    <h1>Detail Relawan Sosmed</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <tr>
                    <td width="50%">Foto Profil</td>
                    <td width="50%">
                        @if ($volunteer->profile_image)
                            <img src="{{ asset('public/storage/' . $volunteer->profile_image) }}" height="80px" width="80px"
                                alt="{{ $volunteer->name }}" style="object-fit: cover; border-radius: 100%">
                        @else
                            <img src="{{ url('public/assets/default.jpeg') }}" height="80px" width="80px"
                                alt="{{ $volunteer->name }}" style="object-fit: cover">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Owner By</td>
                    <td width="50%">{{ $owner->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Admin By</td>
                    <td width="50%">{{ $admin->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Nama</td>
                    <td width="50%">{{ $volunteer->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Jenis Kelamin</td>
                    <td width="50%">{{ $volunteer->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Email</td>
                    <td width="50%">{{ $volunteer->email }}</td>
                </tr>
                {{-- <tr>
                    <td width="50%">Jenis Kelamin</td>
                    <td width="50%">{{ $volunteer->gender }}</td>
                </tr> --}}
                <tr>
                    <td width="50%">NIK</td>
                    <td width="50%">{{ $volunteer->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Nomor Handphone</td>
                    <td width="50%">{{ $volunteer->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Alamat</td>
                    <td width="50%">{{ $volunteer->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Provinsi</td>
                    <td width="50%">
                        @if ($volunteer->province)
                            {{ $volunteer->province->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kabupaten/Kota</td>
                    <td width="50%">
                        @if ($volunteer->city)
                            {{ $volunteer->city->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Kecamatan</td>
                    <td width="50%">
                        @if ($volunteer->district)
                            {{ $volunteer->district->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Desa/Kelurahan</td>
                    <td width="50%">
                        @if ($volunteer->village)
                            {{ $volunteer->village->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Terverifikasi</td>
                    <td width="50%">
                        {{ $volunteer->email_verified_at ? $volunteer->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">Tanggal dibuat</td>
                    <td width="50%">{{ $volunteer->created_at->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td width="50%">Tps</td>
                    <td width="50%">{{ $volunteer->tps }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
