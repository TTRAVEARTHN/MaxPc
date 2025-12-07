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
                <label class="form-label">Specifications (JSON)</label>

                <textarea name="specs" rows="3"
                          class="input font-mono text-sm"
                          placeholder='{"cpu": "Intel i9", "gpu": "RTX 4090", "ram": "32GB"}'></textarea>

                <p class="text-gray-400 text-sm mt-1">
                    Example: {"cpu": "Intel i9", "gpu": "RTX 4090"}
                </p>
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
