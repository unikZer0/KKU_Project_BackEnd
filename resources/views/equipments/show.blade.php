<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6 items-start">

            <div class="md:w-1/2">
                <img src="{{ $equipment->photo_path }}" 
                     alt="{{ $equipment->name }}" 
                     class="w-full h-auto object-cover rounded-lg shadow-lg">
            </div>

            <div class="md:w-1/2 flex flex-col justify-start gap-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $equipment->name }}</h1>
                <p class="text-gray-500 text-lg">{{ $equipment->category }}</p>
                <p class="text-gray-400">{{ $equipment->code }}</p>
                
                <p class="text-gray-700">Status: 
                    <span class="font-semibold {{ $equipment->status == 'available' ? 'text-green-600' : 'text-red-500' }}">
                        {{ ucfirst($equipment->status) }}
                    </span>
                </p>
                <p class="text-green-600 font-semibold text-lg">Free</p>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_at');
        const endDateInput = document.getElementById('end_at');
        const borrowButton = document.getElementById('borrowButton');
        const messageElement = document.getElementById('message');

        // Set min date for the start date to today
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);

        // Add input event listeners to check for valid date range
        startDateInput.addEventListener('input', validateDates);
        endDateInput.addEventListener('input', validateDates);

        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            messageElement.textContent = ''; // Clear previous messages

            if (startDateInput.value && endDateInput.value) {
                if (endDate <= startDate) {
                    messageElement.textContent = 'End date must be after the start date.';
                    messageElement.classList.remove('text-green-600');
                    messageElement.classList.add('text-red-500');
                    borrowButton.disabled = true;
                } else {
                    messageElement.textContent = '';
                    borrowButton.disabled = false;
                }
            } else {
                borrowButton.disabled = true;
            }
        }
    });
</script>
