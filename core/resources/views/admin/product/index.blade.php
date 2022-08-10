@extends('admin.layouts.app')

@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Sl')</th>
                                <th scope="col">@lang('Image')</th>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Type')</th>
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $key => $product)
                            <tr>
                                <td data-label="@lang('Sl')">{{$key+1}}</td>
                                <td data-label="@lang('Image')">
                                    <img style="width: 300px" src="{{ getImage('assets/images/product/'. $product->image,  null, true)}}"  alt="Image {{$product->name}}" class="img-fluid">
                                    </td>
                                <td data-label="@lang('Name')">{{ __($product->name) }}</td>
                                <td data-label="@lang('Type')">{{ $product->weight }} Gram</td>
                                <td data-label="@lang('Price')">{{ getAmount($product->price) }} {{$general->cur_text}}
                                </td>
                                <td data-label="@lang('Status')">
                                    @if($product->status == 1)
                                    <span
                                        class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                    @else
                                    <span
                                        class="text--small badge font-weight-normal badge--danger">@lang('Inactive')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Action')">
                                    <button type="button" class="icon-btn edit" data-toggle="tooltip"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-status="{{ $product->status }}"
                                         data-weight="{{ $product->weight }}"
                                        data-image="{{ $product->image }}"
                                        data-price="{{ $product->price }}" data-original-title="Edit">
                                        <i class="la la-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            <div class="card-footer py-4">
                {{ $products->links('admin.partials.paginate') }}
            </div>
        </div>
    </div>
</div>


{{-- edit modal--}}
<div id="edit-product" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Product')</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form method="post" action="{{route('admin.products.update')}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <input class="form-control plan_id" type="hidden" name="id">
                    <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold"> @lang('Product Image') <small>(recommended image ratio 9:16)</small></label>
                            <input class="form-control form-control-lg image" type="file" accept="image/*"  onchange="loadFile(event)" name="images">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold"> @lang('Name')</label>
                            <input type="text" class="form-control name" name="name"  placeholder="Product ABC" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold"> @lang('Price') </label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">{{$general->cur_sym}}
                                    </span></div>
                                <input type="text" class="form-control price" placeholder="10000" name="price" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold">@lang('Type')</label>
                            <input type="number" class="form-control weight" name="weight" step="0.001" placeholder="0.001" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold">@lang('Status')</label>
                            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')"
                                name="status" checked>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-block btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="add-product" class="modal  fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Product')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{route('admin.products.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <input class="form-control plan_id" type="hidden" name="id">
                    <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold"> @lang('Product Image') <small>(recommended image ratio 9:16)</small></label>
                            <input class="form-control form-control-lg" type="file" accept="image/*"  onchange="loadFile(event)" name="images"  required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold"> @lang('Name')</label>
                            <input type="text" class="form-control" name="name"  placeholder="Product ABC" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold"> @lang('Price') </label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">{{$general->cur_sym}}
                                    </span></div>
                                <input type="text" class="form-control" placeholder="10000" name="price" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold">@lang('Type')</label>
                            <input type="number" class="form-control" name="weight" step="0.001" placeholder="0.001" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col">
                            <label class="font-weight-bold">@lang('Status')</label>
                            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')"
                                name="status" checked>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-block btn btn--primary">@lang('Submit')</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<a href="javascript:void(0)" class="btn btn-sm btn--success add-product"><i class="fa fa-fw fa-plus"></i>@lang('Add
    New')</a>
@endpush

@push('script')
<script>
    "use strict";
        (function ($) {
            $('.edit').on('click', function () {
                console.log($(this).data('image'));
                var modal = $('#edit-product');
                modal.find('.name').val($(this).data('name'));
                modal.find('.price').val($(this).data('price'));
                modal.find('.weight').val($(this).data('weight'));
                var input = modal.find('.image');
                // input.setAttribute("value", "http://localhost/microgold/assets/images/avatar.png");

                if($(this).data('status')){
                    modal.find('.toggle').removeClass('btn--danger off').addClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',true);

                }else{
                    modal.find('.toggle').addClass('btn--danger off').removeClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',false);
                }

                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.add-product').on('click', function () {
                var modal = $('#add-product');
                modal.modal('show');
            });
        })(jQuery);
</script>
<script>
    var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src)
            }
        };

</script>
@endpush