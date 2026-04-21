<?php

namespace Tests\Feature;

use App\Models\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    /** Проверяет сохранение и чтение двуязычного контента модели. */
    public function test_it_sets_and_gets_translated_content(): void
    {
        SiteContent::setContent('about', [
            'ru' => 'Текст RU',
            'tk' => 'Tekst TK',
        ]);

        $this->assertSame('Текст RU', SiteContent::getContent('about', 'ru'));
        $this->assertSame('Tekst TK', SiteContent::getContent('about', 'tk'));
    }

    /** Проверяет, что HomeController передаёт контент about в Inertia props. */
    public function test_home_page_contains_about_content_props(): void
    {
        SiteContent::setContent('about_title', [
            'ru' => 'О нашем комплексе',
            'tk' => 'Toplumymyz hakda',
        ]);
        SiteContent::setContent('about', [
            'ru' => 'Русский текст',
            'tk' => 'Türkmen tekst',
        ]);

        $response = $this
            ->withHeader('X-Inertia', 'true')
            ->get(route('home'));

        $response->assertOk();
        $response->assertJsonPath('props.aboutContent.title.ru', 'О нашем комплексе');
        $response->assertJsonPath('props.aboutContent.title.tk', 'Toplumymyz hakda');
        $response->assertJsonPath('props.aboutContent.text.ru', 'Русский текст');
        $response->assertJsonPath('props.aboutContent.text.tk', 'Türkmen tekst');
    }
}
