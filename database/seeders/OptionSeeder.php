<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Helpers\Options;

class OptionSeeder extends Seeder
{
    private function defaultOptions()
    {
        return [
            'site_title' => config('app.name'),
            'site_tagline' => '',
            'enable_register' => 'N',
            'site_language' => config('app.locale'),
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = new Options();
        $options->setMany($this->defaultOptions());
    }
}
