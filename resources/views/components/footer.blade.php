<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex justify-between  p-4">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-semibold">KKU</div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Borrow</span>
                </div>
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                     borrow equipment
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 tracking-wider uppercase">Links</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Home</a></li>
                    <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 border-t border-gray-200 dark:border-gray-700 pt-4 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500 dark:text-gray-400">
            <p>© {{ date('Y') }} KKU Borrow. All rights reserved.</p>
            <div class="space-x-4 mt-2 md:mt-0">
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200">Terms</a>
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200">Privacy</a>
            </div>
        </div>
    </div>
</footer>


