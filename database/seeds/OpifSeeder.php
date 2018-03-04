<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpifSeeder extends Seeder
{
    protected $pifs = [
        // Классические фонды
        ['Акции роста', 'Альфа-Капитал Акции роста', 'https://www.alfacapital.ru/disclosure/pifs/ipifa_akar/'],
        ['Баланс', 'Альфа-Капитал Баланс', 'https://www.alfacapital.ru/disclosure/pifs/opif_aks/'],
        ['Резерв', 'Альфа-Капитал Резерв', 'https://www.alfacapital.ru/disclosure/pifs/opif_akr/'],
        ['Еврооблигации', 'Альфа-Капитал Еврооблигации', 'https://www.alfacapital.ru/disclosure/pifs/opifo_akbond/'],
        ['Индекс ММВБ', 'Альфа-Капитал Индекс ММВБ', 'https://www.alfacapital.ru/disclosure/pifs/oipif_ak_mmvb/'],
        ['Облигации Плюс', 'Альфа-Капитал Облигации Плюс', 'https://www.alfacapital.ru/disclosure/pifs/opif_akop/'],
        ['Ликвидные акции', 'Альфа-Капитал Ликвидные акции', 'https://www.alfacapital.ru/disclosure/pifs/opifa_akliq/'],

        // Отраслевые фонды
        ['Ресурсы', 'Альфа-Капитал Ресурсы', 'https://www.alfacapital.ru/disclosure/pifs/opifa_akn/'],
        ['Технологии', 'Альфа-Капитал Технологии', 'https://www.alfacapital.ru/disclosure/pifs/opifa_akt/'],
        ['Инфраструктура', 'Альфа-Капитал Инфраструктура', 'https://www.alfacapital.ru/disclosure/pifs/opifa_ake/'],
        ['Торговля', 'Альфа-Капитал Торговля', 'https://www.alfacapital.ru/disclosure/pifs/opifa_akps/'],
        ['Бренды', 'Альфа-Капитал Бренды', 'https://www.alfacapital.ru/disclosure/pifs/opifa_akf/'],

        // Специализированные фонды
        ['Стратегические инвестиции', 'Альфа-Капитал Стратегические инвестиции', 'https://www.alfacapital.ru/disclosure/pifs/opifsi_fpr/'],
        ['Альфа-Капитал', 'Альфа-Капитал', 'https://www.alfacapital.ru/disclosure/pifs/ipifsi_ak/'],
        ['Золото', 'Альфа-Капитал Золото', 'https://www.alfacapital.ru/disclosure/pifs/opifa_akg/'],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = DB::table('opifs');

        $table->truncate();

        foreach ($this->pifs as $pif) {
            $table->insert([
                'name' => $pif[0],
                'fullName' => $pif[1],
                'publicDataUrl' => $pif[2],
            ]);
        }
    }
}
