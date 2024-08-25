@extends('layout.dashboard')
@section('title', 'Bobot Pertanyaan')
@section('content')
    <div class="flex flex-row justify-between items-center pb-3">
        <p class="text-xl font-bold text-gray-600 whitespace-nowrap">Daftar Bobot Pertanyaan</p>
        <button
            class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md bg-teal text-white hover:bg-white hover:text-teal hover:border hover:border-teal"
            data-modal-target="question-weight-modal" data-modal-toggle="question-weight-modal" onclick="openModal('add')">
            <p>Tambah Bobot Pertanyaan</p>
        </button>
    </div>

    <div class="bg-white p-4 mt-3 rounded-lg shadow overflow-x-auto">
        <table id="question-weight" class="display">
            <thead class="">
                <tr>
                    <th class="text-center px-6 py-2">No</th>
                    <th class="text-center px-6 py-2">Kode Pertanyaan</th>
                    <th class="text-center px-6 py-2">Bobot</th>
                    <th class="text-center px-6 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr class="hover:bg-gray-50 px-2.5">
                        <td class="text-center px-6 py-2 bg-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-2">{{ $item->question->code ?? null }}</td>
                        <td class="px-6 py-2 text-center">{{ number_format($item->weight ?? null, 2) }}</td>
                        <td>
                            <div class="flex flex-row gap-2 justify-center">
                                <button data-modal-target="question-weight-modal" data-modal-toggle="question-weight-modal"
                                    class="px-4 py-2 text-sm text-center font-medium inline-flex items-center rounded-md border border-amber bg-amber text-white hover:bg-white hover:text-amber hover:border hover:border-amber"
                                    onclick="openModal('edit', {{ $item->id }}, '{{ $item->weight }}', '{{ $item->question_id }}')">Edit</button>
                                <form action="{{ route('question-weight.destroy', $item->id) }}" method="POST"
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

    <div id="question-weight-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 id="modal-title" class="text-xl font-semibold text-gray-900 dark:text-white"></h3>
                    <button type="button"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="question-weight-modal">
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
                    <form id="question-weight-form" class="space-y-4" action="" method="POST">
                        @csrf
                        <div>
                            <label for="question_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Pertanyaan</label>
                            <select id="question_id" name="question_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach ($questions as $item)
                                    @if($item['weights'] === null)
                                        <option value="{{ $item['id'] }}">
                                            {{ $item['code'] }}
                                        </option>
                                    @else
                                        <option value="{{ $item['id'] }}" disabled>
                                            {{ $item['code'] }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <input type="hidden" id="hidden_question_id" name="question_id_hidden">
                        </div>
                        <div>
                            <label for="weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bobot</label>
                            <input type="text" name="weight" id="weight"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required />
                        </div>
                        <button type="submit"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('question-weight.script')
@endsection
