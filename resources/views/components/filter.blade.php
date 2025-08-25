<div class="">
    <div id="filter"
         data-categories='@json(($categories ?? []))'
         data-current-category='{{ request('category','') }}'
         data-current-status='{{ request('status','') }}'>
    </div>
</div>

