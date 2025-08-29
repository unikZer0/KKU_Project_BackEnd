<div x-show="open" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
             x-transition>
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
                <!-- Close button -->
                <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                    ✕
                </button>

                <!-- Modal content -->
                <h2 class="text-xl font-bold text-gray-800 mb-4">Request Details</h2>
                
                <template x-if="selectedReq">
                    <div>
                        <p><span class="font-semibold">Request ID:</span> <span x-text="selectedReq.req_id"></span></p>
                        <p><span class="font-semibold">Status:</span> <span x-text="selectedReq.status"></span></p>
                        <template x-if="selectedReq.status === 'rejected'">
                            <p class="text-red-600"><span class="font-semibold">Reason:</span> <span x-text="selectedReq.reject_reason"></span></p>
                        </template>

                        <div class="mt-3">
                            <h3 class="font-semibold text-gray-700">Equipment</h3>
                            <p x-text="selectedReq.equipment.name"></p>
                            <p x-text="'Code: ' + selectedReq.equipment.code"></p>
                            <p x-text="'Category: ' + selectedReq.equipment.category.name"></p>
                        </div>

                        <div class="mt-3">
                            <h3 class="font-semibold text-gray-700">Borrower</h3>
                            <p x-text="selectedReq.user.username"></p>
                            <p x-text="selectedReq.user.email"></p>
                            <p x-text="'Tel: ' + selectedReq.user.phonenumber"></p>
                        </div>

                        <div class="mt-3">
                            <h3 class="font-semibold text-gray-700">Period</h3>
                            <p x-text="'Start: ' + selectedReq.start_at"></p>
                            <p x-text="'End: ' + selectedReq.end_at"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>