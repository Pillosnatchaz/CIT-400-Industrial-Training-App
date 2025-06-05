<div
    x-show="showDetailModal"
    x-transition
    @keydown.escape.window="showDetailModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    style="display: none;"
>
    <div @click.away="showDetailModal = false"
        class="bg-white overflow-y-auto shadow-xl sm:rounded-lg p-8 w-3/4 max-w-7xl mx-auto sm:px-6 lg:px-8" style='max-height: 90vh;'>

        <h2 class="text-lg font-semibold mb-4" x-text="modalTitle"></h2>

        <form :action="formAction" method="POST">
            <template x-if="formMethod === 'PUT'">
                <input type="hidden" name="_method" value="PUT">
            </template>
            @csrf

            <div class="max-w-7xl mx-auto bg-white shadow-md rounded-lg p-6 text-sm">
                <div class="flex items-start justify-between border-b pb-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 font-bold text-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v1H2V5zm0 3h16v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8z"/>
                            </svg>
                            <span x-text="detailReceipt.project ? detailReceipt.project.name : 'No Project'"></span>
                        </div>
                    </div>
                    <div class="text-right text-gray-700 text-sm">
                        <div><strong x-text="detailReceipt.warehouse ? detailReceipt.warehouse.name : ''"></strong></div>
                        <div x-text="detailReceipt.warehouse ? detailReceipt.warehouse.address : ''"></div>
                    </div>
                </div>

                <div class="text-center font-semibold text-xl mb-2" x-text="detailReceipt.type === 'checkout' ? 'CHECKOUT' : 'CHECK-IN'"></div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 border-b pb-4 mb-4">
                    <div>
                        <!-- <div><strong>Date: </strong> May 26, 2025</div> -->
                        <div><strong>Reference: </strong><span x-text="detailReceipt ? detailReceipt.receipt_number : ''"></span></div>
                        <div><strong>Date: </strong> <span x-text="detailReceipt.formatted_expected_return_date"></span></div>
                        <div><strong>Created at: </strong> <span x-text="detailReceipt.formatted_created_at"></span></div>
                    </div>
                    <div>
                        <div><strong>For:</strong></div>
                        <div><span x-text="detailReceipt.borrower ? detailReceipt.borrower.name : ''"></span></div>
                        <div><span x-text="detailReceipt.borrower ? detailReceipt.borrower.phone : ''"></span></div>
                        <div><span x-text="detailReceipt.borrower ? detailReceipt.borrower.email : ''"></span></div>
                    </div>
                </div>
                <!-- selected items -->
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 border-t border-b">
                        <tr>
                            <th class="px-2 py-2 font-medium text-gray-700">Item</th>
                            <th class="px-2 py-2 font-medium text-gray-700">Warehouse</th>
                            <th class="px-2 py-2 font-medium text-gray-700">Warehouse Address</th>
                            <th class="px-2 py-2 font-medium text-gray-700 text-center">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in detailReceipt.grouped_items" :key="item.item_name + item.warehouse_name">
                            <tr>
                                <td class="px-2 py-2" x-text="item.item_name"></td>
                                <td class="px-2 py-2" x-text="item.warehouse_name"></td>
                                <td class="px-2 py-2" x-text="item.warehouse_address"></td>
                                <td class="px-2 py-2 text-center" x-text="item.quantity"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <p class="mt-6 text-gray-600 text-xs">
                    Quis perferendis facere consequatur animi fuga. Aliquid et eaque vero aut eligendi. Dolorem aut et sequi sed.
                </p>
            </div>
        </form>
    </div>
</div>

