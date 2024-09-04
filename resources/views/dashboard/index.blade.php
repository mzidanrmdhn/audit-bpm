@extends('layout.dashboard')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Dashboard</p>
    </div>

    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">

        <div class="flex items-center mb-4">
            <span class="relative flex h-2.5 w-2.5 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-caribbean opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-caribbean"></span>
            </span>
            <p class="font-bold text-lg">
                Pengguna yang Sedang Aktif
            </p>
        </div>
        <table id="user-online" class="display">
            <thead class="">
                <tr>
                    <th class="text-center px-6 py-2">No</th>
                    <th class="text-center px-6 py-2">Nama</th>
                    <th class="text-center px-6 py-2">Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usersOnline as $index => $item)
                    <tr class="hover:bg-gray-50 px-2.5">
                        <td class="text-center px-6 py-2 bg-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-2 text-center">{{ $item->name }}</td>
                        <td class="px-6 py-2 text-center">{{ $item->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">
        <p class="font-bold text-lg mb-4">Aktivitas Pengguna</p>
        <table id="user-activity" class="display">
            <thead class="">
                <tr>
                    <th class="text-center px-6 py-2">No</th>
                    <th class="text-center px-6 py-2">Nama</th>
                    <th class="text-center px-6 py-2">Tanggal & Waktu</th>
                    <th class="text-center px-6 py-2">Aktivitas</th>
                    <th class="text-center px-6 py-2">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $index => $item)
                    <tr class="hover:bg-gray-50 px-2.5">
                        <td class="text-center px-6 py-2 bg-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-2 text-center">{{ $item->user->name }}</td>
                        <td class="px-6 py-2 text-center">{{ $item->timestamp }}</td>
                        <td class="px-6 py-2 text-center">{{ $item->activity }}</td>
                        <td class="px-6 py-2 text-center">{{ $item->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('dashboard.script')
@endsection
