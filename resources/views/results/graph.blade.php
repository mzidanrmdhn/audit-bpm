@extends('layout.dashboard')
@section('title', 'Penilaian Unit')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Laporan Penilaian Unit</p>
        <a href="{{ route('generatePdf',  ['unit_id' => auth()->user()->unit_id]) }}"
            class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md bg-caribbean text-white hover:bg-white hover:text-teal hover:border hover:border-teal">
            <svg class="w-5 h-5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M16.444 18H19a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2.556M17 11V5a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v6h10ZM7 15h10v4a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4Z"/>
              </svg>
            <p>Cetak Laporan</p>
        </a>
    </div>
    <div class="flex flex-row gap-3 mt-4">
        <form method="GET" action="{{ route('grafik.index') }}">
            <label for="unit_id" class="text-sm font-medium text-gray-700">Pilih Unit:</label>
            <select id="unit_id" name="unit_id" class="mt-1 text-sm border-gray-300 rounded-md shadow-sm sm:text-sm">
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="mt-1 text-sm px-4 py-2 bg-smokeWhite text-caribbean border border-caribbean rounded-md">Tampilkan</button>
        </form>
    </div>

    <div class="grid 2xl:grid-cols-2 grid-cols-1 gap-5 pb-3 max-h-min">
            {{--  Rekap Nilai  --}}
            <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto h-screen">
                <div class="flex flex-row justify-between items-center mb-4">
                    <p class="font-bold text-lg">Rekap Nilai</p>
                    <a href="{{ url('/skor') }}" target="blank" class="px-4 py-2 text-sm text-center font-medium text-jet"><span class="border-b-2 border-b-caribbean/50">Lihat keseluruhan nilai</span></a>
                </div>
                <table class="w-full text-sm">
                    <thead class="text-center text-jet uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-2 rounded-s-lg">Kriteria</th>
                            <th class="px-6 py-2">Nilai per kriteria</th>
                            <th class="px-6 py-2">Rata-rata per kriteria</th>
                            <th class="px-6 py-2 rounded-e-lg">Sebutan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-2">{{ $data['criteria'] }}</td>
                                <td class="px-6 py-2 text-center">{{ $data['total_score'] }}</td>
                                <td class="px-6 py-2 text-center">{{ $data['average_score'] }}</td>
                                <td class="px-6 py-2 text-center">
                                    <span class="{{ $data['sebutan_class'] }} p-2 text-white text-sm rounded-lg w-full inline-block">
                                        {{ $data['sebutan'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-sm">
                        <tr class="bg-gray-100 text-jet text-center rounded-lg">
                            <th class="px-6 py-2" colspan="2">Total Rata-rata</th>
                            <td class="px-6 py-2">{{ $sumAvg }}</td>
                            <td class="px-6 py-2 text-center "><span class="{{ $sebutan_class }} p-2 text-white text-sm rounded-lg w-full inline-block">{{ $sebutan }}</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {{--  Saran Perbaikan  --}}
            <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-y-auto border-s-4 border-s-[#D32F2F]/75 h-screen">
                <p class="font-bold text-lg mb-4">Saran Perbaikan</p>
                <table class="w-full text-sm">
                    <thead class="text-center text-jet uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-2 rounded-s-lg">Kode</th>
                            <th class="px-6 py-2">Indikator</th>
                            <th class="px-6 py-2">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saran_perbaikan as $saran)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-2 whitespace-nowrap">{{ $saran['question_code'] }}</td>
                                <td class="px-6 py-2"><p class="whitespace-pre-wrap">{{ $saran['question_text'] }}</p></td>
                                <td class="px-6 py-2 text-center">{{ $saran['score'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </div>

    <div class="mt-4">
        <p class="font-bold text-lg text-jet">Peta per Kriteria</p>
        <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5 pb-3" id="chartContainer"></div>
    </div>

    <div class="mt-4">
        <p class="font-bold text-lg text-jet">Peta Capaian Mutu</p>
        <div id="allDataChartContainer" class="bg-white p-4 mt-3 rounded-lg shadow justify-center"></div>
    </div>

    <input type="hidden" name="" class="bg-emerald-800">
    <input type="hidden" name="" class="bg-[#FF9800]">

    @include('results.graph-script')
@endsection
