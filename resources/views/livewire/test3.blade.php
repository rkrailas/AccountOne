<div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select myOption</label>
                        <x-select2 wire:model="myOption" placeholder="Select My Options" id="myOption">
                            <option value=" ">---โปรดเลือก---</option>
                            <option value="Option1">Option1</option>
                            <option value="Option2">Option2</option>
                            <option value="Option3">Option3</option>
                            <option value="Option4">Option4</option>
                            <option value=" Option5 ">Option5</option>
                        </x-select2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select Team Members2</label>
                    <x-select2 wire:model="myOption2" placeholder="Select My Options2" id="myOption2">
                        <option value=" ">---โปรดเลือก---</option>
                        <option>Option11</option>
                        <option>Option22</option>
                        <option>Option33</option>
                        <option>Option44</option>
                        <option>Option55</option>
                    </x-select2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary" wire:click="display">Display Value</button>
                <button class="btn btn-danger" wire:click="clearValue">Clear Value</button>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table>
                    <tr>
                      <th>Company</th>
                      <th>Contact</th>
                      <th>Country</th>
                    </tr>
                    <tr>
                      <td>Alfreds Futterkiste</td>
                      <td>Maria Anders</td>
                      <td class="csstextoverflow"> 
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid autem a amet tempore architecto nam qui, veritatis voluptatibus, reiciendis nesciunt fugiat recusandae incidunt facere odio! Quos illum iusto natus perferendis ipsa! Rem officia illo facilis totam quisquam voluptatibus ullam corporis a necessitatibus quas libero nostrum quod, repellat ex harum laborum molestiae sed hic porro quo magnam quia aliquam! Adipisci consequatur, voluptatem obcaecati at, quam nemo nesciunt totam impedit veniam cupiditate natus, explicabo vero voluptate. Ratione fugit officiis porro, ad ex labore corporis odio culpa error provident quod eum similique eveniet? Ipsam accusamus magnam odit dignissimos neque illum dicta, est, sapiente architecto ea numquam voluptas dolorem eum voluptate temporibus, assumenda magni pariatur alias et veritatis necessitatibus inventore! Cupiditate inventore necessitatibus voluptatem voluptas illo, quis sit sequi blanditiis deserunt magni numquam sed dicta optio! Quidem eveniet autem ea cupiditate voluptatibus! Culpa, incidunt aliquam quasi mollitia quis similique sint temporibus facilis eius nisi rem nemo nesciunt nobis ad perspiciatis, saepe quas harum natus vitae quos accusantium adipisci tempore ullam! Aliquid explicabo repudiandae, laborum cumque quos officiis, neque doloribus, possimus dolorem rem ratione nulla tempora. Magni in impedit dignissimos nisi sint unde ducimus delectus saepe dolorem fugit consectetur sed nemo, eligendi vel accusamus maiores.
                       </td>
                    </tr>
                    <tr>
                      <td>Centro comercial Moctezuma</td>
                      <td>Francisco Chang</td>
                      <td>Mexico</td>
                    </tr>
                  </table>
                <div style="width: 30%" class="csstextoverflow">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid autem a amet tempore architecto nam qui, veritatis voluptatibus, reiciendis nesciunt fugiat recusandae incidunt facere odio! Quos illum iusto natus perferendis ipsa! Rem officia illo facilis totam quisquam voluptatibus ullam corporis a necessitatibus quas libero nostrum quod, repellat ex harum laborum molestiae sed hic porro quo magnam quia aliquam! Adipisci consequatur, voluptatem obcaecati at, quam nemo nesciunt totam impedit veniam cupiditate natus, explicabo vero voluptate. Ratione fugit officiis porro, ad ex labore corporis odio culpa error provident quod eum similique eveniet? Ipsam accusamus magnam odit dignissimos neque illum dicta, est, sapiente architecto ea numquam voluptas dolorem eum voluptate temporibus, assumenda magni pariatur alias et veritatis necessitatibus inventore! Cupiditate inventore necessitatibus voluptatem voluptas illo, quis sit sequi blanditiis deserunt magni numquam sed dicta optio! Quidem eveniet autem ea cupiditate voluptatibus! Culpa, incidunt aliquam quasi mollitia quis similique sint temporibus facilis eius nisi rem nemo nesciunt nobis ad perspiciatis, saepe quas harum natus vitae quos accusantium adipisci tempore ullam! Aliquid explicabo repudiandae, laborum cumque quos officiis, neque doloribus, possimus dolorem rem ratione nulla tempora. Magni in impedit dignissimos nisi sint unde ducimus delectus saepe dolorem fugit consectetur sed nemo, eligendi vel accusamus maiores.
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>

    window.addEventListener('clear-select2', event => {
        clearSelect2('myOption');
    })
</script>
@endpush


@push('styles')
<style>
    .csstextoverflow {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush