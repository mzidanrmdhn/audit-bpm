<aside
    class="fixed overflow-y-auto left-0 top-0 w-64 z-40 h-screen md:static transition-transform -translate-x-full bg-caribbean border-r md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidenav" id="drawer-navigation">
    <div class="items-center justify-center px-6 py-5">
        <a href="" class="flex items-center justify-center">
            {{-- <img src="https://flowbite.s3.amazonaws.com/logo.svg" class="mr-3 h-8" alt="Flowbite Logo" /> --}}
            <span
                class="self-center text-2xl font-semibold whitespace-nowrap text-whiteSmoke dark:text-white">Audit</span>
        </a>
    </div>
    <div class="overflow-y-auto py-2 px-3 h-full bg-caribbean dark:bg-gray-800">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center p-2 text-sm font-medium text-white rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg aria-hidden="true"
                        class="w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-jet dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>
            <li>
                <button type="button"
                    class="flex items-center p-2 w-full text-sm font-medium text-white rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-pages" data-collapse-toggle="dropdown-pages">
                    <svg class="w-6 h-6 text-white group-hover:text-jet dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 4v10m0 0a2 2 0 1 0 0 4m0-4a2 2 0 1 1 0 4m0 0v2m6-16v2m0 0a2 2 0 1 0 0 4m0-4a2 2 0 1 1 0 4m0 0v10m6-16v10m0 0a2 2 0 1 0 0 4m0-4a2 2 0 1 1 0 4m0 0v2"/>
                    </svg>

                    <span class="flex-1 ml-3 text-left whitespace-nowrap">Pengukuran Mutu</span>
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <ul id="dropdown-pages" class="py-2 space-y-2">
                    <li>
                        <a href="{{ url('/target') }}"
                            class="{{ request()->is('target') ? 'bg-gray-100 text-jet' : 'text-white' }} flex items-center p-2 pl-11 w-full text-sm font-medium rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700">Form Target</a>
                    </li>
                    <li>
                        <a href="{{ url('/capaian') }}"
                            class="{{ request()->is('capaian') ? 'bg-gray-100 text-jet' : 'text-white' }} flex items-center p-2 pl-11 w-full text-sm font-medium rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700">Form Capaian</a>
                    </li>
                </ul>
            </li>
            <li>
                <button type="button"
                    class="flex items-center p-2 w-full text-sm font-medium text-white rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-sales" data-collapse-toggle="dropdown-sales">
                    <svg aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 text-white transition duration-75 group-hover:text-jet dark:text-gray-400 dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap">Laporan</span>
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <ul id="dropdown-sales" class="py-2 space-y-2">
                    <li>
                        <a href="{{ url('skor') }}"
                            class="{{ request()->is('skor') ? 'bg-gray-100 text-jet' : 'text-white' }} flex items-center p-2 pl-11 w-full text-sm font-medium rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700">Hasil Penilaian Mutu</a>
                    </li>
                    <li>
                        <a href="{{ url('/grafik') }}"
                            class="{{ request()->is('grafik') ? 'bg-gray-100 text-jet' : 'text-white' }} flex items-center p-2 pl-11 w-full text-sm font-medium rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700">Peta Capaian</a>
                    </li>
                </ul>
            </li>
            {{--  <li>
                <a href="#"
                    class="flex items-center p-2 text-sm font-medium text-white rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-jet dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z">
                        </path>
                        <path
                            d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z">
                        </path>
                    </svg>
                    <span class="flex-1 ml-3 whitespace-nowrap">Messages</span>
                    <span
                        class="inline-flex justify-center items-center w-5 h-5 text-xs font-semibold rounded-full text-caribbean bg-whiteSmoke dark:bg-primary-200 dark:text-primary-800">
                        4
                    </span>
                </a>
            </li>  --}}
            <li>
                <a href="{{ route('management-unit.index') }}"
                    class="{{ request()->is('manajemen-unit') ? 'bg-gray-100 text-jet' : 'text-white'}} flex items-center p-2 text-sm font-medium rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg aria-hidden="true"
                        class="w-5 h-5 transition duration-75 dark:text-gray-400 group-hover:text-jet dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                    </svg>
                    <span class="ml-3">Manajemen Unit</span>
                </a>
            </li>
            <li>
                <a href="{{ route('management-user.index') }}"
                    class="{{ request()->is('manajemen-pengguna') ? 'bg-gray-100 text-jet' : 'text-white'}} flex items-center p-2 text-sm font-medium rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg class="w-6 h-6 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-3">Manajemen Pengguna</span>
                </a>
            </li>
            <li>
                <button type="button"
                    class="flex items-center p-2 w-full text-sm font-medium text-white rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-authentication" data-collapse-toggle="dropdown-authentication">
                    <svg class="w-6 h-6 text-white group-hover:text-jet dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm4.996 2a1 1 0 0 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 8a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Zm-4.004 3a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 11a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Zm-4.004 3a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM11 14a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2h-6Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap">Master Data Kriteria</span>
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <ul id="dropdown-authentication" class="py-2 space-y-2">
                    <li>
                        <a href="{{route('criteria.index')}}"
                            class="{{ request()->is('criteria') ? 'bg-gray-100 text-jet' : 'text-white' }} flex items-center p-2 pl-11 w-full text-sm font-medium rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700">Kriteria</a>
                    </li>
                    <li>
                        <a href="{{route('sub-criteria.index')}}"
                            class="{{ request()->is('sub-kriteria') ? 'bg-gray-100 text-jet' : 'text-white' }} flex items-center p-2 pl-11 w-full text-sm font-medium rounded-lg transition duration-75 group hover:bg-gray-100 hover:text-jet dark:text-white dark:hover:bg-gray-700">Sub Kriteria</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('questions.index') }}"
                    class="{{ request()->is('data-pertanyaan') ? 'bg-gray-100 text-jet' : 'text-white'}} flex items-center p-2 text-sm font-medium rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>
                      </svg>

                    <span class="ml-3">Master Data Pertanyaan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('question-weight.index') }}"
                    class="{{ request()->is('bobot') ? 'bg-gray-100 text-jet' : 'text-white'}} flex items-center p-2 text-sm font-medium rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 4a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm-2.952.462c-.483.19-.868.432-1.19.71-.363.315-.638.677-.831.93l-.106.14c-.21.268-.36.418-.574.527C6.125 6.883 5.74 7 5 7a1 1 0 0 0 0 2c.364 0 .696-.022 1-.067v.41l-1.864 4.2a1.774 1.774 0 0 0 .821 2.255c.255.133.538.202.825.202h2.436a1.786 1.786 0 0 0 1.768-1.558 1.774 1.774 0 0 0-.122-.899L8 9.343V8.028c.2-.188.36-.38.495-.553.062-.079.118-.15.168-.217.185-.24.311-.406.503-.571a1.89 1.89 0 0 1 .24-.177A3.01 3.01 0 0 0 11 7.829V20H5.5a1 1 0 1 0 0 2h13a1 1 0 1 0 0-2H13V7.83a3.01 3.01 0 0 0 1.63-1.387c.206.091.373.19.514.29.31.219.532.465.811.78l.025.027.02.023v1.78l-1.864 4.2a1.774 1.774 0 0 0 .821 2.255c.255.133.538.202.825.202h2.436a1.785 1.785 0 0 0 1.768-1.558 1.773 1.773 0 0 0-.122-.899L18 9.343v-.452c.302.072.633.109 1 .109a1 1 0 1 0 0-2c-.48 0-.731-.098-.899-.2-.2-.12-.363-.293-.651-.617l-.024-.026c-.267-.3-.622-.7-1.127-1.057a5.152 5.152 0 0 0-1.355-.678 3.001 3.001 0 0 0-5.896.04Z" clip-rule="evenodd"/>
                      </svg>

                    <span class="ml-3">Master Data Bobot</span>
                </a>
            </li>
            <hr class="h-px my-10 bg-gray-200 border-0 dark:bg-gray-700">
            <li class="py-3">
                <a href="{{ route('performance-unit.index') }}"
                    class="{{ request()->is('performance-unit') ? 'bg-gray-100 text-jet' : 'text-white'}} flex items-center p-2 text-sm font-medium rounded-lg dark:text-white hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-width="2" d="M3 11h18m-9 0v8m-8 0h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                      </svg>

                    <span class="ml-3">Evaluasi Kinerja Unit</span>
                </a>
            </li>
        </ul>
        {{--  <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
            <li>
                <a href="#"
                    class="flex items-center p-2 text-sm font-medium text-white rounded-lg transition duration-75 hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 dark:text-white group">
                    <svg aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-jet dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">Docs</span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="flex items-center p-2 text-sm font-medium text-white rounded-lg transition duration-75 hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 dark:text-white group">
                    <svg aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-jet dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                        </path>
                    </svg>
                    <span class="ml-3">Components</span>
                </a>
            </li>
            <li>
                <a href="#"
                    class="flex items-center p-2 text-sm font-medium text-white rounded-lg transition duration-75 hover:bg-gray-100 hover:text-jet dark:hover:bg-gray-700 dark:text-white group">
                    <svg aria-hidden="true"
                        class="flex-shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 group-hover:text-jet dark:group-hover:text-jet"
                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-3">Help</span>
                </a>
            </li>
        </ul>  --}}
    </div>
</aside>
