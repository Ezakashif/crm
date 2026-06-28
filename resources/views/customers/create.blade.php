<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">

        <h2 class="text-xl font-bold mb-6">Create Customer</h2>

        <form method="POST" action="{{ route('customers.store') }}"
              class="bg-white p-6 rounded-lg shadow space-y-4">
            @csrf

            <input name="name" placeholder="Customer Name"
                   class="w-full border p-2 rounded">

            <input name="email" placeholder="Email"
                   class="w-full border p-2 rounded">

            <input name="phone" placeholder="Phone"
                   class="w-full border p-2 rounded">

            <input name="company_name" placeholder="Company Name"
                   class="w-full border p-2 rounded">

            <textarea name="address" placeholder="Address"
                      class="w-full border p-2 rounded"></textarea>

            <textarea name="notes" placeholder="Notes"
                      class="w-full border p-2 rounded"></textarea>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded">
                Save Customer
            </button>
        </form>

    </div>
</x-app-layout>