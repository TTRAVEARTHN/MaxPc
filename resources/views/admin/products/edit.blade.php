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
                <label class="form-label">Specifications (JSON)</label>
                <textarea name="specs"
                          rows="3"
                          class="input font-mono text-sm">{{ json_encode($product->specs, JSON_PRETTY_PRINT) }}</textarea>

                <p class="text-gray-400 text-sm mt-1">
                    Example: {"cpu": "Intel i9", "gpu": "RTX 4090"}
                </p>
            </div>

            {{-- IMAGE --}}
            <div>
                <label class="form-label">Current Image</label>

                @if($product->main_image)
                    <img src="{{ asset($product->main_image) }}"
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
