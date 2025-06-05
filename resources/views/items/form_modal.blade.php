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
                <label for="name" class="block text-sm font-bold mb-2">Item Name<span class="text-red-500">*</span></label>
                <input type="text" name="name" x-model="form.name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
                <datalist id="itemNames">
                    @foreach($itemNames as $itemName)
                        <option value="{{ $itemName }}">
                    @endforeach
                </datalist>
            </div>
            <!-- <div class="mb-4">
                <label for="category" class="block text-sm font-bold mb-2">Category</label>
                <select name="category" x-model="form.category"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required> 
                    <option value=""> --Select Category--</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
                <div x-show="form.category === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a category.</div>
            </div> -->
            <div class="mb-4">
                <label for="category" class="block text-sm font-bold mb-2">Category<span class="text-red-500">*</span></label>
                <input list="categories" name="category" x-model="form.category"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
                <datalist id="categories">
                    @foreach($categories as $category)
                        <option value="{{ $category }}">
                    @endforeach
                </datalist>
                <div x-show="form.category === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a category.</div>
            </div>

            <template x-if="showStatus">
                <div class="mb-4">
                    <label for="status" class="block text-sm font-bold mb-2">Status<span class="text-red-500">*</span></label>
                    <select name="status" x-model="form.status"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required> 
                        <option value=""> --Select Status--</option>
                        <option value="available"> available</option>
                        <option value="in use"> in use</option>
                        <option value="maintenance"> maintenance</option>
                        <option value="damaged"> damaged</option>
                        <option value="unavailable"> unavailable</option>
                    </select>
                    <div x-show="form.status === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a status for the items.</div>
                </div>
            </template>
            <div class="mb-4">
                <label for="warehouse_id" class="block text-sm font-bold mb-2">Warehouse<span class="text-red-500">*</span></label>
                <select name="warehouse_id" x-model="form.warehouse_id"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required> 
                    <option value="" disabled selected x-bind:hidden ="form.items !== ''"> --Select Warehouse--</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                <div x-show="form.warehouse_id === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a warehouse.</div>
            </div>
            <template x-if="showQuantity">
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-bold mb-2">Quantity<span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" x-model="form.quantity" min="1" max="100"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
                </div>
            </template>
            <div class="mb-4">
                <label for="notes" class="block text-sm font-bold mb-2">Notes</label>
                <textarea name="notes" x-model="form.notes"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"></textarea>
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
