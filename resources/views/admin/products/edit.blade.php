@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title">Edit Product</h1>

        {{-- EDIT FORM --}}
        <form action="{{ route('admin.products.update', $product->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="card-box space-y-6">

            @csrf
            @method('PATCH')

            {{-- NAME --}}
            <div>
                <label class="form-label">Product Name</label>
                <input type="text"
                       name="name"
                       class="input"
                       value="{{ $product->name }}"
                       required>
            </div>

            {{-- PRICE --}}
            <div>
                <label class="form-label">Price ($)</label>
                <input type="number"
                       name="price"
                       step="0.01"
                       class="input"
                       value="{{ $product->price }}"
                       required>
            </div>

            {{-- CATEGORY --}}
            <div>
                <label class="form-label">Category</label>
                <select name="category_id" class="input bg-[#2b3142]">
                    <option value="">No Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($product->category_id == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DESCRIPTION --}}
            <div>
                <label class="form-label">Description</label>
                <textarea name="description"
                          rows="4"
                          class="input">{{ $product->description }}</textarea>
            </div>

            {{-- SPECS --}}
            <div>
                <label class="form-label">Specifications</label>

                @php
                    // берём либо old('specs'), либо текущий specs из модели
                    $rawSpecs = old('specs', $product->specs ?? []);

                    // превращаем в массив вида [ ['key'=>..., 'value'=>...], ...]
                    if (!empty($rawSpecs) && is_array($rawSpecs)) {
                        // вариант: пришло с формы как [ ['key'=>..,'value'=>..], ...]
                        if (isset($rawSpecs[0]) && is_array($rawSpecs[0]) && array_key_exists('key', $rawSpecs[0])) {
                            $existingSpecs = $rawSpecs;
                        } else {
                            // вариант: ассоциативный массив ['cpu' => 'Intel', ...]
                            $existingSpecs = [];
                            foreach ($rawSpecs as $k => $v) {
                                $existingSpecs[] = ['key' => $k, 'value' => $v];
                            }
                        }
                    } else {
                        $existingSpecs = [['key' => '', 'value' => '']];
                    }
                @endphp

                <div id="specs-wrapper" class="space-y-2">
                    @foreach($existingSpecs as $i => $spec)
                        <div class="flex gap-2 spec-row">
                            <input type="text"
                                   name="specs[{{ $i }}][key]"
                                   class="input"
                                   placeholder="e.g. CPU"
                                   value="{{ $spec['key'] ?? '' }}">

                            <input type="text"
                                   name="specs[{{ $i }}][value]"
                                   class="input"
                                   placeholder="e.g. Intel i9"
                                   value="{{ $spec['value'] ?? '' }}">

                            <button type="button"
                                    class="gray-btn px-3 py-2 rounded text-sm remove-spec-row">
                                ✕
                            </button>
                        </div>
                    @endforeach
                </div>

                <button type="button"
                        id="add-spec-row"
                        class="gray-btn mt-2 px-4 py-2 rounded text-sm">
                    + Add specification
                </button>

                @error('specs.*.key')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('specs.*.value')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- IMAGE --}}
            <div>
                <label class="form-label">Current Image</label>

                @if($product->main_image)
                    <img src="{{ asset('storage/' . $product->main_image) }}"
                         class="w-32 h-32 object-cover rounded border border-gray-700 mb-3">
                @else
                    <p class="text-gray-500 mb-3">No image uploaded</p>
                @endif

                <label class="form-label">Upload New Image</label>
                <input type="file"
                       name="main_image"
                       accept="image/*"
                       class="text-gray-300">
            </div>

            {{-- SAVE BUTTON --}}
            <button type="submit" class="blue-btn w-full py-3 rounded">
                Save Changes
            </button>

        </form>

        {{-- DELETE PRODUCT --}}
        <form method="POST"
              action="{{ route('admin.products.delete', $product->id) }}"
              class="mt-6 card-box">

            @csrf
            @method('DELETE')

            <button class="w-full bg-red-600 hover:bg-red-700 py-3 rounded font-semibold">
                Delete Product
            </button>
        </form>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('specs-wrapper');
            const addBtn  = document.getElementById('add-spec-row');

            if (!wrapper || !addBtn) return;

            let index = wrapper.querySelectorAll('.spec-row').length;

            function attachRemoveHandlers() {
                wrapper.querySelectorAll('.remove-spec-row').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const row = btn.closest('.spec-row');
                        const rowsCount = wrapper.querySelectorAll('.spec-row').length;
                        if (row && rowsCount > 1) {
                            row.remove();
                        }
                    });
                });
            }

            attachRemoveHandlers();

            addBtn.addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = 'flex gap-2 spec-row';
                row.innerHTML = `
                <input type="text"
                       name="specs[${index}][key]"
                       class="input"
                       placeholder="e.g. CPU">

                <input type="text"
                       name="specs[${index}][value]"
                       class="input"
                       placeholder="e.g. Intel i9">

                <button type="button"
                        class="gray-btn px-3 py-2 rounded text-sm remove-spec-row">
                    ✕
                </button>
            `;
                index++;
                wrapper.appendChild(row);
                attachRemoveHandlers();
            });
        });
    </script>
@endpush
