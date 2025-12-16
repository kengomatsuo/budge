<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class ExpenseOCRController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'receipt' => 'required|image|max:10240', // 10MB max
        ]);

        try {
            $image = $request->file('receipt');
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));

            $prompt = "Analyze this receipt image. Extract the following information in JSON format:
            - merchant_name (string)
            - date (YYYY-MM-DD)
            - currency (ISO code like USD, IDR, EUR, JPY)
            - total_amount (decimal)
            - tax_amount (decimal)
            - service_charge (decimal)
            - tip_amount (decimal)
            - discount_amount (decimal)
            - payment_method (string: 'cash', 'debit_card', 'credit_card', 'e_wallet') - infer from receipt text (e.g., 'VISA', 'CASH', 'MASTERCARD')
            - category_guess (string) - suggest a category based on the merchant and items. MUST be one of the following exact strings: 'Food', 'Transportation', 'Shopping', 'Entertainment', 'Healthcare', 'Bills & Utilities', 'Education', 'Housing', 'Personal Care', 'Other'.
            - items (array of objects with: name, quantity, unit_price, total_price, tax_rate, tax_category)

            If a field is not found, use null. Ensure numbers are numeric.
            For items, try to identify tax rate if listed, otherwise null.
            Return ONLY the JSON object, no markdown formatting.";

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4.1-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a receipt scanning assistant. Extract structured data from receipt images accurately.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => [
                                'url' => 'data:image/jpeg;base64,' . $base64Image,
                                'detail' => 'high'
                            ]],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
            ]);

            $content = $response->choices[0]->message->content;
            $data = json_decode($content, true);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to scan receipt: ' . $e->getMessage()
            ], 500);
        }
    }
}
