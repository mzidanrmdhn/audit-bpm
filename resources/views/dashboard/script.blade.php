<script>
    $(document).ready(function() {
        $('#user-online').DataTable({
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

        $('#user-activity').DataTable({
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
        });
    });
</script>
