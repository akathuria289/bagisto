@if ($product->type == 'downloadable')
    {!! view_render_event('bagisto.shop.products.view.downloadable.before', ['product' => $product]) !!}

    @if ($product->downloadable_samples->count())
        <div class="sample-list mb-[25px] mt-[30px]">
            <h3 class="font-medium mb-[15px]">
                @lang('shop::app.products.view.type.downloadable.samples')
            </h3>

            <ul>
                @foreach ($product->downloadable_samples as $sample)
                    <li class="mb-[5px]">
                        <a 
                            href="{{ route('shop.downloadable.download_sample', ['type' => 'sample', 'id' => $sample->id]) }}" 
                            class="text-[#0A49A7]"
                            target="_blank"
                        >
                            {{ $sample->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($product->downloadable_links->count())
        <h3 class="font-medium mt-[30px] mb-[15px]">
            @lang('shop::app.products.view.type.downloadable.links')
        </h3>

        <div class="grid gap-[15px]">
            @foreach ($product->downloadable_links as $link)
                <div class="select-none flex gap-x-[15px]">
                    <div class="flex">
                        <v-field
                            type="checkbox"
                            name="links[]"
                            value="{{ $link->id }}"
                            id="{{ $link->id }}"
                            class="hidden peer"
                            rules="required"
                            label="{{ trans('shop::app.products.view.type.downloadable.links') }}"
                        >
                        </v-field>
                        
                        <label
                            class="icon-uncheck text-[24px] text-navyBlue peer-checked:icon-check-box peer-checked:text-navyBlue cursor-pointer"
                            for="{{ $link->id }}"
                        ></label>
                        
                        <label
                            for="{{ $link->id }}"
                            class="ml-1 cursor-pointer"
                        >
                            {{ $link->title . ' + ' . core()->currency($link->price) }}
                        </label>

                        <a 
                            href="{{ route('shop.downloadable.download_sample', ['type' => 'link', 'id' => $link->id]) }}"
                            target="_blank"
                            class="text-[#0A49A7] ml-2"
                        >
                            @lang('shop::app.products.view.type.downloadable.sample')
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {!! view_render_event('bagisto.shop.products.view.downloadable.before', ['product' => $product]) !!}
@endif
