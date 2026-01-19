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
            <form method="POST" action="{{ route('compare.clear') }}" class="mb-4"
                data-compare-form="clear">
                @csrf
                @method('DELETE')
                <button class="gray-btn px-4 py-2">
                    Clear compare
                </button>
            </form>


            {{-- 🔹 контейнер с горизонтальным скроллом --}}
            <div class="card-box overflow-x-auto">

                @php
                    $allSpecs = [];
                    foreach ($products as $p) {
                        if (is_array($p->specs)) {
                            $allSpecs = array_unique(array_merge($allSpecs, array_keys($p->specs)));
                        }
                    }
                @endphp

                <table class="table min-w-[900px]">
                    <thead>
                    <tr>
                        <th class="min-w-[160px]">Feature</th>

                        @foreach($products as $p)
                            <th class="align-top min-w-[220px]">

                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-semibold">
                                            {{ $p->name }}
                                        </div>
                                        <div class="text-gray-400 text-sm">
                                            {{ $p->category->name ?? '—' }}
                                        </div>
                                        <div class="font-bold mt-1">
                                            ${{ number_format($p->price, 2) }}
                                        </div>
                                    </div>

                                    <form method="POST"
                                          action="{{ route('compare.remove', $p->id) }}"
                                          data-compare-form="remove">
                                        @csrf
                                        @method('DELETE')
                                        <button class="gray-btn px-3 py-1 text-sm">Remove</button>
                                    </form>

                                </div>

                            </th>
                        @endforeach
                    </tr>
                    </thead>

                    <tbody>

                    {{-- характеристики --}}
                    @foreach($allSpecs as $specKey)
                        <tr>
                            <td class="font-semibold uppercase text-gray-300">
                                {{ $specKey }}
                            </td>
                            @foreach($products as $p)
                                <td>
                                    {{ is_array($p->specs) ? ($p->specs[$specKey] ?? '—') : '—' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    {{-- описание --}}
                    <tr>
                        <td class="font-semibold text-gray-300">Description</td>
                        @foreach($products as $p)
                            <td class="text-gray-400">
                                {{ $p->description ?? '—' }}
                            </td>
                        @endforeach
                    </tr>

                    </tbody>
                </table>

            </div>

        @endif

    </div>

@endsection
