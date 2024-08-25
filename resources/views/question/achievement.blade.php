@extends('layout.dashboard')
@section('title', 'Manajemen Pengguna')
@section('content')
    <div class="flex flex-row justify-between pb-3">
        <div class="justify-start">
            <p class="text-xl font-bold text-jet whitespace-nowrap">Capaian</p>
        </div>
    </div>
    <form method="POST" action="{{ route('achievement.save') }}">
        @csrf
        <ul>
            <div class="flex justify-between px-6 py-3 m-2 my-4 bg-white border border-gray-200 rounded-lg shadow">
                <select id="navigateIndex" class="w-full border-0 focus:ring-0 font-bold">
                    @foreach($criteriaKeys as $index => $criteria)
                        <option value="{{ $index }}" {{ $currentIndex == $index ? 'selected' : '' }}>
                            {{ $criteria->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @foreach ($questions as $question)
                <div class="p-6 m-2 my-4 bg-white shadow rounded-lg">
                    <span class="bg-gray-100 text-caribbean text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded mb-4 border border-caribbean ">{{ $question->code }}</span>
                    <span class="bg-gray-100 text-teal text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded mb-4 border border-teal ">{{ $question->subCriteria->name }}</span>

                    @if ($question->type === 'input')
                        <div>
                            <p class="mb-2.5 whitespace-pre-wrap">{{ $question->main_question }}</p>
                            @foreach ($question->inputs as $input)
                                <div class="text-sm mb-2">
                                    @if (!str_contains($input->label, 'sub_main'))
                                        <div class="mb-4">
                                            <label for="{{ $input->label }}" class="text-sm mb-2">{{ $input->input_question }}</label>
                                            <input type="text" id="{{ $input->label }}" name="answers[{{ $question->id }}][{{ $input->label }}]"  value="{{ old('answers.' . $question->id . '.' . $input->label, $parsedAnswers[$question->id][$input->label] ?? '') }}" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-caribbean focus:border-caribbean">
                                        </div>
                                    @else
                                        <p>{{ $input->input_question }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div>
                            <li>
                                <p class="mb-6 leading-relaxed whitespace-pre-wrap">{{ $question->main_question }}</p>
                                <ul>
                                    @foreach ($question->choices as $choice)
                                        <li class="mb-2">
                                            <input type="radio" id="{{ $choice->id }}" name="answers[{{ $question->id }}]" value="{{ $choice->value }}" class="hidden peer" {{ old('answers.' . $question->id, $parsedAnswers[$question->id] ?? '') == $choice->value ? 'checked' : '' }} />

                                            <label for="{{ $choice->id}}"
                                                class="flex items-center gap-6 justify-between w-full p-2 text-sm text-gray-700 border border-gray-200 rounded-lg cursor-pointer peer-checked:border-white peer-checked:text-white peer-checked:bg-teal hover:text-gray-600 hover:bg-gray-100">
                                                <div class="block">
                                                    <div class="w-full whitespace-pre-wrap">{{ $choice->description }}</div>
                                                </div>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </div>
                    @endif
                </div>
            @endforeach
        </ul>
        <div class="m-2 my-4 flex justify-between">
            @if ($currentIndex > 0)
                <a href="{{ route('achievement.index', ['index' => $currentIndex - 1]) }}" class="px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-gray-500 rounded-lg hover:bg-gray-500">Sebelumnya</a>
            @endif
            @if ($currentIndex < count($criteriaKeys) - 1)
                <button type="submit" class="px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-teal rounded-lg hover:bg-teal">Selanjutnya</button>
            @endif
            @if ($currentIndex == count($criteriaKeys) - 1)
                <button type="submit" class="px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-teal rounded-lg hover:bg-teal">Simpan</button>
            @endif
        </div>
        <input type="hidden" name="currentIndex" value="{{ $currentIndex }}">
    </form>

    @include('question.question-script')
@endsection
