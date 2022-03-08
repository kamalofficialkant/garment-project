@include('layout.header', ['title' => 'Rack Order', 'inbound' => ''])

{{-- page title --}}
<div class="text-center mt-5">
    <h3>Rack Order</h3>
</div>

{{-- form --}}
<form action="{{ route('rack-order.store') }}" method="post" class="mt-5">
@csrf 
    <div class="row mt-2">
        <div class="offset-md-5">
            <label class="font-weight-bold">R1</label>
            <input type="text" class="ml-5" id="" name="rack1" placeholder="Enter rank" value="{{ $ranks['rack1'] }}" required>
        </div>
    </div>
    <div class="row mt-2">
        <div class="offset-md-5">
            <label class="font-weight-bold">R2</label>
            <input type="text" class="ml-5" id="" name="rack2" placeholder="Enter rank" value="{{ $ranks['rack2'] }}"  required>
        </div>
    </div>
    <div class="row mt-2">
        <div class="offset-md-5">
            <label class="font-weight-bold">R3</label>
            <input type="text" class="ml-5" id="" name="rack3" placeholder="Enter rank" value="{{ $ranks['rack3'] }}"  required>
        </div>
    </div>
    <div class="row mt-2">
        <div class="offset-md-5">
            <label class="font-weight-bold">R4</label>
            <input type="text" class="ml-5" id="" name="rack4" placeholder="Enter rank" value="{{ $ranks['rack4'] }}"  required>
        </div>
    </div>
    <div class="row mt-2">
        <div class="offset-md-5">
            <label class="font-weight-bold">R5</label>
            <input type="text" class="ml-5" id="" name="rack5" placeholder="Enter rank" value="{{ $ranks['rack5'] }}"  required>
        </div>
    </div>
    <div class="text-center mt-3">
        <input type="submit" class="btn btn-primary" value="Submit" name="store-rank">
    </div>
</form>

@if(isset($error))
<script>
    toastr.error("{{ $error }}");
</script>
@endif

@include('layout.footer')