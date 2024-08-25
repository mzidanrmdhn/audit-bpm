<script>
    $(document).ready(function() {
        var table = $('#sub-criteria').DataTable({
            processing: false,
            serverSide: false,
            responsive: true,
            language: {
                search: 'Cari: ',
                lengthMenu: "_MENU_ entri per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(difilter dari total _MAX_ entri)",
            },
            dom: '<"flex justify-between items-center"<"flex items-center justify-start"l><"flex items-center justify-end"f>>' +
                '<"mt-4"t>' +
                '<"flex justify-between items-center"<"flex items-center justify-start mt-4"i><"flex items-center justify-end mt-4"p>>',
        })
    })

    function openModal(action, id = null, name = null, criteriaId = null) {
        const modalTitle = document.getElementById('modal-title')
        const subCriteriaForm = document.getElementById('sub-criteria-form')
        const nameInput = document.getElementById('name')
        const criteriaSelect = document.getElementById('criteria_id')

        if (action === 'add') {
            modalTitle.textContent = 'Tambah Data Sub Kriteria'
            subCriteriaForm.action = "{{ route('sub-criteria.store') }}"
            nameInput.value = ''
            criteriaSelect.value = ''
        } else if (action === 'edit') {
            modalTitle.textContent = 'Edit Data Sub Kriteria'
            subCriteriaForm.action = `{{ route('sub-criteria.store') }}/${id}`
            nameInput.value = name
            if (criteriaId !== null) {
                criteriaSelect.value = criteriaId
            }
        }

        document.querySelectorAll('[data-modal-hide="sub-criteria-modal"]').forEach(element => {
            element.addEventListener('click', () => {
                document.getElementById('sub-criteria-modal').classList.add('hidden')
            })
        })

        document.getElementById('sub-criteria-modal').classList.remove('hidden')
    }

</script>
