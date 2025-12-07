@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title">Users</h1>

        {{-- SEARCH --}}
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search users..."
                   class="input w-64">

            <button class="blue-btn px-4 py-2">Search</button>
        </form>

        {{-- USERS TABLE --}}
        <div class="card-box overflow-x-auto">

            <table class="w-full">
                <thead class="border-b border-gray-700 text-gray-400">
                <tr>
                    <th class="table-th">ID</th>
                    <th class="table-th">Name</th>
                    <th class="table-th">Email</th>
                    <th class="table-th">Role</th>
                    <th class="table-th">Registered</th>
                    <th class="table-th">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr class="border-b border-gray-800">
                        {{-- ID --}}
                        <td class="table-td">{{ $user->id }}</td>

                        {{-- NAME --}}
                        <td class="table-td">{{ $user->name }}</td>

                        {{-- EMAIL --}}
                        <td class="table-td">{{ $user->email }}</td>

                        {{-- ROLE --}}
                        <td class="table-td">
                            <span class="text-blue-300">{{ $user->role }}</span>
                        </td>

                        {{-- DATE --}}
                        <td class="table-td">
                            {{ $user->created_at->format('Y-m-d') }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="table-td">

                            {{-- DELETE --}}
                            <form method="POST"
                                  action="{{ route('admin.users.delete', $user->id) }}"
                                  onsubmit="return confirm('Delete this user?');"
                                  class="inline-block">

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
