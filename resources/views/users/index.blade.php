<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div x-data="userModal()" @open-user-modal.window="openModal($event)">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>

        @include('users.form_modal')
    </div>
    

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            function userModal() {
                return {
                    showModal: false,
                    form: { first_name: '', first_name: '', name: '', password: '', phone: '', role: '' },
                    formAction: '{{ route("user.store") }}',
                    formMethod: 'POST',
                    modalTitle: 'Add New User',
                    submitLabel: 'Create',
                    showPassword: false,

                    openModal(event) {
                        const { mode, data, action } = event.detail;

                        console.log('Mode:', mode); // Add this line
                        console.log('Data:', data);   // Add this line  

                        this.showModal = true;
                        this.formMethod = mode === 'edit' ? 'PUT' : 'POST';
                        this.modalTitle = mode === 'edit' ? 'Edit User' : 'Add New User';
                        this.submitLabel = mode === 'edit' ? 'Update' : 'Create';
                        this.showPassword = mode === 'create';
                        this.form.role = data?.status_role || data?.role || '';
                        this.form = {
                            first_name: data?.first_name || '',
                            last_name: data?.last_name || '',
                            email: data?.email || '',
                            password: data?.password || '',
                            phone: data?.phone || '',
                            // role: data?.role || ''
                            role: data?.role_raw || data?.role || '',
                        };
                        this.formAction = action;
                    }
                }
            }

            // jQuery handler to trigger Alpine modal
            $(document).on('click', '.open-create-modal', function () {
                window.dispatchEvent(new CustomEvent('open-user-modal', {
                    detail: {
                        mode: 'create',
                        data: null,
                        action: '{{ route("user.store") }}'
                    }
                }));
            });

            $(document).on('click', '#edit-selected-btn', function () {
                let table = $('#users-table').DataTable();
                let selectedData = table.rows({ selected: true }).data();

                if (selectedData.length === 0) {
                    alert('Please select a user first.');
                    return;
                }

                let user = selectedData[0]; // Only handle the first selected row

                window.dispatchEvent(new CustomEvent('open-user-modal', {
                    detail: {
                        mode: 'edit',
                        data: user,
                        action: '{{ url("/user") }}/' + user.id
                    }
                }));

            
            });
            
            $(document).on('click', '#delete-selected-btn', function () {
                let table = $('#users-table').DataTable();
                let selectedData = table.rows({ selected: true}).data();

                if (selectedData.length === 0) {
                    alert('Please select a user to delete.');
                    return;
                }

                if (!confirm('Are you sure you want to delete the selected user?')) {
                    return;
                }

                // Multiple delete
                let ids = [];
                for (let i = 0; i < selectedData.length; i++) {
                    ids.push(selectedData[i].id);
                }

                let deleteRequests = ids.map(id => {
                    return axios.delete(`/user/${id}`);
                });

                Promise.all(deleteRequests)
                    .then(() => {
                        alert('Selected user(s) deleted.');
                        table.ajax.reload(null, false); 
                    })
                    .catch(error => {
                        console.error('Delete failed:', error);
                        alert('Failed to delete one or more users.');
                    });

                // 1 by 1 deletion
                // let user = selectedData[0];
                // console.log(selectedData[0]);
                // let id = user.id;

                // axios.delete(`/user/${id}`)
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
