<script>
    (() => {
        const userAgent = navigator.userAgent || '';
        const isAndroidWebView = /;\s*wv\)/i.test(userAgent)
            || (/Android/i.test(userAgent) && /Version\/4\.0/i.test(userAgent) && /Chrome\/\d+/i.test(userAgent));
        const isIosWebView = /iP(?:hone|ad|od)/i.test(userAgent) && !/Safari/i.test(userAgent);
        const hasNativeBridge = typeof window.ReactNativeWebView !== 'undefined'
            || typeof window.flutter_inappwebview !== 'undefined'
            || typeof window.Android !== 'undefined';

        if (isAndroidWebView || isIosWebView || hasNativeBridge) {
            document.documentElement.classList.add('is-mobile-webview');
        }
    })();
</script>
