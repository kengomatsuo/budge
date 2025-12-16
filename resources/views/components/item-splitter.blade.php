<div class="space-y-4" x-show="splitType === 'items'">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('messages.assign_items') }}</h3>

    <div class="grid grid-cols-1 gap-4">
        <template x-for="(item, itemIndex) in items" :key="'assign-'+itemIndex">
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md border dark:border-gray-700">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="item.name"></span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                            (<span x-text="item.quantity"></span> x <span x-text="formatMoney(item.unit_price)"></span>)
                        </span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-gray-100" x-text="formatMoney(item.total_price)"></span>
                </div>

                <div class="space-y-2">
                    <template x-for="user in selectedUsers" :key="user.id">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300" x-text="user.name"></span>
                            <div class="flex items-center space-x-2">
                                <input type="number"
                                    x-model="itemAssignments[itemIndex][user.id]"
                                    min="0"
                                    :max="item.quantity"
                                    step="0.1"
                                    class="w-20 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-right text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Qty">
                                <span class="text-xs text-gray-500 w-16 text-right"
                                      x-text="formatMoney((itemAssignments[itemIndex][user.id] || 0) * item.unit_price)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Validation/Status -->
                <div class="mt-2 text-xs text-right"
                     :class="calculateAssignedQty(itemIndex) > item.quantity ? 'text-red-600' : (calculateAssignedQty(itemIndex) < item.quantity ? 'text-yellow-600' : 'text-green-600')">
                    <span x-text="calculateAssignedQty(itemIndex)"></span> / <span x-text="item.quantity"></span> assigned
                </div>
            </div>
        </template>
    </div>

    <!-- Hidden inputs for assignments -->
    <template x-for="(item, itemIndex) in items" :key="'hidden-assign-'+itemIndex">
        <template x-for="user in selectedUsers" :key="'hidden-assign-user-'+user.id">
            <input type="hidden"
                   :name="'assignments['+itemIndex+']['+user.id+']'"
                   :value="itemAssignments[itemIndex][user.id] || 0">
        </template>
    </template>
</div>
