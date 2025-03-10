{!! view_render_event('bagisto.shop.checkout.payment.method.before') !!}

<v-payment-method ref="vPaymentMethod">
    <x-shop::shimmer.checkout.onepage.payment-method></x-shop::shimmer.checkout.onepage.payment-method>
</v-payment-method>

{!! view_render_event('bagisto.shop.checkout.payment.method.after') !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-payment-method-template">
        <div class="mt-[30px]  mb-[30px]">
            <template v-if="! isShowPaymentMethod && isPaymentMethodLoading">
                <x-shop::shimmer.checkout.onepage.payment-method></x-shop::shimmer.checkout.onepage.payment-method>
            </template>
    
            <template v-if="isShowPaymentMethod">
                <div>
                    <x-shop::accordion>
                        <x-slot:header>
                            <div class="flex justify-between items-center">
                                <h2 class="text-[26px] font-medium max-sm:text-[20px]">
                                    @lang('shop::app.checkout.onepage.payment.payment-method')
                                </h2>
                            </div>
                        </x-slot:header>
        
                        <x-slot:content>
                            <div class="flex flex-wrap gap-[29px] mt-[30px]">
                                <div 
                                    class="relative relative max-sm:max-w-full max-sm:flex-auto cursor-pointer"
                                    v-for="(payment, index) in paymentMethods"
                                >
                                    <input 
                                        type="radio" 
                                        name="payment[method]" 
                                        :value="payment.payment"
                                        :id="payment.method"
                                        class="hidden peer"    
                                        @change="store(payment)"
                                    >
        
                                    <label 
                                        :for="payment.method" 
                                        class="icon-radio-unselect text-[24px] text-navyBlue absolute right-[20px] top-[20px] peer-checked:icon-radio-select cursor-pointer"
                                    >
                                    </label>
        
                                    <label 
                                        :for="payment.method" 
                                        class="block border border-[#E9E9E9] p-[20px] rounded-[12px] w-[190px] max-sm:w-full cursor-pointer"
                                    >
                                        <img 
                                            class="mx-w-[55px] max-h-[45px]" 
                                            src="{{ bagisto_asset('images/paypal.png') }}" 
                                            :alt="payment.method_title" 
                                            :title="payment.method_title"
                                        >
                                        
                                        <p class="text-[14px] font-semibold mt-[5px]">
                                            @{{ payment.method_title }}
                                        </p>
                                        
                                        <p class="text-[12px] font-medium mt-[10px]">
                                            @{{ payment.description }}
                                        </p>
                                    </label>

                                    {{-- Todo implement the additionalDetails --}}
                                    {{-- \Webkul\Payment\Payment::getAdditionalDetails($payment['method'] --}}
                                </div>
                            </div>
                        </x-slot:content>
                    </x-shop::accordion>
                </div>
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-payment-method', {
            template: '#v-payment-method-template',

            data() {
                return {
                    paymentMethods: [],

                    isShowPaymentMethod: false,

                    isPaymentMethodLoading: false,
                }
            },

            methods: {
                store(selectedPaymentMethod) {
                    this.$axios.post("{{ route('shop.checkout.onepage.payment_methods.store') }}", {
                            payment: selectedPaymentMethod
                        })
                        .then(response => {
                            this.$parent.$refs.vCartSummary.selectedPaymentMethod = selectedPaymentMethod;

                            if (response.data.cart) {
                                this.$parent.$refs.vCartSummary.canPlaceOrder = true;
                            }
                        })
                        .catch(error => console.log(error));
                },
            },
        });
    </script>
@endPushOnce
