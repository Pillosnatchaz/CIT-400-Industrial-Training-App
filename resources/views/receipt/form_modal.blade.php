<div
    x-show="showModal"
    x-transition
    @keydown.escape.window="showModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    style="display: none;"
>
    <div @click.away="showModal = false"
         class="bg-white overflow-y-auto shadow-xl sm:rounded-lg p-8 w-1/2 max-w-7xl mx-auto sm:px-6 lg:px-8" style="max-height: 90vh;">
        <h2 class="text-lg font-semibold mb-4" x-text="modalTitle"></h2>

        <form :action="formAction" method="POST">
            <template x-if="formMethod === 'PUT'">
                <input type="hidden" name="_method" value="PUT">
            </template>
            @csrf

            <div class="mb-4">
                <label for="project_id" class="block text-sm font-bold mb-2">
                    Project <span class="text-red-500">*</span>
                </label>
                <select name="project_id" x-model="form.project_id"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required> 
                    <option value="" disabled selected x-bind:hidden="form.project !== ''">-- Select Project--</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                <div x-show="form.project_id === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a warehouse.</div>
            </div>

            <div class="mb-4">
                <label for="borrower_user_id" class="block text-sm font-bold mb-2">
                    Created For <span class="text-red-500">*</span>
                </label>
                <select name="borrower_user_id" x-model="form.borrower_user_id"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required> 
                    <option value="" disabled selected x-bind:hidden="form.borrower_user_id !== ''"> </option>
                    @foreach($borrowerID as $borrowerID)
                        <option value="{{ $borrowerID->id }}">{{ $borrowerID->name }}</option>
                    @endforeach
                </select>
                <div x-show="form.borrower_user_id === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a user.</div>
            </div>

            <div class="mb-4">
                <label for="expected_return_date" class="block text-sm font-bold mb-2"> Expected Return Date <span class="text-red-500">*</span>
            </label>
                <input type="datetime-local" name="expected_return_date" x-model="form.expected_return_date"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required>
                <div x-show="form.expected_return_date === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a return date.</div>
            </div>
            <template x-if="showActualReturnDate">
                <div class="mb-4">
                    <label for="actual_return_date" class="block text-sm font-bold mb-2">Actual Return Date<span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="actual_return_date" x-model="form.actual_return_date"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
                    <div x-show="form.actual_return_date === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a return date.</div>
                </div>
            </template>

            <template x-if="showStatus">
                <div class="mb-4">
                    <label for="status" class="block text-sm font-bold mb-2">
                        Receipt Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" x-model="form.status"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring" required> 
                        <option value="" disabled selected x-bind:hidden="form.borrower_user_id !== ''">-- Select Status--</option>
                        <option value="checked_out"> checked out</option>
                        <option value="approved"> approved</option>
                        <option value="completed"> completed</option>
                        <option value="overdue"> overdue</option>
                        <option value="pending"> pending</option>
                        <option value="draft"> draft</option>
                    </select>
                    <div x-show="form.status === '' && formSubmitted" class="text-red-500 text-xs mt-1">Please select a status for the items.</div>
                </div>
            </template>

            <!-- Item Search and Selection -->
            <template x-if="showItemSelection">
                <div x-data="itemSelector()" class="mb-4">
                    <label class="block text-sm font-bold mb-2">Category</label>
                    <select x-model="selectedCategory" @change="searchItems"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>

                    <label class="block text-sm font-bold mb-2 mt-4">Add Items <span class="text-red-500">*</span></label>
                    <!-- <input 
                        type="text" 
                        x-model="search" 
                        @input="searchItems" 
                        placeholder="Scan qr code or search items"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                    > -->

                    <!-- Search Results Dropdown -->
                    <div class="relative">
                        <input 
                            type="text"
                            x-model="search"
                            @input="searchItems"
                            placeholder="Scan qr code or search items"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                        >
                        <div x-show="searchResults.length > 0"
                            class="absolute left-0 w-full bg-white border rounded shadow mt-1 max-h-48 overflow-y-auto z-10">
                            <template x-for="item in searchResults" :key="item.id">
                                <div 
                                    @click="addItem(item)" 
                                    class="px-4 py-2 hover:bg-blue-100 cursor-pointer"
                                    x-text="item.name + ' (Available: ' + item.available_stock + ')'"
                                ></div>
                            </template>
                        </div>
                    </div>

                    <!-- x-text="item.name + ' (' + item.category + ')'" -->

                    <!-- Selected Items Table -->
                    <table class="w-full text-sm border-collapse mt-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 text-left">Item</th>
                                <th class="p-2 text-left">Quantity</th>
                                <th class="p-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in selectedItems" :key="item.id">
                                <tr>
                                    <td class="p-2" x-text="item.name"></td>
                                    <td class="p-2 flex items-center">
                                        <button type="button" @click="changeQuantity(idx, -1)" class="px-2">-</button>
                                        <input type="number" min="1" x-model="item.quantity" class="w-16 border rounded px-1 py-0.5 text-center">
                                        <button type="button" @click="changeQuantity(idx, 1)" class="px-2">+</button>
                                    </td>
                                    <td class="p-2">
                                        <button type="button" @click="removeItem(idx)" class="text-red-500 hover:underline">Remove</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="selectedItems.length === 0">
                                <td colspan="3" class="p-2 text-gray-400 text-center">Add item to the list by search or scan QR code</td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="items" :value="JSON.stringify(selectedItems)">
                    <input type="hidden" name="type" value="checkout">
                </div>
            </template>

            <div class="mb-4">
                <label for="notes" class="block text-sm font-bold mb-2">Notes</label>
                <textarea name="notes" x-model="form.notes"
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
                <button type="submit" :disabled="selectedItems.some(item => item.quantity > item.available_stock)"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        x-text="submitLabel">
                    Save
                </button>
                <!-- <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        x-text="submitLabel">
                    Save
                </button> -->
            </div>
        </form>
    </div>
</div>
