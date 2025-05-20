<!-- <div
    x-show="showCreateModal"
    x-transition
    @keydown.escape.window="showCreateModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    style="display: none;"
>
    <div @click.away="showCreateModal = false" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 w-1/2 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-lg font-semibold mb-4">Add New Warehouse</h2>

        <form action="{{ route('warehouse.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" id="name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-bold mb-2">Address</label>
                <input type="text" name="address" id="address"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button"
                        @click="showCreateModal = false"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create
                </button>
            </div>

        </form>
    </div>
</div> -->
