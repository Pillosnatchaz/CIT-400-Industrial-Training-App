<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>

    <div x-data="projectModal()" @open-project-modal.window="openModal($event)">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>

        @include('projects.form_modal')
    </div>

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            function projectModal() {
                return {
                    showModal: false,
                    form: { name: '', client_name:'', start_range: '', end_range: '', location: '', description: '' },
                    formAction: '{{ route("project.store") }}',
                    formMethod: 'POST',
                    modalTitle: 'Add New Project',
                    submitLabel: 'Create',

                    

                    openModal(event) {
                        const { mode, data, action } = event.detail;

                        this.showModal = true;
                        this.formMethod = mode === 'edit' ? 'PUT' : 'POST';
                        this.modalTitle = mode === 'edit' ? 'Edit Project' : 'Add New Project';
                        this.submitLabel = mode === 'edit' ? 'Update' : 'Create';
                        this.form = {
                            name: data?.name || '',
                            client_name: data?.client_name || '',
                            // start_range: data?.start_range || '',
                            start_range: Array.isArray(data?.start_range) ? data.start_range.join(',') : data?.start_range || '',
                            end_range: data?.end_range || '',
                            location: data?.location || '',
                            description: data?.description || ''
                        };
                        this.formAction = action;

                        this.$nextTick(() => this.initFlatpickr());
                    },
                    
                    initFlatpickr() {
                        const input = this.$refs.startRangePicker;
                        if (input._flatpickr) {
                            input._flatpickr.destroy(); // destroy if re-initializing
                        }

                        flatpickr(input, {
                            mode: 'multiple',
                            dateFormat: 'Y-m-d',
                            defaultDate: this.form.start_range.split(','), // handles both init & edit
                            onChange: (selectedDates, dateStr) => {
                                this.form.start_range = dateStr;
                            }
                        });
                    }
                }
            }

            // jQuery handler to trigger Alpine modal
            $(document).on('click', '.open-create-modal', function () {
                window.dispatchEvent(new CustomEvent('open-project-modal', {
                    detail: {
                        mode: 'create',
                        data: null,
                        action: '{{ route("project.store") }}'
                    }
                }));
            });

            // Example for edit (adjust based on your setup)
            $(document).on('click', '#edit-selected-btn', function () {
                let table = $('#projects-table').DataTable();
                let selectedData = table.rows({ selected: true }).data();

                if (selectedData.length === 0) {
                    alert('Please select a project first.');
                    return;
                }

                let project = selectedData[0]; // Only handle the first selected row

                window.dispatchEvent(new CustomEvent('open-project-modal', {
                    detail: {
                        mode: 'edit',
                        data: project,
                        action: '{{ url("/project") }}/' + project.id
                    }
                }));

            
            });
            
            $(document).on('click', '#delete-selected-btn', function () {
                let table = $('#projects-table').DataTable();
                let selectedData = table.rows({ selected: true}).data();

                if (selectedData.length === 0) {
                    alert('Please select a project to delete.');
                    return;
                }

                if (!confirm('Are you sure you want to delete the selected project?')) {
                    return;
                }

                // Multiple delete
                let ids = [];
                for (let i = 0; i < selectedData.length; i++) {
                    ids.push(selectedData[i].id);
                }

                let deleteRequests = ids.map(id => {
                    return axios.delete(`/project/${id}`);
                });

                Promise.all(deleteRequests)
                    .then(() => {
                        alert('Selected project(s) deleted.');
                        table.ajax.reload(null, false); 
                    })
                    .catch(error => {
                        console.error('Delete failed:', error);
                        alert('Failed to delete one or more projects.');
                    });

                // 1 by 1 deletion
                // let project = selectedData[0];
                // console.log(selectedData[0]);
                // let id = project.id;

                // axios.delete(`/project/${id}`)
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
