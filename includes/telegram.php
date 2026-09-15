<?php
/**
 * Отправка сообщения в Telegram-бот.
 * Токены и chat_id настраиваются в админке (Настройки → Боты),
 * хранятся в data/settings.json.
 */
function tg_send($token, $chat_id, $text) {
    if (empty($token) || empty($chat_id)) {
        return ['ok' => false, 'error' => 'Бот не настроен (нет token/chat_id)'];
    }
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $payload = json_encode([
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
    ]);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
        ]);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) return ['ok' => false, 'error' => $err];
        return ['ok' => true, 'response' => $result];
    }

    // Фолбэк без cURL
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $payload,
            'timeout' => 8,
        ],
    ]);
    $result = @file_get_contents($url, false, $context);
    return ['ok' => $result !== false, 'response' => $result];
}

/**
 * Отправить уведомление о новой заявке в нужного бота (курсы или консультация)
 * согласно настройкам из админки.
 */
function notify_new_lead($lead) {
    $settings = read_json('settings.json', []);
    $botKey = $lead['type'] === 'consultation' ? 'consult_bot' : 'course_bot';
    $bot = $settings[$botKey] ?? null;
    if (!$bot) return;

    $label = $lead['type'] === 'consultation' ? 'Заявка на консультацию' : 'Заявка на курс';
    $text = "<b>{$label}</b>\n"
        . "Имя: " . h($lead['name']) . "\n"
        . "Телефон: " . h($lead['phone']) . "\n"
        . (!empty($lead['course']) ? "Курс: " . h($lead['course']) . "\n" : "")
        . (!empty($lead['message']) ? "Комментарий: " . h($lead['message']) . "\n" : "")
        . "Время: " . $lead['created_at'];

    tg_send($bot['token'] ?? '', $bot['chat_id'] ?? '', $text);
}
