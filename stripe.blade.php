// in head tag
<script src="https://js.stripe.com/v3/"></script>
//
<x-guest-layout>
    <style>
        #card-element {
            margin-bottom: 16px;
        }

        #card-errors {
            color: #dc3545;
            margin-bottom: 16px;
        }

        .subscription-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .billing-info {
            margin-bottom: 1rem;
        }

        #coupon-link {
            color: #007bff;
            cursor: pointer;
        }

        #coupon-link:hover {
            text-decoration: underline;
        }

        #coupon-input-container {
            margin-top: 1rem;
        }

        .shift {
            float: inline-end;
        }

        .discount {
            background: linear-gradient(to right, #198754 0%, #1976ffa3 100%);
            border: 1px solid #adb5bd;
            color: white;
            border-radius: 10px;
            display: inline-block;
            padding: 2px 5px;
        }
    </style>

    <div class="container mt-2">
        <button class="btn btn-outline-primary btn-sm mb-2" onclick="goBack()"><i class="fa-solid fa-less-than mx-1"></i>Go
            Back
        </button>
        <div class="row">
            <!-- Left Card -->
            <div class="col-md-6">
                <div class="card border-light-subtle w-auto">
                    <div class="card-header">
                        <h3>Choose a plan</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('testCreateCustomer') }}" method="post" id="subscription-form">
                            @csrf
                            <div id="card-element" class="mb-3">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <div id="card-errors" role="alert" class="mb-3"></div>
                            <div class="icons mb-2">
                                <img src="{{ asset('assets/images/mastercard.png') }}" width="30" class="me-2">
                                <img src="{{ asset('assets/images/visa.png') }}" width="30" class="me-2">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name on Card</label>
                                    <input type="text" id="name" name="name" minlength="3" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_address_line1" class="form-label">Address line 1</label>
                                    <input type="text" id="billing_address_line1" name="billing_address_line1"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_address_line2" class="form-label">Address line 2</label>
                                    <input type="text" id="billing_address_line2" name="billing_address_line2"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_city" class="form-label">City</label>
                                    <input type="text" id="billing_city" min="3" maxlength="15" name="billing_city" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_state" class="form-label">State</label>
                                    <input type="text" id="billing_state" min="3" maxlength="15" name="billing_state" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_postal_code" class="form-label">Zip Code</label>
                                    <input type="number" id="billing_postal_code" maxlength="5" name="billing_postal_code"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="billing_country" class="form-label">Select country</label>
                                    <select name="billing_country" class="form-select" id="billing_country">
                                        <option value="US" selected="selected">United States</option>
                                        @if (file_exists(public_path('assets/countries.json')))
                                            @foreach (json_decode(file_get_contents(public_path('assets/countries.json'))) as $code => $country)
                                                <option value='{{ $code }}'>{{ $country }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No countries available</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="form-check float-left">
                                <input class="form-check-input" type="checkbox" value="" id="saveBillingDetails"
                                    checked>
                                <label class="form-check-label" for="saveBillingDetails">
                                    Save Billing Details
                                </label>
                            </div>
                            <button type="submit" id="save_card_btn" class="btn btn-dark mt-2 float-end">Pay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var stripe = Stripe('pk_test_51O2Ch3SIHMYlbzPNrRdpaTZjZ6hcCVCKYh4SqLgY9tFSlsOAc4u7OEP3msiPfMYNIniIXhxbfqCF2TWZ9wVAWA6z00gRM3jDle');
        // var stripe = Stripe('pk_live_51NXHyoSHKPSW8tPra74wBlKXhTnTMEFD3SyR5uWGW72KnYvNfKOai2n35l5YcTXbosPtncvYYhF1FftD2JrofYfZ0021Oyd80A');

        // Create an instance of Elements
        var elements = stripe.elements();

        // Check if the element with id 'card-element' exists
        var cardElement = document.querySelector('#card-element');

        if (cardElement) {
            // If the element exists, create and mount the card
            var card = elements.create('card');
            card.mount('#card-element');

            // Handle real-time validation errors on the Card Element
            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
        }

        // Handle form submission
        var stripeForm = document.getElementById('save_card_btn');
        var saveBillingDetailsCheckbox = document.getElementById('saveBillingDetails');
        if (stripeForm) {
            stripeForm.addEventListener('click', function(event) {
                event.preventDefault();
                // Disable the submit button to prevent multiple submissions
                document.querySelector('button').disabled = true;
                // Create a PaymentMethod
                stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                    billing_details: {
                        name: document.getElementById('name').value,
                        email: document.getElementById('email').value,
                    },
                }).then(function(result) {
                    if (result.error) {
                        // Show error to your customer (e.g., insufficient funds, card declined, etc.)
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        // Send the PaymentMethod ID to your server
                        stripeTokenHandler(result.paymentMethod);
                    }
                });
            });
            // Submit the form with the PaymentMethod ID to your server
            function stripeTokenHandler(paymentMethod) {
                console.log('test1', paymentMethod);
                var stripeForm = document.getElementById('subscription-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', paymentMethod.id);
                stripeForm.appendChild(hiddenInput);
                var checkboxInput = document.createElement('input');
                checkboxInput.setAttribute('type', 'hidden');
                checkboxInput.setAttribute('name', 'saveBillingDetails');
                checkboxInput.setAttribute('value', saveBillingDetailsCheckbox.checked ? 1 : 0);
                stripeForm.appendChild(checkboxInput);
                // Submit the form
                stripeForm.submit();
            }
        }
        // Back button
        function goBack() {
            window.history.back();
        }
    </script>
</x-guest-layout>
