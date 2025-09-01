<x-admin-layout>
    <div class="p-4 bg-white rounded-lg border">
        <h2 class="text-xl font-bold mb-4">User Report</h2>
<!-- Request Report Button -->
<div class="flex space-x-4 mb-4">    
    <a href="{{ route('admin.report.index') }}" 
       class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center justify-center"
       title="Request Report">
       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                 d="M9 12h6m-6 4h6M4 6h16v12H4V6z"/>
        </svg>
    </a>

<!-- Icon Navigation Buttons, simple layout -->    
    <a href="{{ route('admin.report.users') }}" 
       class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center justify-center"
       title="Users">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M5.121 17.804A8.966 8.966 0 0112 15c2.21 0 4.21.895 5.879 2.344M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </a>

    <a href="{{ route('admin.report.equipments') }}" 
       class="p-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center"
       title="Equipments">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M5 17h14M5 17a2 2 0 002 2h10a2 2 0 002-2M5 17V7a2 2 0 012-2h10a2 2 0 012 2v10" />
        </svg>
    </a>

    <a href="{{ route('admin.report.categories') }}" 
       class="p-2 bg-purple-500 text-white rounded hover:bg-purple-600 flex items-center justify-center"
       title="Categories">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M4 6h16M4 10h16M4 14h16M4 18h16" />
        </svg>
    </a>
</div>

        <table class="min-w-full border">
            <thead>
                <tr>
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">Username</th>
                    <th class="border px-2 py-1">Age</th>
                    <th class="border px-2 py-1">Phone</th>
                    <th class="border px-2 py-1">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="border px-2 py-1">{{ $user['id'] }}</td>
                        <td class="border px-2 py-1">{{ $user['username'] }}</td>
                        <td class="border px-2 py-1">{{ $user['age'] }}</td>
                        <td class="border px-2 py-1">{{ $user['phonenumber'] }}</td>
                        <td class="border px-2 py-1">{{ $user['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
