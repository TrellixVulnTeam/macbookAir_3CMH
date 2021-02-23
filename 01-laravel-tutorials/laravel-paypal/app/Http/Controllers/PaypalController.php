<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;

class PaypalController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->clientId = 'AUUGkfzUw-zZCeeoeNG7Jw5hh-TjrqXnIMfSOjNsYOjrLGlsEeMVxJCAmTN5g5LToYG_a9rNRzX9C42V';
        $this->secret = 'EOY2lG6rMA2HsTwWYB2NvADKH_OA4laANBLjhTLI_phpAgq6Rv9GS7Zb12MzzSvYHb8qFiPO6qjDS6fb';
        $this->uri = 'https://api.sandbox.paypal.com/v1/oauth2/token';

        $this->client = new Guzzle();

    }

    public function createProduct(Request $request)
    {
        
        $params = [
            'name' => 'Produto Teste',
            'description' => 'Converter audio from posts',
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
            'image_url' => 'https://example.com/streaming.jpg',
            'home_url' => 'https://example.com/home'
        ];

        $uri = 'https://api.sandbox.paypal.com/v1/catalogs/products';

        $response = $this->client->request('POST', $uri, [
            'headers' => [
                'Content-Type'      => 'application/json',
                'PayPal-Request-Id' => 'AUDIMA-2000',
                'Authorization'     => "Bearer $request->token",
            ],
            'json' => $params
        ]);

        return $response;
    }

    
    public function getToken() {

        $response = $this->client->request('POST', $this->uri, [
            'headers' =>
            [
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => 'grant_type=client_credentials',
            'auth' => [$this->clientId, $this->secret, 'basic']
        ]);

        $data = json_decode($response->getBody(), true);

        $access_token = $data['access_token'];

        return response()->json(['token' => $access_token]);

    }


    public function createPlan(Request $request)
    {
        $url = 'https://api.sandbox.paypal.com/v1/billing/plans';

        $data = [
            'product_id'    => 'PROD-1MG00333TW090705R',
            'name'          => 'plano audima v2.0',
            'description'   => 'Lorem Ipsum',
            
            // ciclo de cobrança
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit'  => 'MONTH',
                        'interval_count' => 1
                    ],
                    'tenure_type'       => 'REGULAR',
                    'sequence'          => 1,
                    'total_cycles'      => 12,
                    'pricing_scheme'    => [
                        'fixed_price' => [
                            'value'         => 10,
                            'currency_code' => 'USD'
                        ]
                    ]
                ]
            ],

            // preferencia de pagamento
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value'         => 10,
                    'currency_code' => 'USD'
                ],
                'setup_fee_failure_action'  => 'CONTINUE',
                'payment_failure_threshold' => 3
            ],

            // taxas (impostos)
            'taxes' => [
                'percentage'    => '10',
                'inclusive'     => false
            ]

        ];

        $response = $this->client->request('POST', $url, [
            'headers' =>
            [
                'Content-Type'      => 'application/json',
                'Accept'            => 'application/json',
                'PayPal-Request-Id' => 'PLAN-AUDIMA-18062019-001',
                'Authorization'     => "Bearer $request->token",
                'Prefer'            => 'return=representation'
            ],
            'json' => $data
        ]);
        
        return $response;

    }
}
