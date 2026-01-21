@extends('layouts.app')

@section('content')

    <div class="page-container">

        {{-- HEADER + search + add product --}}
        <div class="mb-6">
            <h1 class="page-title mb-4">Products</h1>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                {{-- SEARCH FORM --}}
                <form method="GET"
                      action="{{ route('admin.products') }}"
                      class="flex w-full sm:w-auto gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Search by name..."
                        class="input flex-1 min-w-0"
                    >
                    <button class="gray-btn px-4 py-2 whitespace-nowrap">
                        Search
                    </button>
                </form>

                {{-- ADD BUTTON --}}
                <a href="{{ route('admin.products.create') }}"
                   class="blue-btn px-4 py-2 inline-flex justify-center w-full sm:w-auto">
                    + Add Product
                </a>
            </div>
        </div>

        {{-- TABLE WRAPPER --}}
        <div class="card-box overflow-x-auto">

            <table class="table min-w-[700px]">
                <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th style="width: 130px;">Price</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($products as $p)
                    <tr>
                        {{-- ID --}}
                        <td>{{ $p->id }}</td>

                        {{-- NAME --}}
                        <td>
                            <strong>{{ $p->name }}</strong>
                        </td>

                        {{-- CATEGORY --}}
                        <td class="text-gray-400">
                            {{ $p->category->name ?? '—' }}
                        </td>

                        {{-- PRICE --}}
                        <td>
                            ${{ number_format($p->price, 2) }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="flex gap-3">
                            {{-- EDIT --}}
                            <a href="{{ route('admin.products.edit', $p->id) }}"
                               class="text-blue-400 hover:text-blue-500">
                                Edit
                            </a>

                            {{-- DELETE --}}
                            <form action="{{ route('admin.products.delete', $p->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')

                                <button class="text-red-400 hover:text-red-500">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-4">
                            No products found.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $products->links() }}
            </div>

        </div>

    </div>

@endsection
