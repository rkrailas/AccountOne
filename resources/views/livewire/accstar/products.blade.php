<div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Quantity</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderProducts as $index =>$orderProduct)
                    <tr>
                        <td>
                            <select name="orderProducts[{{ $index }}][product_id]" 
                                    wire:model="orderProducts.{{ $index }}.product_id" 
                                    class="form-control">
                                <option value="">-- choose product --</option>
                                @foreach ($allProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="orderProducts[{{ $index }}][quantity]" 
                                    class="form-control"
                                    wire:model="orderProducts.{{ $index }}.quantity" />

                        </td>
                        <td>
                            <a href="#" wire:click.prevent="removeProduct({{ $index }})">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-sm btn-secondary" 
                    wire:click.prevent="addProduct">+ Add Another Product</button>
        </div>
    </div>

</div>