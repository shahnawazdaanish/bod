<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <script id="myScript"
            src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>

    <style>
        body {
            margin-top: 20px;
            background: #eee;
        }

        .invoice {
            background: #fff;
            padding: 20px
        }

        .invoice-company {
            font-size: 20px
        }

        .invoice-header {
            margin: 0 -20px;
            background: #f0f3f4;
            padding: 20px
        }

        .invoice-date,
        .invoice-from,
        .invoice-to {
            display: table-cell;
            width: 1%
        }

        .invoice-from,
        .invoice-to {
            padding-right: 20px
        }

        .invoice-date .date,
        .invoice-from strong,
        .invoice-to strong {
            font-size: 16px;
            font-weight: 600
        }

        .invoice-date {
            text-align: right;
            padding-left: 20px
        }

        .invoice-price {
            background: #f0f3f4;
            display: table;
            width: 100%
        }

        .invoice-price .invoice-price-left,
        .invoice-price .invoice-price-right {
            display: table-cell;
            padding: 20px;
            font-size: 20px;
            font-weight: 600;
            width: 75%;
            position: relative;
            vertical-align: middle
        }

        .invoice-price .invoice-price-left .sub-price {
            display: table-cell;
            vertical-align: middle;
            padding: 0 20px
        }

        .invoice-price small {
            font-size: 12px;
            font-weight: 400;
            display: block
        }

        .invoice-price .invoice-price-row {
            display: table;
            float: left
        }

        .invoice-price .invoice-price-right {
            width: 25%;
            background: #d62267;
            color: #fff;
            font-size: 28px;
            text-align: right;
            vertical-align: bottom;
            font-weight: 300
        }

        .invoice-price .invoice-price-right small {
            display: block;
            opacity: .6;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 12px
        }

        .invoice-pay {
            width: 100%;
            margin: 0 auto;
        }

        .invoice-pay .invoice-pay-center {
            display: table-cell;
            padding: 20px;
            font-size: 20px;
            width: 10%;
            font-weight: 600;
            position: relative;
            vertical-align: middle;
            text-align: center;
        }

        .invoice-pay .invoice-pay-center a {
            display: block;
        }

        .invoice-footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px
        }

        .invoice-note {
            color: #999;
            margin-top: 80px;
            font-size: 85%
        }

        .invoice > div:not(.invoice-footer) {
            margin-bottom: 20px
        }

        .btn.btn-white, .btn.btn-white.disabled, .btn.btn-white.disabled:focus, .btn.btn-white.disabled:hover, .btn.btn-white[disabled], .btn.btn-white[disabled]:focus, .btn.btn-white[disabled]:hover {
            color: #2d353c;
            background: #fff;
            border-color: #d9dfe3;
        }

        .text-inverse {
            color: #d62267;
        }
    </style>
    <title>Payment - bKash</title>
</head>
<body>
<div class="container">
    <div class="col-md-12">

        @if($linkPayment && !empty($linkPayment))

            @if($linkPayment->merchant)

            <div class="invoice">
                <!-- begin invoice-company -->
                <div class="invoice-company text-inverse f-w-600">
            <span class="pull-right hidden-print">
{{--            <a href="javascript:;" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a>--}}
            <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i
                    class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>
            </span>
                    {{ strtoupper($linkPayment->merchant->name) ?? '' }}
                </div>
                <!-- end invoice-company -->
                <!-- begin invoice-header -->
                <div class="invoice-header">
                    <div class="invoice-from">
                        <small>Billed To</small>
                        <address class="m-t-5 m-b-5">
                            <strong class="text-inverse">bKash Customer Account</strong><br>
                            Phone: {{ $linkPayment->customer_msisdn ?? '' }}<br>
                            Email: {{ $linkPayment->customer_email ?? '' }}
                        </address>
                    </div>
                    {{--                <div class="invoice-to">--}}
                    {{--                    <small>to</small>--}}
                    {{--                    <address class="m-t-5 m-b-5">--}}
                    {{--                        <strong class="text-inverse">Company Name</strong><br>--}}
                    {{--                        Street Address<br>--}}
                    {{--                        City, Zip Code<br>--}}
                    {{--                        Phone: (123) 456-7890<br>--}}
                    {{--                        Fax: (123) 456-7890--}}
                    {{--                    </address>--}}
                    {{--                </div>--}}
                    <div class="invoice-date">
                        <small>Invoice Details</small>
                        <div
                            class="date text-inverse m-t-5">{{ $linkPayment->created_at->format('M d, Y h:i A') }}</div>
                        <div class="invoice-detail">
                            #{{ $linkPayment->reference_id ?? '' }}<br>
                        </div>
                    </div>
                </div>
                <!-- end invoice-header -->
                <!-- begin invoice-content -->
                <div class="invoice-content">
                    <!-- begin table-responsive -->
                    <div class="table-responsive">
                        <table class="table table-invoice">
                            <thead>
                            <tr>
                                <th>PRODUCT DESCRIPTION</th>
                                <th class="text-right" width="20%">TOTAL</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <span class="text-inverse">{{ $linkPayment->payable_product_description ?? '' }}</span><br>
                                </td>
                                <td class="text-right">
                                    @if($linkPayment->allow_custom_amount && $linkPayment->status != 'PAID')
                                        <input class="form-control" type="text"
                                               id="payable_amount" name="payable_amount"
                                               placeholder="Enter an amount">
                                    @else
                                        BDT {{ $linkPayment->payable_amount ?? 0 }}
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                    <!-- begin invoice-price -->
                    <div class="invoice-price">
                        <div class="invoice-price-left">
                            <div class="invoice-price-row">
                                <div class="sub-price">
                                    <small>SUBTOTAL</small>
                                    <span class="text-inverse">BDT <span
                                            id="subtotal">{{ $linkPayment->payable_amount ?? 0 }}</span></span>
                                </div>
                                <div class="sub-price">
                                    <i class="fa fa-plus text-muted"></i>
                                </div>
                                <div class="sub-price">
                                    <small>FEE</small>
                                    <span class="text-inverse">BDT 0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-price-right">
                            <small>TOTAL</small> <span
                                class="f-w-600">BDT <span
                                    id="final_amount">{{ $linkPayment->payable_amount ?? 0 }}</span></span>
                        </div>
                    </div>
                    <!-- end invoice-price -->
                    <!-- begin invoice-price -->
                    <div class="invoice-pay">
                        <div class="invoice-pay-center">
                            @if($linkPayment->status != 'PAID')
                                <a id="bKash_button" href="#"><img style="max-width: 200px"
                                                                   src="{{ asset('images/pay_with_bkash.png') }}"
                                                                   alt=""></a>
                            @else
                                <p for="" class="alert alert-success">PAID</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th class="text-right" width="20%">Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($linkPayment->payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="text-inverse">{{ $payment->trx_id }}</span><br>
                                            </td>
                                            <td class="text-right">{{ $payment->amount }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            @endif
                        </div>
                    </div>
                    <!-- end invoice-price -->
                </div>
                <!-- end invoice-content -->
                <!-- begin invoice-note -->
                <div class="invoice-note">
                    * Make all cheques payable to [Your Company Name]<br>
                    * Payment is due within 30 days<br>
                    * If you have any questions concerning this invoice, contact [Name, Phone Number, Email]
                </div>
                <!-- end invoice-note -->
                <!-- begin invoice-footer -->
                <div class="invoice-footer">
                    <p class="text-center m-b-5 f-w-600">
                        THANK YOU FOR YOUR PAYMENT
                    </p>
                    <p class="text-center">
                        <span class="m-r-10"><i class="fa fa-fw fa-lg fa-globe"></i> bkash.com</span>
                    </p>
                </div>
                <!-- end invoice-footer -->
            </div>
            @else
                <div class="content">
                    <h2 class="title text-center">Merchant information is missing</h2>
                </div>
            @endif


        @else

            <div class="content">
                <h2 class="title text-center">No payment found</h2>
            </div>

        @endif
    </div>
</div>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    @if($linkPayment && !empty($linkPayment))

    $(document).ready(function () {
        var payable_amount = 0;

        $('#payable_amount').keyup(function () {
            var amount = $(this).val();
            $('#subtotal').text(amount);
            $('#final_amount').text(amount);
        });

        var paymentConfig = {
            createCheckoutURL: "{{ route('payment_link_create', ['reference_id'=> $linkPayment->reference_id]) }}",
            executeCheckoutURL: "{{ route('payment_link_execute', ['reference_id'=> $linkPayment->reference_id]) }}",
        };
        var paymentRequest = {amount: 0, intent: 'sale'};

        bKash.init({
            paymentMode: 'checkout',
            paymentRequest: paymentRequest,
            createRequest: function (request) {
                console.log('=> createRequest (request) :: ');
                console.log(request);

                @if($linkPayment->allow_custom_amount)
                    paymentRequest.amount = $('#payable_amount').val();
                @else
                    paymentRequest.amount = '{{ $linkPayment->payable_amount ?? 0 }}';
                @endif


                if (parseInt(paymentRequest.amount) > 0) {
                    $.ajax({
                        url: paymentConfig.createCheckoutURL + "?amount=" + paymentRequest.amount,
                        type: 'GET',
                        contentType: 'application/json',
                        success: function (data) {
                            console.log('got data from create  ..');
                            console.log('data ::=>');
                            console.log(JSON.stringify(data));

                            // var obj = JSON.parse(data);

                            if (data && data.paymentID != null) {
                                paymentID = data.paymentID;
                                bKash.create().onSuccess(data);
                            } else {
                                console.log('error');
                                bKash.create().onError();
                            }
                        },
                        error: function () {
                            console.log('error');
                            bKash.create().onError();
                        }
                    });
                } else {
                    alert("Please provide amount greater than zero");
                    bKash.create().onError();
                }
            },

            executeRequestOnAuthorization: function () {
                console.log('=> executeRequestOnAuthorization');
                $.ajax({
                    url: paymentConfig.executeCheckoutURL + "?paymentID=" + paymentID,
                    type: 'GET',
                    contentType: 'application/json',
                    success: function (data, textStatus, xhr) {
                        window.location.reload();
                    },
                    error: function () {
                        alert('Error! Cannot process you payment.');
                        bKash.execute().onError();
                    }
                });
            }
        });

    });

    function callReconfigure(val) {
        bKash.reconfigure(val);
    }

    function clickPayButton() {
        $("#bKash_button").trigger('click');
    }

    @endif
</script>

</body>
</html>
