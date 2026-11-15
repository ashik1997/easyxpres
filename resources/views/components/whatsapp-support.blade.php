@props([
    'whatsapp' => null,
    'avoidBottomNav' => false,
])

@php
    $rawWhatsapp = trim((string) $whatsapp);
    $formattedWhatsapp = preg_replace('/[\s().-]+/', '', $rawWhatsapp);
    $formattedWhatsapp = preg_replace('/^\+/', '', $formattedWhatsapp);
    $formattedWhatsapp = preg_replace('/^00/', '', $formattedWhatsapp);

    if (preg_match('/^01[3-9]\d{8}$/', $formattedWhatsapp ?? '')) {
        $formattedWhatsapp = '88' . $formattedWhatsapp;
    }

    $whatsappNumber = preg_match('/^[1-9]\d{6,14}$/', $formattedWhatsapp ?? '')
        ? $formattedWhatsapp
        : null;
@endphp

@if ($whatsappNumber)
    @once
        <style>
            .wa-chat-box-poweredby {
                display: none !important;
            }

            .wa-chat-bubble,
            .wa-chat-bubble-close-button {
                display: none !important;
            }

            .wa-chat-box-brand {
                box-sizing: border-box !important;
                padding: 4px !important;
                border: 2px solid rgba(255, 255, 255, .88) !important;
                border-radius: 50% !important;
                background: #fff !important;
                object-fit: contain !important;
                box-shadow: 0 5px 16px rgba(16, 42, 54, .16) !important;
            }
        </style>

        <script>
            (() => {
                const url = 'https://wati-integration-prod-service.clare.ai/v2/watiWidget.js?19806';
                const script = document.createElement('script');
                script.type = 'text/javascript';
                script.async = true;
                script.src = url;

                const options = {
                    enabled: true,
                    chatButtonSetting: {
                        backgroundColor: '#00e785',
                        ctaText: '',
                        borderRadius: '25',
                        marginLeft: '0',
                        marginRight: '20',
                        marginBottom: @json($avoidBottomNav ? '96' : '20'),
                        ctaIconWATI: false,
                        position: 'right'
                    },
                    brandSetting: {
                        brandName: 'EasyXpres Support',
                        brandSubTitle: 'Online · Available now',
                        brandImg: @json(asset('images/logo.png')),
                        welcomeText: "Hi there!\nHow can we help you?",
                        messageText: 'Hello, I need some assistance. Page: @{{page_link}}',
                        backgroundColor: '#00e785',
                        ctaText: 'Chat on WhatsApp',
                        borderRadius: '25',
                        autoShow: false,
                        phoneNumber: @json($whatsappNumber)
                    }
                };

                script.onload = () => {
                    if (typeof window.CreateWhatsappChatWidget === 'function') {
                        window.CreateWhatsappChatWidget(options);

                        // Keep the WATI-rendered launcher, but make it perform the
                        // exact same action as WATI's "Chat on WhatsApp" link.
                        document.addEventListener('click', (event) => {
                            if (!(event.target instanceof Element)) return;

                            const launcher = event.target.closest('.wa-widget-send-button');
                            if (!launcher) return;

                            event.preventDefault();
                            event.stopImmediatePropagation();

                            const chatLink = document.querySelector('.wa-chat-box-content-send-btn');
                            if (chatLink instanceof HTMLAnchorElement) {
                                const opened = window.open(chatLink.href, chatLink.target || '_blank', 'noopener,noreferrer');
                                if (!opened) window.location.href = chatLink.href;
                                return;
                            }

                            const fallbackUrl = `https://api.whatsapp.com/send?phone=${options.brandSetting.phoneNumber}&text=${encodeURIComponent(options.brandSetting.messageText.replace('@{{page_link}}', window.location.href))}`;
                            const opened = window.open(fallbackUrl, '_blank', 'noopener,noreferrer');
                            if (!opened) window.location.href = fallbackUrl;
                        }, true);
                    }
                };

                const firstScript = document.getElementsByTagName('script')[0];
                if (firstScript?.parentNode) {
                    firstScript.parentNode.insertBefore(script, firstScript);
                } else {
                    document.head.appendChild(script);
                }
            })();
        </script>
    @endonce
@endif
