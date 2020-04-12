<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
      integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<style>
    .searchbar {
        margin-bottom: auto;
        margin-top: auto;
        height: 60px;
        /*background-color: #353b48;*/
        background-color: #e43573;
        border-radius: 30px;
        padding: 10px;
    }

    .search_input {
        color: white;
        border: 0;
        outline: 0;
        padding: 0 10px;
        background: none;
        width: 75%;
        caret-color: transparent;
        line-height: 40px;
        transition: width 0.4s linear;
    }

    .search_input::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: white;
        opacity: 1; /* Firefox */
    }

    .search_input:-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: white;
    }

    .search_input::-ms-input-placeholder { /* Microsoft Edge */
        color: white;
    }

    .searchbar:hover > .search_input {
        padding: 0 10px;
        /*width: 100%;*/
        caret-color: #fff0fe;
        transition: width 0.4s linear;
    }

    .searchbar:hover > .search_icon {
        background: white;
        color: #e74c3c;
    }

    .search_icon {
        height: 40px;
        width: 40px;
        float: right;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        color: white;
        text-decoration: none;
    }
</style>

<div class="container h-100">
    <div class="row" style="margin-top: 25px;">
        <div class="col-md-6 col-md-offset-3">
            <div class="d-flex justify-content-center h-100">
                <form action="{{ route('search_submit') }}" method="POST" name="searchBox">
                    {{ csrf_field() }}
                    <div class="searchbar">
                        <input class="search_input" type="text" name="trxid"
                               placeholder="Search a payment using transaction ID/Phone Number" value="{{ old('trxid') }}">
                        <button type="submit" class="search_icon"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if(isset($payments) && $payments)
        @foreach($payments as $payment)
        <div class="row" style="margin-top: 30px">
            <div class="col-md-6 col-md-offset-3 panel panel-primary">
                <div class="table-responsive panel-body">
                    <h2 class="text-center">Payment Information</h2>
                    <br>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th class="text-center" style="background: #e43573; color: white" colspan="7">Transasction
                                ID - <strong>{{ $payment->trx_id }}</strong></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><b>Sender</b></td>
                            <td><strong>{{ $payment->sender_account_no }}</strong></td>
                        </tr>
                        <tr>
                            <td><b>Amont</b></td>
                            <td><strong>{{ $payment->amount }} {{ $payment->currency }}</strong></td>
                        </tr>
                        <tr>
                            <td><b>Receiver</b></td>
                            <td>{{ $payment->receiver_account_no }}</td>
                        </tr>
                        <tr>
                            <td><b>Reference</b></td>
                            <td>{{ $payment->transactionReference }}</td>
                        </tr>
                        <tr>
                            <td><b>Merchant</b></td>
                            <td>{{ $payment->merchant->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><b>Date Time</b></td>
                            <td>{{ $payment->transaction_datetime }}</td>
                        </tr>
                        <tr>
                            <td><b>Merchant Reference</b></td>
                            <td>{{ $payment->merchant_ref }}</td>
                        </tr>
                        @if(empty($payment->merchant_ref))
                        <tr>
                            <td><b>Action</b></td>
                            <td>
                                <form action="{{ route('payment_used') }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="payment_id"
                                           value="{{ Crypt::encrypt($payment->id) }}">
                                    <input type="text" name="merchant_reference" placeholder="Tag a reference" required>
                                    <button class="btn btn-warning" type="submit"
                                            onclick="return confirm('Are you sure to do this?');">Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        {{--<tr>
                            <td><b>Status</b></td>
                            <td>
                                <label class="label label-{{ $payment->payment_status ? 'danger' : 'success' }}">{{ $payment->payment_status ? 'Used' : 'Unused' }}</label>
                            </td>
                        </tr>
                        @if($payment->payment_status == 0)
                            <tr>
                                <td><b>Action</b></td>
                                <td>
                                    <form action="{{ route('payment_used') }}" method="post">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="payment_id"
                                               value="{{ Crypt::encrypt($payment->id) }}">
                                        <button class="btn btn-warning" type="submit"
                                                onclick="return confirm('Are you sure to do this?');">Mark as used
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>
