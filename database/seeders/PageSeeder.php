<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content as Page;

class PageSeeder extends Seeder
{
    private $userID = 1;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->generateAboutPage();
        $this->generatePrivacyPolicyPage();
        $this->generateTermsAndConditionsPage();
        $this->generateDisclaimerPage();
        $this->generateDmcaPage();
    }

    private function generateAboutPage()
    {
        $content = view('stubs.pages.en.about')->render();
        Page::create([
            'user_id' => $this->userID,
            'title' => 'About Us',
            'content' => $content,
            'type' => 'PAGE',
        ]);
    }

    private function generatePrivacyPolicyPage()
    {
        $content = view('stubs.pages.en.privacy-policy')->render();
        Page::create([
            'user_id' => $this->userID,
            'title' => 'Privacy Policy',
            'content' => $content,
            'type' => 'PAGE',
        ]);
    }

    private function generateTermsAndConditionsPage()
    {
        $content = view('stubs.pages.en.terms')->render();
        Page::create([
            'user_id' => $this->userID,
            'title' => 'Terms and Conditions',
            'content' => $content,
            'type' => 'PAGE',
        ]);
    }

    private function generateDisclaimerPage()
    {
        $content = view('stubs.pages.en.disclaimer')->render();
        Page::create([
            'user_id' => $this->userID,
            'title' => 'Disclaimer',
            'content' => $content,
            'type' => 'PAGE',
        ]);
    }

    private function generateDmcaPage()
    {
        $content = view('stubs.pages.en.dmca')->render();
        Page::create([
            'user_id' => $this->userID,
            'title' => 'DMCA Copyright',
            'content' => $content,
            'type' => 'PAGE',
        ]);
    }
}
