<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    /**
     * Download invoice for a specific payment.
     */
    public function downloadInvoice($paymentId)
    {
        $user = Auth::user();
        
        $payment = Payment::where('id', $paymentId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Generate invoice content (in production, you'd use a PDF library like DomPDF)
        $invoiceContent = $this->generateInvoiceContent($payment);
        
        $filename = "invoice_{$payment->id}_{$payment->created_at->format('Y-m-d')}.pdf";
        
        return response($invoiceContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Download receipt for a specific payment.
     */
    public function downloadReceipt($paymentId)
    {
        $user = Auth::user();
        
        $payment = Payment::where('id', $paymentId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Generate receipt content
        $receiptContent = $this->generateReceiptContent($payment);
        
        $filename = "receipt_{$payment->id}_{$payment->created_at->format('Y-m-d')}.pdf";
        
        return response($receiptContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Generate invoice content (simplified for demo).
     */
    private function generateInvoiceContent($payment)
    {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invoice #{$payment->id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .invoice-details { margin-bottom: 30px; }
                .item { margin-bottom: 20px; }
                .total { font-weight: bold; font-size: 18px; }
                .footer { margin-top: 50px; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>SIL Match</h1>
                <h2>Invoice #{$payment->id}</h2>
            </div>
            
            <div class='invoice-details'>
                <p><strong>Invoice Date:</strong> {$payment->created_at->format('Y-m-d')}</p>
                <p><strong>Payment Date:</strong> {$payment->paid_at->format('Y-m-d')}</p>
                <p><strong>Customer:</strong> {$payment->user->first_name} {$payment->user->last_name}</p>
                <p><strong>Email:</strong> {$payment->user->email}</p>
            </div>
            
            <div class='item'>
                <h3>Service Details</h3>
                <p><strong>Description:</strong> {$payment->description}</p>
                <p><strong>Amount:</strong> {$payment->formatted_amount}</p>
                <p><strong>Status:</strong> {$payment->status_display}</p>
            </div>
            
            <div class='total'>
                <p>Total Amount: {$payment->formatted_amount}</p>
            </div>
            
            <div class='footer'>
                <p>Thank you for your business!</p>
                <p>SIL Match - NDIS Provider Platform</p>
            </div>
        </body>
        </html>
        ";
        
        // In production, you would convert this HTML to PDF using a library like DomPDF
        // For now, we'll return the HTML content
        return $html;
    }

    /**
     * Generate receipt content (simplified for demo).
     */
    private function generateReceiptContent($payment)
    {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt #{$payment->id}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .receipt-details { margin-bottom: 30px; }
                .item { margin-bottom: 20px; }
                .total { font-weight: bold; font-size: 18px; }
                .footer { margin-top: 50px; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>SIL Match</h1>
                <h2>Payment Receipt #{$payment->id}</h2>
            </div>
            
            <div class='receipt-details'>
                <p><strong>Payment Date:</strong> {$payment->paid_at->format('Y-m-d H:i:s')}</p>
                <p><strong>Payment ID:</strong> {$payment->payment_intent_id}</p>
                <p><strong>Customer:</strong> {$payment->user->first_name} {$payment->user->last_name}</p>
                <p><strong>Email:</strong> {$payment->user->email}</p>
            </div>
            
            <div class='item'>
                <h3>Payment Details</h3>
                <p><strong>Description:</strong> {$payment->description}</p>
                <p><strong>Amount:</strong> {$payment->formatted_amount}</p>
                <p><strong>Status:</strong> {$payment->status_display}</p>
            </div>
            
            <div class='total'>
                <p>Total Paid: {$payment->formatted_amount}</p>
            </div>
            
            <div class='footer'>
                <p>Thank you for your payment!</p>
                <p>SIL Match - NDIS Provider Platform</p>
            </div>
        </body>
        </html>
        ";
        
        return $html;
    }
}
