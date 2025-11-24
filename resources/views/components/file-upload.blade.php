<div x-data="{ fileName: '{{ $fileName ?? '' }}', imagePreview: null }">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label ?? __('messages.receipt_image') }}</label>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Accepted formats: JPG, PNG, PDF.</p>
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
        <div x-show="imagePreview" class="relative">
            <img :src="imagePreview" class="max-h-64 mx-auto rounded" alt="Preview">
            <x-danger-button type="button" @click="imagePreview = null; fileName = '{{ $existingFile->original_filename ?? '' }}'; document.getElementById('receipt').value = ''" class="absolute top-2 right-2 p-2 rounded-full">
                <x-heroicon-o-x-mark class="w-4 h-4" />
            </x-danger-button>
        </div>
    </div>
    <x-input-error class="mt-2" :messages="$errors->get('receipt')" />
</div>
