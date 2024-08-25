@extends('layout.dashboard')
@section('title', 'Penilaian Unit')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Hasil Penilaian Unit</p>
        <a href="{{ route('formula.generate') }}"
            class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md bg-teal text-white hover:bg-white hover:text-teal hover:border hover:border-teal">
            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4" />
            </svg>
            <p>Generate Nilai</p>
        </a>

    </div>

    {{--  Alert  --}}
    <div class="flex items-center py-2 px-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-100 max-w-" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            fill="currentColor" viewBox="0 0 20 20">
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>
        <span class="sr-only">Info</span>
        <div>
            <p>Apabila data tidak tampil, silakan tekan tombol <span class="font-bold">Generate Nilai</span> untuk melakukan
                generate.</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mt-4">
        <div class="px-6 py-3 text-gray-600 bg-white rounded-lg shadow border-s-4 border-s-caribbean/75">
            <p class="font-medium mb-2">Terlampaui</p>
            <p class="text-3xl font-bold">{{ $persentaseTerlampaui }}%</p>
        </div>
        <div class="px-6 py-3 text-gray-600 bg-white rounded-lg shadow border-s-4 border-s-amber/75">
            <p class="font-medium mb-2">Tercapai</p>
            <p class="text-3xl font-bold">{{ $persentaseTercapai }}%</p>
        </div>
        <div class="px-6 py-3 text-gray-600 bg-white rounded-lg shadow border-s-4 border-s-[#D32F2F]/75">
            <p class="font-medium mb-2">Tidak Tercapai</p>
            <p class="text-3xl font-bold">{{ $persentaseTidakTercapai }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-6 gap-3 mt-4">
        <select name="criteria_id" id="criteria_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option value="" disabled selected>Tampilkan Berdasarkan Kriteria</option>
            @foreach ($criteria as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <select id="unit_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option value="" disabled selected>Pilih Unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">
        <table id="user" class="w-screen text-center">
            <thead class="">
                <tr>
                    <th>Kode Pertanyaan</th>
                    <th>Target</th>
                    <th>Capaian</th>
                    <th>Sebutan</th>
                    <th>Ketercapaian</th>
                    <th>Prediksi Capaian Akreditasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tableData as $item)
                    <tr data-criteria="{{ $item['criteria_id'] }}" class="hover:bg-gray-50">
                        <td class="text-center">
                            <p data-popover-target="popover-question-{{ $item['code'] }}">{{ $item['code'] }}</p>
                            <div id="popover-question-{{ $item['code'] }}" data-popover role="tooltip" class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0">
                                <div class="px-3 py-2">
                                    <p class="whitespace-pre-wrap">{{ $item['question'] }}</p>
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </td>
                        {{--  <td><p class="text-left whitespace-pre-wrap">{{ $item['question'] }}</p></td>  --}}
                        <td class="text-center">{{ $item['target_score'] }}</td>
                        <td class="text-center">{{ $item['achieve_score'] }}</td>
                        <td class="text-center">
                            <span class="bg-{{ $item['sebutan_class'] }} p-2 text-white text-sm rounded-lg w-full inline-block">
                                {{ $item['sebutan'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="bg-{{ $item['ketercapaian_class'] }} p-2 text-white text-sm rounded-lg w-full inline-block">
                                {{ $item['ketercapaian'] }}
                            </span>
                        </td>
                        <td class="text-center">{{ $item['pred_value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <input type="hidden" name="" class="bg-cerulean">

    </div>
    @include('results.score-script')
@endsection
