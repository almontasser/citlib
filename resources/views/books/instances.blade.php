@extends('layouts.backend')

@section('content')
    <div class="content">

        <div class="row">
            <form name="new_instance_form" action="{{ route('book-instances', [$book]) }}" method="POST">
                @csrf
            </form>
            <script>
                function addNewClick() {
                    document.new_instance_form.submit()
                }

            </script>
            <x-overview-block link="{{ route('book-instances-generate', ['book' => $book]) }}">
                <x-slot name="content">
                    <i class="fa fa-plus text-success"></i>
                </x-slot>
                <x-slot name="title">
                    <span class="text-success">إضافة</span>
                </x-slot>
            </x-overview-block>

            <x-overview-block link="#">
                <x-slot name="content">
                    <span class="text-primary">{{ $book_instances->where('status', 'available')->count() }}</span>
                </x-slot>
                <x-slot name="title">
                    <span class="text-primary">الكتب المتوفرة</span>
                </x-slot>
            </x-overview-block>

            <x-overview-block link="#">
                <x-slot name="content">
                    <span class="text-success">{{ $book_instances->where('status', 'loaned')->count() }}</span>
                </x-slot>
                <x-slot name="title">
                    <span class="text-success">الكتب المستعارة</span>
                </x-slot>
            </x-overview-block>

            <x-overview-block link="javascript:void()">
                <x-slot name="content">
                    <span class="text-dark">{{ $book_instances->count() }}</span>
                </x-slot>
                <x-slot name="title">
                    <span class="text-dark">إجمالي النسخ</span>
                </x-slot>
            </x-overview-block>

        </div>

        <x-block title="مخزون الكتاب" subtitle="{{ $book->title }}">
            <table class="table table-bordered table-striped table-vcenter mt-2">
                <thead>
                    <tr>
                        {{-- <th class="text-center" style="width: 80px;">#</th> --}}
                        <th class="text-center">رقم النسخة</th>
                        <th class="text-center">الباركود</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">المستعير</th>
                        <th class="text-center">تاريخ الترجيع</th>
                        <th class="text-center">أوامر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($book_instances as $instance)
                        <tr>
                            {{-- <td class="text-center">{{ $instance->id }}</td> --}}
                            <td class="text-center">
                                <a href="{{ route('book-instance', ['book_instance' => $instance]) }}">{{ $instance->instance_number }}</a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('book-instance', ['book_instance' => $instance]) }}">
                                {{ $instance->barcode }}
                                </a>
                            </td>
                            <td class="text-center py-0">
                                <a href="{{ route('book-instance', ['book_instance' => $instance]) }}">
                                @if ($instance->status == 'available')
                                    <span class="badge badge-primary py-2" style="width: 100%">متوفر</span>
                                @elseif($instance->status == 'loaned')
                                    <span class="badge badge-success py-2" style="width: 100%">مستعار</span>
                                @elseif($instance->status == 'damaged')
                                    <span class="badge badge-warning py-2" style="width: 100%">تالف</span>
                                @elseif($instance->status == 'missing')
                                    <span class="badge badge-danger py-2" style="width: 100%">مفقود</span>
                                @endif
                                </a>
                            </td>
                            <td class="text-center">
                              @if($instance->status == 'loaned')
                                {{$instance->movements()->orderBy('id', 'DESC')->first()->user->name}}
                              @endif
                            </td>
                            <td class="text-center">
                              @if($instance->status == 'loaned')
                                <?php $borrow_end = $instance->movements()->orderBy('id', 'DESC')->first()->borrow_end; ?>
                                <span class="badge py-2 {{$borrow_end < date('Y-m-d') ? 'badge-danger' : 'badge-success'}}">{{$borrow_end}}</span>
                              @endif
                            </td>
                            <td class="text-center">
                              <?php
                              $barcode = $instance->barcode;
                              $barcodeImage = generateBarcodeImage($barcode);
                              ?>
                                <div class="btn-group">
                                  <a class="btn btn-sm btn-alt-light" href="javascript:printBarcode(['<?= $barcodeImage ?>'], ['<?= $barcode ?>']);">
                                    <i class="fa fa-print"></i>
                                  </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-block>
    </div>
    @if (isset($new_barcodes))
        <script>
            <?php
            $images = '';
            $barcodes = '';
            foreach($new_barcodes as $barcode) {
                if ($images != '') {
                    $images .= ',';
                }
                $images .= '\'' . generateBarcodeImage($barcode) . '\'';
                if ($barcodes != '') {
                    $barcodes .= ',';
                }
                $barcodes .= '\'' . $barcode . '\'';
            }
            $images = '[' . $images . ']';
            $barcodes = '[' . $barcodes . ']';
            ?>
            document.addEventListener("DOMContentLoaded", function (event) {
              printBarcode(<?= $images ?>, <?= $barcodes ?>);
              window.location.href = '<?= $url ?>';
            });
        </script>
    @endif
@endsection
