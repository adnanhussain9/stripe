public function testCreateCustomer(Request $request){
        Stripe::setApiKey(config('services.stripe.secret'));
        $paymentMethodId = $request->input('paymentMethod');
        $checkboxInput = $request->input('saveBillingDetails');
        $campaign_id = $request->input('campaign_id');
        // $plan = $request->input('plan');
        $campaign = Campaign::find($campaign_id);
        $billingAddress = [
            'city' => $request->input('billing_city'),
            'country' => $request->input('billing_country'),
            'line1' => $request->input('billing_address_line1'),
            'line2' => $request->input('billing_address_line2'),
            'postal_code' => $request->input('billing_postal_code'),
            'state' => $request->input('billing_state'),
        ];
        $stripeCustomer = Customer::create([
                'email' => $request->email,
                'name' => $request->name,
                'payment_method' => $paymentMethodId,
                'address' => $billingAddress,
                'shipping' => [
                    'name' => $request->name,
                    'address' => $billingAddress,
                ],
            ]);
        $subscription = Subscription::create([
                    'customer' => $stripeCustomer->id,
                    'items' => [['price' => 'price_1OU5PgSIHMYlbzPN7SCWtDWl']],
                    'default_payment_method' => $paymentMethodId,
                    'off_session' => true,
                    'billing_cycle_anchor' => null,
                    'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                    'expand' => ['latest_invoice.payment_intent'],
                ]);
        return redirect()->route('home')->with('success', 'Test Payment Successfull');
    }
