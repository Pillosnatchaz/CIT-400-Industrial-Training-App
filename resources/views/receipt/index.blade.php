<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ucfirst($receiptType) }} Receipts
        </h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-slot>
    

    <div x-data="receiptModal({{ $currentUserId }})" @open-receipt-modal.window="openModal($event)">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    {{ $dataTable->table() }}
                </div>        
            </div>
        </div>
    

        @include('receipt.form_modal')
        @include('receipt.detail_modal')
    </div>


    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            function receiptModal(currentUserId) {
                return {
                    showModal: false,
                    form: {
                        borrower_user_id: '',
                        parent_checkout_receipt_id: '',
                        project_id: '',
                        type: '',
                        expected_return_date: '',
                        actual_return_date: '',
                        status: '',
                        created_by: currentUserId,
                    },
                    formAction: '{{ route("receipt.store") }}',
                    formMethod: 'POST',
                    modalTitle: 'Add New Receipt',
                    submitLabel: 'Create',
                    formSubmitted: false,
                    showItemSelection: false,
                    showStatus: false,
                    showActualReturnDate: false,
                    selectedItems: [], // Initialize selectedItems here, crucial for reactivity

                    showDetailModal: false,
                    detailReceipt: {},
                    modalTitle: 'Receipt Details',

                    init() {
                        window.addEventListener('open-detail-modal', (event) => {
                            this.detailReceipt = event.detail.data;
                            this.showDetailModal = true;
                            this.modalTitle = 'Receipt Details';

                            // console.log('Mode:', mode);
                            console.log('Data received:', detailReceipt);
                        });
                    },

                    openModal(event) {
                        const { mode, data, action } = event.detail;
                        this.showModal = true;
                        this.formMethod = mode === 'edit' ? 'PUT' : 'POST';
                        this.modalTitle = mode === 'edit' ? 'Edit Receipt' : 'Add New Receipt';
                        this.submitLabel = mode === 'edit' ? 'Update' : 'Create';
                        this.showStatus = mode === 'edit';
                        this.showActualReturnDate = mode === 'edit';
                        this.selectedItems = mode === 'create';

                        console.log('Mode:', mode);
                        console.log('Data received:', data); // [cite: 1, 2]

                        const formatDateTimeLocal = (dateString) => {
                            if (!dateString) return '';
                            try {
                                const date = new Date(dateString);
                                const year = date.getFullYear();
                                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                                const day = date.getDate().toString().padStart(2, '0');
                                const hours = date.getHours().toString().padStart(2, '0');
                                const minutes = date.getMinutes().toString().padStart(2, '0');
                                return `${year}-${month}-${day}T${hours}:${minutes}`;
                            } catch (e) {
                                console.error('Error parsing date:', dateString, e);
                                return '';
                            }
                        };

                        this.form = {
                            borrower_user_id: mode === 'create' ? currentUserId : data?.borrower_user_id || '',
                            parent_checkout_receipt_id: data?.parent_checkout_receipt_id || '',
                            project_id: data?.project_id || '',
                            type: data?.type || '',
                            expected_return_date: formatDateTimeLocal(data?.expected_return_date),
                            actual_return_date: formatDateTimeLocal(data?.actual_return_date),
                            status: data?.status_raw || data?.status || '',
                            notes: data?.notes || '',
                            created_by: data?.created_by || currentUserId,
                        };

                        this.selectedItems = []; // Clear previous items
                        console.log(this.selectedItems);

                        if (mode === 'edit' && data.receipt_items && Array.isArray(data.receipt_items)) {
                            const tempSelectedItems = {};
                            data.receipt_items.forEach(ri => {
                                if (ri.item_stock && ri.item_stock.item) {
                                    const itemId = ri.item_stock.item.id;
                                    if (!tempSelectedItems[itemId]) {
                                        tempSelectedItems[itemId] = {
                                            id: itemId,
                                            name: ri.item_stock.item.name,
                                            quantity: 0,
                                            available_stock: 999999999
                                        };
                                    }
                                    tempSelectedItems[itemId].quantity += 1;
                                }
                            });
                            this.selectedItems = Object.values(tempSelectedItems);
                            console.log('Selected Items:', this.selectedItems); // Debug!
                        } else if (mode === 'create') {
                            this.selectedItems = [];
                        }

                        this.formAction = action;
                        this.formSubmitted = false;
                    }
                }
            }

            function itemSelector() {
                return {
                    search: '',
                    selectedCategory: '',
                    searchResults: [],
                    selectedItems: [],
                    searchTimeout: null,
                    searchItems() {
                        clearTimeout(this.searchTimeout);
                        if (this.search.length < 2 && !this.selectedCategory) {
                            this.searchResults = [];
                            return;
                        }
                        this.searchTimeout = setTimeout(() => {
                            let url = `/api/items/search?q=${encodeURIComponent(this.search)}`;
                            if (this.selectedCategory) {
                                url += `&category=${encodeURIComponent(this.selectedCategory)}`;
                            }
                            fetch(url)
                                .then(res => res.json())
                                .then(data => {
                                    this.searchResults = data.filter(i => !this.selectedItems.some(s => s.id === i.id));
                                });
                        }, 300);
                    },
                    addItem(item) {
                        this.selectedItems.push({...item, quantity: 1});
                        this.search = '';
                        this.searchResults = [];
                    },
                    removeItem(idx) {
                        this.selectedItems.splice(idx, 1);
                    },
                    changeQuantity(idx, delta) {
                        let qty = this.selectedItems[idx].quantity + delta;
                        if (qty > 0 && qty <= this.selectedItems[idx].available_stock) {
                            this.selectedItems[idx].quantity = qty;
                        } else {
                            alert('Insufficient stock. Only ' + this.selectedItems[idx].available_stock + ' available.');
                        }
                    },
                    hasInsufficientStock() {
                        return this.selectedItems.some(item => item.quantity > item.available_stock);
                    }
                }
            }

            // jQuery handler to trigger Alpine modal
            $(document).on('click', '.open-create-modal', function () {
                window.dispatchEvent(new CustomEvent('open-receipt-modal', {
                    detail: {
                        mode: 'create',
                        data: null,
                        action: '{{ route("receipt.store") }}'
                    }
                }));
            });

            // Example for edit (adjust based on your setup)
            $(document).on('click', '#edit-selected-btn', function () {
                let table = $('#receipts-table').DataTable();
                let selectedData = table.rows({ selected: true }).data();

                if (selectedData.length === 0) {
                    alert('Please select a receipt first.');
                    return;
                }

                let receipt = selectedData[0]; // Only handle the first selected row

                $.get('/receipt/' + receipt.id, function(fullReceipt) {
                    window.dispatchEvent(new CustomEvent('open-receipt-modal', {
                        detail: {
                            mode: 'edit',
                            data: fullReceipt,
                            action: '/receipt/' + receipt.id
                        }
                    }));
                });
            });

            $(document).on('click', '#delete-selected-btn', function () {
                let table = $('#receipts-table').DataTable();
                let selectedData = table.rows({ selected: true}).data();

                if (selectedData.length === 0) {
                    alert('Please select a receipt to delete.');
                    return;
                }

                if (!confirm('Are you sure you want to delete the selected receipt?')) {
                    return;
                }

                // Multiple delete
                let ids = [];
                for (let i = 0; i < selectedData.length; i++) {
                    ids.push(selectedData[i].id);
                }

                let deleteRequests = ids.map(id => {
                    return axios.delete(`/receipt/${id}`);
                });

                Promise.all(deleteRequests)
                    .then(() => {
                        alert('Selected receipt(s) deleted.');
                        table.ajax.reload(null, false);
                    })
                    .catch(error => {
                        console.error('Delete failed:', error);
                        alert('Failed to delete one or more receipts.');
                    });

                // 1 by 1 deletion
                // let receipt = selectedData[0];
                // console.log(selectedData[0]);
                // let id = receipt.id;

                // axios.delete(`/receipt/${id}`)
                //     .then(response => {
                //         alert('Deleted!');
                //         table.ajax.reload();
                //     })
                //     .catch(error => {
                //         console.error(error);
                //         alert('Failed to delete');
                // });

            });

            $(document).on('click', '.open-detail-modal', function () {
                let id = $(this).data('id');
                $.get('/receipt/' + id, function(fullReceipt) {
                    window.dispatchEvent(new CustomEvent('open-detail-modal', {
                        detail: {
                            data: fullReceipt
                        }
                    }));
                });
            });

        </script>
    @endpush
</x-app-layout>