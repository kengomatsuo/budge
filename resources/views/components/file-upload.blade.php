<div x-data="{ fileName: '{{ $fileName ?? '' }}', imagePreview: null }">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label ?? __('messages.receipt_image') }}</label>
    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md"
         @drop.prevent="
            let file = $event.dataTransfer.files[0];
            fileName = file?.name;
            if (file && file.type.startsWith('image/')) {
                let reader = new FileReader();
                reader.onload = (e) => imagePreview = e.target.result;
                reader.readAsDataURL(file);
            }
         "
         @dragover.prevent>
        <div class="space-y-1 text-center" x-show="!imagePreview">
            <x-heroicon-o-photo class="mx-auto h-12 w-12 text-gray-400" />
            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                <label for="receipt" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none">
                    <span>{{ $uploadText ?? __('messages.upload_new_file') }}</span>
                    <input id="receipt" name="receipt" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf"
                        @change="
                            fileName = $event.target.files[0]?.name;
                            let file = $event.target.files[0];
                            if (file && file.type.startsWith('image/')) {
                                let reader = new FileReader();
                                reader.onload = (e) => imagePreview = e.target.result;
                                reader.readAsDataURL(file);
                            }
                        ">
                </label>
                <p class="pl-1">{{ $dragDropText ?? __('messages.drag_drop') }}</p>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $fileHint ?? __('messages.file_types') }}</p>
            <p x-show="fileName" x-text="fileName" class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-2"></p>
            @if(isset($existingFile) && $existingFile)
                @php
                    $extension = strtolower(pathinfo($existingFile->file_name, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                    <img src="{{ asset('storage/' . $existingFile->file_path) }}" alt="{{ $existingFile->original_filename ?? '' }}" class="mt-2 max-h-64 mx-auto rounded border border-gray-300 dark:border-gray-600">
                @endif
            @endif
        </div>
        <div x-show="imagePreview" class="relative w-full h-full">
            <img :src="imagePreview" class="max-h-64 mx-auto rounded" alt="Preview">

            <!-- Scan Button -->
            <div class="absolute bottom-2 left-0 right-0 flex justify-center" x-show="!$store.ocr.scanning">
                <button type="button"
                        @click="$dispatch('scan-receipt', { file: document.getElementById('receipt').files[0] })"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg">
                    <x-heroicon-o-sparkles class="w-6 h-6 mr-2" />
                    {{ __('messages.scan_receipt') }}
                </button>
            </div>

            <!-- Loading State -->
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded" x-show="$store.ocr.scanning" x-transition>
                <div class="text-white flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ __('messages.scanning') }}</span>
                </div>
            </div>

            <x-danger-button type="button" @click="imagePreview = null; fileName = '{{ $existingFile->original_filename ?? '' }}'; document.getElementById('receipt').value = ''" class="absolute top-2 right-2 p-2 rounded-full">
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </x-danger-button>
        </div>
    </div>
    <x-input-error class="mt-2" :messages="$errors->get('receipt')" />
</div>
