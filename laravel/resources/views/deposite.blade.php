@extends('Layout.usergame')
@section('content')

    <div class="deposite-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="pay-tabs">
                        <a href="#" class="custom-tabs-link active">DEPOSIT</a>
                        <a href="/withdraw" class="custom-tabs-link">WITHDRAW</a>
                    </div>

                    <input type="hidden" name="username" id="username" value="">
                    <input type="hidden" name="password" id="password" value="">

                    <div class="pay-options">
                        <div class="payment-cols">
{{--                            <div class="grid-view">--}}
{{--                                <div class="grid-list" onclick="paymentGatewayDetails('6')">--}}
{{--                                    <button class="btn payment-btn" data-tab="aamarpay">--}}
{{--                                        <img src="images/app-logo/aamarpay.png" />--}}
{{--                                        <div class="PaymentCard_limit">Min {{setting('min_recharge')}}</div>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="deposite-box" id="aamarpay">
                                <div class="d-box">
                                    <h5>Contact on WhatsApp:</h5>
                                    <ul class="deposit-numbers">
                                        @forelse($depositNumbers as $depositNumber)
                                        <li class="deposit-number">
                                            <a href="https://wa.me/{{ $depositNumber }}" target="_blank"
                                               class="d-flex align-items-center btn btn-success rounded-pill deposit-number-link">
                                                <i class="fa fa-whatsapp me-2"></i>
                                                <h6 class="mb-0">{{ $depositNumber }}</h6>
                                            </a>
                                        </li>
                                        @empty
                                        @endforelse
                                    </ul>

                                </div>
{{--                                <form action="{{ route('aamarpay.pay') }}" method="POST">--}}
{{--                                    @csrf--}}
{{--                                <div class="d-box">--}}
{{--                                    <div class="limit-txt">LIMITS:<span>{{setting('min_recharge')}}</span></div>--}}
{{--                                    <div class="row g-3">--}}
{{--                                        <div class="col-6">--}}
{{--                                            <div class="login-controls mt-3 rounded-pill h42">--}}
{{--                                                <label for="Username" class="rounded-pill">--}}
{{--                                                    <input type="text" class="form-control text-i10 amount"--}}
{{--                                                        id="net_bank_amount"--}}
{{--                                                           name="amount"--}}
{{--                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">--}}
{{--                                                    <input type="hidden" id="net_bank_min_amount" value="{{setting('min_recharge')}}">--}}
{{--                                                    <input type="hidden" id="net_bank_max_amount" value="">--}}
{{--                                                    <i class="Input_currency">--}}
{{--                                                        BDT--}}
{{--                                                    </i>--}}
{{--                                                </label>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-6">--}}
{{--                                            <button type="submit" class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow">--}}
{{--                                                DEPOSIT--}}
{{--                                            </button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="amount-tooltips">--}}
{{--                                        <button class="btn amount-tooltips-btn">500</button>--}}
{{--                                        <button class="btn amount-tooltips-btn active">1000</button>--}}
{{--                                        <button class="btn amount-tooltips-btn">2500</button>--}}
{{--                                        <button class="btn amount-tooltips-btn">5000</button>--}}
{{--                                    </div>--}}
{{--                                    <label for="net_bank_amount" class="error" id="net_bank_amount-error"></label>--}}
{{--                                </div>--}}
{{--                                </form>--}}
                            </div>
                        </div>
                        </div>

                    @forelse($errors->all() as $error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="{{ url('user/deposit.js') }}"></script>
    @isset($_GET['msg'])
    @if ($_GET['msg'] == 'Success')
        <script>
            toastr.success("Request send successfully!")
        </script>
    @endif
    @endisset
@endsection
