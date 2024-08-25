@extends('layout.dashboard')
@section('title', 'Pertanyaan')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Daftar Pertanyaan</p>
        <a href="{{ route('questions.edit') }}" class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md bg-teal text-white hover:bg-white hover:text-teal hover:border hover:border-teal">Tambah Pertanyaan</a>
    </div>

    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto min-w-full">
        <table id="question" class="min-w-full">
            <thead class="">
                <tr>
                    <th class="text-center px-2.5 py-2">No</th>
                    <th class="text-center px-2.5 py-2">Kode Pertanyaan</th>
                    <th class="text-center px-2.5 py-2">Kriteria</th>
                    <th class="text-center px-2.5 py-2">Sub Kriteria</th>
                    <th class="text-center px-2.5 py-2">Tipe</th>
                    <th class="text-center px-2.5 py-2 w-[40%]">Pertanyaan</th>
                    {{--  <th class="text-center px-2.5 py-2">Pilihan/Input</th>  --}}
                    <th class="text-center px-2.5 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr class="hover:bg-gray-50 text-wrap">
                        <td class="text-center px-2.5 py-2 border-b">{{ $index + 1 }}</td>
                        <td class="text-center px-2.5 py-2 border-b">{{ $item->code ?? null }}</td>
                        <td class="text-center px-2.5 py-2 border-b">{{ $item->subCriteria->criteria->name ?? null }}</td>
                        <td class="text-center px-2.5 py-2 border-b">{{ $item->subCriteria->name ?? null }}</td>
                        <td class="text-center px-2.5 py-2 border-b">{{ ucfirst($item->type) ?? null }}</td>
                        <td class="px-2.5 py-2 border-b w-[40%]"><p class="whitespace-pre-wrap">{{ $item->main_question ?? null }}</p></td>
                        <td class="px-2.5 py-2 border-b">
                            <div class="flex flex-row gap-2">
                                <button onclick="openModal(this)"
                                    data-question="{{ $item->main_question }}"
                                    data-type="{{ $item->type }}"
                                    data-choices="{{ $item->choices }}"
                                    data-inputs="{{ $item->inputs }}"
                                    data-modal-target="question-modal" data-modal-toggle="question-modal"
                                    class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md border border-teal bg-teal text-white hover:bg-white hover:text-teal hover:border hover:border-teal">
                                    Detail
                                </button>
                                <a href="{{ route('questions.edit', $item->id) }}" class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md border border-amber bg-amber text-white hover:bg-white hover:text-amber hover:border hover:border-amber">
                                    Edit
                                </a>
                                <form action="{{ route('question.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
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

    {{--  Modal  --}}
    <div id="question-modal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 justify-center items-center bg-black bg-opacity-75">
        <div class="relative overflow-y-auto justify-center p-4 w-full max-w-2xl max-h-full bg-white rounded-lg shadow-lg">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Detail Pertanyaan</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" data-modal-hide="question-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4">
                <p id="modal-question" class="text-gray-700 mb-4 whitespace-pre-wrap"></p>
                <div id="modal-options-inputs" class="space-y-2"></div>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-end p-4 border-t">
                <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" data-modal-hide="question-modal">Close</button>
            </div>
            </div>
        </div>
    </div>

    @include('management-question.script')
@endsection
