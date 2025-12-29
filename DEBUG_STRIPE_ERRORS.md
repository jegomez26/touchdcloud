# Debugging Stripe 500 Errors

## Common Issues and Fixes

### 1. Metadata Access Error
**Problem**: Accessing Stripe metadata as object properties instead of array
**Fix**: Convert metadata to array: `$metadata = (array) $paymentIntent->metadata;`

### 2. Payment Method Not Found
**Problem**: Payment method might be in different location
**Fix**: Check both `payment_method` and `latest_charge.payment_method`

### 3. Trial Period Handling
**Problem**: For trials, payment amount is $0, which requires different handling
**Fix**: 
- Use `confirmation_method => 'manual'` for $0 amounts
- Allow `requires_capture` status for trials

### 4. Payment Record Creation
**Problem**: Trying to create duplicate payment records
**Fix**: Use `updateOrCreate` instead of `create`

## How to Debug

1. **Check Laravel Logs**: `storage/logs/laravel.log`
   - Look for "Stripe Subscription Creation Error"
   - Look for "Unexpected error creating subscription"

2. **Check Browser Console**: 
   - Open Developer Tools (F12)
   - Check Network tab for the failed request
   - Look at the response body for error details

3. **Enable Debug Mode**:
   - Set `APP_DEBUG=true` in `.env`
   - Error messages will include more details

4. **Check Stripe Dashboard**:
   - Go to Stripe Dashboard → Payments
   - Check if Payment Intent was created
   - Check Payment Intent status and metadata

## Recent Fixes Applied

1. ✅ Fixed metadata access (array instead of object)
2. ✅ Added better payment method retrieval
3. ✅ Improved error logging with full trace
4. ✅ Added payment record updateOrCreate to prevent duplicates
5. ✅ Added support for $0 payment intents (trials)
6. ✅ Added status validation for trials

## Testing Steps

1. Try subscribing to Starter Plan (no trial)
2. Try subscribing to Growth Plan (with trial)
3. Try subscribing with promo pricing
4. Check logs after each attempt

## If Error Persists

1. Check if Stripe keys are set in `.env`:
   ```env
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   ```

2. Verify user has a provider profile

3. Check database for existing subscriptions that might conflict

4. Review full error trace in `storage/logs/laravel.log`

