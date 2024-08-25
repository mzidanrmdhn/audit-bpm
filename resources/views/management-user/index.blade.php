@extends('layout.dashboard')
@section('title', 'Manajemen Unit')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Daftar Pengguna</p>
        <button
            class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md bg-teal text-white hover:bg-white hover:text-teal hover:border hover:border-teal"
            data-modal-target="user-modal" data-modal-toggle="user-modal" onclick="openModal('add')">
            <p>Tambah Pengguna</p>
        </button>

    </div>

    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">
        <table id="management-unit" class="">
            <thead class="">
                <tr>
                    <th class="text-center">Nomor</th>
                    <th class="text-center">Nama</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr class="hover:bg-gray-50 px-2.5">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->name ?? null }}</td>
                        <td>{{ $item->email ?? null }}</td>
                        <td>{{ $item->unit->name ?? null }}</td>
                        <td>{{ $item->role->name ?? null }}</td>
                        <td>
                            <div class="flex flex-row gap-2">

                                <button data-modal-target="user-modal" data-modal-toggle="user-modal"
                                    class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md border border-amber bg-amber text-white hover:bg-white hover:text-amber hover:border hover:border-amber"
                                    onclick="openModal('edit', {{ $item->id }}, '{{ $item->name }}', '{{ $item->email }}', '{{ $item->unit['id'] }}', '{{ $item->role['id'] }}')">Edit</button>
                                <form action="{{ route('management-user.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md border border-[#D32F2F] bg-[#D32F2F] text-white hover:bg-white hover:text-[#D32F2F] hover:border hover:border-[#D32F2F]">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="user-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 id="modal-title" class="text-xl font-semibold text-gray-900 dark:text-white"></h3>
                    <button type="button"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="user-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form id="user-form" action="" method="POST">
                        @method('PUT')
                        @csrf
                        <div>
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama</label>
                            <input type="text" name="name" id="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                required />
                        </div>
                        <div>
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="text" name="email" id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                required />
                        </div>
                        <div>
                            <label for="unit_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit</label>
                            <select name="unit_id" id="unit_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="role_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role</label>
                            <select name="role_id" id="role_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('management-user.script')
@endsection
