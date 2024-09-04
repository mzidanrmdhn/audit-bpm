<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <title>Cetak</title>

</head>
<body>
    <div class="h-screen p-6 bg-whiteSmoke gap-6">
        <div class="items-center pb-3">
            <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Laporan Unit</p>
        </div>
        <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">
            <p class="font-bold text-lg text-jet pb-3">Hasil Penilaian Unit</p>

            <div class="grid grid-cols-3 p-4 gap-4 mb-4">
                <div class="px-2 py-3 text-gray-600 bg-white rounded-lg shadow border-s-4 border-s-caribbean/75">
                    <p class="font-medium mb-2">Terlampaui</p>
                    <p class="text-2xl font-bold">{{ $persentaseTerlampaui }}%</p>
                </div>
                <div class="px-2 py-3 text-gray-600 bg-white rounded-lg shadow border-s-4 border-s-amber/75">
                    <p class="font-medium mb-2">Tercapai</p>
                    <p class="text-2xl font-bold">{{ $persentaseTercapai }}%</p>
                </div>
                <div class="px-2 py-3 text-gray-600 bg-white rounded-lg shadow border-s-4 border-s-[#D32F2F]/75">
                    <p class="font-medium mb-2">Tidak Tercapai</p>
                    <p class="text-2xl font-bold">{{ $persentaseTidakTercapai }}%</p>
                </div>
            </div>

            <div class="relative overflow-x-auto mb-6">
                <table id="user" class="w-full text-sm text-left text-gray-500">
                    <thead >
                        <tr class="text-gray-700 uppercase bg-whiteSmoke p-4">
                            <th class="text-center px-2 py-3">Kode Pertanyaan</th>
                            <th class="text-center px-2 py-3">Target</th>
                            <th class="text-center px-2 py-3">Capaian</th>
                            <th class="text-center px-2 py-3">Sebutan</th>
                            <th class="text-center px-2 py-3">Ketercapaian</th>
                            <th class="text-center px-2 py-3">Prediksi Capaian Akreditasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tableData as $item)
                            <tr class="hover:bg-gray-50 bg-white border-b">
                                <td class="text-center px-2 py-1">
                                    <p>{{ $item['code'] }}</p>
                                </td>
                                {{--  <td><p class="text-left whitespace-pre-wrap">{{ $item['question'] }}</p></td>  --}}
                                <td class="text-center px-2 py-1">{{ $item['target_score'] }}</td>
                                <td class="text-center px-2 py-1">{{ $item['achieve_score'] }}</td>
                                <td class="text-center px-2 py-1">
                                    <span class="bg-{{ $item['sebutan_class'] }} p-2 text-white text-sm rounded-lg w-full inline-block">
                                        {{ $item['sebutan'] }}
                                    </span>
                                </td>
                                <td class="text-center px-2 py-1">
                                    <span class="bg-{{ $item['ketercapaian_class'] }} p-2 text-white text-sm rounded-lg w-full inline-block">
                                        {{ $item['ketercapaian'] }}
                                    </span>
                                </td>
                                <td class="text-center px-2 py-1">{{ $item['pred_value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-4 mt-8 rounded-lg shadow overflow-x-auto">
            <p class="font-bold text-lg text-jet pb-3">Rekap Nilai</p>
            <div class="relative overflow-x-auto">
                <table id="user" class="w-full text-sm text-left text-gray-500">
                    <thead >
                        <tr class="text-gray-700 uppercase bg-whiteSmoke p-4">
                            <th class="text-center px-2 py-3">Kriteria</th>
                            <th class="text-center px-2 py-3">Nilai per kriteria</th>
                            <th class="text-center px-2 py-3">Rata-rata per kriteria</th>
                            <th class="text-center px-2 py-3">Sebutan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2">{{ $data['criteria'] }}</td>
                                <td class="px-2 py-2 text-center">{{ $data['total_score'] }}</td>
                                <td class="px-2 py-2 text-center">{{ $data['average_score'] }}</td>
                                <td class="px-2 py-2 text-center">
                                    <span class="{{ $data['sebutan_class'] }} p-2 text-white text-sm rounded-lg w-full inline-block">
                                        {{ $data['sebutan'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-sm">
                        <tr class="bg-whiteSmoke text-jet text-center rounded-lg">
                            <th class="px-2 py-2" colspan="2">Total Rata-rata</th>
                            <td class="px-2 py-2">{{ $sumAvg }}</td>
                            <td class="px-2 py-2 text-center "><span class="{{ $sebutan_class }} p-2 text-white text-sm rounded-lg w-full inline-block">{{ $sebutan }}</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="bg-white p-4 mt-8 rounded-lg shadow overflow-x-auto">
            <p class="font-bold text-lg text-jet pb-3">Saran Perbaikan</p>
            <div class="relative overflow-x-auto">
                <table id="user" class="w-full text-sm text-left text-gray-500">
                    <thead >
                        <tr class="text-gray-700 uppercase bg-whiteSmoke p-4">
                            <th class="px-2 py-3">Kode</th>
                            <th class="px-2 py-3">Indikator</th>
                            <th class="px-2 py-3">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saran_perbaikan as $saran)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap">{{ $saran['question_code'] }}</td>
                                <td class="px-2 py-2"><p class="whitespace-pre-wrap">{{ $saran['question_text'] }}</p></td>
                                <td class="px-2 py-2 text-center">{{ $saran['score'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-4 mt-8 rounded-lg shadow overflow-x-auto">
            <div class="mt-4">
                <p class="font-bold text-lg text-jet">Peta per Kriteria</p>
                <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5 pb-3" id="chartContainer"></div>
            </div>

            <div class="mt-4">
                <p class="font-bold text-lg text-jet">Peta Capaian Mutu</p>
                <div id="allDataChartContainer" class="bg-white p-4 mt-3 rounded-lg shadow justify-center"></div>
            </div>
        </div>
    </div>

    <input type="hidden" name="" class="bg-[#FF9800]">

    @include('reports.script')

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
