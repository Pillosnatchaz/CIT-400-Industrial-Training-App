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
                <label for="name" class="block text-sm font-bold mb-2">Name<span class="text-red-500">*</span></label>
                <input type="text" name="name" x-model="form.name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-bold mb-2">Address<span class="text-red-500">*</span></label>
                <input type="text" name="address" x-model="form.address"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

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
