<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('messages.receipt_details') }}</h3>
        <x-secondary-button type="button" @click="addItem()">
            <x-heroicon-o-plus class="w-4 h-4 mr-2" />
            {{ __('messages.add_item') }}
        </x-secondary-button>
    </div>

    <div class="overflow-x-auto border rounded-md dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.item_name') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">{{ __('messages.quantity') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">{{ __('messages.price') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">{{ __('messages.total') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="(item, index) in items" :key="index">
                    <tr>
                        <td class="px-4 py-2">
                            <input type="text" x-model="item.name" class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm dark:text-gray-300" placeholder="Item name">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" x-model="item.quantity" @input="calculateItemTotal(index)" step="0.01" class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm text-right dark:text-gray-300">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" x-model="item.unit_price" @input="calculateItemTotal(index)" step="0.01" class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm text-right dark:text-gray-300">
                        </td>
                        <td class="px-4 py-2 text-right text-sm dark:text-gray-300">
                            <span x-text="formatMoney(item.total_price)"></span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                </template>
                <tr x-show="items.length === 0">
                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('messages.no_items') }}
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.subtotal') }}</td>
                    <td class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100" x-text="formatMoney(subtotal)"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.tax') }}</td>
                    <td class="px-4 py-2 text-right">
                        <input type="number" x-model="taxAmount" @input="calculateGrandTotal()" step="0.01" class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.service_charge') }}</td>
                    <td class="px-4 py-2 text-right">
                        <input type="number" x-model="serviceCharge" @input="calculateGrandTotal()" step="0.01" class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.discount') }}</td>
                    <td class="px-4 py-2 text-right">
                        <input type="number" x-model="discountAmount" @input="calculateGrandTotal()" step="0.01" class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm text-right font-medium text-red-600 dark:text-red-400">
                    </td>
                    <td></td>
                </tr>
                <tr class="border-t border-gray-200 dark:border-gray-700">
                    <td colspan="3" class="px-4 py-3 text-right text-base font-bold text-gray-900 dark:text-gray-100">{{ __('messages.grand_total') }}</td>
                    <td class="px-4 py-3 text-right text-base font-bold text-indigo-600 dark:text-indigo-400" x-text="formatMoney(grandTotal)"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Hidden inputs to submit item data -->
    <template x-for="(item, index) in items" :key="'input-'+index">
        <div>
            <input type="hidden" :name="'items['+index+'][name]'" :value="item.name">
            <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
            <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.unit_price">
            <input type="hidden" :name="'items['+index+'][total_price]'" :value="item.total_price">
        </div>
    </template>
    <input type="hidden" name="subtotal" :value="subtotal">
    <input type="hidden" name="tax_amount" :value="taxAmount">
    <input type="hidden" name="service_charge" :value="serviceCharge">
    <input type="hidden" name="discount_amount" :value="discountAmount">
</div>
