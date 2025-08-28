<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
            @foreach ($reQuests as $req)
            <p>{{$req->req_id}}</p>
            <p>{{$req->start_at}}</p>
            <p>{{$req->end_at}}</p>
            <p>{{$req->status}}</p>
            <p>{{$req->req_id}}</p>
            <p>{{$req->user->username}}</p>
            <p>{{$req->user->age}}</p>
            <p>{{$req->user->email}}</p>
            
            <p>{{$req->user->phonenumber}}</p>
            
            <p>{{$req->equipment->code}}</p>
            <p>{{$req->equipment->name}}</p>
            <p>{{$req->equipment->category->name}}</p>
            <p>{{$req->equipment->description}}</p>
            
            @endforeach
    </div>
</x-app-layout>
