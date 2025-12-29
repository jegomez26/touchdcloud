# Stripe Payment Integration Setup

This application uses Stripe Checkout for processing subscription payments. Follow these steps to configure Stripe:

## Step 1: Add Stripe Keys to .env File

Open your `.env` file (located in the project root) and add the following environment variables:

```env
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

### Where to find your Stripe keys:

1. **Publishable Key (STRIPE_KEY)**: 
   - Log in to your [Stripe Dashboard](https://dashboard.stripe.com/)
   - Go to **Developers** → **API keys**
   - Copy the **Publishable key** (starts with `pk_test_` for test mode or `pk_live_` for live mode)

2. **Secret Key (STRIPE_SECRET)**:
   - In the same **API keys** section
   - Click **Reveal test key** or **Reveal live key**
   - Copy the **Secret key** (starts with `sk_test_` for test mode or `sk_live_` for live mode)

3. **Webhook Secret (STRIPE_WEBHOOK_SECRET)**:
   - Go to **Developers** → **Webhooks** in Stripe Dashboard
   - Click **Add endpoint** or select an existing endpoint
   - Set the endpoint URL to: `https://yourdomain.com/stripe    /webhook`
   - Select the following events to listen to:
     - `checkout.session.completed`
     - `customer.subscription.created`
     - `customer.subscription.updated`
     - `customer.subscription.deleted`
     - `invoice.payment_succeeded`
     - `invoice.payment_failed`
   - After creating the endpoint, click on it and copy the **Signing secret** (starts with `whsec_`)

## Step 2: Clear Configuration Cache

After adding the keys to your `.env` file, clear the configuration cache:

```bash
php artisan config:clear
php artisan config:cache
```

## Step 3: Test Mode vs Live Mode

- **Test Mode**: Use keys starting with `pk_test_` and `sk_test_` for development/testing
- **Live Mode**: Use keys starting with `pk_live_` and `sk_live_` for production

**Important**: Make sure to use test mode keys during development and switch to live keys only when going to production.

## Step 4: Testing Payments

You can use Stripe's test card numbers to test payments:

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- Use any future expiry date (e.g., 12/34)
- Use any 3-digit CVC

## How It Works

1. When a user subscribes to a plan, they are redirected to Stripe Checkout
2. After successful payment, Stripe redirects back to the success page
3. Stripe sends webhook events to update subscription status in the database
4. The webhook endpoint is: `/stripe/webhook`

## Troubleshooting

- **"Payment system is not configured"**: Make sure `STRIPE_KEY` and `STRIPE_SECRET` are set in your `.env` file
- **Webhook not working**: Verify `STRIPE_WEBHOOK_SECRET` is correct and the webhook endpoint URL matches your domain
- **Subscription not activating**: Check Laravel logs (`storage/logs/laravel.log`) for webhook errors

## Security Notes

- Never commit your `.env` file to version control
- Keep your secret keys secure and never expose them in client-side code
- The publishable key is safe to use in frontend code, but it's not needed for Stripe Checkout (handled server-side)
- Always use HTTPS in production for webhook endpoints

