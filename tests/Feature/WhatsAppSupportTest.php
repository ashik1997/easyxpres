<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WhatsAppSupportTest extends TestCase
{
    public function test_it_renders_a_safe_click_to_chat_link_for_an_international_number(): void
    {
        $html = Blade::render(
            '<x-whatsapp-support :whatsapp="$number" :avoid-bottom-nav="true" />',
            ['number' => '+960 777-1234']
        );

        $this->assertStringContainsString('wati-integration-prod-service.clare.ai/v2/watiWidget.js?19806', $html);
        $this->assertStringContainsString("phoneNumber: \"9607771234\"", $html);
        $this->assertStringContainsString("marginBottom: \"96\"", $html);
        $this->assertStringContainsString('EasyXpres Support', $html);
        $this->assertStringContainsString('Online · Available now', $html);
        $this->assertStringContainsString('Chat on WhatsApp', $html);
        $this->assertStringContainsString('Hello, I need some assistance. Page: {{page_link}}', $html);
        $this->assertStringContainsString('autoShow: false', $html);
        $this->assertStringContainsString('.wa-chat-box-poweredby', $html);
        $this->assertStringContainsString('display: none !important', $html);
        $this->assertStringContainsString('.wa-chat-bubble-close-button', $html);
        $this->assertStringContainsString('.wa-chat-box-brand', $html);
        $this->assertStringContainsString('background: #fff !important', $html);
        $this->assertStringContainsString("closest('.wa-widget-send-button')", $html);
        $this->assertStringContainsString("querySelector('.wa-chat-box-content-send-btn')", $html);
    }

    public function test_it_normalizes_the_existing_bangladesh_local_number(): void
    {
        $html = Blade::render('<x-whatsapp-support :whatsapp="$number" />', [
            'number' => '01731002123',
        ]);

        $this->assertStringContainsString("phoneNumber: \"8801731002123\"", $html);
    }

    #[DataProvider('invalidNumbers')]
    public function test_it_does_not_render_for_a_missing_or_invalid_number(?string $number): void
    {
        $html = Blade::render('<x-whatsapp-support :whatsapp="$number" />', compact('number'));

        $this->assertStringNotContainsString('watiWidget.js', $html);
        $this->assertStringNotContainsString('CreateWhatsappChatWidget', $html);
    }

    public static function invalidNumbers(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'local number' => ['07771234'],
            'too short' => ['123456'],
            'too long' => ['1234567890123456'],
            'letters' => ['960CALLNOW'],
            'url' => ['https://wa.me/9607771234'],
        ];
    }
}
