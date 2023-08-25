<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetInfo extends Command
{
    private const HOST = 'http://89.108.115.241:6969/api/';
    private const KEY = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
    private const COUNT_PER_PAGE = 500;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get info and save it in DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $page = 1;
        do {
            $arrayStocksResponse = $this->getJsonResponse('stocks', $page, date('Y-m-d'));

            foreach ($arrayStocksResponse['data'] as $stock) {
                error_log(json_encode($stock));
                Stock::create($stock);
            }

            $page++;
        } while ($page <= $arrayStocksResponse['meta']['last_page']);

        $page = 1;
        do {
            $arrayIncomesResponse = $this->getJsonResponse('incomes', $page);

            foreach ($arrayIncomesResponse['data'] as $income) {
                error_log(json_encode($income));
                Income::create($income);
            }

            $page++;
        } while ($page <= $arrayIncomesResponse['meta']['last_page']);

        $page = 1;
        do {
            $arraySalesResponse = $this->getJsonResponse('sales', $page);

            foreach ($arraySalesResponse['data'] as $sale) {
                error_log(json_encode($sale));
                Sale::create($sale);
            }

            $page++;
        } while ($page <= $arraySalesResponse['meta']['last_page']);

        $page = 1;
        do {
            $arrayOrdersResponse = $this->getJsonResponse('orders', $page);

            foreach ($arrayOrdersResponse['data'] as $order) {
                error_log(json_encode($order));
                Order::create($order);
            }

            $page++;
        } while ($page <= $arrayOrdersResponse['meta']['last_page']);

        return 0;
    }

    private function getJsonResponse($path, $page, $dateFrom = '2023-08-01'): array
    {
        $response = Http::get(self::HOST . $path, [
            'dateFrom' => $dateFrom,
            'dateTo' => date('Y-m-d'),
            'page' => $page,
            'key' => self::KEY,
            'limit' => self::COUNT_PER_PAGE
        ]);

        return json_decode($response, true);
    }
}
