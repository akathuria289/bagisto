<v-coupon 
    :is-coupon-applied="cart.coupon_code"
    :sub-total="cart.base_grand_total"
>
</v-coupon>

@pushOnce('scripts')
    <script type="text/x-template" id="v-coupon-template">
        <div class="flex text-right justify-between">
            <p class="text-[16px] max-sm:text-[14px] max-sm:font-normal">
                @lang('shop::app.checkout.cart.coupon.discount')
            </p>

            <p class="text-[16px] max-sm:text-[14px] max-sm:font-medium font-medium">
                <x-shop::modal ref="couponModel">
                    <x-slot:toggle>
                        <span 
                            class="text-[#0A49A7] cursor-pointer" 
                            v-if="! isCouponApplied"
                        >
                            @lang('shop::app.checkout.cart.coupon.apply')
                        </span>
                    </x-slot:toggle>

                    <x-slot:header>
                        <h2 class="text-[25px] font-medium max-sm:text-[22px]">
                            @lang('Apply Coupon')
                        </h2>
                    </x-slot:header>

                    <x-slot:content>
                       
                        <x-shop::form
                            v-slot="{ meta, errors, handleSubmit }"
                            as="div"
                        >
                            <form @submit="handleSubmit($event, applyCoupon)">
                                <x-shop::form.control-group>
                                    <div class="p-[30px] bg-white">
                                        <x-shop::form.control-group.control
                                            type="text"
                                            name="code"
                                            class="text-[14px] appearance-none border rounded-[12px] w-full py-[20px] px-[25px] focus:outline-none focus:shadow-outline"
                                            placeholder="Enter your code"
                                            rules="required"
                                            v-model="code"
                                        >
                                        </x-shop::form.control-group.control>

                                        <x-shop::form.control-group.error
                                            class="flex"
                                            control-name="code"
                                        >
                                        </x-shop::form.control-group.error>
                                    </div>
                                </x-shop::form.control-group>

                                <div class="p-[30px] bg-white mt-[20px]">
                                    <div class="flex justify-between items-center gap-[15px] flex-wrap">
                                        <p class="text-[14px] font-medium text-[#7D7D7D]">
                                            @lang('Subtotal')
                                        </p>

                                        <div class="flex gap-[30px] items-center flex-auto flex-wrap">
                                            <p 
                                                class="text-[30px] font-semibold max-sm:text-[22px]"
                                                v-text="subTotal"
                                            >
                                            </p>

                                            <button
                                                class="block flex-auto bg-navyBlue text-white text-base w-max font-medium py-[11px] px-[43px] rounded-[18px] text-center cursor-pointer max-sm:text-[14px] max-sm:px-[25px]"
                                                type="submit"
                                            >
                                               @lang('shop::app.customers.account.save')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </x-shop::form>
                    </x-slot:content>
                </x-shop::modal>

                <div 
                    class="text-[12px] font-small flex justify-between items-center"
                    v-if="isCouponApplied"
                >
                    <p class="text-[12px] mr-2">
                        @lang('Coupon applied')
                    </p>
                    
                    <p 
                        class="text-[16px] font-medium cursor-pointer text-navyBlue"
                        title="@lang('Applied coupon')"
                    >
                        "@{{ isCouponApplied }}"
                    </p>

                    <span 
                        class="icon-cancel text-[30px] cursor-pointer"
                        title="@lang('Remove coupon')"
                        @click="destroyCoupon"
                    >
                    </span>
                </div>
            </p>
        </div>
    </script>

    <script type="module">
        app.component('v-coupon', {
            template: '#v-coupon-template',
            
            props: ['isCouponApplied', 'subTotal'],

            data() {
                return {
                    coupons: [],

                    code: '',
                }
            },

            methods: {
                applyCoupon(params) {
                    this.$axios.post("{{ route('shop.api.checkout.cart.coupon.apply') }}", params)
                        .then((response) => {
                            this.$parent.$parent.$refs.vCart.get();
                  
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.$refs.couponModel.toggle();
                        })
                        .catch((error) => {
                            if ([400, 422].includes(error.response.request.status)) {
                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });

                                this.$refs.couponModel.toggle();

                                return;
                            }

                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                            this.$refs.couponModel.toggle();
                        })
                },

                destroyCoupon() {
                    this.$axios.delete("{{ route('shop.api.checkout.cart.coupon.remove') }}", {
                            '_token': "{{ csrf_token() }}"
                        })
                        .then((response) => {
                            this.$parent.$parent.$refs.vCart.get();

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => console.log(error));
                },
            }
        })
    </script>
@endPushOnce