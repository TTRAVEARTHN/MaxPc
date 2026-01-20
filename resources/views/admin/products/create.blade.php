@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title">Add Product</h1>

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('admin.products.store') }}"
              enctype="multipart/form-data"
              class="card-box space-y-6">

            @csrf

            {{-- NAME --}}
            <div>
                <label class="form-label">Product Name</label>
                <input type="text" name="name" required
                       class="input"
                       placeholder="Ultimate Gaming Beast">
            </div>

            {{-- PRICE --}}
            <div>
                <label class="form-label">Price ($)</label>
                <input type="number" step="0.01" name="price" required
                       class="input" placeholder="3499">
            </div>

            {{-- CATEGORY --}}
            <div>
                <label class="form-label">Category</label>
                <select name="category_id" class="input bg-[#2b3142]">
                    <option value="">No Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- DESCRIPTION --}}
            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="4"
                          class="input"
                          placeholder="High-end gaming desktop with premium cooling..."></textarea>
            </div>

            {{-- SPECS --}}
            <div>
                <label class="form-label">Specifications</label>

                @php
                    // если была ошибка валидации – восстановим введённое
                    $oldSpecs = old('specs', [
                        ['key' => '', 'value' => ''],
                    ]);
                @endphp

                <div id="specs-wrapper" class="space-y-2">
                    @foreach($oldSpecs as $i => $spec)
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
                <label class="form-label">Main Image</label>
                <input type="file" name="main_image"
                       class="text-gray-300"
                       accept="image/*">
            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="blue-btn w-full py-3 rounded">
                Create Product
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
                        // хотя бы одна строка должна остаться
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
