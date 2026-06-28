<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">

        <h2 class="text-xl font-bold mb-6">Edit Customer</h2>

        <form method="POST" action="{{ route('customers.update', $customer) }}"
              class="bg-white p-6 rounded-lg shadow space-y-4">

            @csrf
            @method('PUT')

            <input name="name" value="{{ $customer->name }}"
                   class="w-full border p-2 rounded">

            <input name="email" value="{{ $customer->email }}"
                   class="w-full border p-2 rounded">

            <input name="phone" value="{{ $customer->phone }}"
                   class="w-full border p-2 rounded">

            <input name="company_name" value="{{ $customer->company_name }}"
                   class="w-full border p-2 rounded">

            <textarea name="address"
                      class="w-full border p-2 rounded">{{ $customer->address }}</textarea>

            <textarea name="notes"
                      class="w-full border p-2 rounded">{{ $customer->notes }}</textarea>

            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Update Customer
            </button>

        </form>

    </div>
</x-app-layout>