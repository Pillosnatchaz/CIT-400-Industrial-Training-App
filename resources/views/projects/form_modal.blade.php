<div
    x-show="showModal"
    x-transition
    @keydown.escape.window="showModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    style="display: none;"
>
    <div @click.away="showModal = false"
         class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 w-1/2 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <h2 class="text-lg font-semibold mb-4" x-text="modalTitle"></h2>

        <form :action="formAction" method="POST">
            <template x-if="formMethod === 'PUT'">
                <input type="hidden" name="_method" value="PUT">
            </template>
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" x-model="form.name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="client_name" class="block text-sm font-bold mb-2">Client Name</label>
                <input type="text" name="client_name" x-model="form.client_name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <!-- <div class="mb-4">
                <label for="start_range" class="block text-sm font-bold mb-2">Start Range</label>
                <input type="text" name="start_range" id="start_range_picker" x-model="form.start_range"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div> -->
            <div class="mb-4">
                <label for="start_range" class="block text-sm font-bold mb-2">Start Range</label>
                <input type="text" name="start_range" x-model="form.start_range" x-ref="startRangePicker"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="end_range" class="block text-sm font-bold mb-2">End Range</label>
                <input type="datetime-local" name="end_range" x-model="form.end_range"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-bold mb-2">Location</label>
                <input type="text" name="location" x-model="form.location"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-bold mb-2">Description</label>
                <textarea name="description" x-model="form.description"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"></textarea>
            </div>

            <!-- <div class="mb-4">
                <label for="created_by" class="block text-sm font-bold mb-2">Created By</label>
                <textarea name="created_by" x-model="form.created_by"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div> -->

            <div class="flex justify-end space-x-2">
                <button type="button"
                        @click="showModal = false"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        x-text="submitLabel">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
