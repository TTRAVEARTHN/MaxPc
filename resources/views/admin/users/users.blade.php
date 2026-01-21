@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title mb-4">Users</h1>

        {{-- SEARCH --}}
        <form method="GET"
              class="flex flex-col sm:flex-row gap-3 mb-6">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search users..."
                   class="input w-full sm:w-64">

            <button class="gray-btn px-4 py-2 whitespace-nowrap">
                Search
            </button>
        </form>

        {{-- USERS TABLE --}}
        <div class="card-box admin-table-wrapper">

            <table class="table admin-table">
                <thead>
                <tr>
                    <th class="table-col-id">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th class="table-col-actions">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr>
                        {{-- ID --}}
                        <td>{{ $user->id }}</td>

                        {{-- NAME --}}
                        <td>{{ $user->name }}</td>

                        {{-- EMAIL --}}
                        <td>{{ $user->email }}</td>

                        {{-- ROLE --}}
                        <td>
                            <span class="text-blue-300">{{ $user->role }}</span>
                        </td>

                        {{-- DATE --}}
                        <td>
                            {{ $user->created_at->format('Y-m-d') }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="table-actions-cell">
                            <form method="POST"
                                  action="{{ route('admin.users.delete', $user->id) }}"
                                  onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')

                                <button class="link-danger">
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
