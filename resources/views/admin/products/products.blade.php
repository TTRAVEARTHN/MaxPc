@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title">Products</h1>

        {{-- ADD BUTTON --}}
        <a href="{{ route('admin.products.create') }}"
           class="blue-btn inline-block mb-6">
            + Add Product
        </a>

        {{-- TABLE --}}
        <div class="card-box">

            <table class="table">
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
                @foreach($products as $p)
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
                @endforeach
                </tbody>

            </table>

        </div>

    </div>

@endsection
