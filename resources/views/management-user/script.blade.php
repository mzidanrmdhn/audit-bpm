<script>
    $(document).ready(function() {
        var table = $('#management-unit').DataTable({
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

    function openModal(action, id = null, name = null, email = null, unitId = null, roleId = null) {

        const modalTitle = document.getElementById('modal-title');
        const userForm = document.getElementById('user-form');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const unitInput = document.getElementById('unit_id');
        const roleInput = document.getElementById('role_id');
        const methodInput = document.querySelector('input[name="_method"]');

        if (action === 'add') {
            modalTitle.textContent = 'Tambah Data User';
            userForm.action = "{{ route('management-user.create') }}";  // Gunakan rute store
            if (methodInput) methodInput.remove();
            nameInput.value = '';
            emailInput.value = '';
            unitInput.value = '';
            roleInput.value = '';
        } else if (action === 'edit') {
            modalTitle.textContent = 'Edit Data User';
            userForm.method = "PUT"
            userForm.action = `{{ url('manajemen-pengguna/') }}/${id}`;
            nameInput.value = name;
            emailInput.value = email;
            if (unitId !== null || roleId !== null) {
                unitInput.value = unitId;
                roleInput.value = roleId;
            }
        }

        document.querySelectorAll('[data-modal-hide="user-modal"]').forEach(element => {
            element.addEventListener('click', () => {
                document.getElementById('user-modal').classList.add('hidden');
            });
        });

        document.getElementById('user-modal').classList.remove('hidden');
    }


</script>
