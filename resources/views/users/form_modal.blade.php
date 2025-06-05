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
                <label for="first_name" class="block text-sm font-bold mb-2">First Name<span class="text-red-500">*</span></label>
                <input type="text" name="first_name" x-model="form.first_name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="last_name" class="block text-sm font-bold mb-2">Last Name<span class="text-red-500">*</span></label>
                <input type="text" name="last_name" x-model="form.last_name"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-bold mb-2">Email<span class="text-red-500">*</span></label>
                <input type="email" name="email" x-model="form.email"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <template x-if="showPassword">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-bold mb-2">Password<span class="text-red-500">*</span></label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
                </div>
            </template>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-bold mb-2">Phone Number<span class="text-red-500">*</span></label>
                <input type="number" name="phone" x-model="form.phone"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring">
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-bold mb-2">Role<span class="text-red-500">*</span></label>
                <select name="role" x-model="form.role"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"> 
                    <option value="member">Member</option>
                    <option value="admin">Admin</option>
                </select>
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
