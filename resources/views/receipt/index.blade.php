<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receipts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex justify-between items-center mb-4">
                    
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <select class="border rounded px-2 py-1 mr-2">
                            <option>Filter</option>
                            <!-- Add filter options here -->
                        </select>
                        <input type="text" placeholder="Search" class="border rounded px-2 py-1">
                    </div>
                    <!-- <button class="text-gray-600">Reset</button> -->
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                       + Create New Checkout
                    </button>
                </div>

                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-2 py-3 border-b-2 border-r border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Checkout
                            </th>
                            <th class="px-2 py-3 border-b-2 border-r border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Relations
                            </th>
                            <th class="px-2 py-3 border-b-2 border-r border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Project
                            </th>
                            <th class="px-2 py-3 border-b-2 border-r border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    
                        <tbody>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
                                    <p class="text-gray-600 whitespace-no-wrap px-2">TCO</p>
                                    <p class="text-gray-600 whitespace-no-wrap px-2">May 19, 2025</p> 
                                    <p class="text-gray-600 whitespace-no-wrap px-2">Draft: <span class="text-red-500">X</span></p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-left">
                                    <p class="text-gray-600 whitespace-no-wrap px-2">Contact: Dr. Hal Klocko</p>
                                    <p class="text-gray-600 whitespace-no-wrap px-2">Warehouse: Warehouse 2</p>
                                    <p class="text-gray-600 whitespace-no-wrap px-2">User: Rosemary Schowalter</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-justify">
                                    <p class="text-gray-600 whitespace-no-wrap">Placeat fugit aut eum doloremque voluptatem a nihil vero.</p>
                                </td>
                                <td class="px-3 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    <div class="inline-flex space-x-1 text-center" >
                                        <button class="bg-blue-500 rounded-l">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                                            </svg>
                                        </button>
                                        <button class="bg-green-500 hover:bg-green-700 ">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button class="bg-red-500 hover:bg-red-700 rounded" >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- More rows here -->
                        </tbody>
                
                </table>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>


    @push('scripts')
        {{ $dataTable->scripts() }}
    @endpush
</x-app-layout>