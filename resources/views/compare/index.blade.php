@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title mb-4">Compare Products</h1>

        @if(session('success'))
            <div class="alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error mb-4">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert-success mb-4">{{ session('info') }}</div>
        @endif

        @if($products->isEmpty())
            <p class="text-gray-400">
                You have no products in compare list.
            </p>
        @else

            {{-- очистить весь список --}}
            <form method="POST"
                  action="{{ route('compare.clear') }}"
                  class="mb-4"
                  data-compare-form="clear">
                @csrf
                @method('DELETE')
                <button class="gray-btn px-4 py-2">
                    Clear compare
                </button>
            </form>

            @php
                $allSpecs = [];
                foreach ($products as $p) {
                    if (is_array($p->specs)) {
                        $allSpecs = array_unique(array_merge($allSpecs, array_keys($p->specs)));
                    }
                }
            @endphp

            {{-- контейнер с горизонтальным скроллом --}}
            <div class="card-box overflow-x-auto">

                <div id="compareWrapper" class="card-box overflow-x-auto">

                    <table class="table min-w-[900px]">
                        <thead>
                        <tr>
                            {{-- левая колонка с названиями характеристик --}}
                            <th class="min-w-[160px]">Feature</th>

                            {{-- по одному столбцу на каждый товар --}}
                            @foreach($products as $p)
                                <th class="min-w-[220px] align-top"
                                    data-compare-product-id="{{ $p->id }}">
                                    <div class="flex items-start justify-between gap-2">

                                        {{-- ссылка на товар --}}
                                        <a href="{{ route('product.show', $p) }}"
                                           class="font-semibold hover:text-blue-400">
                                            {{ $p->name }}
                                        </a>

                                        {{-- кнопка удаления ТОЛЬКО этого товара --}}
                                        <form method="POST"
                                              action="{{ route('compare.remove', $p->id) }}"
                                              data-compare-form="remove"
                                              data-product-id="{{ $p->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="gray-btn px-2 py-1 text-xs">
                                                Remove
                                            </button>
                                        </form>
                                    </div>

                                    <p class="text-gray-400 text-sm mt-1">
                                        ${{ number_format($p->price, 2) }}
                                    </p>
                                </th>
                            @endforeach
                        </tr>
                        </thead>

                        <tbody>
                        {{-- строки с характеристиками --}}
                        @foreach($allSpecs as $specKey)
                            <tr>
                                <td class="font-semibold uppercase text-gray-300">
                                    {{ $specKey }}
                                </td>

                                @foreach($products as $p)
                                    <td data-compare-product-id="{{ $p->id }}">
                                        {{ is_array($p->specs) ? ($p->specs[$specKey] ?? '—') : '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- описание --}}
                        <tr>
                            <td class="font-semibold text-gray-300">Description</td>
                            @foreach($products as $p)
                                <td class="text-gray-400"
                                    data-compare-product-id="{{ $p->id }}">
                                    {{ $p->description ?? '—' }}
                                </td>
                            @endforeach
                        </tr>

                        </tbody>
                    </table>

                </div>

            </div>
        @endif

    </div>

@endsection
