<?php

use Illuminate\Database\Seeder;
use App\Produto;

class ProdutosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Produto::class, 25)->create();
    }
}
