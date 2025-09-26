<div class="">
    <div id="filter"
         data-categories='@json(($categories ?? []))'
         data-brands='@json(($brands ?? []))'
         data-conditions='@json(($conditions ?? []))'
         data-available-specs='@json(($availableSpecs ?? []))'
         data-current-category='{{ request('category','') }}'
         data-current-brand='{{ request('brand','') }}'
         data-current-availability='{{ request('availability','') }}'
         data-current-conditions='@json(request('condition', []))'
         data-current-specs='@json(request('specs', []))'>
    </div>
</div>

