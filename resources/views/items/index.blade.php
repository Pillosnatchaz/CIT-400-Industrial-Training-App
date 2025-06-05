<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    <div x-data="ItemsModal()" @open-items-modal.window="openModal($event)">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
        
        @include('items.form_modal', ['categories' => $categories, 'warehouses' => $warehouses])
    </div>

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            function ItemsModal() {
                return {
                    showModal: false,
                    form: { id: null, name: '', category:'', warehouse_id: '', status: '', quantity: '', notes: '' },
                    formAction: '{{ route("items.store") }}',
                    formMethod: 'POST',
                    modalTitle: 'Add New Item',
                    submitLabel: 'Create',
                    showStatus: false,
                    showQuantity: false,
                    formSubmitted: false,

                    openModal(event) {
                        const { mode, data, action } = event.detail;
                        this.showModal = true;
                        this.formMethod = mode === 'edit' ? 'PUT' : 'POST';
                        this.modalTitle = mode === 'edit' ? 'Edit Item Stock' : 'Add New Item';
                        this.submitLabel = mode === 'edit' ? 'Update' : 'Create';
                        this.showStatus = mode === 'edit';
                        this.showQuantity = mode === 'create';
                        this.form.status = data?.status_raw || data?.status || '';
                        
                        if (mode === 'create') {
                            this.form = { id: null, name: '', category:'', warehouse_id: '', status: 'available', quantity: 1, notes: '' };
                        } else { 
                            this.form = {
                                id: data?.id || null,                           
                                name: data?.name || '',                         
                                category: data?.category || '',                 
                                warehouse_id: data?.actual_warehouse_id || '', 
                                // status: data?.status || '',      
                                status: data?.status_raw || data?.status || '',
                                quantity: '', 
                                notes: data?.notes || ''                    
                            };
                        }
                        this.formAction = action;
                        this.formSubmitted = false;
                    },
                }
            }

            $(document).on('click', '.open-create-modal', function () {
                window.dispatchEvent(new CustomEvent('open-items-modal', {
                    detail: {
                        mode: 'create',
                        data: null,
                        action: '{{ route("items.store") }}'
                    }
                }));
            });

            $(document).on('click', '#edit-selected-btn', function () {
                let table = $('#items-table').DataTable();
                let selectedData = table.rows({ selected: true }).data();

                if (selectedData.length === 0) {
                    alert('Please select an item stock record first.');
                    return;
                }
                if (selectedData.length > 1) {
                    alert('Please select only one item stock record to edit.');
                    return;
                }

                let itemStockRecord = selectedData[0]; 

                window.dispatchEvent(new CustomEvent('open-items-modal', {
                    detail: {
                        mode: 'edit',
                        data: itemStockRecord,
                        action: '{{ url("/items") }}/' + itemStockRecord.id
                    }
                }));
            });
            
            $(document).on('click', '#delete-selected-btn', function () {
                let table = $('#items-table').DataTable();
                let selectedData = table.rows({ selected: true}).data();

                if (selectedData.length === 0) {
                    alert('Please select item stock(s) to delete.');
                    return;
                }

                if (!confirm('Are you sure you want to delete the selected item stock(s)? This may also delete the parent item if it becomes orphaned.')) {
                    return;
                }

                let ids = selectedData.toArray().map(row => row.id); 

                let deleteRequests = ids.map(id => {
                    return axios.delete(`/items/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                });

                Promise.all(deleteRequests)
                    .then((responses) => {
                        alert('Selected item stock(s) deleted successfully.');
                        table.ajax.reload(null, false);
                    })
                    .catch(error => {
                        console.error('Delete failed:', error.response?.data?.message || error.message);
                        alert('Failed to delete one or more item stocks. Check console for details.');
                    });
            });
        </script>
    @endpush
</x-app-layout>
