@extends('layouts.app')

@section('title', 'Edit Product')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <form id="product-form" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name">Tên sản phẩm<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="product_name" required value="{{ $product->product_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_id">Phân loại <span class="text-danger">*</span></label>
                                        <select class="form-control" name="category_id" id="category_id" required>
                                            @foreach(\Modules\Product\Entities\Category::all() as $category)
                                                <option {{ $category->id == $product->category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_cost">Cost <span class="text-danger">*</span></label>
                                        <input id="product_cost" type="text" class="form-control" min="0" name="product_cost" required value="{{ $product->product_cost }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_price">Giá <span class="text-danger">*</span></label>
                                        <input id="product_price" type="text" class="form-control" min="0" name="product_price" required value="{{ $product->product_price }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group custom-control custom-switch">
                                        <input @if($product->product_is_discount == 1) checked @endif type="checkbox" class="custom-control-input" id="product_is_discount" name="product_is_discount" value="1">
                                        <label class="custom-control-label" for="product_is_discount">Giảm giá</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row discount" id="discount">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_discount_percentage">Phần trăm</label>
                                        <input type="text" class="form-control" name="product_discount_percentage" value="{{ $product->product_discount_percentage }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_discount_amount">Số tiền</label>
                                        <input type="text" class="form-control" name="product_discount_amount" value="{{ $product->product_discount_amount }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="product_discount_amount">Lưu ý: Giảm giá sẽ ưu tiên theo phần trăm khi phần trăm và tiền mặt đều lớn hơn 0</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="product_note">Ghi chú</label>
                                <textarea name="product_note" id="product_note" rows="4 " class="form-control">{{ $product->product_note }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="image">Ảnh sản phẩm<i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Max Files: 3, Max File Size: 1MB, Image Size: 400x400"></i></label>
                                <div class="dropzone d-flex flex-wrap align-items-center justify-content-center" id="document-dropzone">
                                    <div class="dz-message" data-dz-message>
                                        <i class="bi bi-cloud-arrow-up"></i>
                                    </div>
                                </div>
                            </div>
                            @include('utils.alerts')
                            <div class="form-group text-right" >
                                <button class="btn btn-primary">Cập nhật</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('third_party_scripts')
    <script src="{{ asset('js/dropzone.js') }}"></script>
@endsection

@push('page_scripts')
    <script>
        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route('dropzone.upload') }}',
            maxFilesize: 1,
            acceptedFiles: '.jpg, .jpeg, .png',
            maxFiles: 3,
            addRemoveLinks: true,
            dictRemoveFile: "<i class='bi bi-x-circle text-danger'></i> remove",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
                uploadedDocumentMap[file.name] = response.name;
            },
            removedfile: function (file) {
                file.previewElement.remove();
                var name = '';
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name;
                } else {
                    name = uploadedDocumentMap[file.name];
                }
                $('form').find('input[name="document[]"][value="' + name + '"]').remove();
            },
            init: function () {
                @if(isset($product) && $product->getMedia('images'))
                var files = {!! json_encode($product->getMedia('images')) !!};
                for (var i in files) {
                    var file = files[i];
                    this.options.addedfile.call(this, file);
                    this.options.thumbnail.call(this, file, file.original_url);
                    file.previewElement.classList.add('dz-complete');
                    $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">');
                }
                @endif
            }
        }
    </script>

    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function () {

            {{--$('#product_cost').maskMoney({--}}
            {{--    prefix:'{{ settings()->currency->symbol }}',--}}
            {{--    thousands:'{{ settings()->currency->thousand_separator }}',--}}
            {{--    decimal:'{{ settings()->currency->decimal_separator }}',--}}
            {{--});--}}
            {{--$('#product_price').maskMoney({--}}
            {{--    prefix:'{{ settings()->currency->symbol }}',--}}
            {{--    thousands:'{{ settings()->currency->thousand_separator }}',--}}
            {{--    decimal:'{{ settings()->currency->decimal_separator }}',--}}
            {{--});--}}

            {{--$('#product_cost').maskMoney('mask');--}}
            {{--$('#product_price').maskMoney('mask');--}}

            {{--$('#product-form').submit(function () {--}}
            {{--    var product_cost = $('#product_cost').maskMoney('unmasked')[0];--}}
            {{--    var product_price = $('#product_price').maskMoney('unmasked')[0];--}}
            {{--    $('#product_cost').val(product_cost);--}}
            {{--    $('#product_price').val(product_price);--}}
            {{--});--}}
        });
    </script>
@endpush

