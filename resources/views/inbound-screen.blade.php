@include('layout.header', ['title' => 'Inbound Screen', 'inbound' => 'inbound'])

<div class="w-85 text-left">
    {{-- page title --}}
    <div class="text-center mt-5">
        <h3>Inbound Screem</h3>
    </div>

    {{-- form --}}
    <form id="submitForm" action="{{ route('inbound.store') }}" method="post" class="mt-5">
    @csrf
        <div class="mt-2">
            <label for="date" class="font-weight-bold">Date</label>
            <input type="date" id="date" class="ml-3" name="date" placeholder="select date" readonly>
    
            <label for="challan" class="font-weight-bold ml-5">Challan No</label>
            <input type="text" id="challan" class="ml-3" name="challan" placeholder="Enter challan no">
    
            <label for="sku" class="font-weight-bold ml-5">SKU</label>
            <input type="text" class="ml-3" id="sku" name="sku" placeholder="Enter SKU">
        </div>
    </form>
    
    {{-- Add row --}}
    <div class="mt-2 mb-4 text-left">
        <input type="button" id="addRow" class="btn btn-primary" value="Add Row">
    </div>

    {{-- sku row(s) --}}
    <table class="table table-borderless sku-table" style="display:none">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Suggested Space</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="newRow"></tbody>
    </table>
    
</div>


<script>
    $(document).ready(function(){

        //submit / update the sku value
        $('#sku').on('keyup', function(e){
            let keyCode = (e.keyCode ? e.keyCode : e.which);
            if(keyCode == 13){
                //validate the form
                let challan = $('#challan').val().trim();
                let sku = $('#sku').val().trim();

                let validation = true;

                //validate the value of challan no
                if(challan === ""){
                    toastr.error("Please enter challan no!");
                    validation = false;
                }

                //validate the value of sku
                if(sku === ""){
                    toastr.error("Please enter sku!");
                    validation = false;
                }

                if(validation){
                    // $('#submitForm').submit();
                    manageSKU({'challan':challan,'sku':sku});
                }
            }
        });

        //manage sku
        function manageSKU(data){

            if($('.sku-row').length > 0){
                
                //make sure the sku name exists in the row
                let noSkuMatch = true

                //scan each sku and add the quantity accordingly
                $(".sku-name").each(function(){
                    if(this.value === data.sku){
                        //sku matches
                        noSkuMatch = false;     
                        sendSKURequest($(this).closest('tr'),'manage');
                    } 
                });

                if(noSkuMatch){
                    toastr.error('Please enter a valid sku!');    
                }
            }else{
                toastr.error('Please click on Add Row button to add sku row!');
            }

        }

        // ajax call
        function sendSKURequest(row,type){

            //disable the input while ajax call is executing
            $('#sku').attr('disabled',true);
            
            let name = row.find('.sku-name').val();
            let token = $('input[name="_token"]').val();
            let challan = $('#challan').val();
            let srack = "";

            //if type is storage, disable the Done button
            if(type === 'store'){
                row.find('.sku-done').attr('disabled',true);
                srack = row.find('.sku-space').val()? row.find('.sku-space').val(): "";
            }


            let qty = row.find('.sku-quantity').val()? row.find('.sku-quantity').val(): 0;

            //incraement quantity if type is manage
            if(type === 'manage'){
                qty = Number(qty)+1;            
            }

            $.ajax({  
                type:"POST",  
                url:"{{ route('inbound.store') }}",  
                data:{
                    actionType: type,
                    name: name, 
                    qty: qty, 
                    challan: challan,
                    rack: srack,
                    _token: token
                },  
                success: function(data){  
                    if(type === 'store'){
                        //empty the input boxes
                        row.find('.sku-quantity').val(0);
                        row.find('.sku-space').val("");
                        row.find('.sku-done').hide();
                        row.find('.sku-done').attr('disabled',false);
                        $('#sku').attr('disabled',false);
                    }else{
                        //change string to obj
                        data = JSON.parse(data);
                        
                        if(Number(data.status) == 1){
                            if(data.ss){
                                row.find('.sku-space').val(data.ss);
                            }
                            row.find('.sku-name').attr('readonly',true);
                            row.find('.sku-done').show();
                        }else{
                            $('#sku').attr('disabled',false);
                        }
                        row.find('.sku-quantity').val(qty);
                    }
                },
                error: function (error) {
                    toastr.error("There was an error while executing the request!");
                    $('#sku').attr('disabled',false);
                    row.find('.sku-done').attr('disabled',false);
                    row.find('.sku-quantity').val(qty);
                }
            });  
        }

        //store skus in rack
        $(document).on('click', '.sku-done', function(){ 
            sendSKURequest($(this).closest('tr'),'store');
        });

        // add row
        $("#addRow").click(function () {
            let totalRows = $('.sku-row').length;
            if(totalRows < 5){
                let skuRow = '';
                skuRow += "<tr class='sku-row'>";
                    skuRow += "<td>";
                    skuRow += "<input type='text' class='sku-name' name='skuName[]' placeholder='SKU'>";
                    skuRow += "</td>";
                    skuRow += "<td>";
                    skuRow += "<input type='text' class='sku-quantity' name='skuQuantity[]' placeholder='Quantity' readonly>";
                    skuRow += "</td>";
                    skuRow += "<td>";
                    skuRow += "<input type='text' class='sku-space' name='skuSpace[]' placeholder='Suggested Space' readonly>";
                    skuRow += "</td>";
                    skuRow += "<td>";
                    skuRow += "<input type='button' class='sku-done' value='Done' style='display:none'>";
                    skuRow += "</td>";
                skuRow += "</tr>";
    
                $('#newRow').append(skuRow);
                $('.sku-table').show();
            }else{
                toastr.error("Can't create more than five rows!");
            }
        });

        

        // set date in the date box
        var date = new Date();

        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;

        var today = year + "-" + month + "-" + day;       
        $("#date").val(today);
    });   
</script>

@include('layout.footer')