@extends('adminlte::page')

@section('title', 'Detail Owner')

@section('content_header')
    <h1>Detail Owner</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table">
                <tr>
                    <td width="50%">Foto Profil</td>
                    <td width="50%">
                        @if ($owner->profile_image)
                            <img src="{{ asset('public/storage/' . $owner->profile_image) }}" height="80px" width="80px"
                                alt="{{ $owner->name }}" style="object-fit: cover; border-radius: 100%">
                        @else
                            <img src="{{ url('public/assets/default.jpeg') }}" height="80px" width="80px"
                                alt="{{ $owner->name }}" style="object-fit: cover">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Nama</td>
                    <td width="50%">{{ $owner->name }}</td>
                </tr>
                <tr>
                    <td width="50%">Email</td>
                    <td width="50%">{{ $owner->email }}</td>
                </tr>
                <tr>
                    <td width="50%">Jenis Kelamin</td>
                    <td width="50%">{{ $owner->gender }}</td>
                </tr>
                <!--<tr>-->
                <!--    <td width="50%">NIK</td>-->
                <!--    <td width="50%">{{ $owner->nik ?? '-' }}</td>-->
                <!--</tr>-->
                <tr>
                    <td width="50%">Nomor Handphone</td>
                    <td width="50%">{{ $owner->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Alamat</td>
                    <td width="50%">{{ $owner->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td width="50%">Provinsi</td>
                    <td width="50%">
                        @if ($owner->province)
                            {{ $owner->province->name }}
                        @else
                            {{ __('-') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%">Terverifikasi</td>
                    <td width="50%">
                        {{ $owner->email_verified_at ? $owner->email_verified_at->format('d/m/Y') : 'Belum diverifikasi' }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">Tanggal dibuat</td>
                    <td width="50%">{{ $owner->created_at->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
