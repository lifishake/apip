<?php
/**
 * @file    jmp.php
 * @brief   跳转到外链画面
 * @group   2.10
 * @since   1.46.4
 * @note    1.46.4      2026-11-23      初版
 * @version 1.46.8      2026-11-26      增加对referer的判断
 * @date    2026-11-26
 */

header('X-Robots-Tag: noindex, nofollow', true);
// 检查referer
$current_host = isset($_SERVER['HTTP_HOST']) ? strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'])) : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';
$referer_host = $referer !== '' ? strtolower((string) parse_url($referer, PHP_URL_HOST)) : '';

// 如果没有 Referer（如直接地址栏粘贴、书签打开）或来源域名与当前站点不一致
if ($current_host === '' || $referer_host === '' || $referer_host !== $current_host) {
    header('HTTP/2.0 403 Forbidden');
    exit;
}

if (0 === url_to_postid($referer)) {
    header('HTTP/2.0 403 Forbidden');
    exit;
}

// 协议确认
$default_url = home_url();

$raw_url = isset($_GET['state']) ? trim($_GET['state']) : '';
if (function_exists('yh_decrypt')) {
    $url = yh_decrypt($raw_url);
} else {
    $url = base64_decode($raw_url, true);
}

$is_valid = false;
$target_url = $default_url;
if ($url !== false && filter_var($url, FILTER_VALIDATE_URL) ) {
    $target_url = $url;
    $is_valid = true;
}

$safe_url = htmlspecialchars($target_url, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>即将离开本站 - 安全跳转提示</title>
    <?php wp_head(); ?>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", var(--🖊-base), Roboto, "PingFang SC", "Microsoft YaHei", sans-serif;
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            width: 100%;
            max-width: 460px;
            padding: 32px 28px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            text-align: center;
        }
        .title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 18px;
        }
        .url-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: var(--color-ref, #2563eb);
            word-break: break-all;
            margin-bottom: 24px;
            text-align: left;
            max-height: 80px;
            overflow-y: auto;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .btn {
            flex: 1;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        .btn-cancel {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        .btn-cancel:hover {
            background-color: #e5e7eb;
            color: #1f2937;
        }
        .btn-go {
            background-color:var(--color-ref, #2563eb);
            color: #ffffff;
        }
        .btn-go:hover {
            background-color:var(--color-havor, #1d4ed8);
        }
        .countdown {
            font-weight: 600;
            color:var(--color-ref, #2563eb);
        }
    </style>
</head>
<body>

<div class="card">
    <div class="title">即将离开本站</div>
    <p class="desc">
        <?php if ($is_valid): ?>
            您正在访问外部网站，请注意账号和财产安全。<br>页面将在 <span id="timer" class="countdown">5</span> 秒后自动跳转：
        <?php else: ?>
            目标链接已过期，<span id="timer" class="countdown">5</span> 秒后将返回首页：
        <?php endif; ?>
    </p>

    <div class="url-box" id="targetUrl"><?php echo $safe_url; ?></div>

    <div class="btn-group">
        <button type="button" class="btn btn-cancel" id="cancelBtn">取消并关闭</button>
        <a href="<?php echo $safe_url; ?>" class="btn btn-go" rel="nofollow noreferrer">立即前往</a>
    </div>
</div>

<script>
    (function () {
        var targetUrl = <?php echo json_encode($target_url, JSON_UNESCAPED_SLASHES); ?>;
        var seconds = 5;
        var timerEl = document.getElementById('timer');
        var cancelBtn = document.getElementById('cancelBtn');
        var isCanceled = false;

        var interval = setInterval(function () {
            if (isCanceled) {
                clearInterval(interval);
                return;
            }
            seconds--;
            if (seconds > 0) {
                timerEl.textContent = seconds;
            } else {
                clearInterval(interval);
                timerEl.textContent = '0';
                window.location.replace(targetUrl);
            }
        }, 1000);

        cancelBtn.addEventListener('click', function () {
            isCanceled = true;
            clearInterval(interval);

            window.opener = null;
            window.open('', '_self');
            window.close();

            setTimeout(function () {
                if (!window.closed) {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        document.querySelector('.title').textContent = '已取消跳转';
                        document.querySelector('.desc').textContent = '受浏览器安全限制无法自动关闭标签页，请手动关闭此窗口。';
                        cancelBtn.style.display = 'none';
                    }
                }
            }, 150);
        });
    })();
</script>
<?php wp_footer(); ?>
</body>
</html>
