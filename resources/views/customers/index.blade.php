<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Customers</h2>

            <a href="{{ route('customers.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                + Add Customer
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Phone</th>
                        <th class="p-3">Company</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $customer)
                        <tr class="border-b">
                            <td class="p-3 font-medium">{{ $customer->name }}</td>
                            <td class="p-3">{{ $customer->email }}</td>
                            <td class="p-3">{{ $customer->phone }}</td>
                            <td class="p-3">{{ $customer->company_name }}</td>

                            <td class="p-3 flex gap-2">
                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="text-blue-600 hover:underline">Edit</a>

                                <form method="POST" action="{{ route('customers.destroy', $customer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>

    </div>
</x-app-layout>