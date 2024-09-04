@extends('layout.dashboard')
@section('title', 'Performance Unit')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Evaluasi Kinerja Unit per Tahun</p>
    </div>

    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto min-w-full">
        <div class="mb-4">
            <table>
                <tr>
                    <th class="text-left">Unit</th>
                    <th>:</th>
                    <td class="pl-2 capitalize">{{ $unit->name }}</td>
                </tr>
                <tr>
                    <th class="text-left align-middle">Tahun</th>
                    <th class="align-middle">:</th>
                    <td class="pl-2 pt-4 align-middle">
                        <form method="GET" action="{{ route('performance-unit.index') }}">
                            <select name="year" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <form action="{{ url('performance-unit', ['id' => request()->edit_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (request()->has('edit_id'))
                @method('PUT')
            @endif
            <input type="hidden" name="parent" value="{{ request()->parent }}">
            <table class="border-collapse border border-slate-500 w-full mt-4 text-sm text-jet">
                <tr>
                    <th colspan="6" class="border border-gray-400 p-2">Ketercapaian Kinerja Unit</th>
                    <th colspan="3" class="border border-gray-400 p-2">Penilaian Auditor</th>
                </tr>
                <tr>
                    <th class="border border-gray-400 p-2">NO</th>
                    <th class="border border-gray-400 p-2">Keterangan</th>
                    <th class="border border-gray-400 p-2">Target</th>
                    <th class="border border-gray-400 p-2">Realisasi</th>
                    <th class="border border-gray-400 p-2">Waktu Pelaksanaan</th>
                    <th class="border border-gray-400 p-2">Dokumen</th>

                    <th class="border border-gray-400 p-2">Evaluasi</th>
                    <th class="border border-gray-400 p-2">Catatan</th>
                    <th class="border border-gray-400 p-2"></th>
                </tr>

                @foreach ($data as $index => $item)
                    @if (request()->edit_id == $item->id)
                        <tr>
                            <td class="border border-gray-400 p-2 text-center">
                                {{ $index + 1 }}
                            </td>
                            <td class="border border-gray-400 p-2">
                                <input type="text" name="work_planning" class="bg-transparent h-8 w-full border-0 border-b text-sm focus:rounded-lg focus:border-0 focus:ring-caribbean" value="{{ $item->work_planning }}">
                            </td>
                            <td class="border border-gray-400 p-2">
                                <input type="text" name="target" class="bg-transparent h-8 w-full border-0 border-b text-sm focus:rounded-lg focus:border-0 focus:ring-caribbean" value="{{ $item->target }}">
                            </td>
                            <td class="border border-gray-400 p-2">
                                <input type="text" name="achieve" class="bg-transparent h-8 w-full border-0 border-b text-sm focus:rounded-lg focus:border-0 focus:ring-caribbean" value="{{ $item->achieve }}">
                            </td>
                            <td class="border border-gray-400 p-2">
                                <input type="text" name="time_target" class="bg-transparent h-8 w-full border-0 border-b text-sm focus:rounded-lg focus:border-0 focus:ring-caribbean" value="{{ $item->time_target }}">
                            </td>
                            <td class="border border-gray-400 p-2 w-1/5">
                                <input type="file" name="document" class="bg-gray-50 w-full border border-gray-300 text-xs text-jet rounded-lg cursor-pointer">
                                @if ($item->document)
                                    <a href="{{ asset('storage/' . $item->document) }}" class="text-blue-500" target="_blank">View Document</a>
                                @endif
                            </td>
                            <td class="border border-gray-400 p-2"></td>
                            <td class="border border-gray-400 p-2"></td>
                            <td class="border border-gray-400 p-2">
                                <button type="submit" class="border-0 bg-caribbean text-white rounded-lg p-2 w-full">Simpan</button>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="border border-gray-400 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-400 p-2">{{ $item->work_planning }}</td>
                            <td class="border border-gray-400 p-2 text-center">{{ $item->target }}</td>
                            <td class="border border-gray-400 p-2 text-center">{{ $item->achieve }}</td>
                            <td class="border border-gray-400 p-2 text-center">{{ $item->time_target }}</td>
                            <td class="border border-gray-400 p-2 text-center">
                                @if ($item->document)
                                    <a href="{{ asset('storage/' . $item->document) }}" class="text-blue-500" target="_blank">View Document</a>
                                @endif
                            </td>
                            <td class="border border-gray-400 p-2 text-center"></td>
                            <td class="border border-gray-400 p-2"></td>
                            <td class="border border-gray-400 p-2">
                                <select onchange="actions(this, '{{ $item->id }}')" class="rounded-lg border-caribbean/50 text-sm focus:ring-caribbean">
                                    <option value="" selected>Aksi</option>
                                    <option value="add_above">Add Above</option>
                                    <option value="add_below">Add Below</option>
                                    <option value="edit_id">Edit</option>
                                    <option value="hapus_id">Hapus</option>
                                </select>
                            </td>
                        </tr>
                    @endif
                @endforeach

                @if (!request()->edit_id)
                <tr>
                    <td class="border border-gray-400 p-2 text-center">
                        {{ request()->parent }}
                    </td>
                    <td class="border border-gray-400 p-2">
                        @if ($role_name === 'Unit' || $role_name == 'Super Admin')
                            <input type="text" name="work_planning" class="bg-transparent h-8 w-full border-0 border-b focus:rounded-lg focus:border-0 focus:ring-caribbean">
                        @endif
                    </td>
                    <td class="border border-gray-400 p-2">
                        @if ($role_name === 'Unit' || $role_name == 'Super Admin')
                        <input type="text" name="target" class="bg-transparent h-8 w-full border-0 border-b focus:rounded-lg focus:border-0 focus:ring-caribbean">
                        @endif
                    </td>
                    <td class="border border-gray-400 p-2">
                        @if ($role_name === 'Unit' || $role_name == 'Super Admin')
                        <input type="text" name="achieve" class="bg-transparent h-8 w-full border-0 border-b focus:rounded-lg focus:border-0 focus:ring-caribbean">
                        @endif
                    </td>
                    <td class="border border-gray-400 p-2">
                        @if ($role_name === 'Unit' || $role_name == 'Super Admin')
                        <input type="date" name="time_target" class="bg-transparent h-8 w-full border-0 border-b focus:rounded-lg focus:border-0 focus:ring-caribbean">
                        @endif
                    </td>
                    <td class="border border-gray-400 p-2 w-1/5">
                        @if ($role_name === 'Unit' || $role_name == 'Super Admin')
                        <input type="file" name="document" class="bg-gray-50 w-full border border-gray-300 text-xs text-jet rounded-lg cursor-pointer">
                        @endif
                    </td>
                    <td class="border border-gray-400 p-2"></td>
                    <td class="border border-gray-400 p-2"></td>
                    <td class="border border-gray-400 p-2">
                        <button type="submit" class="border-0 bg-caribbean text-white rounded-lg p-2 w-full">Simpan</button>
                    </td>
                </tr>
                @endif
            </table>
        </form>
    </div>

    <script>
        function actions(event, id) {
            console.log(id)
            // location.href = 'performance-unit?' + event.value + '=' + id
            var action = event.value;
            if (action === 'hapus_id') {
                if (confirm('Yakin ingin menghapus?')) {
                    axios.delete(`performance-unit/${id}`)
                        .then(function() {
                            location.reload()
                        })
                }
            } else if (action === 'edit_id') {
                location.href = 'performance-unit?' + action + '=' + id;
            } else {
                location.href = 'performance-unit?' + action + '=' + id;
            }
        }
    </script>
@endsection
