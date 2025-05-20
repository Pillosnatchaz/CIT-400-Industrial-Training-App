<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Warehouses') }}
        </h2>
    </x-slot>

    <!-- <div x-data="{ showCreateModal: false }" @open-create-modal.window="showCreateModal = true"> -->
    <div x-data="warehouseModal()" @open-warehouse-modal.window="openModal($event)">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>

        @include('warehouse.form_modal') <!-- Include modal here -->
    </div>

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            function warehouseModal() {
                return {
                    showModal: false,
                    form: { name: '', address: '' },
                    formAction: '{{ route("warehouse.store") }}',
                    formMethod: 'POST',
                    modalTitle: 'Add New Warehouse',
                    submitLabel: 'Create',

                    openModal(event) {
                        const { mode, data, action } = event.detail;

                        this.showModal = true;
                        this.formMethod = mode === 'edit' ? 'PUT' : 'POST';
                        this.modalTitle = mode === 'edit' ? 'Edit Warehouse' : 'Add New Warehouse';
                        this.submitLabel = mode === 'edit' ? 'Update' : 'Create';

                        this.form = {
                            name: data?.name || '',
                            address: data?.address || ''
                        };
                        this.formAction = action;
                    }
                }
            }

            // jQuery handler to trigger Alpine modal
            $(document).on('click', '.open-create-modal', function () {
                window.dispatchEvent(new CustomEvent('open-warehouse-modal', {
                    detail: {
                        mode: 'create',
                        data: null,
                        action: '{{ route("warehouse.store") }}'
                    }
                }));
            });

            // Example for edit (adjust based on your setup)
            $(document).on('click', '#edit-selected-btn', function () {
                let table = $('#warehouses-table').DataTable();
                let selectedData = table.rows({ selected: true }).data();

                if (selectedData.length === 0) {
                    alert('Please select a warehouse first.');
                    return;
                }

                let warehouse = selectedData[0]; // Only handle the first selected row

                window.dispatchEvent(new CustomEvent('open-warehouse-modal', {
                    detail: {
                        mode: 'edit',
                        data: warehouse,
                        action: '{{ url("/warehouse") }}/' + warehouse.id
                    }
                }));

            
            });
            
            $(document).on('click', '#delete-selected-btn', function () {
                let table = $('#warehouses-table').DataTable();
                let selectedData = table.rows({ selected: true}).data();

                if (selectedData.length === 0) {
                    alert('Please select a warehouse to delete.');
                    return;
                }

                if (!confirm('Are you sure you want to delete the selected warehouse?')) {
                    return;
                }

                // Multiple delete
                let ids = [];
                for (let i = 0; i < selectedData.length; i++) {
                    ids.push(selectedData[i].id);
                }

                let deleteRequests = ids.map(id => {
                    return axios.delete(`/warehouse/${id}`);
                });

                Promise.all(deleteRequests)
                    .then(() => {
                        alert('Selected warehouse(s) deleted.');
                        table.ajax.reload(null, false); 
                    })
                    .catch(error => {
                        console.error('Delete failed:', error);
                        alert('Failed to delete one or more warehouses.');
                    });

                // 1 by 1 deletion
                // let warehouse = selectedData[0];
                // console.log(selectedData[0]);
                // let id = warehouse.id;

                // axios.delete(`/warehouse/${id}`)
                //     .then(response => {
                //         alert('Deleted!');
                //         table.ajax.reload();
                //     })
                //     .catch(error => {
                //         console.error(error);
                //         alert('Failed to delete');
                // });

            });
        </script>
    @endpush
</x-app-layout>
